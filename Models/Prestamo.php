<?php
require_once __DIR__ . '/BaseModel.php';

class Prestamo extends BaseModel {
    public static function all() {
        // Preferir cliente (clientes table) si existe columna cliente_id, de lo contrario usar usuario_id para compatibilidad
        $pdo = self::pdo();
        $hasClienteCol = false;
        $cols = $pdo->query("SHOW COLUMNS FROM prestamos LIKE 'cliente_id'")->fetchAll();
        if (!empty($cols)) {
            $hasClienteCol = true;
        }
        if ($hasClienteCol) {
            $stmt = $pdo->query('SELECT p.*, l.titulo, c.nombre as cliente_nombre FROM prestamos p LEFT JOIN libros l ON p.libro_id = l.id LEFT JOIN clientes c ON p.cliente_id = c.id ORDER BY p.fecha_prestamo DESC');
        } else {
            $stmt = $pdo->query('SELECT p.*, l.titulo, u.nombre as usuario_nombre FROM prestamos p LEFT JOIN libros l ON p.libro_id = l.id LEFT JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.fecha_prestamo DESC');
        }
        return $stmt->fetchAll();
    }

    public static function find($id) {
        $stmt = self::pdo()->prepare('SELECT * FROM prestamos WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($libro_id, $cliente_or_usuario_id) {
        $pdo = self::pdo();
        // si existe cliente_id usarlo
        $cols = $pdo->query("SHOW COLUMNS FROM prestamos LIKE 'cliente_id'")->fetchAll();
        if (!empty($cols)) {
            $stmt = $pdo->prepare('INSERT INTO prestamos (libro_id, cliente_id, fecha_prestamo, devuelto) VALUES (?, ?, NOW(), 0)');
            $stmt->execute([$libro_id, $cliente_or_usuario_id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO prestamos (libro_id, usuario_id, fecha_prestamo, devuelto) VALUES (?, ?, NOW(), 0)');
            $stmt->execute([$libro_id, $cliente_or_usuario_id]);
        }
        return $pdo->lastInsertId();
    }

    public static function markReturned($id) {
        $stmt = self::pdo()->prepare('UPDATE prestamos SET devuelto = 1, fecha_devolucion = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }
}
