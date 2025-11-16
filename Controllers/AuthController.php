<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/User.php';

class AuthController extends BaseController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $user = User::findByEmail($email);
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user'] = ['id' => $user['id'], 'nombre' => $user['nombre'], 'rol' => $user['rol']];
                $this->addFlash('success', 'Inicio de sesión correcto.');
                $this->redirect('?c=libro&a=index');
            } else {
                $this->addFlash('error', 'Credenciales incorrectas');
                $this->render('auth/login');
            }
        } else {
            $this->render('auth/login');
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            // Validaciones server-side
            if (trim($nombre) === '') {
                $this->addFlash('error', 'El nombre es obligatorio.');
                $this->render('auth/register', compact('nombre','email'));
                return;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Email no válido.');
                $this->render('auth/register', compact('nombre','email'));
                return;
            }
            if (strlen($password) < 6) {
                $this->addFlash('error', 'La contraseña debe tener al menos 6 caracteres.');
                $this->render('auth/register', compact('nombre','email'));
                return;
            }
            if (User::findByEmail($email)) {
                $this->addFlash('error', 'Email ya registrado');
                $this->render('auth/register', compact('nombre','email'));
                return;
            }
            $id = User::create($nombre, $email, $password);
            $_SESSION['user'] = ['id' => $id, 'nombre' => $nombre, 'rol' => 'bibliotecario'];
            $this->addFlash('success', 'Registro completado. Bienvenido.');
            $this->redirect('?c=libro&a=index');
        } else {
            $this->render('auth/register');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('?c=auth&a=login');
    }
}
