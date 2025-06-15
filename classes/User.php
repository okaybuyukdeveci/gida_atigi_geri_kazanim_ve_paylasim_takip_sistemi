<?php
require_once '../includes/config.php';

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Kullanıcı kaydı
    public function register($username, $password, $email, $role, $location) {
        if (empty($username) || empty($password) || empty($email) || empty($role) || empty($location)) {
            return false;
        }

        $valid_roles = ['provider', 'recipient'];
        if (!in_array($role, $valid_roles) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        try {
            // Aynı kullanıcı adı veya e-posta var mı?
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email
            ]);

            if ($stmt->fetchColumn() > 0) {
                return false;
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, password, email, role, location)
                VALUES (:username, :password, :email, :role, :location)
            ");

            return $stmt->execute([
                ':username' => $username,
                ':password' => $hashed_password,
                ':email' => $email,
                ':role' => $role,
                ':location' => $location
            ]);
        } catch (PDOException $e) {
            error_log("Kayıt hatası: " . $e->getMessage());
            return false;
        }
    }

    // Kullanıcı girişi
    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                session_regenerate_id(true);
                return true;
            }

            return false;
        } catch (PDOException $e) {
            error_log("Giriş hatası: " . $e->getMessage());
            return false;
        }
    }

    // Oturumu kapatma
    public function logout() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
            }
            session_destroy();
        }
    }

    // Giriş kontrolü
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']);
    }

    // ID'ye göre kullanıcı bilgisi alma
    public function getUserById($id) {
        if (!is_numeric($id) || $id <= 0) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Kullanıcı getirme hatası: " . $e->getMessage());
            return false;
        }
    }
}
?>
