<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Biblioteca</title>
    <link rel="stylesheet" href="Views/assets/style.css">
    </head>
<body>
<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<header class="site-header">
    <div class="container">
        <h1 class="brand"><a href="?c=libro&a=index">Biblioteca</a></h1>
        <nav class="main-nav">
            <?php if(isset($_SESSION['user'])): ?>
                <a href="?c=libro&a=index">Libros</a>
                <a href="?c=autor&a=index">Autores</a>
                <a href="?c=cliente&a=index">Clientes</a>
                <a href="?c=autorLibro&a=index">Relaciones</a>
                <a href="?c=prestamo&a=index">Préstamos</a>
            <?php endif; ?>
        </nav>
        <div class="user-actions">
            <?php if(isset($_SESSION['user'])): ?>
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['user']['nombre']); ?></span>
                <a class="btn small" href="?c=auth&a=logout">Salir</a>
            <?php else: ?>
                <a class="btn" href="?c=auth&a=login">Login</a>
                <a class="btn ghost" href="?c=auth&a=register">Registro</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<?php
// Mostrar mensajes flash si hay
if (!empty($_SESSION['flash'])):
    $flashes = $_SESSION['flash'];
    unset($_SESSION['flash']);
    echo '<div class="container">';
    foreach ($flashes as $f) {
        $t = htmlspecialchars($f['type']);
        $m = htmlspecialchars($f['message']);
        echo "<div class=\"flash flash-{$t}\">{$m}</div>";
    }
    echo '</div>';
endif;

?><main class="container">

