<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../classes/Request.php';
require_once '../classes/FoodItem.php';

// Yetki kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recipient') {
    $_SESSION['error'] = 'Bu sayfaya erişim yetkiniz yok.';
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

$request = new Request($pdo);
$foodItem = new FoodItem($pdo);

// Geçici mesajlar
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

// 1. Geçmiş talepler
$requests = $request->getRequestsByUser($_SESSION['user_id']);

// 2. Talep edilmemiş ürünler
$allFoods = $foodItem->getAllFoodItems();
$availableFoods = array_filter($allFoods, function ($food) use ($request) {
    return !$request->hasRequest($food['id'], $_SESSION['user_id']);
});

include_once '../includes/header.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">Kullanıcı Paneli</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Talep edilebilecek ürünler -->
    <h3>Talep Edebileceğiniz Gıda Ürünleri</h3>
    <?php if (empty($availableFoods)): ?>
        <div class="alert alert-info">Talep edebileceğiniz yeni ürün bulunmamaktadır.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($availableFoods as $food): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($food['name']); ?></h5>
                            <p class="card-text">
                                Miktar: <?php echo htmlspecialchars($food['quantity']); ?><br>
                                Son Kullanma: <?php echo htmlspecialchars($food['expiry_date']); ?><br>
                                Konum: <?php echo htmlspecialchars($food['location']); ?><br>
                                Açıklama: <?php echo htmlspecialchars($food['description']); ?>
                            </p>
                            <form method="POST" action="request_food.php" style="display:inline;">
                                <input type="hidden" name="food_id" value="<?php echo $food['id']; ?>">
                                <button type="submit" class="btn btn-primary">Talep Et</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <hr>

    <!-- Geçmiş talepler -->
    <h3>Geçmiş Talepler</h3>
    <?php if (empty($requests)): ?>
        <div class="alert alert-secondary">Henüz talep bulunmamaktadır.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Ürün</th>
                    <th>Konum</th>
                    <th>Durum</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $req): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($req['name']); ?></td>
                        <td><?php echo htmlspecialchars($req['location']); ?></td>
                        <td><?php echo htmlspecialchars($req['status']); ?></td>
                     <td>
    <?php if ($req['status'] === 'pending'): ?>
        <a href="delete_request.php?request_id=<?php echo $req['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu talebi iptal etmek istediğinize emin misiniz?');">İptal Et</a>
    <?php elseif ($req['status'] === 'approved'): ?>
        <span class="text-primary">Onaylandı, teslimat bekleniyor</span>
    <?php elseif ($req['status'] === 'delivered'): ?>
        <span class="text-success">Teslim edildi</span>
    <?php elseif ($req['status'] === 'cancelled'): ?>
        <span class="text-danger">Talep iptal edildi</span>
    <?php else: ?>
        <span class="text-muted">Durum bilinmiyor</span>
    <?php endif; ?>
</td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
