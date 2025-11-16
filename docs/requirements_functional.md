# Requisitos funcionales — aplicación Biblioteca (versión amigable)

Fecha: 2025-11-14

Este documento resume, en un lenguaje claro y práctico, qué debe hacer la aplicación: cómo se gestionan usuarios, libros, autores y préstamos, y cuáles son las reglas de negocio más importantes.

Si prefieres, puedo convertir esto en historias de usuario o en un diagrama de casos de uso.

## 1. Objetivo
Construir un sistema sencillo y seguro para administrar una biblioteca: inicio de sesión y registro de usuarios, gestión de libros/autores/categorías y el flujo de préstamos y devoluciones.

## 2. Actores (quién usa el sistema)
- Administrador: controla todo el sistema (crear/editar/borrar usuarios, ver y modificar datos globales).
- Bibliotecario: gestiona libros y préstamos. No necesariamente puede tocar la administración de usuarios.
- Cliente (prestatario): la persona que toma prestado el libro. En este sistema puede ser un registro separado (`clientes`) o un `usuario` del sistema, según tu preferencia.

## 3. Funcionalidades clave (lo que el sistema debe hacer)

Autenticación
- Registro de usuario: formulario con nombre, email y contraseña. El sistema debe validar datos, evitar emails duplicados y almacenar la contraseña de forma segura (hash bcrypt).
- Login: el usuario inicia sesión con email y contraseña; si las credenciales son correctas, se inicia una sesión y se guarda información mínima (id, nombre, rol).
- Logout: cierre seguro de sesión.

Gestión de usuarios
- CRUD de usuarios (solo para administradores). Reglas: email válido y único, contraseña con longitud mínima, rol válido.

Gestión de libros y autores
- CRUD de libros con validaciones: título obligatorio, ISBN opcional (si se da, validar 10/13 dígitos), fecha de publicación (DATE idealmente), y control de stock.
- Autores y categorías: CRUD y asociación N:M con libros (tablas intermedias).

Préstamos
- Registrar préstamo: seleccionar libro (o ejemplar), seleccionar cliente/usuario y registrar fecha. Al prestar, actualizar stock (o marcar ejemplar como prestado).
- Devolver: marcar devolución y actualizar stock.
- Regla importante: no permitir prestar si no hay unidades disponibles.

Reglas transversales
- Validaciones en servidor (siempre). Opcionalmente añadir validaciones en cliente para mejor UX.
- Mensajes flash para feedback (éxito/error).
- Control de acceso por roles (admin vs bibliotecario).
- Seguridad: usar prepared statements (PDO), hashear contraseñas, proteger formularios destructivos con CSRF, y usar POST para cambios en la DB.

## 4. Contratos de entrada y salida (ejemplos)
- Registro: { nombre: string, email: string, password: string }
- Login: { email: string, password: string }
- Crear libro: { titulo, isbn?, editorial?, fecha_publicacion?, cantidad_total:int, author_ids:[] }
- Crear préstamo: { libro_id, cliente_id }

## 5. Casos borde a cubrir
- Intento de registro con email ya usado.
- Formato de fecha incorrecto.
- Intento de préstamo cuando no hay stock.
- Eliminación de un autor que todavía tiene libros asociados (decidir la política: negar eliminación o eliminar relaciones).

## 6. Requisitos no funcionales (mínimos)
- Seguridad: TLS en producción, contraseñas hasheadas, tokens CSRF.
- Mantenibilidad: seguir MVC, tener una capa de servicios para reglas complejas.
- Escalabilidad: normalizar la BD (ver `docs/db_normalization.md`).

## 7. Organización de la lógica de negocio (sugerencia práctica)
- Models: clases con acceso a la base de datos (PDO), CRUD y consultas reutilizables.
- Controllers: validaciones y orquestación, llaman a Models y renderizan Views.
- Views: plantillas limpias (sin lógica de negocio compleja).
- Services (opcional, recomendado): encapsulan reglas complejas como el proceso de préstamo (ver `PrestamoService`).

---

## Actualización importante — 2025-11-15 (cambios aplicados)

Se aplicaron varias mejoras en el código que afectan a las reglas de negocio y validaciones. A continuación se resumen los cambios y el comportamiento esperado:

- Validaciones server-side reforzadas:
	- `AuthController::register`: ahora valida nombre no vacío, email con `filter_var`, contraseña mínima 6 caracteres y unicidad de email.
	- `ClienteController::create|update`: valida nombre, formato de email, longitud de teléfono y comprueba unicidad de email antes de insertar/actualizar.
	- `LibroController::create|update`: valida título, cantidad no negativa, fecha (acepta YYYY o fecha válida), verifica formato de ISBN (10/13 dígitos) y comprueba unicidad de ISBN.

- Manejo de errores de base de datos:
	- Operaciones de inserción/actualización que puedan violar constraints (ej. UNIQUE) ahora capturan PDOException con SQLSTATE `23000` y muestran un mensaje amigable en la interfaz (flash), evitando que la app muestre una excepción cruda.

- Experiencia de usuario (formularios):
	- Los formularios de creación (`clientes`, `libros`, `autores`, `auth/register`) ahora repueblan los campos ingresados cuando ocurre un error de validación, evitando pérdida de datos.

- Integridad y recomendaciones:
	- Aunque la app valida duplicados de `email` e `ISBN` antes de insertar, se recomienda añadir las constraints en la BD (si no existen) y agregar la FK para `prestamos.cliente_id` para reforzar integridad (ver `docs/db_normalization.md`).

### Casos de prueba prioritarios (para QA)

1. Registrar usuario con email inválido y contraseña corta — debe mostrar errores apropiados y no crear usuario.
2. Crear cliente con email duplicado — debe mostrar mensaje y no insertar duplicado.
3. Crear libro con ISBN duplicado — debe mostrar mensaje y no insertar.
4. Intentar prestar un libro sin stock — debe mostrar error y no decrementar stock.
