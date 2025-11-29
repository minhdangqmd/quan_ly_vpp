<?php
require_once __DIR__ . '/../config/database.php';

class NhanVien {
    private $conn;
    private $table_name = "nhanvien";
    
    public $id;
    public $id_taikhoan;
    public $ho_ten;
    public $sdt;
    public $dia_chi;
    public $id_chuc_vu;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function docTatCa() {
        $query = "SELECT nv.*, tk.email, tk.ten_dang_nhap, cv.ten_chuc_vu 
                  FROM " . $this->table_name . " nv
                  LEFT JOIN taikhoan tk ON nv.id_taikhoan = tk.id
                  LEFT JOIN chucvu cv ON nv.id_chuc_vu = cv.id
                  ORDER BY nv.ho_ten";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function layThongTinTheoTaiKhoan($id_taikhoan) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_taikhoan = :id_taikhoan LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_taikhoan", $id_taikhoan);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->id = $row['id'];
            $this->id_taikhoan = $row['id_taikhoan'];
            $this->ho_ten = $row['ho_ten'];
            $this->sdt = $row['sdt'];
            $this->dia_chi = $row['dia_chi'];
            $this->id_chuc_vu = $row['id_chuc_vu'];
            return true;
        }
        return false;
    }
}
?>

