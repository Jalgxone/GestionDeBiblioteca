<?php
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    public static function findByEmail($email) {
        $stmt = self::pdo()->prepare('SELECT * FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function all() {
        $stmt = self::pdo()->query('SELECT id, nombre, email FROM usuarios ORDER BY nombre');
        return $stmt->fetchAll();
    }

    public static function create($nombre, $email, $password, $rol = 'bibliotecario') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = self::pdo()->prepare('INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?, ?, ?, ?)');
        $stmt->execute([$nombre, $email, $hash, $rol]);
        return self::pdo()->lastInsertId();
    }

    public static function findById($id) {
        $stmt = self::pdo()->prepare('SELECT * FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
