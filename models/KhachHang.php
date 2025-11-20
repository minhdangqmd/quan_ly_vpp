<?php
require_once __DIR__ . '/../config/database.php';

class KhachHang {
    private $conn;
    private $table_name = "khachhang";
    
    public $id;
    public $id_taikhoan;
    public $ho_ten;
    public $so_dien_thoai;
    public $email;
    public $dia_chi;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function CapNhatThongTin() {
        $query = "UPDATE " . $this->table_name . " 
                  SET ho_ten = :ho_ten, so_dien_thoai = :so_dien_thoai, 
                      dia_chi = :dia_chi 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ho_ten", $this->ho_ten);
        $stmt->bindParam(":so_dien_thoai", $this->so_dien_thoai);
        $stmt->bindParam(":dia_chi", $this->dia_chi);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function TraCuuThongTin() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->ho_ten = $row['ho_ten'];
            $this->so_dien_thoai = $row['so_dien_thoai'];
            $this->dia_chi = $row['dia_chi'];
            $this->id_taikhoan = $row['id_taikhoan'];
            return true;
        }
        return false;
    }

    public function GuiPhanHoi($noi_dung) {
        require_once __DIR__ . '/PhanHoi.php';
        $phanHoi = new PhanHoi($this->conn);
        $phanHoi->id = "PH" . time();
        $phanHoi->id_khach_hang = $this->id;
        $phanHoi->noi_dung = $noi_dung;
        return $phanHoi->TiepNhanPhanHoi();
    }

    public function taoMoi() {
        $query = "INSERT INTO " . $this->table_name . " (id_taikhoan, ho_ten, so_dien_thoai, dia_chi, email)\n                  VALUES (:id_taikhoan, :ho_ten, :so_dien_thoai, :dia_chi, :email)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_taikhoan", $this->id_taikhoan);
        $stmt->bindParam(":ho_ten", $this->ho_ten);
        $stmt->bindParam(":so_dien_thoai", $this->so_dien_thoai);
        $stmt->bindParam(":dia_chi", $this->dia_chi);
        $stmt->bindParam(":email", $this->email);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function docTatCa() {
        $query = "SELECT kh.*, tk.ten_dang_nhap, tk.email as account_email 
                  FROM " . $this->table_name . " kh
                  LEFT JOIN nguoidung tk ON kh.id_taikhoan = tk.id
                  ORDER BY kh.ho_ten";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function xoa() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>

