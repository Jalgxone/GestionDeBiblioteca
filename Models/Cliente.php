<?php
require_once __DIR__ . '/BaseModel.php';

class Cliente extends BaseModel {
    public static function all() {
        $stmt = self::pdo()->query('SELECT id, nombre, email, telefono FROM clientes ORDER BY nombre');
        return $stmt->fetchAll();
    }

    public static function find($id) {
        $stmt = self::pdo()->prepare('SELECT * FROM clientes WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($nombre, $email = null, $telefono = null, $documento = null) {
        $stmt = self::pdo()->prepare('INSERT INTO clientes (nombre, email, telefono, documento) VALUES (?, ?, ?, ?)');
        $stmt->execute([$nombre, $email, $telefono, $documento]);
        return self::pdo()->lastInsertId();
    }

    public static function findByEmail($email) {
        $stmt = self::pdo()->prepare('SELECT * FROM clientes WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function update($id, $nombre, $email = null, $telefono = null, $documento = null) {
        $stmt = self::pdo()->prepare('UPDATE clientes SET nombre = ?, email = ?, telefono = ?, documento = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
        $stmt->execute([$nombre, $email, $telefono, $documento, $id]);
    }

    public static function delete($id) {
        $stmt = self::pdo()->prepare('DELETE FROM clientes WHERE id = ?');
        $stmt->execute([$id]);
    }
}
