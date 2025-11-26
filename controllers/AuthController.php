<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/NguoiDung.php';
require_once __DIR__ . '/../utils/session.php';

class AuthController {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ten_dang_nhap = $_POST['ten_dang_nhap'] ?? '';
            $mat_khau = $_POST['mat_khau'] ?? '';
            
            $nguoiDung = new NguoiDung($this->conn);
            $nguoiDung->ten_dang_nhap = $ten_dang_nhap;
            $nguoiDung->mat_khau = $mat_khau;
            
            if ($nguoiDung->DangNhap()) {
                $_SESSION['user_id'] = $nguoiDung->id;
                $_SESSION['username'] = $nguoiDung->ten_dang_nhap;
                $_SESSION['email'] = $nguoiDung->email;
                $_SESSION['user_role'] = ($nguoiDung->quyen && isset($nguoiDung->quyen->ten_vai_tro)) ? $nguoiDung->quyen->ten_vai_tro : 'KhachHang';
                
                $baseUrl = getBaseUrl();
                header("Location: " . $baseUrl . "/index.php");
                exit();
            } else {
                return "Tên đăng nhập hoặc mật khẩu không đúng!";
            }
        }
        return null;
    }

    public function logout() {
        $nguoiDung = new NguoiDung($this->conn);
        $nguoiDung->DangXuat();
        $baseUrl = getBaseUrl();
        header("Location: " . $baseUrl . "/login.php");
        exit();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once __DIR__ . '/../models/KhachHang.php';
            
            $ten_dang_nhap = $_POST['ten_dang_nhap'] ?? '';
            $mat_khau = $_POST['mat_khau'] ?? '';
            $email = $_POST['email'] ?? '';
            $ho_ten = $_POST['ho_ten'] ?? '';
            $so_dien_thoai = $_POST['so_dien_thoai'] ?? '';
            $dia_chi = $_POST['dia_chi'] ?? '';
            
            // Create account
            $nguoiDung = new NguoiDung($this->conn);
            $nguoiDung->ten_dang_nhap = $ten_dang_nhap;
            $nguoiDung->mat_khau = $mat_khau;
            $nguoiDung->email = $email;
            $nguoiDung->id_vai_tro = 3; // KhachHang role
            
            if ($nguoiDung->kiemTraTonTai()) {
                return "Tên đăng nhập đã tồn tại, vui lòng chọn tên khác!";
            }
            
            if ($nguoiDung->kiemTraEmailTonTai()) {
                return "Email đã được sử dụng, vui lòng chọn email khác!";
            }
            
            if ($nguoiDung->taoMoi()) {
                // Create customer
                $khachHang = new KhachHang($this->conn);
                $khachHang->id_taikhoan = $nguoiDung->id;
                $khachHang->ho_ten = $ho_ten;
                $khachHang->sdt = $so_dien_thoai;
                $khachHang->dia_chi = $dia_chi;
                
                if ($khachHang->taoMoi()) {
                    // Tự động đăng nhập sau khi đăng ký thành công
                    $_SESSION['user_id'] = $nguoiDung->id;
                    $_SESSION['username'] = $nguoiDung->ten_dang_nhap;
                    $_SESSION['email'] = $nguoiDung->email;
                    $_SESSION['user_role'] = 'KhachHang';
                    
                    $baseUrl = getBaseUrl();
                    header("Location: " . $baseUrl . "/index.php");
                    exit();
                }
                return "Không thể tạo thông tin khách hàng!";
            }
            
            return "Đăng ký thất bại!";
        }
        return null;
    }
}
?>

