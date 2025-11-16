<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Libro.php';
require_once __DIR__ . '/../Models/Autor.php';

class AutorLibroController extends BaseController {
    public function index() {
        $this->requireLogin();
        $libros = Libro::all();
        foreach ($libros as &$l) {
            $l['autores'] = Libro::getAuthors($l['id']);
        }
        unset($l);
        $this->render('autor_libro/index', compact('libros'));
    }

    public function create() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $libro_id = intval($_POST['libro_id'] ?? 0);
            $author_ids = $_POST['author_ids'] ?? [];
            if (!$libro_id || empty($author_ids)) {
                $this->addFlash('error', 'Seleccione libro y al menos un autor.');
                $this->redirect('?c=autorLibro&a=create');
            }
            Libro::addAuthors($libro_id, $author_ids);
            $this->addFlash('success', 'Autores asignados correctamente.');
            $this->redirect('?c=autorLibro&a=index');
        } else {
            $libros = Libro::all();
            $autores = Autor::all();
            $this->render('autor_libro/create', compact('libros', 'autores'));
        }
    }

    public function remove() {
        $this->requireLogin();
        $libro_id = intval($_GET['libro_id'] ?? 0);
        $autor_id = intval($_GET['autor_id'] ?? 0);
        if ($libro_id && $autor_id) {
            Libro::removeAuthor($libro_id, $autor_id);
            $this->addFlash('success', 'Relación eliminada.');
        } else {
            $this->addFlash('error', 'Parámetros inválidos.');
        }
        $this->redirect('?c=autorLibro&a=index');
    }
}
