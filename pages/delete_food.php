<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/config.php';
require_once '../classes/FoodItem.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    $_SESSION['error'] = 'Bu işlemi gerçekleştirmek için yetkiniz yok.';
    header('Location: login.php');
    exit;
}

$foodItem = new FoodItem($pdo);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $food_id = (int)$_GET['id'];
    try {
        if ($foodItem->deleteFoodItem($food_id)) {
            $_SESSION['success'] = 'Ürün başarıyla silindi.';
            header('Location: provider_dashboard.php');
            exit;
        } else {
            $_SESSION['error'] = 'Ürün silinemedi. Ürün bulunamadı veya yetkiniz yok.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Ürün silinirken bir hata oluştu: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'Geçersiz ürün ID\'si.';
}

header('Location: provider_dashboard.php');
exit;
?>