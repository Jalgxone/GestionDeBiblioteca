## Documentación del código — guía práctica y cambios recientes

Fecha: 2025-11-15

Este documento resume la estructura del proyecto, el contrato público (entradas/salidas) de los controladores y modelos más relevantes, y documenta los cambios recientes realizados en validaciones, manejo de errores y vistas.

Objetivo: que cualquier desarrollador pueda entender rápidamente qué hace cada módulo y cuáles son las reglas de validación y errores posibles después de los últimos cambios.

### Estructura breve del proyecto

- `index.php` — punto de entrada y router (parámetros `?c=<Controlador>&a=<accion>`).
- `Config/` — configuración, p. ej. `database.php`.
- `Controllers/` — orquestan la lógica, validaciones y renderizan `Views`.
- `Models/` — acceso a la base de datos mediante PDO (consultas preparadas).
- `Views/` — plantillas (formulario, listados) y `Views/layout/header.php`.

### Cambios aplicados (resumen)

- Añadidas validaciones server-side más robustas en controladores: `AuthController::register`, `ClienteController::create|update`, `LibroController::create|update`.
- Manejo de excepciones PDO alrededor de operaciones que pueden violar constraints (ej. unique) y mensajes flash en caso de conflicto.
- Nuevos métodos auxiliares en modelos:
	- `Models\Cliente::findByEmail($email)` — devuelve cliente por email.
	- `Models\Libro::findByISBN($isbn)` — devuelve libro por ISBN.
- Mejora UX: formularios (`Views/*/create.php`, `Views/auth/register.php`, `Views/autores/create.php`) ahora repueblan valores cuando hay errores de validación.
- Pequeña mejora en vistas: botón "Editar" añadido en `Views/autor_libro/index.php` para editar autor directamente desde la lista de autores de un libro.

### Contratos y validaciones (resumen por controlador)

- `AuthController::register` (POST)
	- Inputs: { nombre: string, email: string, password: string }
	- Validaciones: nombre no vacío; email con `filter_var(..., FILTER_VALIDATE_EMAIL)`; password longitud mínima 6; email único.
	- Salida: redirección a index de libros en éxito; flash con error y re-render del formulario con valores previos en fallo.

- `ClienteController::create|update` (POST)
	- Inputs: { nombre, email?, telefono?, documento? }
	- Validaciones: nombre no vacío; email formato válido si presente; telefono longitud <= 50.
	- Reglas adicionales: comprobación de unicidad de `email` antes de insert/update; captura de PDOException (SQLSTATE 23000) para errores de constraint.

- `LibroController::create|update` (POST)
	- Inputs: { titulo, isbn?, editorial?, fecha_publicacion?, cantidad_total:int, author_ids:[] }
	- Validaciones: titulo obligatorio; cantidad >= 0; fecha acepta año 'YYYY' o formato de fecha; ISBN permite guiones y espacios pero validará 10/13 dígitos.
	- Reglas adicionales: comprobación de unicidad de `isbn` (si se proporciona); captura de PDOException (23000).

- `PrestamoController::create` (POST)
	- Inputs: { libro_id, cliente_id }
	- Validaciones: ids válidos (>0); libro disponible (cantidad_disponible>0). En éxito crea préstamo y decrementa stock.

### Modelos — métodos añadidos y notas

- `Cliente::findByEmail($email)` — usado para chequear unicidad antes de insertar/actualizar.
- `Libro::findByISBN($isbn)` — usado para detectar duplicados de ISBN.
- Todos los modelos usan consultas preparadas (mitigación básica de SQL injection).

### Vistas — comportamiento

- Los formularios `create` ahora recuperan los valores previos (variables inyectadas por el controlador) para evitar pérdida de datos al re-renderizar tras errores.
- Se preserva `author_ids[]` en el formulario de creación de libros cuando la validación falla.

### Manejo de errores y mensajes

- Se usan `addFlash(type, message)` y redirecciones/re-render para dar feedback al usuario.
- Se capturan errores PDO con SQLSTATE `23000` (violación constraint) y se muestra un mensaje amigable en lugar de un stack trace.

### Recomendaciones técnicas y próximos pasos

1. Añadir PHPDoc en controladores y modelos para que phpDocumentor u otras herramientas puedan generar documentación automatizada.
2. Añadir pruebas automáticas (unitarias/integ) para validar validaciones principales: registro con email duplicado, ISBN duplicado, préstamo sin stock.
3. Implementar CSRF tokens en formularios para mayor seguridad.
4. Considerar centralizar validaciones en una clase `Validator` o `Request` para evitar lógica duplicada entre create/update.
