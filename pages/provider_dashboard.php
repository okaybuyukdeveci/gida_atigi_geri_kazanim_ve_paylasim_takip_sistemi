<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../classes/FoodItem.php';
require_once '../classes/Request.php';

// Erişim kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    $_SESSION['error'] = 'Bu sayfaya erişim yetkiniz yok.';
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

$foodItem = new FoodItem($pdo);
$request = new Request($pdo);
$error = '';
$success = '';

// Ürün silme işlemi
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    if ($foodItem->deleteFoodItem($delete_id, $_SESSION['user_id'])) {
        $success = 'Ürün başarıyla silindi.';
    } else {
        $error = 'Ürün silinemedi. Yetkiniz olmayabilir veya ürün bulunamadı.';
    }
}

// Giriş yapan kullanıcıya ait ürünleri al
$foods = $foodItem->getFoodItemsByUser($_SESSION['user_id']);

include_once '../includes/header.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">Sağlayıcı Paneli</h2>

    <a href="<?php echo BASE_URL; ?>add_food.php" class="btn btn-primary mb-4">Yeni Ürün Ekle</a>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <h4>Eklenen Ürünler</h4>
    <?php if (empty($foods)): ?>
        <div class="alert alert-info">Henüz ürün eklenmemiş.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($foods as $food): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($food['name']); ?></h5>
                            <p class="card-text">
                                Miktar: <?php echo htmlspecialchars($food['quantity']); ?><br>
                                Son Kullanma: <?php echo htmlspecialchars($food['expiry_date']); ?><br>
                                Konum: <?php echo htmlspecialchars($food['location']); ?>
                            </p>
                            <a href="<?php echo BASE_URL; ?>edit_food.php?food_id=<?php echo $food['id']; ?>" class="btn btn-warning btn-sm">Düzenle</a>
                            <a href="<?php echo BASE_URL; ?>provider_dashboard.php?delete_id=<?php echo $food['id']; ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?');">Sil</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <hr class="my-4">

    <h4>Ürün Talepleri</h4>
    <?php $has_requests = false; ?>
    <?php foreach ($foods as $food): ?>
        <?php $requests = $request->getRequestsByFood($food['id']); ?>
        <?php if (!empty($requests)): ?>
            <?php $has_requests = true; ?>
            <div class="card mb-4">
                <div class="card-header">
                    <?php echo htmlspecialchars($food['name']); ?> için Talepler
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kullanıcı</th>
                                <th>Durum</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $req): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($req['username']); ?></td>
                                    <td><?php echo htmlspecialchars($req['status']); ?></td>
                                    <td>
                                        <?php if ($req['status'] === 'pending'): ?>
                                            <a href="<?php echo BASE_URL; ?>update_request.php?request_id=<?php echo $req['id']; ?>&status=approved" class="btn btn-success btn-sm">Onayla</a>
                                            <a href="<?php echo BASE_URL; ?>update_request.php?request_id=<?php echo $req['id']; ?>&status=cancelled" class="btn btn-danger btn-sm">İptal</a>
                                        <?php elseif ($req['status'] === 'approved'): ?>
                                            <a href="<?php echo BASE_URL; ?>update_request.php?request_id=<?php echo $req['id']; ?>&status=delivered" class="btn btn-primary btn-sm">Teslim Edildi</a>
                                        <?php else: ?>
                                            <span class="text-muted">Tamamlandı</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if (!$has_requests): ?>
        <div class="alert alert-secondary">Henüz talep bulunmamaktadır.</div>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
