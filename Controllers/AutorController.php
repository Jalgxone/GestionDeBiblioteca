<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Autor.php';

class AutorController extends BaseController {
    public function index() {
        $this->requireLogin();
        $autores = Autor::all();
        $this->render('autores/index', compact('autores'));
    }

    public function create() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $nacionalidad = $_POST['nacionalidad'] ?? null;
            $fecha = $_POST['fecha_nacimiento'] ?? null;
            if (trim($nombre) === '') {
                $this->addFlash('error', 'El nombre del autor es obligatorio.');
                $this->render('autores/create');
                return;
            }
            if (strlen(trim($nombre)) < 2) {
                $this->addFlash('error', 'El nombre del autor debe tener al menos 2 caracteres.');
                $this->render('autores/create');
                return;
            }
            if (!empty($fecha) && !strtotime($fecha)) {
                $this->addFlash('error', 'La fecha de nacimiento no es válida.');
                $this->render('autores/create');
                return;
            }
            Autor::create($nombre, $nacionalidad, $fecha);
            $this->addFlash('success', 'Autor creado correctamente.');
            $this->redirect('?c=autor&a=index');
        } else {
            $this->render('autores/create');
        }
    }

    public function edit() {
        $this->requireLogin();
        $id = intval($_GET['id'] ?? 0);
        $autor = Autor::find($id);
        if (!$autor) {
            $this->addFlash('error', 'Autor no encontrado.');
            $this->redirect('?c=autor&a=index');
            return;
        }
        $this->render('autores/edit', compact('autor'));
    }

    public function update() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?c=autor&a=index');
            return;
        }
        $id = intval($_POST['id'] ?? 0);
        $nombre = $_POST['nombre'] ?? '';
        $nacionalidad = $_POST['nacionalidad'] ?? null;
        $fecha = $_POST['fecha_nacimiento'] ?? null;
        if (trim($nombre) === '') {
            $this->addFlash('error', 'El nombre del autor es obligatorio.');
            $this->redirect('?c=autor&a=edit&id=' . $id);
            return;
        }
        if (strlen(trim($nombre)) < 2) {
            $this->addFlash('error', 'El nombre del autor debe tener al menos 2 caracteres.');
            $this->redirect('?c=autor&a=edit&id=' . $id);
            return;
        }
        if (!empty($fecha) && !strtotime($fecha)) {
            $this->addFlash('error', 'La fecha de nacimiento no es válida.');
            $this->redirect('?c=autor&a=edit&id=' . $id);
            return;
        }
        Autor::update($id, $nombre, $nacionalidad, $fecha);
        $this->addFlash('success', 'Autor actualizado correctamente.');
        $this->redirect('?c=autor&a=index');
    }

    public function delete() {
        $this->requireLogin();
        $id = intval($_GET['id'] ?? 0);
        $autor = Autor::find($id);
        if (!$autor) {
            $this->addFlash('error', 'Autor no encontrado.');
            $this->redirect('?c=autor&a=index');
            return;
        }
        Autor::delete($id);
        $this->addFlash('success', 'Autor eliminado.');
        $this->redirect('?c=autor&a=index');
    }
}
