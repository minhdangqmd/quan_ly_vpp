<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/KhachHang.php';
require_once __DIR__ . '/ChuongTrinhKhuyenMai.php';

class DonHang {
    private $conn;
    private $table_name = "donhang";
    private $table_name_detail = "chitietdonhang";
    
    public $id;
    public $loai_don_hang;
    public $id_khach_hang;
    public $ngay_dat;
    public $tong_tien;
    public $trang_thai_don_hang;
    public $ghi_chu;
    public $trang_thai_thanh_toan;
    public $dia_chi_giao;
    public $sdt_nhan;
    public $id_hinh_thuc_tt;
    public $id_chuong_trinh_km;
    public $id_nguoi_tao;
    
    public $khachHang; // KhachHang object
    public $chuongTrinhKhuyenMai; // ChuongTrinhKhuyenMai object

    public function __construct($db) {
        $this->conn = $db;
    }

    public function TaoDonHang() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id, loai_don_hang, id_khach_hang, ngay_dat, tong_tien, 
                   trang_thai, dia_chi_giao, sdt_nhan, id_hinh_thuc_tt, 
                   id_chuong_trinh_km, ghi_chu, trang_thai_thanh_toan, id_nguoi_tao) 
                  VALUES (:id, :loai_don_hang, :id_khach_hang, NOW(), :tong_tien, 
                          :trang_thai, :dia_chi_giao, :sdt_nhan, :id_hinh_thuc_tt, 
                          :id_chuong_trinh_km, :ghi_chu, :trang_thai_thanh_toan, :id_nguoi_tao)";
        $stmt = $this->conn->prepare($query);
        
        $this->trang_thai_don_hang = $this->trang_thai_don_hang ?? 'Đang xử lý';
        $this->trang_thai_thanh_toan = $this->trang_thai_thanh_toan ?? 'Chưa thanh toán';
        $this->loai_don_hang = $this->loai_don_hang ?? 'Thường';
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":loai_don_hang", $this->loai_don_hang);
        $stmt->bindParam(":id_khach_hang", $this->id_khach_hang);
        $stmt->bindParam(":tong_tien", $this->tong_tien);
        $stmt->bindParam(":trang_thai", $this->trang_thai_don_hang);
        $stmt->bindParam(":dia_chi_giao", $this->dia_chi_giao);
        $stmt->bindParam(":sdt_nhan", $this->sdt_nhan);
        $stmt->bindParam(":id_hinh_thuc_tt", $this->id_hinh_thuc_tt);
        $stmt->bindParam(":id_chuong_trinh_km", $this->id_chuong_trinh_km);
        $stmt->bindParam(":ghi_chu", $this->ghi_chu);
        $stmt->bindParam(":trang_thai_thanh_toan", $this->trang_thai_thanh_toan);
        $stmt->bindParam(":id_nguoi_tao", $this->id_nguoi_tao);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function CapNhatTrangThaiDonHang($trangThaiMoi) {
        $query = "UPDATE " . $this->table_name . " 
                  SET trang_thai = :trang_thai 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":trang_thai", $trangThaiMoi);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            $this->trang_thai_don_hang = $trangThaiMoi;
            return true;
        }
        return false;
    }

    public function CapNhatTrangThaiThanhToan($trangThaiMoi) {
        $query = "UPDATE " . $this->table_name . " 
                  SET trang_thai_thanh_toan = :trang_thai_thanh_toan 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":trang_thai_thanh_toan", $trangThaiMoi);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            $this->trang_thai_thanh_toan = $trangThaiMoi;
            return true;
        }
        return false;
    }

    public function TraCuuTrangThai() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->loai_don_hang = $row['loai_don_hang'];
            $this->id_khach_hang = $row['id_khach_hang'];
            $this->ngay_dat = $row['ngay_dat'];
            $this->tong_tien = $row['tong_tien'];
            $this->trang_thai_don_hang = $row['trang_thai'];
            $this->ghi_chu = $row['ghi_chu'];
            $this->trang_thai_thanh_toan = $row['trang_thai_thanh_toan'];
            $this->dia_chi_giao = $row['dia_chi_giao'];
            $this->sdt_nhan = $row['sdt_nhan'];
            $this->id_hinh_thuc_tt = $row['id_hinh_thuc_tt'];
            $this->id_chuong_trinh_km = $row['id_chuong_trinh_km'];
            $this->id_nguoi_tao = $row['id_nguoi_tao'];
            
            // Load KhachHang
            if($this->id_khach_hang) {
                $kh = new KhachHang($this->conn);
                $kh->id = $this->id_khach_hang;
                if($kh->TraCuuThongTin()) {
                    $this->khachHang = $kh;
                }
            }
            
            // Load ChuongTrinhKhuyenMai
            if($this->id_chuong_trinh_km) {
                $km = new ChuongTrinhKhuyenMai($this->conn);
                $km->id = $this->id_chuong_trinh_km;
                if($km->docTheoId()) {
                    $this->chuongTrinhKhuyenMai = $km;
                }
            }
            
            return true;
        }
        return false;
    }

    public function docChiTiet() {
        $query = "SELECT ctdh.*, sp.ten_san_pham, sp.hinh_anh 
                  FROM " . $this->table_name_detail . " ctdh
                  JOIN sanpham sp ON ctdh.id_san_pham = sp.id
                  WHERE ctdh.id_don_hang = :id_don_hang";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_don_hang", $this->id);
        $stmt->execute();
        return $stmt;
    }

    public function themChiTiet($idSanPham, $soLuong, $donGia) {
        $query = "INSERT INTO " . $this->table_name_detail . " 
                  (id_don_hang, id_san_pham, so_luong, don_gia, thanh_tien) 
                  VALUES (:id_don_hang, :id_san_pham, :so_luong, :don_gia, :thanh_tien)";
        $stmt = $this->conn->prepare($query);
        
        $thanhTien = $soLuong * $donGia;
        $stmt->bindParam(":id_don_hang", $this->id);
        $stmt->bindParam(":id_san_pham", $idSanPham);
        $stmt->bindParam(":so_luong", $soLuong);
        $stmt->bindParam(":don_gia", $donGia);
        $stmt->bindParam(":thanh_tien", $thanhTien);
        
        return $stmt->execute();
    }

    public function docTatCa($limit = 100, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  ORDER BY ngay_dat DESC 
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}
?>

