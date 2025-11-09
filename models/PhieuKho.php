<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/NguoiDung.php';

abstract class PhieuKho {
    protected $conn;
    protected $table_name;
    
    public $id;
    public $ngay_lap;
    public $id_nguoi_tao;
    
    public $nguoiTao; // NguoiDung object

    public function __construct($db) {
        $this->conn = $db;
    }

    public function TaoPhieu() {
        // Abstract method, implemented in child classes
        return false;
    }

    public function CapNhatSoLuongKho() {
        // Update product stock quantity
        return true;
    }

    public function docTheoId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->ngay_lap = $row['ngay_nhap'] ?? $row['ngay_xuat'] ?? null;
            $this->id_nguoi_tao = $row['id_nguoi_tao'] ?? null;
            
            // Load NguoiTao
            if($this->id_nguoi_tao) {
                $nguoiDung = new NguoiDung($this->conn);
                $nguoiDung->id = $this->id_nguoi_tao;
                if($nguoiDung->docTheoId()) {
                    $this->nguoiTao = $nguoiDung;
                }
            }
            
            return true;
        }
        return false;
    }
}
?>

