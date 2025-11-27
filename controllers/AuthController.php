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

    public function dangNhap() {
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

    public function dangXuat() {
        $nguoiDung = new NguoiDung($this->conn);
        $nguoiDung->DangXuat();
        $baseUrl = getBaseUrl();
        header("Location: " . $baseUrl . "/dangNhap.php");
        exit();
    }

    public function dangKy() {
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

    public function quenMatKhau() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'] ?? '';
            
            if (empty($email)) {
                return "Vui lòng nhập email!";
            }
            
            $nguoiDung = new NguoiDung($this->conn);
            $nguoiDung->email = $email;
            
            if ($nguoiDung->docTheoEmail()) {
                $token = $nguoiDung->taoTokenResetPassword();
                
                if ($token) {
                    // Trong môi trường thực tế, gửi email ở đây
                    // Hiện tại sẽ trả về token để hiển thị
                    return ['success' => true, 'token' => $token, 'email' => $email];
                } else {
                    return "Không thể tạo token reset password!";
                }
            } else {
                // Không tiết lộ email có tồn tại hay không (bảo mật)
                return ['success' => true, 'token' => null, 'email' => $email];
            }
        }
        return null;
    }

    public function resetMatKhau() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $token = $_POST['token'] ?? '';
            $mat_khau_moi = $_POST['mat_khau_moi'] ?? '';
            $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'] ?? '';
            
            if (empty($token) || empty($mat_khau_moi) || empty($xac_nhan_mat_khau)) {
                return "Vui lòng điền đầy đủ thông tin!";
            }
            
            if ($mat_khau_moi !== $xac_nhan_mat_khau) {
                return "Mật khẩu xác nhận không khớp!";
            }
            
            if (strlen($mat_khau_moi) < 6) {
                return "Mật khẩu phải có ít nhất 6 ký tự!";
            }
            
            $nguoiDung = new NguoiDung($this->conn);
            
            if ($nguoiDung->kiemTraToken($token)) {
                if ($nguoiDung->capNhatMatKhau($mat_khau_moi)) {
                    return ['success' => true];
                } else {
                    return "Không thể cập nhật mật khẩu!";
                }
            } else {
                return "Token không hợp lệ hoặc đã hết hạn!";
            }
        }
        return null;
    }
}
?>

