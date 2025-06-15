<?php
// Oturumu güvenli başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id(true);
}

// Veritabanı yapılandırma sabitleri
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '159TUYabc23*');
define('DB_NAME', 'gida_paylasim');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    error_log("Veritabanı bağlantı hatası: " . $e->getMessage());
    die("Veritabanına bağlanılamadı. Lütfen MySQL Workbench'de 'gida_paylasim' veritabanını oluşturun ve tekrar deneyin.");
}

define('BASE_URL', 'http://localhost:8000/pages/');
?>
