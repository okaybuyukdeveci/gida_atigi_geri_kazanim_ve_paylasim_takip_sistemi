<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../classes/User.php';

$user = new User($pdo);
$error = '';
$username = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Kullanıcı adı ve şifre zorunludur.';
    } elseif ($user->login($username, $password)) {
        session_regenerate_id(true);

        // Rol bazlı yönlendirme
        if ($_SESSION['role'] === 'provider') {
            header('Location: provider_dashboard.php');
        } elseif ($_SESSION['role'] === 'recipient') {
            header('Location: user_dashboard.php');
        } else {
            $error = 'Geçersiz rol tanımı.';
        }
        exit;
    } else {
        $error = 'Geçersiz kullanıcı adı veya şifre.';
    }
}

include_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4">Giriş Yap</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
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

                <button type="submit" class="btn btn-primary">Giriş Yap</button>
            </form>

            <p class="mt-3">Hesabınız yok mu? <a href="register.php">Kayıt Ol</a></p>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
