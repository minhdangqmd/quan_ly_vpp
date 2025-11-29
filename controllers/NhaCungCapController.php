<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/NhaCungCap.php';
require_once __DIR__ . '/../utils/session.php';

class NhaCungCapController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function index() {
        $nhaCungCap = new NhaCungCap($this->conn);
        return $nhaCungCap->docTatCa();
    }

    public function create() {
        requireRole('Admin');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nhaCungCap = new NhaCungCap($this->conn);
            $nhaCungCap->id = $_POST['id'] ?? '';
            $nhaCungCap->ten_nha_cung_cap = $_POST['ten_nha_cung_cap'] ?? '';
            $nhaCungCap->dia_chi = $_POST['dia_chi'] ?? '';
            $nhaCungCap->sdt = $_POST['so_dien_thoai'] ?? '';
            $nhaCungCap->email = $_POST['email'] ?? '';
            
            if ($nhaCungCap->taoMoi()) {
                $baseUrl = getBaseUrl();
                header("Location: " . $baseUrl . "/admin/nhacungcap.php?success=1");
                exit();
            }
        }
        return null;
    }

    public function edit($id) {
        requireRole('Admin');
        $nhaCungCap = new NhaCungCap($this->conn);
        $nhaCungCap->id = $id;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nhaCungCap->ten_nha_cung_cap = $_POST['ten_nha_cung_cap'] ?? '';
            $nhaCungCap->dia_chi = $_POST['dia_chi'] ?? '';
            $nhaCungCap->sdt = $_POST['so_dien_thoai'] ?? '';
            $nhaCungCap->email = $_POST['email'] ?? '';

            if ($nhaCungCap->CapNhatThongTin()) {
                $baseUrl = getBaseUrl();
                header("Location: " . $baseUrl . "/admin/nhacungcap.php?success=1");
                exit();
            }
        } else {
            if ($nhaCungCap->TraCuuThongTin()) {
                return $nhaCungCap;
            }
        }
        return null;
    }

    public function delete($id) {
        requireRole('Admin');
        
        $nhaCungCap = new NhaCungCap($this->conn);
        $nhaCungCap->id = $id;
        
        if ($nhaCungCap->xoa()) {
            $baseUrl = getBaseUrl();
            header("Location: " . $baseUrl . "/admin/nhacungcap.php?success=1");
            exit();
        } else {
            $baseUrl = getBaseUrl();
            header("Location: " . $baseUrl . "/admin/nhacungcap.php?error=1");
            exit();
        }
    }
}
?>

