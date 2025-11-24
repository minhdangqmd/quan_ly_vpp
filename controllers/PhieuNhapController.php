<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/PhieuNhap.php';
require_once __DIR__ . '/../models/NhaCungCap.php';
require_once __DIR__ . '/../models/Kho.php';
require_once __DIR__ . '/../models/SanPham.php';
require_once __DIR__ . '/../models/NhanVien.php';
require_once __DIR__ . '/../utils/session.php';

class PhieuNhapController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function index() {
        $phieuNhap = new PhieuNhap($this->conn);
        return $phieuNhap->docTatCa();
    }

    public function detail($id) {
        $phieuNhap = new PhieuNhap($this->conn);
        // Manually load basic info first
        $query = "SELECT pn.*, ncc.ten_nha_cung_cap, nv.ho_ten as ten_nhan_vien, k.ten_kho 
                  FROM phieunhap pn
                  LEFT JOIN nhacungcap ncc ON pn.id_nha_cung_cap = ncc.id
                  LEFT JOIN nhanvien nv ON pn.id_nhan_vien = nv.id
                  LEFT JOIN kho k ON pn.id_kho = k.id
                  WHERE pn.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$info) return null;
        
        // Load details
        $details = $phieuNhap->docChiTiet($id);
        
        return ['info' => $info, 'details' => $details];
    }

    public function create() {
        requireRole('Admin'); // Or Staff
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phieuNhap = new PhieuNhap($this->conn);
            
            // Generate ID: PN + YmdHis
            $phieuNhap->id = 'PN' . date('YmdHis');
            $phieuNhap->id_nha_cung_cap = $_POST['id_nha_cung_cap'];
            $phieuNhap->id_kho = $_POST['id_kho'];
            $phieuNhap->ghi_chu = $_POST['ghi_chu'] ?? '';
            $phieuNhap->tong_tien = 0; // Will update as we add details
            
            // Get current staff ID
            $user = getCurrentUser(); // from session
            if ($user && isset($user['id'])) {
                $nhanVien = new NhanVien($this->conn);
                if ($nhanVien->layThongTinTheoTaiKhoan($user['id'])) {
                    $phieuNhap->id_nhan_vien = $nhanVien->id;
                } else {
                    // Fallback or error if current user is not a staff (e.g. just Admin account not linked to NhanVien)
                    // For now, let's assume Admin has a NhanVien record or we set NULL/Dummy
                    // If user is pure Admin without NhanVien record, this might fail FK.
                    // Let's check if table allows NULL. schema says id_nhan_vien varchar(10) DEFAULT NULL.
                    $phieuNhap->id_nhan_vien = null;
                }
            } else {
                $phieuNhap->id_nhan_vien = null;
            }

            if ($phieuNhap->TaoPhieu()) {
                $products = $_POST['products'] ?? []; // Array of {id_san_pham, so_luong, don_gia}
                
                foreach ($products as $prod) {
                    if (!empty($prod['id_san_pham']) && !empty($prod['so_luong']) && !empty($prod['don_gia'])) {
                        $phieuNhap->themChiTiet(
                            $prod['id_san_pham'], 
                            $prod['so_luong'], 
                            $prod['don_gia']
                        );
                    }
                }
                
                // Update stock
                $phieuNhap->XacNhapNhapKho();
                
                $baseUrl = getBaseUrl();
                header("Location: " . $baseUrl . "/admin/phieunhap.php?success=1");
                exit();
            }
        }
        
        // Prepare data for view
        $ncc = new NhaCungCap($this->conn);
        $kho = new Kho($this->conn);
        $sp = new SanPham($this->conn);
        
        return [
            'nhaCungCaps' => $ncc->docTatCa(),
            'khos' => $kho->docTatCa(),
            'sanPhams' => $sp->docTatCa()
        ];
    }
}
?>

