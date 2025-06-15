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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');
    $expiry_date = $_POST['expiry_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($name) || empty($quantity) || empty($expiry_date) || empty($location)) {
        $error = 'Zorunlu alanlar doldurulmalıdır.';
    } elseif (strlen($name) > 100 || strlen($location) > 100 || strlen($description) > 500) {
        $error = 'Alanlar çok uzun.';
    } elseif (!is_numeric($quantity) || $quantity <= 0) {
        $error = 'Miktar pozitif bir sayı olmalıdır.';
    } elseif (strtotime($expiry_date) < time()) {
        $error = 'Son kullanma tarihi geçmiş olamaz.';
    } else {
        if ($foodItem->addFoodItem($_SESSION['user_id'], $name, $quantity, $expiry_date, $location, $description)) {
            $success = 'Gıda ürünü başarıyla eklendi.';
            $name = $quantity = $expiry_date = $location = $description = '';
        } else {
            $error = 'Gıda ürünü eklenemedi.';
        }
    }
}

include_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4">Gıda Ürünü Ekle</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Ürün Adı</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Miktar</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="<?php echo htmlspecialchars($quantity ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="expiry_date" class="form-label">Son Kullanma Tarihi</label>
                    <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?php echo htmlspecialchars($expiry_date ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Konum</label>
                    <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($location ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Açıklama</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Ürün Ekle</button>
            </form>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
