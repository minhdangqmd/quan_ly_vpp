<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/SanPham.php';

class GioHang {
    private $conn;
    private $table_name = "giohang";
    private $table_name_detail = "chitietgiohang";
    
    public $id;
    public $id_khach_hang;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function ThemSanPham($sanPham, $soLuong) {
        // Ensure cart exists
        if(!$this->kiemTraTonTai()) {
            $this->taoMoi();
        }
        
        // Check if product already in cart
        $query = "SELECT * FROM " . $this->table_name_detail . " 
                  WHERE id_gio_hang = :id_gio_hang AND id_san_pham = :id_san_pham";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_gio_hang", $this->id);
        $stmt->bindParam(":id_san_pham", $sanPham->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            // Update quantity
            $newQuantity = $row['so_luong'] + $soLuong;
            $query = "UPDATE " . $this->table_name_detail . " 
                      SET so_luong = :so_luong 
                      WHERE id_gio_hang = :id_gio_hang AND id_san_pham = :id_san_pham";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":so_luong", $newQuantity);
            $stmt->bindParam(":id_gio_hang", $this->id);
            $stmt->bindParam(":id_san_pham", $sanPham->id);
            return $stmt->execute();
        } else {
            // Insert new
            $query = "INSERT INTO " . $this->table_name_detail . " 
                      (id_gio_hang, id_san_pham, so_luong) 
                      VALUES (:id_gio_hang, :id_san_pham, :so_luong)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id_gio_hang", $this->id);
            $stmt->bindParam(":id_san_pham", $sanPham->id);
            $stmt->bindParam(":so_luong", $soLuong);
            return $stmt->execute();
        }
    }

    public function XoaSanPham($sanPham) {
        $query = "DELETE FROM " . $this->table_name_detail . " 
                  WHERE id_gio_hang = :id_gio_hang AND id_san_pham = :id_san_pham";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_gio_hang", $this->id);
        $stmt->bindParam(":id_san_pham", $sanPham->id);
        return $stmt->execute();
    }

    public function CapNhatSoLuong($sanPham, $soLuongMoi) {
        $query = "UPDATE " . $this->table_name_detail . " 
                  SET so_luong = :so_luong 
                  WHERE id_gio_hang = :id_gio_hang AND id_san_pham = :id_san_pham";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":so_luong", $soLuongMoi);
        $stmt->bindParam(":id_gio_hang", $this->id);
        $stmt->bindParam(":id_san_pham", $sanPham->id);
        return $stmt->execute();
    }

    public function TinhTongTien() {
        $query = "SELECT SUM(sp.gia_ban * ctg.so_luong) as tong_tien 
                  FROM " . $this->table_name_detail . " ctg
                  JOIN sanpham sp ON ctg.id_san_pham = sp.id
                  WHERE ctg.id_gio_hang = :id_gio_hang";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_gio_hang", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['tong_tien'] ?? 0;
    }

    public function docChiTiet() {
        $query = "SELECT ctg.*, sp.ten_san_pham, sp.gia_ban, sp.hinh_anh 
                  FROM " . $this->table_name_detail . " ctg
                  JOIN sanpham sp ON ctg.id_san_pham = sp.id
                  WHERE ctg.id_gio_hang = :id_gio_hang";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_gio_hang", $this->id);
        $stmt->execute();
        return $stmt;
    }

    public function kiemTraTonTai() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_khach_hang = :id_khach_hang LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_khach_hang", $this->id_khach_hang);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->id = $row['id'];
            return true;
        }
        return false;
    }

    public function taoMoi() {
        $this->id = "GH" . $this->id_khach_hang . time();
        $query = "INSERT INTO " . $this->table_name . " (id, id_khach_hang) VALUES (:id, :id_khach_hang)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":id_khach_hang", $this->id_khach_hang);
        return $stmt->execute();
    }
}
?>

