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

    /* ======================
       HÀM STATIC DÙNG CHO CONTROLLER
       ====================== */

    // Lấy giỏ hàng theo id khách hàng
    public static function layTheoKhachHang($idKH)
    {
        $db = Database::getConnection();
        $stm = $db->prepare("SELECT * FROM giohang WHERE id_khach_hang = ?");
        $stm->execute([$idKH]);
        $row = $stm->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $gioHang = new GioHang($db);
            $gioHang->id = $row['id'];
            $gioHang->id_khach_hang = $row['id_khach_hang'];
            return $gioHang;
        }
        return null;
    }

    // Tạo giỏ hàng mới
    public static function taoMoi($idKH)
    {
        $db = Database::getConnection();
        // nếu id là varchar
        $id = "GH" . $idKH . time();

        $stm = $db->prepare("INSERT INTO giohang (id, id_khach_hang) VALUES (?, ?)");
        $stm->execute([$id, $idKH]);

        return $id;
    }

    // Thêm sản phẩm vào giỏ (dùng trong controller)
    public static function ThemSanPham($idGioHang, $idSanPham, $soLuong = 1)
    {
        $db = Database::getConnection();

        // CHÚ Ý: dùng cột id_giohang (không có gạch dưới)
        $check = $db->prepare("SELECT * FROM chitietgiohang 
                               WHERE id_giohang = ? AND id_san_pham = ?");
        $check->execute([$idGioHang, $idSanPham]);

        if ($check->rowCount() > 0) {
            // Cập nhật số lượng
            $update = $db->prepare("UPDATE chitietgiohang 
                                    SET so_luong = so_luong + ? 
                                    WHERE id_giohang = ? AND id_san_pham = ?");
            return $update->execute([$soLuong, $idGioHang, $idSanPham]);
        } else {
            // Thêm mới
            $insert = $db->prepare("INSERT INTO chitietgiohang (id_giohang, id_san_pham, so_luong)
                                    VALUES (?, ?, ?)");
            return $insert->execute([$idGioHang, $idSanPham, $soLuong]);
        }
    }

    /* ======================
       CÁC HÀM DẠNG OBJECT
       ====================== */

    // Nếu muốn gọi theo kiểu object: $gioHang->ThemSanPhamObject($sanPham, $soLuong);
    public function ThemSanPhamObject($sanPham, $soLuong) {
        return self::ThemSanPham($this->id, $sanPham->id, $soLuong);
    }

    public function XoaSanPham($sanPham) {
        $query = "DELETE FROM " . $this->table_name_detail . " 
                  WHERE id_giohang = :id_giohang AND id_san_pham = :id_san_pham";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_giohang", $this->id);
        $stmt->bindParam(":id_san_pham", $sanPham->id);
        return $stmt->execute();
    }

    public function CapNhatSoLuong($sanPham, $soLuongMoi) {
        $query = "UPDATE " . $this->table_name_detail . " 
                  SET so_luong = :so_luong 
                  WHERE id_giohang = :id_giohang AND id_san_pham = :id_san_pham";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":so_luong", $soLuongMoi);
        $stmt->bindParam(":id_giohang", $this->id);
        $stmt->bindParam(":id_san_pham", $sanPham->id);
        return $stmt->execute();
    }

    public function TinhTongTien() {
        $query = "SELECT SUM(sp.gia_ban * ctg.so_luong) as tong_tien 
                  FROM " . $this->table_name_detail . " ctg
                  JOIN sanpham sp ON ctg.id_san_pham = sp.id
                  WHERE ctg.id_giohang = :id_giohang";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_giohang", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['tong_tien'] ?? 0;
    }

    public function docChiTiet() {
        $query = "SELECT ctg.*, sp.ten_san_pham, sp.gia_ban, sp.hinh_anh 
                  FROM " . $this->table_name_detail . " ctg
                  JOIN sanpham sp ON ctg.id_san_pham = sp.id
                  WHERE ctg.id_giohang = :id_giohang";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_giohang", $this->id);
        $stmt->execute();
        return $stmt;
    }

    public function kiemTraTonTai() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id_khach_hang = :id_khach_hang LIMIT 1";
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
}
