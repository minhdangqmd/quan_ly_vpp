<?php
require_once __DIR__ . '/../config/database.php';

class NhaCungCap {
    private $conn;
    private $table_name = "nhacungcap";
    
    public $id;
    public $ten_nha_cung_cap;
    public $dia_chi;
    public $sdt;
    public $email;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function CapNhatThongTin() {
        $query = "UPDATE " . $this->table_name . " 
                  SET ten_nha_cung_cap = :ten_nha_cung_cap, 
                      dia_chi = :dia_chi, 
                      sdt = :sdt, 
                      email = :email 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ten_nha_cung_cap", $this->ten_nha_cung_cap);
        $stmt->bindParam(":dia_chi", $this->dia_chi);
        $stmt->bindParam(":sdt", $this->sdt);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }

    public function TraCuuThongTin() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->ten_nha_cung_cap = $row['ten_nha_cung_cap'] ?? null;
            $this->dia_chi = $row['dia_chi'] ?? null;
            $this->sdt = $row['sdt'] ?? null;
            $this->email = $row['email'] ?? null;
            return true;
        }
        return false;
    }

    public function taoMoi() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id, ten_nha_cung_cap, dia_chi, sdt, email) 
                  VALUES (:id, :ten_nha_cung_cap, :dia_chi, :sdt, :email)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":ten_nha_cung_cap", $this->ten_nha_cung_cap);
        $stmt->bindParam(":dia_chi", $this->dia_chi);
        $stmt->bindParam(":sdt", $this->sdt);
        $stmt->bindParam(":email", $this->email);
        
        return $stmt->execute();
    }

    public function docTatCa() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY ten_nha_cung_cap";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
