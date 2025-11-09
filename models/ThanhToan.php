<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/DonHang.php';

class ThanhToan {
    private $conn;
    private $table_name = "thanhtoan";
    
    public $id;
    public $id_don_hang;
    public $phuong_thuc_thanh_toan;
    public $so_tien;
    public $ngay_thanh_toan;
    public $trang_thai;
    
    public $donHang; // DonHang object

    public function __construct($db) {
        $this->conn = $db;
    }

    public function ChonPhuongThuc() {
        // This method is for selecting payment method, validation happens here
        $validMethods = ['Tiền mặt', 'Chuyển khoản', 'Thẻ tín dụng', 'Ví điện tử'];
        if(in_array($this->phuong_thuc_thanh_toan, $validMethods)) {
            return true;
        }
        return false;
    }

    public function XacNhanThanhToan() {
        if(!$this->ChonPhuongThuc()) {
            return false;
        }
        
        $query = "INSERT INTO " . $this->table_name . " 
                  (id, id_don_hang, phuong_thuc_thanh_toan, so_tien, ngay_thanh_toan, trang_thai) 
                  VALUES (:id, :id_don_hang, :phuong_thuc_thanh_toan, :so_tien, NOW(), :trang_thai)";
        $stmt = $this->conn->prepare($query);
        
        $this->trang_thai = $this->trang_thai ?? 'Đã thanh toán';
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":id_don_hang", $this->id_don_hang);
        $stmt->bindParam(":phuong_thuc_thanh_toan", $this->phuong_thuc_thanh_toan);
        $stmt->bindParam(":so_tien", $this->so_tien);
        $stmt->bindParam(":trang_thai", $this->trang_thai);
        
        if($stmt->execute()) {
            // Update order payment status
            $donHang = new DonHang($this->conn);
            $donHang->id = $this->id_don_hang;
            if($donHang->TraCuuTrangThai()) {
                $donHang->CapNhatTrangThaiThanhToan('Đã thanh toán');
            }
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
            $this->phuong_thuc_thanh_toan = $row['phuong_thuc_thanh_toan'];
            $this->so_tien = $row['so_tien'];
            $this->ngay_thanh_toan = $row['ngay_thanh_toan'];
            $this->trang_thai = $row['trang_thai'];
            
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
}
?>

