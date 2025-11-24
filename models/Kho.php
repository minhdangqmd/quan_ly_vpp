<?php
require_once __DIR__ . '/../config/database.php';

class Kho {
    private $conn;
    private $table_name = "kho";
    
    public $id;
    public $ten_kho;
    public $dia_chi;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function docTatCa() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY ten_kho";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>

