<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../classes/User.php';

$user = new User($pdo);
$user->logout();

// Oturum çerezini güvenli şekilde temizle (ek güvenlik)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}

// Giriş sayfasına yönlendir
header('Location: ' . BASE_URL . 'login.php');
exit;
?>
