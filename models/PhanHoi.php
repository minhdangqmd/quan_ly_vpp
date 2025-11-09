<?php
require_once __DIR__ . '/../config/database.php';

class PhanHoi {
    private $conn;
    private $table_name = "phanhoi";
    
    public $id;
    public $id_khach_hang;
    public $noi_dung;
    public $ngay_gui;
    public $trang_thai;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function TiepNhanPhanHoi() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id, id_khach_hang, noi_dung, trang_thai) 
                  VALUES (:id, :id_khach_hang, :noi_dung, :trang_thai)";
        $stmt = $this->conn->prepare($query);
        
        $this->trang_thai = $this->trang_thai ?? 'Chưa xử lý';
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":id_khach_hang", $this->id_khach_hang);
        $stmt->bindParam(":noi_dung", $this->noi_dung);
        $stmt->bindParam(":trang_thai", $this->trang_thai);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    private function XuLyPhanHoi() {
        $query = "UPDATE " . $this->table_name . " 
                  SET trang_thai = :trang_thai 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":trang_thai", $this->trang_thai);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function capNhatTrangThai($trangThaiMoi) {
        $this->trang_thai = $trangThaiMoi;
        return $this->XuLyPhanHoi();
    }

    public function docTatCa() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY ngay_gui DESC";
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
            $this->id_khach_hang = $row['id_khach_hang'];
            $this->noi_dung = $row['noi_dung'];
            $this->ngay_gui = $row['ngay_gui'];
            $this->trang_thai = $row['trang_thai'];
            return true;
        }
        return false;
    }
}
?>

