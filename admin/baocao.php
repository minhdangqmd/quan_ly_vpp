<?php
require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../controllers/BaoCaoController.php';

$pageTitle = 'Báo cáo thống kê';
requireRole('Admin');

$controller = new BaoCaoController();
$reportType = $_GET['type'] ?? 'doanhthu';
$action = $_GET['action'] ?? 'view';

// Handle Excel export
if ($action == 'export') {
    switch ($reportType) {
        case 'doanhthu':
            $tuNgay = $_GET['tu_ngay'] ?? date('Y-m-01');
            $denNgay = $_GET['den_ngay'] ?? date('Y-m-d');
            $data = $controller->baoCaoDoanhThu($tuNgay, $denNgay);
            $headers = ['Ngày', 'Số đơn hàng', 'Tổng doanh thu'];
            $filename = 'BaoCaoDoanhThu_' . date('YmdHis');
            $controller->exportToExcel('doanhthu', $data, $headers, $filename);
            break;
            
        case 'tonkho':
            $data = $controller->baoCaoTonKho();
            $headers = ['Mã SP', 'Tên sản phẩm', 'Số lượng tồn', 'Danh mục', 'Đơn vị tính'];
            $filename = 'BaoCaoTonKho_' . date('YmdHis');
            $controller->exportToExcel('tonkho', $data, $headers, $filename);
            break;
            
        case 'donhang':
            $trangThai = $_GET['trang_thai'] ?? null;
            $data = $controller->baoCaoDonHang($trangThai);
            $headers = ['Mã đơn', 'Ngày đặt', 'Tổng tiền', 'Trạng thái', 'TT thanh toán', 'Khách hàng', 'Số điện thoại'];
            $filename = 'BaoCaoDonHang_' . date('YmdHis');
            $controller->exportToExcel('donhang', $data, $headers, $filename);
            break;
            
        case 'sanphambanchay':
            $data = $controller->baoCaoSanPhamBanChay(50);
            $headers = ['Mã SP', 'Tên sản phẩm', 'Giá bán', 'Tổng SL bán', 'Tổng doanh thu', 'Danh mục'];
            $filename = 'BaoCaoSanPhamBanChay_' . date('YmdHis');
            $controller->exportToExcel('sanphambanchay', $data, $headers, $filename);
            break;
            
        case 'khachhang':
            $data = $controller->thongKeKhachHang();
            $headers = ['Mã KH', 'Họ tên', 'Số điện thoại', 'Email', 'Tổng đơn hàng', 'Tổng chi tiêu'];
            $filename = 'ThongKeKhachHang_' . date('YmdHis');
            $controller->exportToExcel('khachhang', $data, $headers, $filename);
            break;
    }
}

// Get data for display
$reportData = null;
$tuNgay = $_GET['tu_ngay'] ?? date('Y-m-01');
$denNgay = $_GET['den_ngay'] ?? date('Y-m-d');
$trangThai = $_GET['trang_thai'] ?? null;

switch ($reportType) {
    case 'doanhthu':
        $reportData = $controller->baoCaoDoanhThu($tuNgay, $denNgay);
        break;
    case 'tonkho':
        $reportData = $controller->baoCaoTonKho();
        break;
    case 'donhang':
        $reportData = $controller->baoCaoDonHang($trangThai);
        break;
    case 'sanphambanchay':
        $reportData = $controller->baoCaoSanPhamBanChay(10);
        break;
    case 'khachhang':
        $reportData = $controller->thongKeKhachHang();
        break;
}

include __DIR__ . '/../views/layout/header.php';
?>

