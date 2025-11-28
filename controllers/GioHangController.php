<?php
require_once __DIR__ . '/../models/KhachHang.php';
require_once __DIR__ . '/../models/GioHang.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/GioHang.php';
require_once __DIR__ . '/../models/SanPham.php';
require_once __DIR__ . '/../utils/session.php';

class GioHangController {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function index() {
        requireLogin();
        
        $user = getCurrentUser();
        require_once __DIR__ . '/../models/KhachHang.php';
        $khachHang = new KhachHang($this->conn);
        $khachHang->id_taikhoan = $user['id'];
        
        // Find customer by account
        $query = "SELECT id FROM khachhang WHERE id_taikhoan = :id_taikhoan LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_taikhoan", $user['id']);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $gioHang = new GioHang($this->conn);
            $gioHang->id_khach_hang = $row['id'];
            if ($gioHang->kiemTraTonTai()) {
                return $gioHang;
            }
        }
        
        return null;
    }

   public function add()
{
    if (!isset($_SESSION['user_id'])) {
        die("Bạn chưa đăng nhập.");
    }

    $idTaiKhoan = $_SESSION['user_id'];

    // Lấy khách hàng
    $kh = KhachHang::layTheoTaiKhoan($idTaiKhoan);
    if (!$kh) {
        // tạo khách hàng nếu chưa có
        $newId = KhachHang::taoMoi($idTaiKhoan);
        $kh = KhachHang::layTheoTaiKhoan($idTaiKhoan);
    }

    // Lấy giỏ hàng
    $gioHang = GioHang::layTheoKhachHang($kh->id);
    if (!$gioHang) {
        // tạo giỏ hàng nếu chưa có
        $gioHangId = GioHang::taoMoi($kh->id);
        $gioHang = GioHang::layTheoKhachHang($kh->id);
    }

    // Lấy sản phẩm từ POST
    $idSanPham = $_POST['id_san_pham'] ?? null;
    $soLuong = $_POST['so_luong'] ?? 1;

    if (!$idSanPham) {
        die("Thiếu mã sản phẩm.");
    }

    // Gọi đúng số tham số
    GioHang::ThemSanPham($gioHang->id, $idSanPham, $soLuong);

    header("Location: giohang.php?success=1");
    exit();
}



    public function update() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idSanPham = $_POST['id_san_pham'] ?? '';
            $soLuong = $_POST['so_luong'] ?? 1;
            
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
                $gioHang = new GioHang($this->conn);
                $gioHang->id_khach_hang = $row['id'];
                $gioHang->kiemTraTonTai();
                
                $sanPham = new SanPham($this->conn);
                $sanPham->id = $idSanPham;
                
                if ($gioHang->CapNhatSoLuong($sanPham, $soLuong)) {
                    $baseUrl = getBaseUrl();
                    header("Location: " . $baseUrl . "/giohang.php?success=1");
                    exit();
                }
            }
        }
        
        $baseUrl = getBaseUrl();
        header("Location: " . $baseUrl . "/giohang.php");
        exit();
    }

    public function remove() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idSanPham = $_POST['id_san_pham'] ?? '';
            
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
                $gioHang = new GioHang($this->conn);
                $gioHang->id_khach_hang = $row['id'];
                $gioHang->kiemTraTonTai();
                
                $sanPham = new SanPham($this->conn);
                $sanPham->id = $idSanPham;
                
                if ($gioHang->XoaSanPham($sanPham)) {
                    $baseUrl = getBaseUrl();
                    header("Location: " . $baseUrl . "/giohang.php?success=1");
                    exit();
                }
            }
        }
        
        $baseUrl = getBaseUrl();
        header("Location: " . $baseUrl . "/giohang.php");
        exit();
    }
}
?>

