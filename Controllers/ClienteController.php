<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Cliente.php';

class ClienteController extends BaseController {
    public function index() {
        $this->requireLogin();
        $clientes = Cliente::all();
        $this->render('clientes/index', compact('clientes'));
    }

    public function create() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? null;
            $telefono = $_POST['telefono'] ?? null;
            $documento = $_POST['documento'] ?? null;
            // Validaciones
            if (trim($nombre) === '') {
                $this->addFlash('error', 'El nombre es obligatorio.');
                $this->render('clientes/create', compact('nombre','email','telefono','documento'));
                return;
            }
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'El email no tiene un formato válido.');
                $this->render('clientes/create', compact('nombre','email','telefono','documento'));
                return;
            }
            if ($telefono && strlen($telefono) > 50) {
                $this->addFlash('error', 'El teléfono es demasiado largo.');
                $this->render('clientes/create', compact('nombre','email','telefono','documento'));
                return;
            }
            // Unicidad de email: comprobar antes de insertar
            if (!empty($email)) {
                $exists = Cliente::findByEmail($email);
                if ($exists) {
                    $this->addFlash('error', 'El email ya está registrado para otro cliente.');
                    $this->render('clientes/create', compact('nombre','email','telefono','documento'));
                    return;
                }
            }
            try {
                Cliente::create($nombre, $email, $telefono, $documento);
            } catch (PDOException $e) {
                // manejar violaciones de constraint
                if ($e->getCode() === '23000') {
                    $this->addFlash('error', 'Error al guardar cliente: posible valor duplicado.');
                    $this->render('clientes/create', compact('nombre','email','telefono','documento'));
                    return;
                }
                throw $e;
            }
            $this->addFlash('success', 'Cliente creado.');
            $this->redirect('?c=cliente&a=index');
        }
        $this->render('clientes/create');
    }

    public function edit() {
        $this->requireLogin();
        $id = intval($_GET['id'] ?? 0);
        $cliente = Cliente::find($id);
        if (!$cliente) {
            $this->addFlash('error', 'Cliente no encontrado.');
            $this->redirect('?c=cliente&a=index');
        }
        $this->render('clientes/edit', compact('cliente'));
    }

    public function update() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?c=cliente&a=index');
        $id = intval($_POST['id']);
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? null;
        $telefono = $_POST['telefono'] ?? null;
        $documento = $_POST['documento'] ?? null;
        // Validaciones
        if (trim($nombre) === '') {
            $this->addFlash('error', 'El nombre es obligatorio.');
            $this->redirect('?c=cliente&a=edit&id=' . $id);
            return;
        }
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'El email no tiene un formato válido.');
            $this->redirect('?c=cliente&a=edit&id=' . $id);
            return;
        }
        if ($telefono && strlen($telefono) > 50) {
            $this->addFlash('error', 'El teléfono es demasiado largo.');
            $this->redirect('?c=cliente&a=edit&id=' . $id);
            return;
        }
        // Comprobar unicidad email (que no pertenezca a otro cliente)
        if (!empty($email)) {
            $exists = Cliente::findByEmail($email);
            if ($exists && intval($exists['id']) !== $id) {
                $this->addFlash('error', 'El email ya está registrado para otro cliente.');
                $this->redirect('?c=cliente&a=edit&id=' . $id);
                return;
            }
        }
        try {
            Cliente::update($id, $nombre, $email, $telefono, $documento);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->addFlash('error', 'Error al actualizar cliente: posible valor duplicado.');
                $this->redirect('?c=cliente&a=edit&id=' . $id);
                return;
            }
            throw $e;
        }
        $this->addFlash('success', 'Cliente actualizado.');
        $this->redirect('?c=cliente&a=index');
    }

    public function delete() {
        $this->requireLogin();
        $id = intval($_GET['id'] ?? 0);
        Cliente::delete($id);
        $this->addFlash('success', 'Cliente eliminado.');
        $this->redirect('?c=cliente&a=index');
    }
}
