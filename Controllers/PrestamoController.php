<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Prestamo.php';
require_once __DIR__ . '/../Models/Libro.php';
require_once __DIR__ . '/../Models/Cliente.php';

class PrestamoController extends BaseController {
    public function index() {
        $this->requireLogin();
        $prestamos = Prestamo::all();
        $this->render('prestamos/index', compact('prestamos'));
    }

    public function create() {
        $this->requireLogin();
        // obtener lista de libros y usuarios para el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $libro_id = intval($_POST['libro_id']);
            $cliente_id = intval($_POST['cliente_id']);
            $libro = Libro::find($libro_id);
            // Validaciones
            if ($libro_id <= 0) {
                $this->addFlash('error', 'Selecciona un libro válido.');
                $this->redirect('?c=prestamo&a=create');
                return;
            }
            if ($cliente_id <= 0) {
                $this->addFlash('error', 'Selecciona un cliente válido.');
                $this->redirect('?c=prestamo&a=create');
                return;
            }
            if (!$libro || $libro['cantidad_disponible'] <= 0) {
                $error = 'Libro no disponible';
                $libros = Libro::all();
                $clientes = Cliente::all();
                $this->render('prestamos/create', compact('error', 'libros', 'clientes'));
                return;
            }
            Prestamo::create($libro_id, $cliente_id);
            Libro::updateAvailable($libro_id, -1);
            $this->addFlash('success', 'Préstamo registrado correctamente.');
            $this->redirect('?c=prestamo&a=index');
        } else {
            $libros = Libro::all();
            $clientes = Cliente::all();
            $this->render('prestamos/create', compact('libros', 'clientes'));
        }
    }

    public function devolver() {
        $this->requireLogin();
        $id = intval($_GET['id'] ?? 0);
        $p = Prestamo::find($id);
        if ($p && !$p['devuelto']) {
            Prestamo::markReturned($id);
            Libro::updateAvailable($p['libro_id'], 1);
            $this->addFlash('success', 'Préstamo marcado como devuelto.');
        } else {
            $this->addFlash('error', 'Préstamo no encontrado o ya devuelto.');
        }
        $this->redirect('?c=prestamo&a=index');
    }
}
