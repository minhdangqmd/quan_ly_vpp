<?php
require_once __DIR__ . '/../config/database.php';

class ChiTietGioHang {
    private $conn;
    private $table_name = "chitietgiohang";
    
    public $id;
    public $id_gio_hang;
    public $id_san_pham;
    public $so_luong;

    public function __construct($db) {
        $this->conn = $db;
    }
}
?>

