<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/PhieuKho.php';
require_once __DIR__ . '/SanPham.php';

class PhieuXuat extends PhieuKho {
    private $table_name_detail = "chitietphieuxuat";
    
    public $id_nhan_vien;
    public $id_kho;
    public $ngay_xuat;
    public $ly_do_xuat;
    public $tong_tien;
    public $ghi_chu;

    public function __construct($db) {
        parent::__construct($db);
        $this->table_name = "phieuxuat";
    }

    public function TaoPhieu() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id, id_nhan_vien, id_kho, ngay_xuat, ly_do_xuat, tong_tien, ghi_chu, id_nguoi_tao) 
                  VALUES (:id, :id_nhan_vien, :id_kho, NOW(), :ly_do_xuat, :tong_tien, :ghi_chu, :id_nguoi_tao)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":id_nhan_vien", $this->id_nhan_vien);
        $stmt->bindParam(":id_kho", $this->id_kho);
        $stmt->bindParam(":ly_do_xuat", $this->ly_do_xuat);
        $stmt->bindParam(":tong_tien", $this->tong_tien);
        $stmt->bindParam(":ghi_chu", $this->ghi_chu);
        $stmt->bindParam(":id_nguoi_tao", $this->id_nguoi_tao);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function XacNhanXuatKho() {
        // Confirm export and update stock
        $query = "SELECT * FROM " . $this->table_name_detail . " WHERE id_phieu_xuat = :id_phieu_xuat";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_phieu_xuat", $this->id);
        $stmt->execute();
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sanPham = new SanPham($this->conn);
            $sanPham->id = $row['id_san_pham'];
            if($sanPham->TraCuuThongTin()) {
                $soLuongMoi = $sanPham->so_luong_ton - $row['so_luong'];
                if($soLuongMoi < 0) {
                    $soLuongMoi = 0;
                }
                $sanPham->CapNhatSoLuongTon($soLuongMoi);
            }
        }
        
        return true;
    }

    public function themChiTiet($idSanPham, $soLuong, $donGia) {
        $query = "INSERT INTO " . $this->table_name_detail . " 
                  (id_phieu_xuat, id_san_pham, so_luong, don_gia, thanh_tien) 
                  VALUES (:id_phieu_xuat, :id_san_pham, :so_luong, :don_gia, :thanh_tien)";
        $stmt = $this->conn->prepare($query);
        
        $thanhTien = $soLuong * $donGia;
        $stmt->bindParam(":id_phieu_xuat", $this->id);
        $stmt->bindParam(":id_san_pham", $idSanPham);
        $stmt->bindParam(":so_luong", $soLuong);
        $stmt->bindParam(":don_gia", $donGia);
        $stmt->bindParam(":thanh_tien", $thanhTien);
        
        if($stmt->execute()) {
            // Update total
            $this->tong_tien += $thanhTien;
            $query = "UPDATE " . $this->table_name . " SET tong_tien = :tong_tien WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":tong_tien", $this->tong_tien);
            $stmt->bindParam(":id", $this->id);
            $stmt->execute();
            return true;
        }
        return false;
    }
}
?>

