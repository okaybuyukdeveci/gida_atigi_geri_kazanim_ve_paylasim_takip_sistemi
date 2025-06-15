<?php
require_once '../includes/config.php';

class FoodItem {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addFoodItem($user_id, $name, $quantity, $expiry_date, $location, $description) {
        if (!$this->isValidId($user_id) || empty($name) || !is_numeric($quantity) || $quantity <= 0 || empty($expiry_date) || empty($location)) {
            return false;
        }

        try {
            $sql = "INSERT INTO food_items (user_id, name, quantity, expiry_date, location, description)
                    VALUES (:user_id, :name, :quantity, :expiry_date, :location, :description)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':user_id' => $user_id,
                ':name' => $name,
                ':quantity' => $quantity,
                ':expiry_date' => $expiry_date,
                ':location' => $location,
                ':description' => $description
            ]);
        } catch (PDOException $e) {
            error_log("FoodItem ekleme hatası: " . $e->getMessage());
            return false;
        }
    }

    public function getAllFoodItems() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM food_items ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Tüm FoodItem'ları getirme hatası: " . $e->getMessage());
            return [];
        }
    }

    public function getFoodItemsByUser($user_id) {
        if (!$this->isValidId($user_id)) return [];

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM food_items WHERE user_id = :user_id ORDER BY created_at DESC");
            $stmt->execute([':user_id' => $user_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Kullanıcıya ait FoodItem'ları getirme hatası: " . $e->getMessage());
            return [];
        }
    }

    public function getFoodItemById($id) {
        if (!$this->isValidId($id)) return false;

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM food_items WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("FoodItem detay getirme hatası: " . $e->getMessage());
            return false;
        }
    }

    public function updateFoodItem($id, $name, $quantity, $expiry_date, $location, $description, $user_id) {
        if (!$this->isValidId($id) || !$this->isValidId($user_id) || empty($name) || !is_numeric($quantity) || $quantity <= 0 || empty($expiry_date) || empty($location)) {
            return false;
        }

        try {
            $sql = "UPDATE food_items 
                    SET name = :name, quantity = :quantity, expiry_date = :expiry_date, 
                        location = :location, description = :description
                    WHERE id = :id AND user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':name' => $name,
                ':quantity' => $quantity,
                ':expiry_date' => $expiry_date,
                ':location' => $location,
                ':description' => $description,
                ':id' => $id,
                ':user_id' => $user_id
            ]);
        } catch (PDOException $e) {
            error_log("FoodItem güncelleme hatası: " . $e->getMessage());
            return false;
        }
    }

    public function deleteFoodItem($id, $user_id) {
        if (!$this->isValidId($id) || !$this->isValidId($user_id)) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare("DELETE FROM food_items WHERE id = :id AND user_id = :user_id");
            return $stmt->execute([':id' => $id, ':user_id' => $user_id]);
        } catch (PDOException $e) {
            error_log("FoodItem silme hatası: " . $e->getMessage());
            return false;
        }
    }

    private function isValidId($id) {
        return is_numeric($id) && $id > 0;
    }
}
