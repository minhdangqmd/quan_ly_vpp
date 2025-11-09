<?php
require_once __DIR__ . '/../config/database.php';

class HinhAnhYeuCau {
    private $conn;
    private $table_name = "hinhanhyeucau";
    
    public $id;
    public $id_yeu_cau;
    public $duong_dan_hinh_anh;
    public $mo_ta_hinh_anh;
    public $ngay_tai_len;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function taiLenHinhAnh() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id, id_yeu_cau, duong_dan_hinh_anh, mo_ta_hinh_anh, ngay_tai_len) 
                  VALUES (:id, :id_yeu_cau, :duong_dan_hinh_anh, :mo_ta_hinh_anh, NOW())";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":id_yeu_cau", $this->id_yeu_cau);
        $stmt->bindParam(":duong_dan_hinh_anh", $this->duong_dan_hinh_anh);
        $stmt->bindParam(":mo_ta_hinh_anh", $this->mo_ta_hinh_anh);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function xemHinhAnh() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_yeu_cau = :id_yeu_cau";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_yeu_cau", $this->id_yeu_cau);
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
            $this->id_yeu_cau = $row['id_yeu_cau'];
            $this->duong_dan_hinh_anh = $row['duong_dan_hinh_anh'];
            $this->mo_ta_hinh_anh = $row['mo_ta_hinh_anh'];
            $this->ngay_tai_len = $row['ngay_tai_len'];
            return true;
        }
        return false;
    }
}
?>

