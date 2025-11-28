<?php
require_once __DIR__ . '/utils/session.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/GioHangController.php';
require_once __DIR__ . '/controllers/DonHangController.php';

$pageTitle = 'Thanh toán';
requireLogin();

$database = new Database();
$conn = $database->getConnection();

$gioHangController = new GioHangController();
$gioHang = $gioHangController->index();

if (!$gioHang) {
    $baseUrl = getBaseUrl();
    header("Location: " . $baseUrl . "/giohang.php");
    exit();
}

$cartItems = $gioHang->docChiTiet();
$tongTien = $gioHang->TinhTongTien();

// Get payment methods
$query = "SELECT * FROM hinhthucthanhtoan";
$stmt = $conn->prepare($query);
$stmt->execute();
$paymentMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current user info
$user = getCurrentUser();
require_once __DIR__ . '/models/KhachHang.php';
$khachHang = new KhachHang($conn);
$khachHang->id_taikhoan = $user['id'];
$query = "SELECT * FROM khachhang WHERE id_taikhoan = :id_taikhoan LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(":id_taikhoan", $user['id']);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle order creation
$donHangController = new DonHangController();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $donHangController->create();
}

include __DIR__ . '/views/layout/header.php';
?>

<div class="checkout-container">
    <div class="checkout-header">
        <h2><i class="fa-solid fa-credit-card"></i> Thanh toán đơn hàng</h2>
        <p class="checkout-subtitle">Vui lòng điền thông tin để hoàn tất đơn hàng</p>
    </div>

    <div class="checkout-wrapper">
        <div class="checkout-main">
            <form method="POST" class="checkout-form">
                <!-- Thông tin giao hàng -->
                <div class="checkout-section">
                    <div class="section-header">
                        <i class="fa-solid fa-truck"></i>
                        <h3>Thông tin giao hàng</h3>
                    </div>
                    <div class="form-group">
                        <label for="dia_chi_giao" class="form-label">
                            <i class="fa-solid fa-location-dot"></i> Địa chỉ giao hàng <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="dia_chi_giao" name="dia_chi_giao" 
                               value="<?php echo htmlspecialchars($customer['dia_chi'] ?? ''); ?>" 
                               placeholder="Nhập địa chỉ giao hàng" required>
                    </div>
                    <div class="form-group">
                        <label for="sdt_nhan" class="form-label">
                            <i class="fa-solid fa-phone"></i> Số điện thoại nhận hàng <span class="required">*</span>
                        </label>
                        <input type="tel" class="form-control" id="sdt_nhan" name="sdt_nhan" 
                               value="<?php echo htmlspecialchars($customer['so_dien_thoai'] ?? ''); ?>" 
                               placeholder="Nhập số điện thoại" required>
                    </div>
                </div>

                <!-- Phương thức thanh toán -->
                <div class="checkout-section">
                    <div class="section-header">
                        <i class="fa-solid fa-wallet"></i>
                        <h3>Phương thức thanh toán</h3>
                    </div>
                    <div class="form-group">
                        <label for="id_hinh_thuc_tt" class="form-label">
                            <i class="fa-solid fa-credit-card"></i> Chọn phương thức thanh toán <span class="required">*</span>
                        </label>
                        <select class="form-control" id="id_hinh_thuc_tt" name="id_hinh_thuc_tt" required>
                            <option value="">-- Chọn phương thức thanh toán --</option>
                            <?php foreach ($paymentMethods as $method): ?>
                                <option value="<?php echo $method['id']; ?>">
                                    <?php echo htmlspecialchars($method['ten_hinh_thuc']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Ghi chú -->
                <div class="checkout-section">
                    <div class="section-header">
                        <i class="fa-solid fa-note-sticky"></i>
                        <h3>Ghi chú đơn hàng</h3>
                    </div>
                    <div class="form-group">
                        <label for="ghi_chu" class="form-label">
                            <i class="fa-solid fa-comment"></i> Ghi chú (tùy chọn)
                        </label>
                        <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="4" 
                                  placeholder="Nhập ghi chú cho đơn hàng (nếu có)"></textarea>
                    </div>
                </div>

                <!-- Danh sách sản phẩm -->
                <div class="checkout-section">
                    <div class="section-header">
                        <i class="fa-solid fa-box"></i>
                        <h3>Sản phẩm trong đơn hàng</h3>
                    </div>
                    <div class="checkout-products">
                        <?php 
                        $cartItems->execute();
                        while ($item = $cartItems->fetch(PDO::FETCH_ASSOC)): 
                            $thanhTien = $item['so_luong'] * $item['gia_ban'];
                        ?>
                            <div class="checkout-product-item">
                                <div class="product-image">
                                    <?php if ($item['hinh_anh']): ?>
                                        <img src="<?php echo getBaseUrl(); ?>/<?php echo htmlspecialchars($item['hinh_anh']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['ten_san_pham']); ?>">
                                    <?php else: ?>
                                        <div class="no-image">
                                            <i class="fa-solid fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h4><?php echo htmlspecialchars($item['ten_san_pham']); ?></h4>
                                    <p class="product-meta">
                                        <span>Số lượng: <strong><?php echo $item['so_luong']; ?></strong></span>
                                        <span>Giá: <strong><?php echo number_format($item['gia_ban'], 0, ',', '.'); ?> đ</strong></span>
                                    </p>
                                </div>
                                <div class="product-total">
                                    <span class="total-price"><?php echo number_format($thanhTien, 0, ',', '.'); ?> đ</span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="checkout-actions">
                    <a href="<?php echo getBaseUrl(); ?>/giohang.php" class="btn-back">
                        <i class="fa-solid fa-arrow-left"></i> Quay lại giỏ hàng
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-check-circle"></i> Xác nhận đặt hàng
                    </button>
                </div>
            </form>
        </div>

        <!-- Tổng kết -->
        <div class="checkout-summary">
            <div class="summary-card">
                <div class="summary-header">
                    <h3><i class="fa-solid fa-receipt"></i> Tổng kết đơn hàng</h3>
                </div>
                <div class="summary-body">
                    <div class="summary-row">
                        <span>Tạm tính:</span>
                        <span><?php echo number_format($tongTien, 0, ',', '.'); ?> đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển:</span>
                        <span class="text-success">Miễn phí</span>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-row total">
                        <span>Tổng cộng:</span>
                        <span class="total-price"><?php echo number_format($tongTien, 0, ',', '.'); ?> đ</span>
                    </div>
                </div>
                <div class="summary-footer">
                    <p class="summary-note">
                        <i class="fa-solid fa-shield-halved"></i> Đơn hàng của bạn được bảo mật và mã hóa
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/views/layout/footer.php'; ?>

