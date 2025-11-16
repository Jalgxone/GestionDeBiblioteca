<?php
require_once __DIR__ . '/BaseModel.php';

class Autor extends BaseModel {
    public static function all() {
        $stmt = self::pdo()->query('SELECT * FROM autores ORDER BY nombre');
        return $stmt->fetchAll();
    }

    public static function find($id) {
        $stmt = self::pdo()->prepare('SELECT * FROM autores WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($nombre, $nacionalidad = null, $fecha_nacimiento = null) {
        $stmt = self::pdo()->prepare('INSERT INTO autores (nombre, nacionalidad, fecha_nacimiento) VALUES (?, ?, ?)');
        $stmt->execute([$nombre, $nacionalidad, $fecha_nacimiento]);
        return self::pdo()->lastInsertId();
    }

    public static function update($id, $nombre, $nacionalidad = null, $fecha_nacimiento = null) {
        $stmt = self::pdo()->prepare('UPDATE autores SET nombre = ?, nacionalidad = ?, fecha_nacimiento = ? WHERE id = ?');
        return $stmt->execute([$nombre, $nacionalidad, $fecha_nacimiento, $id]);
    }

    public static function delete($id) {
        $stmt = self::pdo()->prepare('DELETE FROM autores WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
