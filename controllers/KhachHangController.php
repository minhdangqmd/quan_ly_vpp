<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/KhachHang.php';

class KhachHangController {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    public function index() {
        $khachHang = new KhachHang($this->conn);
        return $khachHang->docTatCa();
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $khachHang = new KhachHang($this->conn);
            $khachHang->ho_ten = $_POST['ho_ten'] ?? '';
            $khachHang->sdt = $_POST['so_dien_thoai'] ?? '';
            $khachHang->dia_chi = $_POST['dia_chi'] ?? '';
            
            // Convert empty string, "0", or invalid values to NULL
            $id_taikhoan = $_POST['id_taikhoan'] ?? '';
            // Trim whitespace and check if it's a valid non-zero integer
            $id_taikhoan = trim($id_taikhoan);
            if ($id_taikhoan === '' || $id_taikhoan === '0' || !is_numeric($id_taikhoan)) {
                $khachHang->id_taikhoan = null;
            } else {
                $khachHang->id_taikhoan = intval($id_taikhoan);
            }
            
            if ($khachHang->taoMoi()) {
                $baseUrl = getBaseUrl();
                header("Location: " . $baseUrl . "/admin/khachhang.php?success=1");
                exit();
            } else {
                $baseUrl = getBaseUrl();
                header("Location: " . $baseUrl . "/admin/khachhang.php?error=1");
                exit();
            }
        }
    }
    
    public function show($id) {
        $khachHang = new KhachHang($this->conn);
        $khachHang->id = $id;
        if ($khachHang->TraCuuThongTin()) {
            return $khachHang;
        }
        return null;
    }
    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $khachHang = new KhachHang($this->conn);
            $khachHang->id = $id;
            $khachHang->ho_ten = $_POST['ho_ten'] ?? '';
            $khachHang->sdt = $_POST['so_dien_thoai'] ?? '';
            $khachHang->dia_chi = $_POST['dia_chi'] ?? '';
            
            if ($khachHang->CapNhatThongTin()) {
                $baseUrl = getBaseUrl();
                header("Location: " . $baseUrl . "/admin/khachhang.php?success=1");
                exit();
            }
        } else {
            return $this->show($id);
        }
    }
    
    public function delete($id) {
        $khachHang = new KhachHang($this->conn);
        $khachHang->id = $id;
        
        if ($khachHang->xoa()) {
            $baseUrl = getBaseUrl();
            header("Location: " . $baseUrl . "/admin/khachhang.php?success=1");
            exit();
        } else {
            $baseUrl = getBaseUrl();
            header("Location: " . $baseUrl . "/admin/khachhang.php?error=1");
            exit();
        }
    }
}
?>

