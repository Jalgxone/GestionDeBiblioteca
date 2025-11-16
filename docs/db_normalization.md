# Normalización de la base de datos — 1NF / 2NF / 3NF

Fecha: 2025-11-15

Este documento resume el estado actual del esquema (según `biblioteca.sql`), evalúa cómo encaja con 1NF/2NF/3NF y lista recomendaciones prácticas (y SQL) para reforzar integridad y trazabilidad.

## 1. Resumen del esquema actual

Tablas principales (resumen):

- `usuarios` (id, nombre, email UNIQUE, password_hash, rol, timestamps)
- `clientes` (id, nombre, email UNIQUE, telefono, documento, timestamps)
- `autores` (id, nombre, nacionalidad, fecha_nacimiento, timestamps)
- `categorias` (id, nombre, descripcion, timestamps)
- `libros` (id, titulo, isbn UNIQUE, editorial, fecha_publicacion [actualmente INT], cantidad_total, cantidad_disponible, timestamps)
- `autores_libros` (libro_id, autor_id, created_at) — relación N:M
- `categorias_libros` (libro_id, categoria_id, created_at) — relación N:M
- `prestamos` (id, libro_id, usuario_id, cliente_id, fecha_prestamo, fecha_devolucion, devuelto, timestamps)

Observaciones iniciales:

- Las relaciones N:M están modeladas con tablas intermedias (`autores_libros`, `categorias_libros`) — esto respeta 1NF/2NF.
- `fecha_publicacion` está exportada como INT (año) en tu volcado; conviene usar DATE o YEAR según necesidades.
- En el volcado actual existe la columna `cliente_id` en `prestamos`, pero NO hay constraint (FK) declarada para esa columna — esto permite filas huérfanas en `prestamos` si un cliente es eliminado o modificado fuera de las reglas de la app.

## 2. Evaluación por forma normal

1NF — Primera Forma Normal (valores atómicos)

- Estado: OK. No se aprecian columnas que almacenen listas o estructuras (ej. `autor1,autor2`), y las relaciones N:M están en tablas separadas.

2NF — Segunda Forma Normal (no dependencias parciales)

- Estado: OK. No hay tablas con clave compuesta que contengan atributos dependientes de solo una parte de la clave (las tablas N:M únicamente contienen claves y un timestamp creado).

3NF — Tercera Forma Normal (no dependencias transitivas)

- Estado: Mayormente OK. No hay dependencias transitivas claras (por ejemplo `cliente_nombre` dentro de `prestamos`). Sin embargo, la ausencia de una FK en `prestamos.cliente_id` es un problema de integridad referencial que hay que corregir para garantizar la forma práctica de 3NF (que los datos se recuperen mediante JOINs y no se dupliquen).

## 3. Recomendaciones concretas y SQL sugerido

1) Cambiar `fecha_publicacion` a DATE o YEAR (seguro y reversible):

```sql
ALTER TABLE libros ADD COLUMN fecha_publicacion_new DATE NULL;
UPDATE libros SET fecha_publicacion_new = STR_TO_DATE(CONCAT(fecha_publicacion, '-01-01'), '%Y-%m-%d') WHERE fecha_publicacion IS NOT NULL;
ALTER TABLE libros DROP COLUMN fecha_publicacion;
ALTER TABLE libros CHANGE fecha_publicacion_new fecha_publicacion DATE NULL;
```

2) Agregar la FK para `prestamos.cliente_id` (verificar antes datos huérfanos):

-- Paso A: encontrar filas huérfanas (si existen)

```sql
SELECT p.id, p.cliente_id FROM prestamos p LEFT JOIN clientes c ON p.cliente_id = c.id WHERE p.cliente_id IS NOT NULL AND c.id IS NULL;
```

-- Si el resultado está vacío, puedes añadir la constraint directamente:

```sql
ALTER TABLE prestamos
  ADD CONSTRAINT fk_prestamos_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL;
```

Nota: `ON DELETE SET NULL` evita borrar historial si borras un cliente; como alternativa `ON DELETE RESTRICT` impide borrar clientes con préstamos.

3) (Opcional) Si necesitas trazabilidad por ejemplar físico, crear tabla `ejemplares`:

```sql
CREATE TABLE ejemplares (
  id INT AUTO_INCREMENT PRIMARY KEY,
  libro_id INT NOT NULL,
  codigo_ejemplar VARCHAR(100) DEFAULT NULL,
  estado ENUM('disponible','prestado','dañado','reservado') DEFAULT 'disponible',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (libro_id) REFERENCES libros(id)
);
```

4) Índices recomendados

- `prestamos(fecha_prestamo)` para consultas por rango de fechas.
- `prestamos(cliente_id)` si no existe index, para agilizar búsquedas por cliente.

## 4. Impacto en la aplicación (código)

-- Los modelos y controladores ya usan las tablas N:M (`autores_libros`) correctamente. Las validaciones aplicadas recientemente (server-side) ayudan a evitar intentos de insertar datos inválidos (p.ej. ISBN no válido) pero no sustituyen la necesidad de restricciones DB.
- Agregar la FK propuesta reforzará la integridad incluso si alguien manipula la base de datos fuera de la app.

## 5. Pasos recomendados para aplicar cambios en producción

1. Hacer backup completo de la BD.
2. Ejecutar consultas de detección de huérfanos y resolver manualmente si aparecen.
3. Ejecutar los ALTERs en una ventana de mantenimiento (o migración controlada).
4. Probar la aplicación (flujo de préstamos, creación de clientes, eliminación de cliente con préstamos).
