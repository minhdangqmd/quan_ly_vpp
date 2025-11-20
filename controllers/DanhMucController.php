<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/DanhMucSanPham.php';

class DanhMucController {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    public function index() {
        $danhMuc = new DanhMucSanPham($this->conn);
        return $danhMuc->docTatCa();
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $danhMuc = new DanhMucSanPham($this->conn);
            $danhMuc->ten_danh_muc = $_POST['ten_danh_muc'] ?? '';
            
            if ($danhMuc->ThemDanhMuc()) {
                $baseUrl = getBaseUrl();
                header("Location: " . $baseUrl . "/admin/danhmuc.php?success=1");
                exit();
            }
        }
    }
    
    public function show($id) {
        $danhMuc = new DanhMucSanPham($this->conn);
        $danhMuc->id = $id;
        if ($danhMuc->docTheoId()) {
            return $danhMuc;
        }
        return null;
    }
    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $danhMuc = new DanhMucSanPham($this->conn);
            $danhMuc->id = $id;
            $danhMuc->ten_danh_muc = $_POST['ten_danh_muc'] ?? '';
            
            if ($danhMuc->CapNhatDanhMuc()) {
                $baseUrl = getBaseUrl();
                header("Location: " . $baseUrl . "/admin/danhmuc.php?success=1");
                exit();
            }
        } else {
            return $this->show($id);
        }
    }
    
    public function delete($id) {
        $danhMuc = new DanhMucSanPham($this->conn);
        $danhMuc->id = $id;
        
        if ($danhMuc->XoaDanhMuc()) {
            $baseUrl = getBaseUrl();
            header("Location: " . $baseUrl . "/admin/danhmuc.php?success=1");
            exit();
        } else {
            $baseUrl = getBaseUrl();
            header("Location: " . $baseUrl . "/admin/danhmuc.php?error=1");
            exit();
        }
    }
}
?>

