<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/BaoCao.php';

class BaoCaoController {
    private $db;
    private $conn;
    private $baoCao;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        $this->baoCao = new BaoCao($this->conn);
    }
    
    public function baoCaoDoanhThu($tuNgay, $denNgay) {
        return $this->baoCao->TaoBaoCaoDoanhThu($tuNgay, $denNgay);
    }
    
    public function baoCaoTonKho() {
        return $this->baoCao->TaoBaoCaoTonKho();
    }
    
    public function baoCaoDonHang($trangThai = null) {
        if ($trangThai) {
            return $this->baoCao->TaoBaoCaoDonHang($trangThai);
        }
        
        // Get all orders if no status specified
        $query = "SELECT 
                    dh.id,
                    dh.ngay_dat,
                    dh.tong_tien,
                    dh.trang_thai_thanh_toan,
                    kh.ho_ten,
                    kh.sdt
                  FROM donhang dh
                  LEFT JOIN khachhang kh ON dh.id_khach_hang = kh.id
                  ORDER BY dh.ngay_dat DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function baoCaoSanPhamBanChay($limit = 10) {
        $query = "SELECT 
                    sp.id,
                    sp.ten_san_pham,
                    sp.gia_ban,
                    SUM(ctdh.so_luong) as tong_so_luong_ban,
                    SUM(ctdh.thanh_tien) as tong_doanh_thu,
                    dm.ten_danh_muc
                  FROM sanpham sp
                  INNER JOIN chitietdonhang ctdh ON sp.id = ctdh.id_san_pham
                  INNER JOIN donhang dh ON ctdh.id_don_hang = dh.id
                  LEFT JOIN danhmuc dm ON sp.id_danh_muc = dm.id
                  WHERE dh.trang_thai_thanh_toan = 'Đã thanh toán'
                  GROUP BY sp.id
                  ORDER BY tong_so_luong_ban DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
    
    public function thongKeKhachHang() {
        $query = "SELECT 
                    kh.id,
                    kh.ho_ten,
                    kh.sdt,
                    COUNT(dh.id) as tong_don_hang,
                    SUM(dh.tong_tien) as tong_chi_tieu
                  FROM khachhang kh
                  LEFT JOIN donhang dh ON kh.id = dh.id_khach_hang
                  GROUP BY kh.id
                  HAVING tong_don_hang > 0
                  ORDER BY tong_chi_tieu DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function exportToExcel($type, $data, $headers, $filename) {
        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Start output buffering
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        
        // Output Excel XML format
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        echo '<Worksheet ss:Name="' . $filename . '">' . "\n";
        echo '<Table>' . "\n";
        
        // Output headers
        echo '<Row>' . "\n";
        foreach ($headers as $header) {
            echo '<Cell><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>' . "\n";
        }
        echo '</Row>' . "\n";
        
        // Output data
        while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
            echo '<Row>' . "\n";
            foreach ($row as $cell) {
                $cellType = is_numeric($cell) ? 'Number' : 'String';
                echo '<Cell><Data ss:Type="' . $cellType . '">' . htmlspecialchars($cell ?? '') . '</Data></Cell>' . "\n";
            }
            echo '</Row>' . "\n";
        }
        
        echo '</Table>' . "\n";
        echo '</Worksheet>' . "\n";
        echo '</Workbook>' . "\n";
        
        exit();
    }
}
?>

