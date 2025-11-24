<?php
require_once __DIR__ . '/../config/database.php';

class BaoCao {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function TaoBaoCaoDoanhThu($tuNgay, $denNgay) {
        $query = "SELECT 
                    DATE(ngay_dat) as ngay,
                    COUNT(*) as so_don,
                    SUM(tong_tien) as tong_doanh_thu
                  FROM donhang 
                  WHERE ngay_dat BETWEEN :tu_ngay AND :den_ngay 
                    AND trang_thai_thanh_toan = 'Đã thanh toán'
                  GROUP BY DATE(ngay_dat)
                  ORDER BY ngay";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tu_ngay", $tuNgay);
        $stmt->bindParam(":den_ngay", $denNgay);
        $stmt->execute();
        return $stmt;
    }

    public function TaoBaoCaoTonKho() {
        $query = "SELECT 
                    sp.id,
                    sp.ten_san_pham,
                    sp.so_luong_ton,
                    dm.ten_danh_muc,
                    dvt.ten_dvt
                  FROM sanpham sp
                  LEFT JOIN danhmuc dm ON sp.id_danh_muc = dm.id
                  LEFT JOIN donvitinh dvt ON sp.id_dvt = dvt.id
                  ORDER BY sp.so_luong_ton ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function TaoBaoCaoDonHang($trangThai) {
        $query = "SELECT 
                    dh.id,
                    dh.ngay_dat,
                    dh.tong_tien,
                    dh.trang_thai,
                    kh.ho_ten,
                    kh.sdt
                  FROM donhang dh
                  LEFT JOIN khachhang kh ON dh.id_khach_hang = kh.id
                  WHERE dh.trang_thai = :trang_thai
                  ORDER BY dh.ngay_dat DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":trang_thai", $trangThai);
        $stmt->execute();
        return $stmt;
    }

    public function TaoBaoCaoKhuyenMai() {
        $query = "SELECT 
                    km.id,
                    km.ten_chuong_trinh,
                    km.ngay_bat_dau,
                    km.ngay_ket_thuc,
                    km.giam_gia,
                    km.trang_thai,
                    COUNT(dh.id) as so_don_ap_dung,
                    SUM(dh.tong_tien) as tong_doanh_thu
                  FROM chuongtrinhkhuyenmai km
                  LEFT JOIN donhang dh ON km.id = dh.id_chuong_trinh_km
                  GROUP BY km.id
                  ORDER BY km.ngay_bat_dau DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>

