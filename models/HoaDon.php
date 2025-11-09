<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/DonHang.php';
require_once __DIR__ . '/NguoiDung.php';

class HoaDon {
    private $conn;
    private $table_name = "hoadon";
    private $table_name_detail = "chitiethoadon";
    
    public $id;
    public $id_don_hang;
    public $id_nguoi_tao;
    public $ngay_lap;
    public $tong_tien;
    
    public $donHang; // DonHang object
    public $nguoiTao; // NguoiDung object

    public function __construct($db) {
        $this->conn = $db;
    }

    public function TaoHoaDon() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id, id_don_hang, id_nguoi_tao, ngay_lap, tong_tien) 
                  VALUES (:id, :id_don_hang, :id_nguoi_tao, NOW(), :tong_tien)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":id_don_hang", $this->id_don_hang);
        $stmt->bindParam(":id_nguoi_tao", $this->id_nguoi_tao);
        $stmt->bindParam(":tong_tien", $this->tong_tien);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function LuuTruHoaDon() {
        // HoaDon is already stored in database, this method can be used for backup/export
        return $this->docTheoId();
    }

    public function InHoaDon() {
        // Return invoice data for printing
        return $this->docChiTiet();
    }

    public function docChiTiet() {
        $query = "SELECT cthd.*, dh.ngay_dat, dh.trang_thai, dh.dia_chi_giao 
                  FROM " . $this->table_name_detail . " cthd
                  JOIN donhang dh ON cthd.id_don_hang = dh.id
                  WHERE cthd.id_hoa_don = :id_hoa_don";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_hoa_don", $this->id);
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
            $this->id_don_hang = $row['id_don_hang'];
            $this->id_nguoi_tao = $row['id_nguoi_tao'];
            $this->ngay_lap = $row['ngay_lap'];
            $this->tong_tien = $row['tong_tien'];
            
            // Load DonHang
            if($this->id_don_hang) {
                $dh = new DonHang($this->conn);
                $dh->id = $this->id_don_hang;
                if($dh->TraCuuTrangThai()) {
                    $this->donHang = $dh;
                }
            }
            
            return true;
        }
        return false;
    }

    public function themChiTiet($idDonHang, $soLuong, $donGia) {
        $query = "INSERT INTO " . $this->table_name_detail . " 
                  (id_hoa_don, id_don_hang, so_luong, don_gia, tong_tien) 
                  VALUES (:id_hoa_don, :id_don_hang, :so_luong, :don_gia, :tong_tien)";
        $stmt = $this->conn->prepare($query);
        
        $tongTien = $soLuong * $donGia;
        $stmt->bindParam(":id_hoa_don", $this->id);
        $stmt->bindParam(":id_don_hang", $idDonHang);
        $stmt->bindParam(":so_luong", $soLuong);
        $stmt->bindParam(":don_gia", $donGia);
        $stmt->bindParam(":tong_tien", $tongTien);
        
        return $stmt->execute();
    }
}
?>

