<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../classes/User.php';

$user = new User($pdo);
$error = '';
$success = '';

$username = '';
$email = '';
$role = '';
$location = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $role = $_POST['role'] ?? '';
    $location = trim($_POST['location'] ?? '');

    $valid_roles = ['provider', 'recipient'];

    if (empty($username) || empty($password) || empty($email) || empty($role) || empty($location)) {
        $error = 'Tüm alanlar zorunludur.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçersiz e-posta adresi.';
    } elseif (strlen($password) < 6) {
        $error = 'Şifre en az 6 karakter olmalıdır.';
    } elseif (!in_array($role, $valid_roles)) {
        $error = 'Geçersiz rol seçimi.';
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $error = 'Kullanıcı adı 3-20 karakter arasında olmalıdır.';
    } else {
        if ($user->register($username, $password, $email, $role, $location)) {
            $success = 'Kayıt başarılı. Giriş sayfasına yönlendiriliyorsunuz...';
            header("refresh:2;url=login.php");
        } else {
            $error = 'Kayıt başarısız. Kullanıcı adı veya e-posta zaten kullanılıyor.';
        }
    }
}

include_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4">Kayıt Ol</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Kullanıcı Adı</label>
                    <input type="text" class="form-control" id="username" name="username"
                        value="<?php echo htmlspecialchars($username); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Şifre</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">E-posta</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Rol</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="" disabled <?php echo empty($role) ? 'selected' : ''; ?>>Rol Seçin</option>
                        <option value="provider" <?php echo $role === 'provider' ? 'selected' : ''; ?>>Gıda Sağlayıcı</option>
                        <option value="recipient" <?php echo $role === 'recipient' ? 'selected' : ''; ?>>İhtiyaç Sahibi</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Konum</label>
                    <input type="text" class="form-control" id="location" name="location"
                        value="<?php echo htmlspecialchars($location); ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Kayıt Ol</button>
            </form>

            <p class="mt-3">Zaten hesabınız var mı? <a href="login.php">Giriş Yap</a></p>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
