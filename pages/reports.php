<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../classes/FoodItem.php';
require_once '../classes/Request.php';

$foodItem = new FoodItem($pdo);
$request = new Request($pdo);

// Haftalık paylaşılan gıda miktarı
$sql = "SELECT DATE(created_at) as date, COUNT(*) as count FROM food_items WHERE created_at >= :start_date GROUP BY DATE(created_at)";
$stmt = $pdo->prepare($sql);
$stmt->execute([':start_date' => date('Y-m-d', strtotime('-7 days'))]);
$food_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$dates = [];
$counts = [];
foreach ($food_data as $data) {
    $dates[] = $data['date'];
    $counts[] = $data['count'];
}

include_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Raporlar</h2>
            <?php if (empty($food_data)): ?>
                <div class="alert alert-info">Son 7 günde paylaşılan gıda ürünü bulunmuyor.</div>
            <?php else: ?>
                <canvas id="foodChart" width="400" height="200"></canvas>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($food_data)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('foodChart').getContext('2d');
    const foodChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: [{
                label: 'Paylaşılan Gıda Ürünleri',
                data: <?php echo json_encode($counts); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)', // Daha iyi tema uyumluluğu
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Ürün Sayısı'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Tarih'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
</script>
<?php endif; ?>

<?php include_once '../includes/footer.php'; ?>