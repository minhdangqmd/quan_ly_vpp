<?php
require_once __DIR__ . '/../config/database.php';

class KhachHang
{
    public $id;
    public $id_taikhoan;
    public $ho_ten;
    public $sdt;
    public $dia_chi;

    // Lấy khách hàng theo tài khoản
    public static function layTheoTaiKhoan($idTK)
    {
        $db = Database::getConnection();
        $stm = $db->prepare("SELECT * FROM khachhang WHERE id_taikhoan = ? LIMIT 1");
        $stm->execute([$idTK]);

        $stm->setFetchMode(PDO::FETCH_CLASS, 'KhachHang');
        return $stm->fetch();
    }

    // Tạo khách hàng mới
    public static function taoMoi($idTK)
    {
        $db = Database::getConnection();
        $stm = $db->prepare("INSERT INTO khachhang (id_taikhoan, ho_ten) VALUES (?, 'Khách hàng mới')");
        $stm->execute([$idTK]);

        return $db->lastInsertId();
    }

    // Lấy khách hàng theo ID
    public static function layTheoId($id)
    {
        $db = Database::getConnection();
        $stm = $db->prepare("SELECT * FROM khachhang WHERE id = ? LIMIT 1");
        $stm->execute([$id]);

        $stm->setFetchMode(PDO::FETCH_CLASS, 'KhachHang');
        return $stm->fetch();
    }
}