<div class="main-content">
    <div class="admin-header">
        <h2><i class="fa-solid fa-chart-line"></i> Báo cáo thống kê</h2>
    </div>
    
    <div class="report-tabs">
        <a href="<?php echo getBaseUrl(); ?>/admin/baocao.php?type=doanhthu" 
           class="report-tab <?php echo $reportType == 'doanhthu' ? 'active' : ''; ?>">
            <i class="fa-solid fa-money-bill-trend-up"></i> Doanh thu
        </a>
        <a href="<?php echo getBaseUrl(); ?>/admin/baocao.php?type=tonkho" 
           class="report-tab <?php echo $reportType == 'tonkho' ? 'active' : ''; ?>">
            <i class="fa-solid fa-boxes-stacked"></i> Tồn kho
        </a>
        <a href="<?php echo getBaseUrl(); ?>/admin/baocao.php?type=donhang" 
           class="report-tab <?php echo $reportType == 'donhang' ? 'active' : ''; ?>">
            <i class="fa-solid fa-clipboard-list"></i> Đơn hàng
        </a>
        <a href="<?php echo getBaseUrl(); ?>/admin/baocao.php?type=sanphambanchay" 
           class="report-tab <?php echo $reportType == 'sanphambanchay' ? 'active' : ''; ?>">
            <i class="fa-solid fa-fire"></i> Sản phẩm bán chạy
        </a>
        <a href="<?php echo getBaseUrl(); ?>/admin/baocao.php?type=khachhang" 
           class="report-tab <?php echo $reportType == 'khachhang' ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i> Khách hàng
        </a>
    </div>

    <?php if ($reportType == 'doanhthu'): ?>
        <!-- Revenue Report -->
        <div class="report-container">
            <div class="report-filters">
                <form method="GET" class="filter-form">
                    <input type="hidden" name="type" value="doanhthu">
                    <div class="form-group">
                        <label><i class="fa-solid fa-calendar"></i> Từ ngày:</label>
                        <input type="date" name="tu_ngay" value="<?php echo $tuNgay; ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fa-solid fa-calendar"></i> Đến ngày:</label>
                        <input type="date" name="den_ngay" value="<?php echo $denNgay; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-filter"></i> Lọc
                    </button>
                    <a href="<?php echo getBaseUrl(); ?>/admin/baocao.php?type=doanhthu&action=export&tu_ngay=<?php echo $tuNgay; ?>&den_ngay=<?php echo $denNgay; ?>" 
                       class="btn btn-success">
                        <i class="fa-solid fa-file-excel"></i> Xuất Excel
                    </a>
                </form>
            </div>
            
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Số đơn hàng</th>
                            <th>Tổng doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $tongDon = 0;
                        $tongDoanhThu = 0;
                        if ($reportData):
                            while ($row = $reportData->fetch(PDO::FETCH_ASSOC)):
                                $tongDon += $row['so_don'];
                                $tongDoanhThu += $row['tong_doanh_thu'];
                        ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($row['ngay'])); ?></td>
                                <td><strong><?php echo number_format($row['so_don']); ?></strong></td>
                                <td class="price"><?php echo number_format($row['tong_doanh_thu'], 0, ',', '.'); ?> đ</td>
                            </tr>
                        <?php
                            endwhile;
                        endif;
                        ?>
                        <tr class="total-row">
                            <td><strong>Tổng cộng</strong></td>
                            <td><strong><?php echo number_format($tongDon); ?></strong></td>
                            <td class="price"><strong><?php echo number_format($tongDoanhThu, 0, ',', '.'); ?> đ</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif ($reportType == 'tonkho'): ?>
        <!-- Inventory Report -->
        <div class="report-container">
            <div class="report-actions">
                <a href="<?php echo getBaseUrl(); ?>/admin/baocao.php?type=tonkho&action=export" 
                   class="btn btn-success">
                    <i class="fa-solid fa-file-excel"></i> Xuất Excel
                </a>
            </div>
            
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mã SP</th>
                            <th>Tên sản phẩm</th>
                            <th>Số lượng tồn</th>
                            <th>Danh mục</th>
                            <th>Đơn vị tính</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reportData): ?>
                            <?php while ($row = $reportData->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['id']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['ten_san_pham']); ?></td>
                                    <td>
                                        <span class="stock-badge <?php echo $row['so_luong_ton'] > 10 ? 'in-stock' : ($row['so_luong_ton'] > 0 ? 'low-stock' : 'out-stock'); ?>">
                                            <?php echo number_format($row['so_luong_ton']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['ten_danh_muc'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($row['ten_dvt'] ?? '-'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="no-data">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif ($reportType == 'donhang'): ?>
        <!-- Orders Report -->
        <div class="report-container">
            <div class="report-filters">
                <form method="GET" class="filter-form">
                    <input type="hidden" name="type" value="donhang">
                    <div class="form-group">
                        <label><i class="fa-solid fa-filter"></i> Trạng thái:</label>
                        <select name="trang_thai" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            <option value="Đang xử lý" <?php echo $trangThai == 'Đang xử lý' ? 'selected' : ''; ?>>Đang xử lý</option>
                            <option value="Đã xác nhận" <?php echo $trangThai == 'Đã xác nhận' ? 'selected' : ''; ?>>Đã xác nhận</option>
                            <option value="Đang giao" <?php echo $trangThai == 'Đang giao' ? 'selected' : ''; ?>>Đang giao</option>
                            <option value="Đã giao" <?php echo $trangThai == 'Đã giao' ? 'selected' : ''; ?>>Đã giao</option>
                            <option value="Đã hủy" <?php echo $trangThai == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                        </select>
                    </div>
                    <a href="<?php echo getBaseUrl(); ?>/admin/baocao.php?type=donhang&action=export<?php echo $trangThai ? '&trang_thai=' . urlencode($trangThai) : ''; ?>" 
                       class="btn btn-success">
                        <i class="fa-solid fa-file-excel"></i> Xuất Excel
                    </a>
                </form>
            </div>
            
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thanh toán</th>
                            <th>Khách hàng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reportData): ?>
                            <?php while ($row = $reportData->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['id']); ?></strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['ngay_dat'])); ?></td>
                                    <td class="price"><?php echo number_format($row['tong_tien'], 0, ',', '.'); ?> đ</td>
                                    <td>
                                        <span class="status-badge status-<?php echo str_replace(' ', '-', strtolower($row['trang_thai'])); ?>">
                                            <?php echo htmlspecialchars($row['trang_thai']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="payment-badge">
                                            <?php echo htmlspecialchars($row['trang_thai_thanh_toan']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['ho_ten'] ?? '-'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="no-data">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif ($reportType == 'sanphambanchay'): ?>
        <!-- Best Selling Products Report -->
        <div class="report-container">
            <div class="report-actions">
                <a href="<?php echo getBaseUrl(); ?>/admin/baocao.php?type=sanphambanchay&action=export" 
                   class="btn btn-success">
                    <i class="fa-solid fa-file-excel"></i> Xuất Excel
                </a>
            </div>
            
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Xếp hạng</th>
                            <th>Mã SP</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá bán</th>
                            <th>Tổng SL bán</th>
                            <th>Tổng doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rank = 1;
                        if ($reportData):
                            while ($row = $reportData->fetch(PDO::FETCH_ASSOC)):
                        ?>
                            <tr>
                                <td>
                                    <span class="rank-badge rank-<?php echo $rank <= 3 ? $rank : 'other'; ?>">
                                        #<?php echo $rank; ?>
                                    </span>
                                </td>
                                <td><strong><?php echo htmlspecialchars($row['id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['ten_san_pham']); ?></td>
                                <td><?php echo htmlspecialchars($row['ten_danh_muc'] ?? '-'); ?></td>
                                <td class="price"><?php echo number_format($row['gia_ban'], 0, ',', '.'); ?> đ</td>
                                <td><strong><?php echo number_format($row['tong_so_luong_ban']); ?></strong></td>
                                <td class="price"><strong><?php echo number_format($row['tong_doanh_thu'], 0, ',', '.'); ?> đ</strong></td>
                            </tr>
                        <?php
                                $rank++;
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="7" class="no-data">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif ($reportType == 'khachhang'): ?>
        <!-- Customer Statistics -->
        <div class="report-container">
            <div class="report-actions">
                <a href="<?php echo getBaseUrl(); ?>/admin/baocao.php?type=khachhang&action=export" 
                   class="btn btn-success">
                    <i class="fa-solid fa-file-excel"></i> Xuất Excel
                </a>
            </div>
            
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mã KH</th>
                            <th>Họ tên</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                            <th>Tổng đơn hàng</th>
                            <th>Tổng chi tiêu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reportData): ?>
                            <?php while ($row = $reportData->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['id']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['ho_ten']); ?></td>
                                    <td><?php echo htmlspecialchars($row['so_dien_thoai']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email'] ?? '-'); ?></td>
                                    <td><strong><?php echo number_format($row['tong_don_hang']); ?></strong></td>
                                    <td class="price"><strong><?php echo number_format($row['tong_chi_tieu'], 0, ',', '.'); ?> đ</strong></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="no-data">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.report-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 0;
}

.report-tab {
    padding: 12px 20px;
    background: #f5f5f5;
    border: none;
    border-radius: 8px 8px 0 0;
    text-decoration: none;
    color: #666;
    transition: all 0.3s;
}

.report-tab:hover {
    background: #e0e0e0;
    color: #333;
}

.report-tab.active {
    background: #fff;
    color: #2196F3;
    border-bottom: 3px solid #2196F3;
    font-weight: 600;
}

.report-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.report-filters, .report-actions {
    margin-bottom: 20px;
}

.filter-form {
    display: flex;
    gap: 15px;
    align-items: flex-end;
    flex-wrap: wrap;
}

.filter-form .form-group {
    margin-bottom: 0;
}

.filter-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.btn-success {
    background: #4CAF50;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-success:hover {
    background: #45a049;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}

.total-row {
    background: #f5f5f5;
    font-weight: bold;
}

.stock-badge.low-stock {
    background: #ff9800;
    color: white;
}

.rank-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-weight: bold;
}

.rank-badge.rank-1 {
    background: #FFD700;
    color: #000;
}

.rank-badge.rank-2 {
    background: #C0C0C0;
    color: #000;
}

.rank-badge.rank-3 {
    background: #CD7F32;
    color: #fff;
}

.rank-badge.rank-other {
    background: #e0e0e0;
    color: #666;
}

.count-badge {
    background: #2196F3;
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.9em;
}

.account-tag {
    background: #9C27B0;
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.9em;
}

.text-muted {
    color: #999;
}
</style>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>

