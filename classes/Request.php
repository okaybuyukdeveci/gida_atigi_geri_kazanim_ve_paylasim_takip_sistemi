<?php
require_once '../includes/config.php';

class Request {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Yeni talep oluştur
    public function createRequest($food_id, $user_id) {
        if (!$this->isValidId($food_id) || !$this->isValidId($user_id)) {
            return false;
        }

        try {
            // Daha önce talep edilmiş mi?
            if ($this->hasRequest($food_id, $user_id)) {
                return false;
            }

            $sql = "INSERT INTO requests (food_id, user_id, status) VALUES (:food_id, :user_id, 'pending')";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':food_id' => $food_id,
                ':user_id' => $user_id
            ]);
        } catch (PDOException $e) {
            error_log("createRequest() hatası: " . $e->getMessage());
            return false;
        }
    }

    // Aynı kullanıcı aynı ürüne daha önce talep göndermiş mi?
    public function hasRequest($food_id, $user_id) {
        if (!$this->isValidId($food_id) || !$this->isValidId($user_id)) {
            return false;
        }

        try {
            $sql = "SELECT COUNT(*) FROM requests WHERE food_id = :food_id AND user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':food_id' => $food_id, ':user_id' => $user_id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("hasRequest() hatası: " . $e->getMessage());
            return false;
        }
    }

    // Kullanıcının tüm taleplerini getir
    public function getRequestsByUser($user_id) {
        if (!$this->isValidId($user_id)) {
            return [];
        }

        try {
            $sql = "SELECT r.*, f.name, f.location 
                    FROM requests r
                    JOIN food_items f ON r.food_id = f.id
                    WHERE r.user_id = :user_id
                    ORDER BY r.created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getRequestsByUser() hatası: " . $e->getMessage());
            return [];
        }
    }

    // Bir ürüne gelen tüm talepleri getir (sağlayıcı paneli için)
    public function getRequestsByFood($food_id) {
        if (!$this->isValidId($food_id)) {
            return [];
        }

        try {
            $sql = "SELECT r.*, u.username 
                    FROM requests r
                    JOIN users u ON r.user_id = u.id
                    WHERE r.food_id = :food_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':food_id' => $food_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getRequestsByFood() hatası: " . $e->getMessage());
            return [];
        }
    }

    // Talep durumunu güncelle (yalnızca sağlayıcı yapabilir)
    public function updateRequestStatus($request_id, $status, $provider_id) {
        $valid_statuses = ['approved', 'cancelled', 'delivered'];
        if (!in_array($status, $valid_statuses) || !$this->isValidId($request_id) || !$this->isValidId($provider_id)) {
            return false;
        }

        try {
            // Talep edilen ürün sağlayıcıya mı ait?
            $sql = "SELECT f.user_id 
                    FROM requests r 
                    JOIN food_items f ON r.food_id = f.id 
                    WHERE r.id = :request_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':request_id' => $request_id]);
            $owner_id = $stmt->fetchColumn();

            if ($owner_id != $provider_id) {
                return false;
            }

            // Durumu güncelle
            $sql = "UPDATE requests SET status = :status WHERE id = :request_id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':status' => $status,
                ':request_id' => $request_id
            ]);
        } catch (PDOException $e) {
            error_log("updateRequestStatus() hatası: " . $e->getMessage());
            return false;
        }
    }

    // Kullanıcı kendi talebini iptal edebilir
    public function deleteRequest($request_id, $user_id) {
        if (!$this->isValidId($request_id) || !$this->isValidId($user_id)) {
            return false;
        }

        try {
            $sql = "DELETE FROM requests WHERE id = :request_id AND user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':request_id' => $request_id,
                ':user_id' => $user_id
            ]);
        } catch (PDOException $e) {
            error_log("deleteRequest() hatası: " . $e->getMessage());
            return false;
        }
    }

    // ID doğrulama yardımcı metodu
    private function isValidId($id) {
        return is_numeric($id) && $id > 0;
    }
}
?>
