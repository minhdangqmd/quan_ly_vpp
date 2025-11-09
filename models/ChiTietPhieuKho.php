<?php
require_once __DIR__ . '/../config/database.php';

class ChiTietPhieuKho {
    private $conn;
    private $table_name = "chitietphieunhap"; // Can be used for both import and export
    
    public $id;
    public $id_phieu;
    public $id_san_pham;
    public $so_luong;
    public $don_gia;
    public $thanh_tien;

    public function __construct($db) {
        $this->conn = $db;
    }
}
?>

