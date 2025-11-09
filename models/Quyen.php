<?php
require_once __DIR__ . '/../config/database.php';

class Quyen {
    private $conn;
    private $table_name = "vaitro";
    
    public $id;
    public $ten_vai_tro;
    public $mo_ta;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function ThemQuyen() {
        $query = "INSERT INTO " . $this->table_name . " (ten_vai_tro, mo_ta) VALUES (:ten_vai_tro, :mo_ta)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ten_vai_tro", $this->ten_vai_tro);
        $stmt->bindParam(":mo_ta", $this->mo_ta);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function XoaQuyen() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function CapNhatQuyen() {
        $query = "UPDATE " . $this->table_name . " SET ten_vai_tro = :ten_vai_tro, mo_ta = :mo_ta WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ten_vai_tro", $this->ten_vai_tro);
        $stmt->bindParam(":mo_ta", $this->mo_ta);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function docTatCa() {
        $query = "SELECT * FROM " . $this->table_name;
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
            $this->ten_vai_tro = $row['ten_vai_tro'];
            $this->mo_ta = $row['mo_ta'] ?? '';
            return true;
        }
        return false;
    }
}
?>

