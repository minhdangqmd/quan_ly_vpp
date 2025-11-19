<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/DanhMucSanPham.php';
require_once __DIR__ . '/NhaCungCap.php';

class SanPham {
    private $conn;
    private $table_name = "sanpham";
    
    public $id;
    public $ten_san_pham;
    public $mo_ta;
    public $gia_ban;
    public $so_luong_ton;
    public $id_danh_muc;
    public $id_nha_cung_cap;
    public $id_dvt;
    public $hinh_anh;
    public $han_su_dung;
    
    public $danhMuc; // DanhMucSanPham object
    public $nhaCungCap; // NhaCungCap object

    public function __construct($db) {
        $this->conn = $db;
    }

    public function CapNhatThongTin() {
        $query = "UPDATE " . $this->table_name . " 
                  SET ten_san_pham = :ten_san_pham, 
                      mo_ta = :mo_ta, 
                      gia_ban = :gia_ban, 
                      id_danh_muc = :id_danh_muc, 
                      id_nha_cung_cap = :id_nha_cung_cap,
                      id_dvt = :id_dvt,
                      hinh_anh = :hinh_anh,
                      han_su_dung = :han_su_dung
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ten_san_pham", $this->ten_san_pham);
        $stmt->bindParam(":mo_ta", $this->mo_ta);
        $stmt->bindParam(":gia_ban", $this->gia_ban);
        $stmt->bindParam(":id_danh_muc", $this->id_danh_muc);
        $stmt->bindParam(":id_nha_cung_cap", $this->id_nha_cung_cap);
        $stmt->bindParam(":id_dvt", $this->id_dvt);
        $stmt->bindParam(":hinh_anh", $this->hinh_anh);
        $stmt->bindParam(":han_su_dung", $this->han_su_dung);
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
            $this->ten_san_pham = $row['ten_san_pham'];
            $this->mo_ta = $row['mo_ta'];
            $this->gia_ban = $row['gia_ban'];
            $this->so_luong_ton = $row['so_luong_ton'];
            $this->id_danh_muc = $row['id_danh_muc'];
            $this->id_nha_cung_cap = $row['id_nha_cung_cap'];
            $this->id_dvt = $row['id_dvt'];
            $this->hinh_anh = $row['hinh_anh'];
            $this->han_su_dung = $row['han_su_dung'];
            
            // Load DanhMuc
            if($this->id_danh_muc) {
                $danhMuc = new DanhMucSanPham($this->conn);
                $danhMuc->id = $this->id_danh_muc;
                if($danhMuc->docTheoId()) {
                    $this->danhMuc = $danhMuc;
                }
            }
            
            // Load NhaCungCap
            if($this->id_nha_cung_cap) {
                $ncc = new NhaCungCap($this->conn);
                $ncc->id = $this->id_nha_cung_cap;
                if($ncc->TraCuuThongTin()) {
                    $this->nhaCungCap = $ncc;
                }
            }
            
            return true;
        }
        return false;
    }

    public function CapNhatSoLuongTon($soLuong) {
        $query = "UPDATE " . $this->table_name . " 
                  SET so_luong_ton = :so_luong_ton 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":so_luong_ton", $soLuong);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            $this->so_luong_ton = $soLuong;
            return true;
        }
        return false;
    }

    public function taoMoi() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id, ten_san_pham, mo_ta, gia_ban, so_luong_ton, 
                   id_danh_muc, id_nha_cung_cap, id_dvt, hinh_anh, han_su_dung) 
                  VALUES (:id, :ten_san_pham, :mo_ta, :gia_ban, :so_luong_ton, 
                          :id_danh_muc, :id_nha_cung_cap, :id_dvt, :hinh_anh, :han_su_dung)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":ten_san_pham", $this->ten_san_pham);
        $stmt->bindParam(":mo_ta", $this->mo_ta);
        $stmt->bindParam(":gia_ban", $this->gia_ban);
        $stmt->bindParam(":so_luong_ton", $this->so_luong_ton);
        $stmt->bindParam(":id_danh_muc", $this->id_danh_muc);
        $stmt->bindParam(":id_nha_cung_cap", $this->id_nha_cung_cap);
        $stmt->bindParam(":id_dvt", $this->id_dvt);
        $stmt->bindParam(":hinh_anh", $this->hinh_anh);
        $stmt->bindParam(":han_su_dung", $this->han_su_dung);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function docTatCa($limit = 100, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  ORDER BY ten_san_pham 
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function timKiem($keyword) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE ten_san_pham LIKE :keyword OR mo_ta LIKE :keyword 
                  ORDER BY ten_san_pham";
        $stmt = $this->conn->prepare($query);
        $keyword = "%{$keyword}%";
        $stmt->bindParam(":keyword", $keyword);
        $stmt->execute();
        return $stmt;
    }

    public function docTheoDanhMuc($id_danh_muc, $limit = 100, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id_danh_muc = :id_danh_muc 
                  ORDER BY ten_san_pham 
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_danh_muc", $id_danh_muc);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function timKiemTheoDanhMuc($keyword, $id_danh_muc) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE (ten_san_pham LIKE :keyword OR mo_ta LIKE :keyword) 
                  AND id_danh_muc = :id_danh_muc 
                  ORDER BY ten_san_pham";
        $stmt = $this->conn->prepare($query);
        $keyword = "%{$keyword}%";
        $stmt->bindParam(":keyword", $keyword);
        $stmt->bindParam(":id_danh_muc", $id_danh_muc);
        $stmt->execute();
        return $stmt;
    }
}
?>

