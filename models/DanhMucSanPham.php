<?php
require_once __DIR__ . '/../config/database.php';

class DanhMucSanPham {
    private $conn;
    private $table_name = "danhmuc";
    
    public $id;
    public $ten_danh_muc;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function ThemDanhMuc() {
        $query = "INSERT INTO " . $this->table_name . " (ten_danh_muc) VALUES (:ten_danh_muc)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ten_danh_muc", $this->ten_danh_muc);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function XoaDanhMuc() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function CapNhatDanhMuc() {
        $query = "UPDATE " . $this->table_name . " SET ten_danh_muc = :ten_danh_muc WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ten_danh_muc", $this->ten_danh_muc);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function docTatCa() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY ten_danh_muc";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function docTheoId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->ten_danh_muc = $row['ten_danh_muc'];
            return true;
        }
        return false;
    }
}
?>

