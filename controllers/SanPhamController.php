<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/SanPham.php';
require_once __DIR__ . '/../models/DanhMucSanPham.php';
require_once __DIR__ . '/../utils/session.php';

class SanPhamController {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function index() {
        $sanPham = new SanPham($this->conn);
        $page = $_GET['page'] ?? 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $keyword = $_GET['keyword'] ?? '';
        $danhMucId = $_GET['danh_muc'] ?? null;
        
        if ($keyword && $danhMucId) {
            $stmt = $sanPham->timKiemTheoDanhMuc($keyword, $danhMucId);
        } elseif ($keyword) {
            $stmt = $sanPham->timKiem($keyword);
        } elseif ($danhMucId) {
            $stmt = $sanPham->docTheoDanhMuc($danhMucId, $limit, $offset);
        } else {
            $stmt = $sanPham->docTatCa($limit, $offset);
        }
        
        return $stmt;
    }

    public function show($id) {
        $sanPham = new SanPham($this->conn);
        $sanPham->id = $id;
        if ($sanPham->TraCuuThongTin()) {
            return $sanPham;
        }
        return null;
    }

    public function create() {
        requireRole('Admin');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $sanPham = new SanPham($this->conn);
            $sanPham->id = $_POST['id'] ?? '';
            $sanPham->ten_san_pham = $_POST['ten_san_pham'] ?? '';
            $sanPham->mo_ta = $_POST['mo_ta'] ?? '';
            $sanPham->gia_ban = $_POST['gia_ban'] ?? 0;
            $sanPham->so_luong_ton = $_POST['so_luong_ton'] ?? 0;
            
            // Convert empty strings to null for foreign keys
            $sanPham->id_danh_muc = !empty($_POST['id_danh_muc']) ? $_POST['id_danh_muc'] : null;
            $sanPham->id_nha_cung_cap = !empty($_POST['id_nha_cung_cap']) ? $_POST['id_nha_cung_cap'] : null;
            $sanPham->id_dvt = !empty($_POST['id_dvt']) ? $_POST['id_dvt'] : null;
            
            $sanPham->han_su_dung = !empty($_POST['han_su_dung']) ? $_POST['han_su_dung'] : null;
            
            // Handle image upload
            if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
                $uploadDir = 'uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = time() . '_' . basename($_FILES['hinh_anh']['name']);
                $targetFile = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $targetFile)) {
                    $sanPham->hinh_anh = $targetFile;
                }
            }
            
            if ($sanPham->taoMoi()) {
                $baseUrl = getBaseUrl();
                header("Location: " . $baseUrl . "/admin/sanpham.php?success=1");
                exit();
            }
        }
        
        // Get categories and suppliers for form
        $danhMuc = new DanhMucSanPham($this->conn);
        $danhMucs = $danhMuc->docTatCa();
        
        require_once __DIR__ . '/../models/NhaCungCap.php';
        $nhaCungCap = new NhaCungCap($this->conn);
        $nhaCungCaps = $nhaCungCap->docTatCa();
        
        return ['danhMucs' => $danhMucs, 'nhaCungCaps' => $nhaCungCaps];
    }

    public function update($id) {
        requireRole('Admin');
        
        $sanPham = new SanPham($this->conn);
        $sanPham->id = $id;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $sanPham->ten_san_pham = $_POST['ten_san_pham'] ?? '';
            $sanPham->mo_ta = $_POST['mo_ta'] ?? '';
            $sanPham->gia_ban = $_POST['gia_ban'] ?? 0;
            $sanPham->so_luong_ton = $_POST['so_luong_ton'] ?? 0;
            
            // Convert empty strings to null for foreign keys
            $sanPham->id_danh_muc = !empty($_POST['id_danh_muc']) ? $_POST['id_danh_muc'] : null;
            $sanPham->id_nha_cung_cap = !empty($_POST['id_nha_cung_cap']) ? $_POST['id_nha_cung_cap'] : null;
            $sanPham->id_dvt = !empty($_POST['id_dvt']) ? $_POST['id_dvt'] : null;
            
            $sanPham->han_su_dung = !empty($_POST['han_su_dung']) ? $_POST['han_su_dung'] : null;
            
            // Handle image upload
            if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
                $uploadDir = 'uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = time() . '_' . basename($_FILES['hinh_anh']['name']);
                $targetFile = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $targetFile)) {
                    $sanPham->hinh_anh = $targetFile;
                }
            }
            
            if ($sanPham->CapNhatThongTin()) {
                $baseUrl = getBaseUrl();
                header("Location: " . $baseUrl . "/admin/sanpham.php?success=1");
                exit();
            }
        } else {
            if ($sanPham->TraCuuThongTin()) {
                return $sanPham;
            }
        }
        
        return null;
    }

    public function delete($id) {
        requireRole('Admin');
        
        $sanPham = new SanPham($this->conn);
        $sanPham->id = $id;
        
        if ($sanPham->xoa()) {
            $baseUrl = getBaseUrl();
            header("Location: " . $baseUrl . "/admin/sanpham.php?success=1");
            exit();
        } else {
            $baseUrl = getBaseUrl();
            header("Location: " . $baseUrl . "/admin/sanpham.php?error=1");
            exit();
        }
    }
}
?>

