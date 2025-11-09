<?php
require_once __DIR__ . '/../config/database.php';

class ChiTietHoaDon {
    private $conn;
    private $table_name = "chitiethoadon";
    
    public $id;
    public $id_hoa_don;
    public $id_don_hang;
    public $so_luong;
    public $don_gia;
    public $tong_tien;

    public function __construct($db) {
        $this->conn = $db;
    }
}
?>

