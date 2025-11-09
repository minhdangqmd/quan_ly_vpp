<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/DonHang.php';

class ChuongTrinhKhuyenMai {
    private $conn;
    private $table_name = "chuongtrinhkhuyenmai";
    
    public $id;
    public $ten_chuong_trinh;
    public $mo_ta;
    public $ngay_bat_dau;
    public $ngay_ket_thuc;
    public $dieu_kien_ap_dung;
    public $giam_gia;
    public $trang_thai;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function TaoChuongTrinh() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id, ten_chuong_trinh, mo_ta, ngay_bat_dau, ngay_ket_thuc, 
                   dieu_kien_ap_dung, giam_gia, trang_thai) 
                  VALUES (:id, :ten_chuong_trinh, :mo_ta, :ngay_bat_dau, :ngay_ket_thuc, 
                          :dieu_kien_ap_dung, :giam_gia, :trang_thai)";
        $stmt = $this->conn->prepare($query);
        
        $this->trang_thai = $this->trang_thai ?? 'Hoạt động';
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":ten_chuong_trinh", $this->ten_chuong_trinh);
        $stmt->bindParam(":mo_ta", $this->mo_ta);
        $stmt->bindParam(":ngay_bat_dau", $this->ngay_bat_dau);
        $stmt->bindParam(":ngay_ket_thuc", $this->ngay_ket_thuc);
        $stmt->bindParam(":dieu_kien_ap_dung", $this->dieu_kien_ap_dung);
        $stmt->bindParam(":giam_gia", $this->giam_gia);
        $stmt->bindParam(":trang_thai", $this->trang_thai);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function ApDungKhuyenMai($donHang) {
        // Check if promotion is active
        $now = date('Y-m-d H:i:s');
        if($now < $this->ngay_bat_dau || $now > $this->ngay_ket_thuc) {
            return false;
        }
        
        if($this->trang_thai != 'Hoạt động') {
            return false;
        }
        
        // Apply discount to order
        $donHang->id_chuong_trinh_km = $this->id;
        $donHang->tong_tien = $donHang->tong_tien - $this->giam_gia;
        if($donHang->tong_tien < 0) {
            $donHang->tong_tien = 0;
        }
        
        return true;
    }

    public function docTheoId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->ten_chuong_trinh = $row['ten_chuong_trinh'];
            $this->mo_ta = $row['mo_ta'];
            $this->ngay_bat_dau = $row['ngay_bat_dau'];
            $this->ngay_ket_thuc = $row['ngay_ket_thuc'];
            $this->dieu_kien_ap_dung = $row['dieu_kien_ap_dung'];
            $this->giam_gia = $row['giam_gia'];
            $this->trang_thai = $row['trang_thai'];
            return true;
        }
        return false;
    }

    public function docTatCa() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY ngay_bat_dau DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>

