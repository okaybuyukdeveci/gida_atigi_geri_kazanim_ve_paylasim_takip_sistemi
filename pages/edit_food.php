<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../classes/FoodItem.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    header('Location: login.php');
    exit;
}

$foodItem = new FoodItem($pdo);
$error = '';
$success = '';

if (!isset($_GET['food_id']) || !is_numeric($_GET['food_id']) || !($food = $foodItem->getFoodItemById((int)$_GET['food_id']))) {
    $_SESSION['error'] = 'Geçersiz gıda ürünü.';
    header('Location: provider_dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');
    $expiry_date = $_POST['expiry_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($name) || empty($quantity) || empty($expiry_date) || empty($location)) {
        $error = 'Zorunlu alanlar doldurulmalıdır.';
    } elseif (!is_numeric($quantity) || $quantity <= 0) {
        $error = 'Miktar pozitif bir sayı olmalıdır.';
    } elseif (strtotime($expiry_date) < time()) {
        $error = 'Son kullanma tarihi geçmiş olamaz.';
    } elseif (strlen($name) > 100 || strlen($location) > 100 || strlen($description) > 500) {
        $error = 'Alanlar çok uzun.';
    } else {
        if ($foodItem->updateFoodItem($food['id'], $name, $quantity, $expiry_date, $location, $description, $_SESSION['user_id'])) {
            $success = 'Gıda ürünü başarıyla güncellendi.';
            $food = $foodItem->getFoodItemById($food['id']);
        } else {
            $error = 'Gıda ürünü güncellenemedi.';
        }
    }
}

include_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4">Gıda Ürünü Düzenle</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Ürün Adı</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($food['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Miktar</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="<?php echo htmlspecialchars($food['quantity'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="expiry_date" class="form-label">Son Kullanma Tarihi</label>
                    <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?php echo htmlspecialchars($food['expiry_date'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Konum</label>
                    <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($food['location'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Açıklama</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($food['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Güncelle</button>
            </form>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
