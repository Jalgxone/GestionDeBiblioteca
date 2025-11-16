<?php
// Front controller / simple router
// Usa: ?c=controlador&a=accion  (ejemplo: ?c=auth&a=login)

// Asegurar reporte de errores para desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Parámetros por defecto
$c = !empty($_GET['c']) ? $_GET['c'] : 'auth';
$a = !empty($_GET['a']) ? $_GET['a'] : 'login';

$controllerClass = ucfirst($c) . 'Controller';
$controllerFile = __DIR__ . '/Controllers/' . $controllerClass . '.php';

if (file_exists($controllerFile)) {
		require_once $controllerFile;
		if (class_exists($controllerClass)) {
				$controller = new $controllerClass();
				if (method_exists($controller, $a)) {
						// Ejecutar la acción
						$controller->{$a}();
						exit;
				} else {
						header('HTTP/1.1 404 Not Found');
						echo "Acción '$a' no encontrada en controlador '$controllerClass'.";
						exit;
				}
		}
}

// Si no existe controlador o clase, mostrar una página simple
?>
<!-- <!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>Biblioteca - Router</title>
	<style>body{font-family:Arial;padding:20px}</style>
</head>
<body>
	<h2>Ruta no encontrada</h2>
	<p>Intenta una de las rutas:</p>
	<ul>
		<li><a href="?c=auth&a=login">Login</a></li>
		<li><a href="?c=auth&a=register">Registro</a></li>
		<li><a href="?c=libro&a=index">Libros</a></li>
		<li><a href="?c=autor&a=index">Autores</a></li>
		<li><a href="?c=prestamo&a=index">Préstamos</a></li>
        
	</ul>
</body>
</html> 
-->

