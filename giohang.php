<?php
require_once __DIR__ . '/utils/session.php';
require_once __DIR__ . '/controllers/GioHangController.php';

$pageTitle = 'Giỏ hàng';
requireLogin();

$gioHangController = new GioHangController();

// Handle actions
$action = $_GET['action'] ?? '';
if ($action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $gioHangController->add();
}
if ($action == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $gioHangController->update();
}
if ($action == 'remove' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $gioHangController->remove();
}

$gioHang = $gioHangController->index();
$cartItems = null;
$tongTien = 0;

if ($gioHang) {
    $cartItems = $gioHang->docChiTiet();
    $tongTien = $gioHang->TinhTongTien();
}

include __DIR__ . '/views/layout/header.php';
?>

<div class="main-content">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i> Cập nhật giỏ hàng thành công!
        </div>
    <?php endif; ?>

    <div class="cart-container">
        <div class="cart-main">
            <div class="cart-header">
                <h2><i class="fa-solid fa-cart-shopping"></i> Giỏ hàng của tôi</h2>
            </div>
            
            <?php if ($cartItems && $cartItems->rowCount() > 0): ?>
                <div class="cart-items">
                    <?php while ($item = $cartItems->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <?php if ($item['hinh_anh']): ?>
                                    <img src="<?php echo getBaseUrl(); ?>/<?php echo htmlspecialchars($item['hinh_anh']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['ten_san_pham']); ?>">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="cart-item-info">
                                <h3><?php echo htmlspecialchars($item['ten_san_pham']); ?></h3>
                                <p class="price"><?php echo number_format($item['gia_ban'], 0, ',', '.'); ?> đ</p>
                            </div>
                            <div class="cart-item-quantity">
                                <form method="POST" action="<?php echo getBaseUrl(); ?>/giohang.php?action=update">
                                    <input type="hidden" name="id_san_pham" value="<?php echo $item['id_san_pham']; ?>">
                                    <div class="quantity-control">
                                        <button type="button" class="qty-btn" onclick="decreaseQty(this)">
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                        <input type="number" name="so_luong" value="<?php echo $item['so_luong']; ?>" 
                                               min="1" class="qty-input" onchange="this.form.submit()">
                                        <button type="button" class="qty-btn" onclick="increaseQty(this)">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="cart-item-total">
                                <p class="total-price">
                                    <?php 
                                    $thanhTien = $item['so_luong'] * $item['gia_ban'];
                                    echo number_format($thanhTien, 0, ',', '.'); 
                                    ?> đ
                                </p>
                            </div>
                            <div class="cart-item-remove">
                                <form method="POST" action="<?php echo getBaseUrl(); ?>/giohang.php?action=remove">
                                    <input type="hidden" name="id_san_pham" value="<?php echo $item['id_san_pham']; ?>">
                                    <button type="submit" class="remove-btn" onclick="return confirm('Xóa sản phẩm này?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="cart-empty">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <p>Giỏ hàng của bạn đang trống</p>
                    <a href="<?php echo getBaseUrl(); ?>/sanpham.php" class="btn">
                        <i class="fa-solid fa-arrow-left"></i> Tiếp tục mua sắm
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($cartItems && $cartItems->rowCount() > 0): ?>
            <div class="cart-summary">
                <h3>Tổng kết đơn hàng</h3>
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
                <a href="<?php echo getBaseUrl(); ?>/thanhtoan.php" class="btn checkout-btn">
                    <i class="fa-solid fa-credit-card"></i> Thanh toán
                </a>
                <a href="<?php echo getBaseUrl(); ?>/sanpham.php" class="btn continue-btn">
                    <i class="fa-solid fa-arrow-left"></i> Tiếp tục mua sắm
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function decreaseQty(btn) {
    const input = btn.parentElement.querySelector('.qty-input');
    if (input.value > 1) {
        input.value = parseInt(input.value) - 1;
        input.form.submit();
    }
}

function increaseQty(btn) {
    const input = btn.parentElement.querySelector('.qty-input');
    input.value = parseInt(input.value) + 1;
    input.form.submit();
}
</script>

<?php include __DIR__ . '/views/layout/footer.php'; ?>

