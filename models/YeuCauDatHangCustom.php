<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/KhachHang.php';
require_once __DIR__ . '/DonHang.php';

class YeuCauDatHangCustom {
    private $conn;
    private $table_name = "yeucaudathangcustom";
    
    public $id;
    public $id_don_hang;
    public $id_khach_hang;
    public $mo_ta_chi_tiet;
    public $trang_thai_yeu_cau;
    public $ngay_yeu_cau;
    public $gia_uoc_tinh;
    public $ngay_hoan_thanh_uoc_tinh;
    public $ghi_chu_noi_bo;
    
    public $khachHang; // KhachHang object
    public $donHang; // DonHang object

    public function __construct($db) {
        $this->conn = $db;
    }

    public function taoYeuCauDatHang() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id, id_khach_hang, mo_ta_chi_tiet, trang_thai_yeu_cau, 
                   ngay_yeu_cau, gia_uoc_tinh, ngay_hoan_thanh_uoc_tinh, ghi_chu_noi_bo) 
                  VALUES (:id, :id_khach_hang, :mo_ta_chi_tiet, :trang_thai_yeu_cau, 
                          NOW(), :gia_uoc_tinh, :ngay_hoan_thanh_uoc_tinh, :ghi_chu_noi_bo)";
        $stmt = $this->conn->prepare($query);
        
        $this->trang_thai_yeu_cau = $this->trang_thai_yeu_cau ?? 'Chờ xử lý';
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":id_khach_hang", $this->id_khach_hang);
        $stmt->bindParam(":mo_ta_chi_tiet", $this->mo_ta_chi_tiet);
        $stmt->bindParam(":trang_thai_yeu_cau", $this->trang_thai_yeu_cau);
        $stmt->bindParam(":gia_uoc_tinh", $this->gia_uoc_tinh);
        $stmt->bindParam(":ngay_hoan_thanh_uoc_tinh", $this->ngay_hoan_thanh_uoc_tinh);
        $stmt->bindParam(":ghi_chu_noi_bo", $this->ghi_chu_noi_bo);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function capNhatTrangThaiYeuCau($trangThaiMoi) {
        $query = "UPDATE " . $this->table_name . " 
                  SET trang_thai_yeu_cau = :trang_thai_yeu_cau 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":trang_thai_yeu_cau", $trangThaiMoi);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            $this->trang_thai_yeu_cau = $trangThaiMoi;
            return true;
        }
        return false;
    }

    public function capNhatGiaUocTinh($gia) {
        $query = "UPDATE " . $this->table_name . " 
                  SET gia_uoc_tinh = :gia_uoc_tinh 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":gia_uoc_tinh", $gia);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            $this->gia_uoc_tinh = $gia;
            return true;
        }
        return false;
    }

    public function capNhatNgayHoanThanhUocTinh($ngay) {
        $query = "UPDATE " . $this->table_name . " 
                  SET ngay_hoan_thanh_uoc_tinh = :ngay_hoan_thanh_uoc_tinh 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ngay_hoan_thanh_uoc_tinh", $ngay);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            $this->ngay_hoan_thanh_uoc_tinh = $ngay;
            return true;
        }
        return false;
    }

    public function docTheoId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->id_don_hang = $row['id_don_hang'];
            $this->id_khach_hang = $row['id_khach_hang'];
            $this->mo_ta_chi_tiet = $row['mo_ta_chi_tiet'];
            $this->trang_thai_yeu_cau = $row['trang_thai_yeu_cau'];
            $this->ngay_yeu_cau = $row['ngay_yeu_cau'];
            $this->gia_uoc_tinh = $row['gia_uoc_tinh'];
            $this->ngay_hoan_thanh_uoc_tinh = $row['ngay_hoan_thanh_uoc_tinh'];
            $this->ghi_chu_noi_bo = $row['ghi_chu_noi_bo'];
            
            // Load KhachHang
            if($this->id_khach_hang) {
                $kh = new KhachHang($this->conn);
                $kh->id = $this->id_khach_hang;
                if($kh->TraCuuThongTin()) {
                    $this->khachHang = $kh;
                }
            }
            
            // Load DonHang
            if($this->id_don_hang) {
                $dh = new DonHang($this->conn);
                $dh->id = $this->id_don_hang;
                if($dh->TraCuuTrangThai()) {
                    $this->donHang = $dh;
                }
            }
            
            return true;
        }
        return false;
    }

    public function docTatCa($limit = 100, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  ORDER BY ngay_yeu_cau DESC 
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}
?>

