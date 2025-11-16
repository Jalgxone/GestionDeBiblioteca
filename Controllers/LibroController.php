<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Libro.php';
require_once __DIR__ . '/../Models/Autor.php';

class LibroController extends BaseController {
    public function index() {
        $this->requireLogin();
        $libros = Libro::all();
        // añadir autores para cada libro
        foreach ($libros as &$l) {
            $l['autores'] = Libro::getAuthors($l['id']);
        }
        unset($l);
        $this->render('libros/index', compact('libros'));
    }

    public function create() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = $_POST['titulo'] ?? '';
            $isbn = $_POST['isbn'] ?? null;
            $editorial = $_POST['editorial'] ?? null;
            $fecha = $_POST['fecha_publicacion'] ?? null;
            $cantidad = intval($_POST['cantidad_total'] ?? 1);
            $author_ids = $_POST['author_ids'] ?? [];
            // Validaciones
            if (trim($titulo) === '') {
                $this->addFlash('error', 'El título es obligatorio.');
                $autores = Autor::all();
                $this->render('libros/create', compact('autores','titulo','isbn','editorial','fecha','cantidad','author_ids'));
                return;
            }
            // cantidad debe ser entero no negativo
            if ($cantidad < 0) {
                $this->addFlash('error', 'La cantidad total debe ser un número positivo.');
                $autores = Autor::all();
                $this->render('libros/create', compact('autores'));
                return;
            }
            // fecha_publicacion: aceptar año (YYYY) o fecha válida
            if (!empty($fecha)) {
                // permitir año como '2025' o fecha ISO
                if (!(ctype_digit($fecha) && (strlen($fecha) === 4)) && !strtotime($fecha)) {
                    $this->addFlash('error', 'La fecha de publicación no es válida. Usa YYYY o formato de fecha.');
                    $autores = Autor::all();
                    $this->render('libros/create', compact('autores'));
                    return;
                }
            }
            // ISBN opcional pero si se proporciona validar caracteres básicos
            if (!empty($isbn)) {
                $cleanIsbn = str_replace(['-', ' '], '', $isbn);
                if (!preg_match('/^\d{10}(\d{3})?$/', $cleanIsbn)) {
                    $this->addFlash('error', 'El ISBN debe tener 10 o 13 dígitos (guiones opcionales).');
                    $autores = Autor::all();
                    $this->render('libros/create', compact('autores','titulo','isbn','editorial','fecha','cantidad','author_ids'));
                    return;
                }
            }
            // comprobar duplicidad de ISBN
            if (!empty($isbn)) {
                $existing = Libro::findByISBN($isbn);
                if ($existing) {
                    $this->addFlash('error', 'Ya existe un libro con ese ISBN.');
                    $autores = Autor::all();
                    $this->render('libros/create', compact('autores','titulo','isbn','editorial','fecha','cantidad','author_ids'));
                    return;
                }
            }
            try {
                $libroId = Libro::create($titulo, $isbn, $editorial, $fecha, $cantidad);
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $this->addFlash('error', 'Error al guardar libro: posible valor duplicado.');
                    $autores = Autor::all();
                    $this->render('libros/create', compact('autores','titulo','isbn','editorial','fecha','cantidad','author_ids'));
                    return;
                }
                throw $e;
            }
            if (!empty($author_ids)) {
                Libro::addAuthors($libroId, $author_ids);
            }
            $this->addFlash('success', 'Libro creado correctamente.');
            $this->redirect('?c=libro&a=index');
        } else {
            $autores = Autor::all();
            $this->render('libros/create', compact('autores'));
        }
    }

    public function edit() {
        $this->requireLogin();
        $id = intval($_GET['id'] ?? 0);
        $libro = Libro::find($id);
        if (!$libro) {
            $this->addFlash('error', 'Libro no encontrado.');
            $this->redirect('?c=libro&a=index');
        }
        $autores = Autor::all();
        $selected = Libro::getAuthorIds($id);
        $this->render('libros/edit', compact('libro','autores','selected'));
    }

    public function update() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?c=libro&a=index');
        }
        $id = intval($_POST['id'] ?? 0);
        $titulo = $_POST['titulo'] ?? '';
        $isbn = $_POST['isbn'] ?? null;
        $editorial = $_POST['editorial'] ?? null;
        $fecha = $_POST['fecha_publicacion'] ?? null;
        $cantidad = intval($_POST['cantidad_total'] ?? 1);
        $author_ids = $_POST['author_ids'] ?? [];

        if (trim($titulo) === '') {
            $this->addFlash('error', 'El título es obligatorio.');
            $this->redirect('?c=libro&a=edit&id=' . $id);
            return;
        }

            // Validaciones similares a create
            if ($cantidad < 0) {
                $this->addFlash('error', 'La cantidad total debe ser un número positivo.');
                $this->redirect('?c=libro&a=edit&id=' . $id);
                return;
            }
            if (!empty($fecha)) {
                if (!(ctype_digit($fecha) && (strlen($fecha) === 4)) && !strtotime($fecha)) {
                    $this->addFlash('error', 'La fecha de publicación no es válida. Usa YYYY o formato de fecha.');
                    $this->redirect('?c=libro&a=edit&id=' . $id);
                    return;
                }
            }
            if (!empty($isbn)) {
                $cleanIsbn = str_replace(['-', ' '], '', $isbn);
                if (!preg_match('/^\d{10}(\d{3})?$/', $cleanIsbn)) {
                    $this->addFlash('error', 'El ISBN debe tener 10 o 13 dígitos (guiones opcionales).');
                    $this->redirect('?c=libro&a=edit&id=' . $id);
                    return;
                }
                // comprobar duplicidad ISBN en otro libro
                $existing = Libro::findByISBN($isbn);
                if ($existing && intval($existing['id']) !== $id) {
                    $this->addFlash('error', 'Otro libro ya usa ese ISBN.');
                    $this->redirect('?c=libro&a=edit&id=' . $id);
                    return;
                }
            }
        try {
            Libro::update($id, $titulo, $isbn, $editorial, $fecha, $cantidad);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->addFlash('error', 'Error al actualizar libro: posible valor duplicado.');
                $this->redirect('?c=libro&a=edit&id=' . $id);
                return;
            }
            throw $e;
        }
        // update authors: remove all and add selected
        Libro::removeAllAuthors($id);
        if (!empty($author_ids)) {
            Libro::addAuthors($id, $author_ids);
        }
        $this->addFlash('success', 'Libro actualizado correctamente.');
        $this->redirect('?c=libro&a=index');
    }

    public function delete() {
        $this->requireLogin();
        $id = intval($_GET['id'] ?? 0);
        $libro = Libro::find($id);
        if (!$libro) {
            $this->addFlash('error', 'Libro no encontrado.');
            $this->redirect('?c=libro&a=index');
        }
        Libro::delete($id);
        $this->addFlash('success', 'Libro eliminado.');
        $this->redirect('?c=libro&a=index');
    }
}
