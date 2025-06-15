<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../classes/Request.php';
require_once '../classes/FoodItem.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recipient') {
    $_SESSION['error'] = 'Bu işlemi gerçekleştirmek için yetkiniz yok.';
    header('Location: ' . BASE_URL . 'pages/' . (!isset($_SESSION['user_id']) ? 'login.php' : 'provider_dashboard.php'));
    exit;
}

$request = new Request($pdo);
$foodItem = new FoodItem($pdo);
$error = '';
$success = '';

// Gıda ürünü geçerli mi kontrolü
$food_id = $_POST['food_id'] ?? $_GET['food_id'] ?? null;
if (!$food_id || !is_numeric($food_id)) {
    $_SESSION['error'] = 'Geçersiz gıda ürünü.';
    header('Location: ' . BASE_URL . 'pages/index.php');
    exit;
}

$food = $foodItem->getFoodItemById((int)$food_id);
if (!$food) {
    $_SESSION['error'] = 'Gıda ürünü bulunamadı.';
    header('Location: ' . BASE_URL . 'pages/index.php');
    exit;
}

// Talep işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($request->hasRequest($food['id'], $_SESSION['user_id'])) {
        $error = 'Bu ürünü zaten talep ettiniz.';
    } elseif ($request->createRequest($food['id'], $_SESSION['user_id'])) {
        $_SESSION['success'] = 'Talep başarıyla oluşturuldu.';
        header('Location: ' . BASE_URL . 'pages/user_dashboard.php');
        exit;
    } else {
        $error = 'Talep oluşturulamadı.';
    }
}

include_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4">Gıda Ürünü Talep Et</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($food['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                    <p class="card-text">
                        Miktar: <?php echo htmlspecialchars($food['quantity'], ENT_QUOTES, 'UTF-8'); ?><br>
                        Son Kullanma: <?php echo htmlspecialchars($food['expiry_date'], ENT_QUOTES, 'UTF-8'); ?><br>
                        Konum: <?php echo htmlspecialchars($food['location'], ENT_QUOTES, 'UTF-8'); ?><br>
                        Açıklama: <?php echo htmlspecialchars($food['description'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <form method="POST">
                        <button type="submit" class="btn btn-primary">Talep Et</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
