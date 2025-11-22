<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/session.php';

$pageTitle = 'Báo cáo thống kê';
requireRole('Admin');

// Kết nối DB
$database = new Database();
$conn = $database->getConnection();

// Nhận loại báo cáo
$selectedType = $_GET['type'] ?? '';
$selectedDanhMuc = $_GET['id_danh_muc'] ?? '';
$selectedNCC = $_GET['id_nha_cung_cap'] ?? '';
$selectedDanhMuc = $_GET['id_danh_muc'] ?? '';
$selectedNhaCungCap = $_GET['id_nha_cung_cap'] ?? '';

// Chuẩn bị biến dữ liệu
$data = [];
$title = "";


// Xử lý theo loại báo cáo
switch ($selectedType) {
    case 'doanhthu':
    $title = "Báo cáo doanh thu (tạm tính)";
    $sql = "SELECT 
                dm.ten_danh_muc,
                SUM(sp.gia_ban * sp.so_luong_ton) AS tong_gia_tri_ton
            FROM sanpham sp
            LEFT JOIN danhmuc dm ON sp.id_danh_muc = dm.id
            GROUP BY dm.ten_danh_muc";
    $data = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    break;


    case 'tonkho':
        $title = "Báo cáo hàng tồn kho";
        $sql = "SELECT sp.id, sp.ten_san_pham, dm.ten_danh_muc, ncc.ten_nha_cung_cap, sp.so_luong_ton 
                FROM sanpham sp
                LEFT JOIN danhmuc dm ON sp.id_danh_muc = dm.id
                LEFT JOIN nhacungcap ncc ON sp.id_nha_cung_cap = ncc.id
                ORDER BY sp.so_luong_ton ASC";
        $data = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'donhang':
        $title = "Báo cáo đơn hàng";
        $sql = "SELECT id, id_khach_hang, ngay_dat, tong_tien, trang_thai, dia_chi_giao, sdt_nhan,id_hinh_thuc_tt    
                FROM donhang
                ORDER BY ngay_dat DESC";
        $data = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'phanhoi':
        $title = "Báo cáo phản hồi khách hàng";
        $sql = "SELECT ph.id, kh.ten_khach_hang, ph.noi_dung, ph.ngay_gui 
                FROM phanhoi ph
                JOIN khachhang kh ON ph.id_khach_hang = kh.id
                ORDER BY ph.ngay_gui DESC";
        $data = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'khuyenmai':
        $title = "Báo cáo chương trình khuyến mãi";
        $sql = "SELECT id, tieu_de, noi_dung, hinh_anh, ngay_dang, id_nhan_vien, id_loai_tin 
                FROM tintuc
                ORDER BY ngay_dang DESC";
        $data = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        break;

    default:
        $title = "Vui lòng chọn loại báo cáo để xem thống kê.";
        break;
}

include __DIR__ . '/../views/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Báo cáo thống kê</h2>
</div>

<!-- Form chọn loại báo cáo -->
<div class="mb-4">
    <form method="GET" class="d-flex gap-3 flex-wrap align-items-end bg-light p-3 rounded shadow-sm">
        <div>
            <label for="type" class="form-label fw-semibold">Loại báo cáo:</label>
            <select class="form-select" name="type" id="type" required>
                <option value="">-- Chọn loại báo cáo --</option>
                <option value="doanhthu" <?= ($selectedType == 'doanhthu') ? 'selected' : '' ?>>Báo cáo doanh thu</option>
                <option value="tonkho" <?= ($selectedType == 'tonkho') ? 'selected' : '' ?>>Báo cáo hàng tồn kho</option>
                <option value="donhang" <?= ($selectedType == 'donhang') ? 'selected' : '' ?>>Báo cáo đơn hàng</option>
                <option value="phanhoi" <?= ($selectedType == 'phanhoi') ? 'selected' : '' ?>>Báo cáo phản hồi khách hàng</option>
                <option value="khuyenmai" <?= ($selectedType == 'khuyenmai') ? 'selected' : '' ?>>Báo cáo khuyến mãi</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-bar-chart"></i> Xem báo cáo
            </button>
        </div>
    </form>
</div>

<!-- Hiển thị kết quả báo cáo -->
<div class="container my-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h4 class="card-title mb-4 fw-bold"><?= htmlspecialchars($title) ?></h4>

            <?php if ($selectedType && !empty($data)): ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle custom-table">
                        <thead class="table-header">
                            <tr>
                                <?php foreach(array_keys($data[0]) as $col): ?>
                                    <th><?= htmlspecialchars(ucwords(str_replace('_', ' ', $col))) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach($data as $row): ?>
                                <tr>
                                    <?php foreach($row as $value): ?>
                                        <td><?= htmlspecialchars($value) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <style>
                    .custom-table {
                        border-radius: 12px;
                        overflow: hidden;
                        background: #fff;
                    }

                    .table-header {
                        background: linear-gradient(45deg, #0d6efd, #3b8cff);
                        color: #fff;
                    }

                    .custom-table th, 
                    .custom-table td {
                        padding: 14px 16px !important;
                        vertical-align: middle;
                        font-size: 15px;
                    }

                    .custom-table tbody tr:hover {
                        background-color: #f1f6ff;
                        transition: 0.2s;
                    }
                </style>

            <?php elseif ($selectedType): ?>
                <div class="alert alert-warning">Không có dữ liệu cho báo cáo này.</div>

            <?php else: ?>
                <div class="alert alert-info">Hãy chọn loại báo cáo để xem thống kê chi tiết.</div>
            <?php endif; ?>
            <a href="xuatbaocaothongke.php?type=<?= $selectedType ?>"
   class="btn btn-success">
   Xuất báo cáo
</a>


        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>
