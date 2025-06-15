<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../classes/Request.php';

// Yetki kontrolü (sadece recipient rolü)
$valid_roles = ['recipient'];
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], $valid_roles)) {
    $_SESSION['error'] = 'Bu işlemi gerçekleştirmek için yetkiniz yok.';
    header('Location: login.php');
    exit;
}

$request = new Request($pdo);

if (isset($_GET['request_id']) && is_numeric($_GET['request_id'])) {
    $request_id = (int)$_GET['request_id'];
    try {
        if ($request->deleteRequest($request_id, $_SESSION['user_id'])) {
            $_SESSION['success'] = 'Talep başarıyla silindi.';
            header('Location: user_dashboard.php');
            exit;
        } else {
            $_SESSION['error'] = 'Talep silinemedi. Yetkiniz olmayabilir veya talep bulunamadı.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Talep silinirken bir hata oluştu.';
        error_log($e->getMessage());
    }
} else {
    $_SESSION['error'] = 'Geçersiz talep ID\'si.';
}

header('Location: user_dashboard.php');
exit;
?>
