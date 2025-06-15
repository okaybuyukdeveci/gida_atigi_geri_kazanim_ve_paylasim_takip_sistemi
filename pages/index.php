<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../classes/FoodItem.php';

$foodItem = new FoodItem($pdo);
$location = trim($_GET['location'] ?? '');

$sql = "SELECT * FROM food_items";
$params = [];

if (!empty($location)) {
    $sql .= " WHERE location LIKE :location";
    $params[':location'] = "%$location%";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Mevcut Gıda Ürünleri</h2>

            <form id="filter-form" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="filter-location" name="location" placeholder="Konum ara..." value="<?php echo htmlspecialchars($location, ENT_QUOTES, 'UTF-8'); ?>">
                    <button class="btn btn-primary" type="submit">Ara</button>
                </div>
            </form>

            <?php if (empty($foods)): ?>
                <div class="alert alert-info">Ürün bulunamadı.</div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($foods as $food): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($food['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <p class="card-text">
                                        Miktar: <?php echo htmlspecialchars($food['quantity'], ENT_QUOTES, 'UTF-8'); ?><br>
                                        Son Kullanma: <?php echo htmlspecialchars($food['expiry_date'], ENT_QUOTES, 'UTF-8'); ?><br>
                                        Konum: <?php echo htmlspecialchars($food['location'], ENT_QUOTES, 'UTF-8'); ?><br>
                                        Açıklama: <?php echo htmlspecialchars($food['description'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'recipient'): ?>
                                        <a href="<?php echo BASE_URL; ?>pages/request_food.php?food_id=<?php echo $food['id']; ?>" class="btn btn-primary">Talep Et</a>
                                    <?php elseif (!isset($_SESSION['user_id'])): ?>
                                        <a href="<?php echo BASE_URL; ?>pages/login.php" class="btn btn-primary">Giriş Yapın</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>

<script>
document.getElementById('filter-form').addEventListener('submit', function(e) {
    e.preventDefault();
    let location = document.getElementById('filter-location').value;
    window.location.href = '?location=' + encodeURIComponent(location);
});
</script>
