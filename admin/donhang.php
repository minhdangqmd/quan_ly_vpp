<?php
require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/DonHang.php';

$pageTitle = 'Quản lý đơn hàng';
requireRole('Admin');

$database = new Database();
$conn = $database->getConnection();

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if ($action == 'update_status' && $_SERVER['REQUEST_METHOD'] == 'POST' && $id) {
    $donHang = new DonHang($conn);
    $donHang->id = $id;
    $trangThai = $_POST['trang_thai'] ?? '';
    if ($donHang->CapNhatTrangThaiDonHang($trangThai)) {
        $baseUrl = getBaseUrl();
        header("Location: " . $baseUrl . "/admin/donhang.php?success=1");
        exit();
    }
}

if ($id) {
    $donHang = new DonHang($conn);
    $donHang->id = $id;
    $donHang->TraCuuTrangThai();
} else {
    $donHang = new DonHang($conn);
    $donHangs = $donHang->docTatCa();
}

include __DIR__ . '/../views/layout/header.php';
?>

<div class="main-content">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i> Cập nhật thành công!
        </div>
    <?php endif; ?>

    <?php if ($id && $donHang): ?>
        <!-- Order Detail -->
        <div class="admin-order-detail">
            <div class="admin-header">
                <div>
                    <a href="<?php echo getBaseUrl(); ?>/admin/donhang.php" class="back-link">
                        <i class="fa-solid fa-arrow-left"></i> Quay lại
                    </a>
                    <h2>Chi tiết đơn hàng: <?php echo htmlspecialchars($donHang->id); ?></h2>
                </div>
            </div>
            
            <div class="order-detail-grid">
                <div class="order-status-card">
                    <h3><i class="fa-solid fa-gear"></i> Quản lý trạng thái</h3>
                    <form method="POST" action="<?php echo getBaseUrl(); ?>/admin/donhang.php?action=update_status&id=<?php echo $donHang->id; ?>">
                        <div class="form-group">
                            <label for="trang_thai">Trạng thái đơn hàng</label>
                            <select id="trang_thai" name="trang_thai" onchange="this.form.submit()">
                                <option value="Đang xử lý" <?php echo $donHang->trang_thai_don_hang == 'Đang xử lý' ? 'selected' : ''; ?>>Đang xử lý</option>
                                <option value="Đã xác nhận" <?php echo $donHang->trang_thai_don_hang == 'Đã xác nhận' ? 'selected' : ''; ?>>Đã xác nhận</option>
                                <option value="Đang giao" <?php echo $donHang->trang_thai_don_hang == 'Đang giao' ? 'selected' : ''; ?>>Đang giao</option>
                                <option value="Đã giao" <?php echo $donHang->trang_thai_don_hang == 'Đã giao' ? 'selected' : ''; ?>>Đã giao</option>
                                <option value="Đã hủy" <?php echo $donHang->trang_thai_don_hang == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                            </select>
                        </div>
                    </form>
                    
                    <div class="order-meta">
                        <div class="meta-item">
                            <i class="fa-regular fa-clock"></i>
                            <div>
                                <strong>Ngày đặt</strong>
                                <p><?php echo date('d/m/Y H:i', strtotime($donHang->ngay_dat)); ?></p>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <div>
                                <strong>Địa chỉ giao hàng</strong>
                                <p><?php echo htmlspecialchars($donHang->dia_chi_giao); ?></p>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-phone"></i>
                            <div>
                                <strong>Số điện thoại</strong>
                                <p><?php echo htmlspecialchars($donHang->sdt_nhan); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="order-products-card">
                    <h3><i class="fa-solid fa-box"></i> Sản phẩm</h3>
                    <div class="admin-order-products">
                        <?php 
                        $details = $donHang->docChiTiet();
                        while ($item = $details->fetch(PDO::FETCH_ASSOC)): 
                        ?>
                            <div class="admin-order-product-item">
                                <div class="product-name"><?php echo htmlspecialchars($item['ten_san_pham']); ?></div>
                                <div class="product-details">
                                    <span>SL: <?php echo $item['so_luong']; ?></span>
                                    <span>×</span>
                                    <span><?php echo number_format($item['don_gia'], 0, ',', '.'); ?> đ</span>
                                    <span>=</span>
                                    <strong><?php echo number_format($item['thanh_tien'], 0, ',', '.'); ?> đ</strong>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="order-total">
                        <span>Tổng cộng:</span>
                        <span class="total-amount"><?php echo number_format($donHang->tong_tien, 0, ',', '.'); ?> đ</span>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Order List -->
        <div class="admin-header">
            <h2><i class="fa-solid fa-clipboard-list"></i> Quản lý đơn hàng</h2>
        </div>
        
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thanh toán</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($donHangs): ?>
                        <?php while ($dh = $donHangs->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($dh['id']); ?></strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($dh['ngay_dat'])); ?></td>
                                <td>
                                    <?php
                                    if ($dh['id_khach_hang']) {
                                        $query = "SELECT ho_ten FROM khachhang WHERE id = :id";
                                        $stmt = $conn->prepare($query);
                                        $stmt->bindParam(":id", $dh['id_khach_hang']);
                                        $stmt->execute();
                                        $kh = $stmt->fetch(PDO::FETCH_ASSOC);
                                        echo $kh ? htmlspecialchars($kh['ho_ten']) : '-';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td class="price"><?php echo number_format($dh['tong_tien'], 0, ',', '.'); ?> đ</td>
                                <td>
                                    <span class="status-badge status-<?php echo str_replace(' ', '-', strtolower($dh['trang_thai'])); ?>">
                                        <?php echo htmlspecialchars($dh['trang_thai']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="payment-badge">
                                        <?php echo htmlspecialchars($dh['trang_thai_thanh_toan']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo getBaseUrl(); ?>/admin/donhang.php?id=<?php echo $dh['id']; ?>" class="btn-view">
                                        <i class="fa-solid fa-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="no-data">
                                <i class="fa-solid fa-inbox"></i>
                                <p>Chưa có đơn hàng nào</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>

