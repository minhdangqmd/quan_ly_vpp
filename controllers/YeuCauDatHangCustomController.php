<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/YeuCauDatHangCustom.php';
require_once __DIR__ . '/../models/HinhAnhYeuCau.php';
require_once __DIR__ . '/../utils/session.php';

class YeuCauDatHangCustomController {
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
                $yeuCau = new YeuCauDatHangCustom($this->conn);
                $yeuCau->id = "YC" . time();
                $yeuCau->id_khach_hang = $row['id'];
                $yeuCau->mo_ta_chi_tiet = $_POST['mo_ta_chi_tiet'] ?? '';
                $yeuCau->gia_uoc_tinh = $_POST['gia_uoc_tinh'] ?? 0;
                $yeuCau->ngay_hoan_thanh_uoc_tinh = $_POST['ngay_hoan_thanh_uoc_tinh'] ?? null;
                
                if ($yeuCau->taoYeuCauDatHang()) {
                    // Handle image uploads
                    if (isset($_FILES['hinh_anh']) && is_array($_FILES['hinh_anh']['name'])) {
                        $uploadDir = 'uploads/custom_orders/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        
                        for ($i = 0; $i < count($_FILES['hinh_anh']['name']); $i++) {
                            if ($_FILES['hinh_anh']['error'][$i] == 0) {
                                $fileName = time() . '_' . $i . '_' . basename($_FILES['hinh_anh']['name'][$i]);
                                $targetFile = $uploadDir . $fileName;
                                
                                if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'][$i], $targetFile)) {
                                    $hinhAnh = new HinhAnhYeuCau($this->conn);
                                    $hinhAnh->id = "HA" . time() . $i;
                                    $hinhAnh->id_yeu_cau = $yeuCau->id;
                                    $hinhAnh->duong_dan_hinh_anh = $targetFile;
                                    $hinhAnh->mo_ta_hinh_anh = $_POST['mo_ta_hinh_anh'][$i] ?? '';
                                    $hinhAnh->taiLenHinhAnh();
                                }
                            }
                        }
                    }
                    
                    $baseUrl = getBaseUrl();
                    header("Location: " . $baseUrl . "/yeucaucustom.php?success=1&id=" . $yeuCau->id);
                    exit();
                }
            }
        }
        
        $baseUrl = getBaseUrl();
        header("Location: " . $baseUrl . "/yeucaucustom.php");
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
            $yeuCau = new YeuCauDatHangCustom($this->conn);
            $stmt = $yeuCau->docTatCa();
            return $stmt;
        }
        
        return null;
    }

    public function show($id) {
        requireLogin();
        
        $yeuCau = new YeuCauDatHangCustom($this->conn);
        $yeuCau->id = $id;
        if ($yeuCau->docTheoId()) {
            // Get images
            $hinhAnh = new HinhAnhYeuCau($this->conn);
            $hinhAnh->id_yeu_cau = $id;
            $images = $hinhAnh->xemHinhAnh();
            
            return ['yeuCau' => $yeuCau, 'images' => $images];
        }
        return null;
    }
}
?>

