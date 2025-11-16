# Biblioteca - Lab1Leng2

Este repositorio contiene una aplicación simple para gestionar una biblioteca (autores, libros, clientes y préstamos) desarrollada en PHP. Este README incluye instrucciones básicas para levantar el proyecto localmente y una breve descripción de la estructura.

Autor: Jose Lopez

## Requisitos

- MySQL 
- XAMPP (o similar) para entorno local
- Un navegador web

## Instalación y puesta en marcha

1. Coloca la carpeta del proyecto en el directorio de tu servidor local (por ejemplo `c:\xampp\htdocs\Lab1Leng2`).
2. Inicia los servicios de Apache y MySQL desde XAMPP.
3. Importa la base de datos: abre phpMyAdmin y ejecuta el archivo `biblioteca.sql` para crear las tablas y datos iniciales.
4. Configura la conexión a la base de datos en `Config/database.php` si necesitas cambiar usuario/contraseña o el nombre de la base de datos.
5. Abre el navegador y visita `http://localhost/Lab1Leng2/index.php`.

## Uso

- Regístrate o inicia sesión (vía `Views/auth/register.php` y `Views/auth/login.php`).
- Gestiona autores, libros, clientes y préstamos desde la interfaz web.

## Estructura principal

- `Config/` - Configuración de la base de datos.
- `Controllers/` - Controladores que implementan la lógica de la aplicación.
- `Models/` - Modelos para acceder a la base de datos.
- `Views/` - Plantillas y vistas (HTML/PHP) para la interfaz.
- `biblioteca.sql` - Script SQL para crear y poblar la base de datos.
- `index.php` - Punto de entrada de la aplicación.

## Notas técnicas

- El proyecto usa una estructura MVC básica.

