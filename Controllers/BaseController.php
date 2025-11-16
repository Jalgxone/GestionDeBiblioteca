<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class BaseController {
    protected function render($view, $params = []) {
        extract($params);
    require __DIR__ . '/../Views/layout/header.php';
    require __DIR__ . '/../Views/' . $view . '.php';
    }

    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }

    protected function isLogged() {
        return isset($_SESSION['user']);
    }

    protected function requireLogin() {
        if (!$this->isLogged()) {
            $this->redirect('?c=auth&a=login');
        }
    }

    // Flash message helpers
    protected function addFlash(string $type, string $message) {
        if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }

    protected function getFlashes(): array {
        $fl = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $fl;
    }
}
