<?php
require_once __DIR__ . '/../config/database.php';

class ChiTietDonHang {
    private $conn;
    private $table_name = "chitietdonhang";
    
    public $id;
    public $id_don_hang;
    public $id_san_pham;
    public $so_luong;
    public $don_gia;
    public $thanh_tien;

    public function __construct($db) {
        $this->conn = $db;
    }
}
?>

