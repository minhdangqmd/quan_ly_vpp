<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Quyen.php';

class NguoiDung {
    private $conn;
    private $table_name = "taikhoan";
    
    public $id;
    public $ten_dang_nhap;
    public $mat_khau;
    public $ho_ten;
    public $email;
    public $so_dien_thoai;
    public $id_vai_tro;
    public $trang_thai;
    public $ngay_tao;
    
    public $quyen; // Quyen object

    public function __construct($db) {
        $this->conn = $db;
    }

    public function DangNhap() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE ten_dang_nhap = :ten_dang_nhap AND trang_thai = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ten_dang_nhap", $this->ten_dang_nhap);
        $stmt->execute();
        
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // if($row && password_verify($this->mat_khau, $row['mat_khau'])) {
    if($row && $this->mat_khau === $row['mat_khau']) { // So sánh mật khẩu thuần
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->id_vai_tro = $row['id_vai_tro'];
            $this->trang_thai = $row['trang_thai'];
            
            // Load Quyen
            $quyen = new Quyen($this->conn);
            $quyen->id = $this->id_vai_tro;
            if($quyen->docTheoId()) {
                $this->quyen = $quyen;
            }
            
            return true;
        }
        return false;
    }

    public function DangXuat() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        return true;
    }

    public function CapNhatThongTin() {
        $query = "UPDATE " . $this->table_name . " 
                  SET email = :email, mat_khau = :mat_khau 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        //$hashed_password = password_hash($this->mat_khau, PASSWORD_DEFAULT);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":mat_khau", $this->mat_khau);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function KiemTraQuyen($ten_quyen) {
        if($this->quyen && $this->quyen->ten_vai_tro == $ten_quyen) {
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
            $this->ten_dang_nhap = $row['ten_dang_nhap'];
            $this->email = $row['email'];
            $this->id_vai_tro = $row['id_vai_tro'];
            $this->trang_thai = $row['trang_thai'];
            $this->ngay_tao = $row['ngay_tao'];
            
            // Load Quyen
            $quyen = new Quyen($this->conn);
            $quyen->id = $this->id_vai_tro;
            if($quyen->docTheoId()) {
                $this->quyen = $quyen;
            }
            
            return true;
        }
        return false;
    }

    public function kiemTraTonTai() {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE ten_dang_nhap = :ten_dang_nhap";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ten_dang_nhap", $this->ten_dang_nhap);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function kiemTraEmailTonTai() {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function taoMoi() {
        // Kiểm tra tên đăng nhập đã tồn tại
        if ($this->kiemTraTonTai()) {
            return false; // Tên đăng nhập đã tồn tại
        }

        // Kiểm tra email đã tồn tại
        if ($this->kiemTraEmailTonTai()) {
            return false; // Email đã tồn tại
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  (ten_dang_nhap, mat_khau, email, id_vai_tro) 
                  VALUES (:ten_dang_nhap, :mat_khau, :email, :id_vai_tro)";
        $stmt = $this->conn->prepare($query);
        
        //k\$hashed_password = password_hash($this->mat_khau, PASSWORD_DEFAULT);
        $stmt->bindParam(":ten_dang_nhap", $this->ten_dang_nhap);
        $stmt->bindParam(":mat_khau", $this->mat_khau);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":id_vai_tro", $this->id_vai_tro);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }
}
?>

