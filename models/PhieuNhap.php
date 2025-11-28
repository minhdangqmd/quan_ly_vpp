<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/PhieuKho.php';
require_once __DIR__ . '/NhaCungCap.php';
require_once __DIR__ . '/SanPham.php';

class PhieuNhap extends PhieuKho {
    private $table_name_detail = "chitietphieunhap";
    
    public $id_nha_cung_cap;
    public $id_nhan_vien;
    public $id_kho;
    public $ngay_nhap;
    public $tong_tien;
    public $ghi_chu;
    
    public $nhaCungCap; // NhaCungCap object

    public function __construct($db) {
        parent::__construct($db);
        $this->table_name = "phieunhap";
    }

    public function TaoPhieu() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id, id_nha_cung_cap, id_nhan_vien, id_kho, ngay_nhap, tong_tien, ghi_chu) 
                  VALUES (:id, :id_nha_cung_cap, :id_nhan_vien, :id_kho, NOW(), :tong_tien, :ghi_chu)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":id_nha_cung_cap", $this->id_nha_cung_cap);
        $stmt->bindParam(":id_nhan_vien", $this->id_nhan_vien);
        $stmt->bindParam(":id_kho", $this->id_kho);
        $stmt->bindParam(":tong_tien", $this->tong_tien);
        $stmt->bindParam(":ghi_chu", $this->ghi_chu);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function docTatCa() {
        $query = "SELECT pn.*, ncc.ten_nha_cung_cap, nv.ho_ten as ten_nhan_vien 
                  FROM " . $this->table_name . " pn
                  LEFT JOIN nhacungcap ncc ON pn.id_nha_cung_cap = ncc.id
                  LEFT JOIN nhanvien nv ON pn.id_nhan_vien = nv.id
                  ORDER BY pn.ngay_nhap DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function docChiTiet($id) {
        $query = "SELECT ctpn.*, sp.ten_san_pham, sp.hinh_anh 
                  FROM " . $this->table_name_detail . " ctpn
                  LEFT JOIN sanpham sp ON ctpn.id_san_pham = sp.id
                  WHERE ctpn.id_phieu_nhap = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt;
    }

    public function XacNhapNhapKho() {
        // Confirm import and update stock
        $query = "SELECT * FROM " . $this->table_name_detail . " WHERE id_phieu_nhap = :id_phieu_nhap";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_phieu_nhap", $this->id);
        $stmt->execute();
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sanPham = new SanPham($this->conn);
            $sanPham->id = $row['id_san_pham'];
            if($sanPham->TraCuuThongTin()) {
                $soLuongMoi = $sanPham->so_luong_ton + $row['so_luong'];
                $sanPham->CapNhatSoLuongTon($soLuongMoi);
            }
        }
        
        return true;
    }

    public function themChiTiet($idSanPham, $soLuong, $donGiaNhap) {
        $query = "INSERT INTO " . $this->table_name_detail . " 
                  (id_phieu_nhap, id_san_pham, so_luong, don_gia_nhap, thanh_tien) 
                  VALUES (:id_phieu_nhap, :id_san_pham, :so_luong, :don_gia_nhap, :thanh_tien)";
        $stmt = $this->conn->prepare($query);
        
        $thanhTien = $soLuong * $donGiaNhap;
        $stmt->bindParam(":id_phieu_nhap", $this->id);
        $stmt->bindParam(":id_san_pham", $idSanPham);
        $stmt->bindParam(":so_luong", $soLuong);
        $stmt->bindParam(":don_gia_nhap", $donGiaNhap);
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

