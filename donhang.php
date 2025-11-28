<?php
require_once __DIR__ . '/utils/session.php';
require_once __DIR__ . '/controllers/DonHangController.php';

$pageTitle = 'Đơn hàng của tôi';
requireLogin();

$controller = new DonHangController();

$id = $_GET['id'] ?? null;
if ($id) {
    $donHang = $controller->show($id);
} else {
    $donHangs = $controller->index();
}

include __DIR__ . '/views/layout/header.php';
?>

<div class="main-content">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i> Đặt hàng thành công!
        </div>
    <?php endif; ?>

    <?php if ($id && $donHang): ?>
        <!-- Order Detail -->
        <div class="order-detail-container">
            <div class="order-detail-header">
                <a href="<?php echo getBaseUrl(); ?>/donhang.php" class="back-link">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
                <h2>Chi tiết đơn hàng: <?php echo htmlspecialchars($donHang->id); ?></h2>
            </div>
            
            <div class="order-detail-content">
                <div class="order-info-card">
                    <h3>Thông tin đơn hàng</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">Trạng thái:</span>
                            <span class="status-badge status-<?php echo str_replace(' ', '-', strtolower($donHang->trang_thai_don_hang)); ?>">
                                <?php echo htmlspecialchars($donHang->trang_thai_don_hang); ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Thanh toán:</span>
                            <span class="payment-badge">
                                <?php echo htmlspecialchars($donHang->trang_thai_thanh_toan); ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Ngày đặt:</span>
                            <span><?php echo date('d/m/Y H:i', strtotime($donHang->ngay_dat)); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Địa chỉ:</span>
                            <span><?php echo htmlspecialchars($donHang->dia_chi_giao); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Số điện thoại:</span>
                            <span><?php echo htmlspecialchars($donHang->sdt_nhan); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="order-products-card">
                    <h3>Sản phẩm đã đặt</h3>
                    <div class="order-products">
                        <?php 
                        $details = $donHang->docChiTiet();
                        while ($item = $details->fetch(PDO::FETCH_ASSOC)): 
                        ?>
                            <div class="order-product-item">
                                <div class="product-name">
                                    <i class="fa-solid fa-box"></i>
                                    <?php echo htmlspecialchars($item['ten_san_pham']); ?>
                                </div>
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
        <div class="orders-container">
            <h2><i class="fa-solid fa-clipboard-list"></i> Đơn hàng của tôi</h2>
            
            <?php if ($donHangs && $donHangs->rowCount() > 0): ?>
                <div class="orders-list">
                    <?php while ($dh = $donHangs->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="order-card">
                            <div class="order-card-header">
                                <div class="order-id">
                                    <i class="fa-solid fa-receipt"></i>
                                    Đơn hàng: <strong><?php echo htmlspecialchars($dh['id']); ?></strong>
                                </div>
                                <div class="order-date">
                                    <i class="fa-regular fa-clock"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($dh['ngay_dat'])); ?>
                                </div>
                            </div>
                            <div class="order-card-body">
                                <div class="order-info">
                                    <div class="order-status">
                                        <span class="status-badge status-<?php echo str_replace(' ', '-', strtolower($dh['trang_thai_don_hang'])); ?>">
                                            <?php echo htmlspecialchars($dh['trang_thai_don_hang']); ?>
                                        </span>
                                        <span class="payment-badge">
                                            <?php echo htmlspecialchars($dh['trang_thai_thanh_toan']); ?>
                                        </span>
                                    </div>
                                    <div class="order-total">
                                        <span>Tổng tiền:</span>
                                        <strong class="total-amount"><?php echo number_format($dh['tong_tien'], 0, ',', '.'); ?> đ</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="order-card-footer">
                                <a href="<?php echo getBaseUrl(); ?>/donhang.php?id=<?php echo $dh['id']; ?>" class="btn view-btn">
                                    <i class="fa-solid fa-eye"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-orders">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <p>Bạn chưa có đơn hàng nào</p>
                    <a href="<?php echo getBaseUrl(); ?>/sanpham.php" class="btn">
                        <i class="fa-solid fa-shopping-bag"></i> Mua sắm ngay
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/views/layout/footer.php'; ?>

