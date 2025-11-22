<?php
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$type = $_GET['type'] ?? '';


if (!$type) {
    die("Thiếu tham số type");
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=baocao_$type.xls");
header("Pragma: no-cache");
header("Expires: 0");
echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>";

switch ($type) {

    case 'doanhthu':
        $sql = "SELECT dm.ten_danh_muc,
                       SUM(sp.gia_ban * sp.so_luong_ton) AS tong_gia_tri_ton
                FROM sanpham sp
                LEFT JOIN danhmuc dm ON sp.id_danh_muc = dm.id
                GROUP BY dm.ten_danh_muc";
        break;

    case 'tonkho':
        $sql = "SELECT sp.id, sp.ten_san_pham, dm.ten_danh_muc,
                       ncc.ten_nha_cung_cap, sp.so_luong_ton
                FROM sanpham sp
                LEFT JOIN danhmuc dm ON sp.id_danh_muc = dm.id
                LEFT JOIN nhacungcap ncc ON sp.id_nha_cung_cap = ncc.id
                ORDER BY sp.so_luong_ton ASC";
        break;

    case 'donhang':
        $sql = "SELECT id, id_khach_hang, ngay_dat, tong_tien, trang_thai,
                       dia_chi_giao, sdt_nhan, id_hinh_thuc_tt
                FROM donhang
                ORDER BY ngay_dat DESC";
        break;

    case 'phanhoi':
        $sql = "SELECT ph.id, kh.ten_khach_hang, ph.noi_dung, ph.ngay_gui
                FROM phanhoi ph
                JOIN khachhang kh ON ph.id_khach_hang = kh.id
                ORDER BY ph.ngay_gui DESC";
        break;

    case 'khuyenmai':
        $sql = "SELECT id, tieu_de, noi_dung, hinh_anh, ngay_dang,
                       id_nhan_vien, id_loai_tin
                FROM tintuc
                ORDER BY ngay_dang DESC";
        break;

    default:
        die("Loại báo cáo không hợp lệ.");
}

$data = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if (empty($data)) {
    die("Không có dữ liệu.");
}

echo "<table border='1'>";
echo "<tr>";
foreach (array_keys($data[0]) as $col) {
    echo "<th>" . htmlspecialchars($col) . "</th>";
}
echo "</tr>";

foreach ($data as $row) {
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>" . htmlspecialchars($value) . "</td>";
    }
    echo "</tr>";
}


echo "</table>";
