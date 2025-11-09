<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/DonHang.php';
require_once __DIR__ . '/../models/GioHang.php';
require_once __DIR__ . '/../models/SanPham.php';
require_once __DIR__ . '/../utils/session.php';

class DonHangController {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = getCurrentUser();
            require_once __DIR__ . '/../models/KhachHang.php';
            $khachHang = new KhachHang($this->conn);
            $khachHang->id_taikhoan = $user['id'];
            
            $query = "SELECT id FROM khachhang WHERE id_taikhoan = :id_taikhoan LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id_taikhoan", $user['id']);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                // Get cart
                $gioHang = new GioHang($this->conn);
                $gioHang->id_khach_hang = $row['id'];
                if (!$gioHang->kiemTraTonTai()) {
                    $baseUrl = getBaseUrl();
                    header("Location: " . $baseUrl . "/giohang.php?error=1");
                    exit();
                }
                
                // Create order
                $donHang = new DonHang($this->conn);
                $donHang->id = "DH" . time();
                $donHang->id_khach_hang = $row['id'];
                $donHang->dia_chi_giao = $_POST['dia_chi_giao'] ?? '';
                $donHang->sdt_nhan = $_POST['sdt_nhan'] ?? '';
                $donHang->id_hinh_thuc_tt = $_POST['id_hinh_thuc_tt'] ?? null;
                $donHang->ghi_chu = $_POST['ghi_chu'] ?? '';
                $donHang->id_nguoi_tao = $user['id'];
                $donHang->tong_tien = $gioHang->TinhTongTien();
                
                if ($donHang->TaoDonHang()) {
                    // Add order details from cart
                    $cartDetails = $gioHang->docChiTiet();
                    while ($item = $cartDetails->fetch(PDO::FETCH_ASSOC)) {
                        $donHang->themChiTiet($item['id_san_pham'], $item['so_luong'], $item['gia_ban']);
                        
                        // Update stock
                        $sanPham = new SanPham($this->conn);
                        $sanPham->id = $item['id_san_pham'];
                        if ($sanPham->TraCuuThongTin()) {
                            $soLuongMoi = $sanPham->so_luong_ton - $item['so_luong'];
                            if ($soLuongMoi < 0) $soLuongMoi = 0;
                            $sanPham->CapNhatSoLuongTon($soLuongMoi);
                        }
                    }
                    
                    // Clear cart
                    $query = "DELETE FROM chitietgiohang WHERE id_gio_hang = :id_gio_hang";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(":id_gio_hang", $gioHang->id);
                    $stmt->execute();
                    
                    $baseUrl = getBaseUrl();
                    header("Location: " . $baseUrl . "/donhang.php?success=1&id=" . $donHang->id);
                    exit();
                }
            }
        }
        
        $baseUrl = getBaseUrl();
        header("Location: " . $baseUrl . "/giohang.php");
        exit();
    }

    public function index() {
        requireLogin();
        
        $user = getCurrentUser();
        require_once __DIR__ . '/../models/KhachHang.php';
        $khachHang = new KhachHang($this->conn);
        $khachHang->id_taikhoan = $user['id'];
        
        $query = "SELECT id FROM khachhang WHERE id_taikhoan = :id_taikhoan LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_taikhoan", $user['id']);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $query = "SELECT * FROM donhang WHERE id_khach_hang = :id_khach_hang ORDER BY ngay_dat DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id_khach_hang", $row['id']);
            $stmt->execute();
            return $stmt;
        }
        
        return null;
    }

    public function show($id) {
        requireLogin();
        
        $donHang = new DonHang($this->conn);
        $donHang->id = $id;
        if ($donHang->TraCuuTrangThai()) {
            return $donHang;
        }
        return null;
    }
}
?>

