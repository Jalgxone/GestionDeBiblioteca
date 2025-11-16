<?php
require_once __DIR__ . '/BaseModel.php';

class Libro extends BaseModel {
    public static function all() {
        $stmt = self::pdo()->query('SELECT * FROM libros ORDER BY titulo');
        return $stmt->fetchAll();
    }

    public static function find($id) {
        $stmt = self::pdo()->prepare('SELECT * FROM libros WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($titulo, $isbn = null, $editorial = null, $fecha_publicacion = null, $cantidad = 1) {
        $pdo = self::pdo();
        $stmt = $pdo->prepare('INSERT INTO libros (titulo, isbn, editorial, fecha_publicacion, cantidad_total, cantidad_disponible) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$titulo, $isbn, $editorial, $fecha_publicacion, $cantidad, $cantidad]);
        $libroId = $pdo->lastInsertId();
        return $libroId;
    }

    public static function updateAvailable($libro_id, $delta) {
        $stmt = self::pdo()->prepare('UPDATE libros SET cantidad_disponible = cantidad_disponible + ? WHERE id = ?');
        $stmt->execute([$delta, $libro_id]);
    }

    public static function addAuthors($libroId, array $authorIds) {
        if (empty($authorIds)) return;
        $pdo = self::pdo();
        $stmt = $pdo->prepare('INSERT IGNORE INTO autores_libros (libro_id, autor_id, created_at) VALUES (?, ?, NOW())');
        foreach ($authorIds as $aid) {
            $stmt->execute([$libroId, intval($aid)]);
        }
    }

    public static function removeAuthor($libroId, $authorId) {
        $stmt = self::pdo()->prepare('DELETE FROM autores_libros WHERE libro_id = ? AND autor_id = ?');
        $stmt->execute([$libroId, $authorId]);
    }

    public static function getAuthors($libroId) {
        $stmt = self::pdo()->prepare('SELECT a.* FROM autores a JOIN autores_libros al ON a.id = al.autor_id WHERE al.libro_id = ?');
        $stmt->execute([$libroId]);
        return $stmt->fetchAll();
    }

    public static function getAuthorIds($libroId) {
        $stmt = self::pdo()->prepare('SELECT autor_id FROM autores_libros WHERE libro_id = ?');
        $stmt->execute([$libroId]);
        return array_map(function($r){ return $r['autor_id']; }, $stmt->fetchAll());
    }

    public static function removeAllAuthors($libroId) {
        $stmt = self::pdo()->prepare('DELETE FROM autores_libros WHERE libro_id = ?');
        $stmt->execute([$libroId]);
    }

    public static function update($id, $titulo, $isbn = null, $editorial = null, $fecha_publicacion = null, $cantidad_total = 1) {
        $pdo = self::pdo();
        $stmt = $pdo->prepare('UPDATE libros SET titulo = ?, isbn = ?, editorial = ?, fecha_publicacion = ?, cantidad_total = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
        $stmt->execute([$titulo, $isbn, $editorial, $fecha_publicacion, $cantidad_total, $id]);
        // Ensure cantidad_disponible does not exceed cantidad_total
        $stmt2 = $pdo->prepare('UPDATE libros SET cantidad_disponible = LEAST(cantidad_disponible, cantidad_total) WHERE id = ?');
        $stmt2->execute([$id]);
    }

    public static function findByISBN($isbn) {
        if ($isbn === null) return null;
        $stmt = self::pdo()->prepare('SELECT * FROM libros WHERE isbn = ?');
        $stmt->execute([$isbn]);
        return $stmt->fetch();
    }

    public static function delete($id) {
        $pdo = self::pdo();
        // remove relations first
        $stmt = $pdo->prepare('DELETE FROM autores_libros WHERE libro_id = ?');
        $stmt->execute([$id]);
        // remove prestamos referencing the book (or keep history?). We'll keep prestamos for history but nullify libro_id is not ideal — delete prestamos for simplicity
        $stmt = $pdo->prepare('DELETE FROM prestamos WHERE libro_id = ?');
        $stmt->execute([$id]);
        $stmt = $pdo->prepare('DELETE FROM libros WHERE id = ?');
        $stmt->execute([$id]);
    }
}
