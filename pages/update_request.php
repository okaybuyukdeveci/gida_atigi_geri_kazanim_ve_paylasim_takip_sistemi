<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../classes/Request.php';

// Yetki kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    $_SESSION['error'] = 'Bu işlemi gerçekleştirmek için yetkiniz yok.';
    header('Location: login.php');
    exit;
}

$request = new Request($pdo);
$valid_statuses = ['approved', 'cancelled', 'delivered'];

if (isset($_GET['request_id']) && is_numeric($_GET['request_id']) && isset($_GET['status']) && in_array($_GET['status'], $valid_statuses)) {
    $request_id = (int)$_GET['request_id'];
    $status = $_GET['status'];
    try {
        if ($request->updateRequestStatus($request_id, $status, $_SESSION['user_id'])) {
            $_SESSION['success'] = 'Talep durumu başarıyla güncellendi.';
            header('Location: provider_dashboard.php');
            exit;
        } else {
            $_SESSION['error'] = 'Talep güncellenemedi. Yetkiniz olmayabilir veya talep bulunamadı.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Talep güncellenirken bir hata oluştu.';
    }
} else {
    $_SESSION['error'] = 'Geçersiz talep ID\'si veya durum.';
}

header('Location: provider_dashboard.php');
exit;
?>