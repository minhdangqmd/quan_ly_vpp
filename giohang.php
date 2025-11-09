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

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Cập nhật giỏ hàng thành công!</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <h2>Giỏ hàng của tôi</h2>
        <?php if ($cartItems && $cartItems->rowCount() > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $cartItems->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($item['hinh_anh']): ?>
                                        <img src="/<?php echo htmlspecialchars($item['hinh_anh']); ?>" 
                                             class="me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($item['ten_san_pham']); ?></span>
                                </div>
                            </td>
                            <td><?php echo number_format($item['gia_ban'], 0, ',', '.'); ?> đ</td>
                            <td>
                                <form method="POST" action="<?php echo getBaseUrl(); ?>/giohang.php?action=update" class="d-inline">
                                    <input type="hidden" name="id_san_pham" value="<?php echo $item['id_san_pham']; ?>">
                                    <input type="number" name="so_luong" value="<?php echo $item['so_luong']; ?>" 
                                           min="1" class="form-control form-control-sm" style="width: 80px;" 
                                           onchange="this.form.submit()">
                                </form>
                            </td>
                            <td>
                                <?php 
                                $thanhTien = $item['so_luong'] * $item['gia_ban'];
                                echo number_format($thanhTien, 0, ',', '.'); 
                                ?> đ
                            </td>
                            <td>
                                <form method="POST" action="<?php echo getBaseUrl(); ?>/giohang.php?action=remove" class="d-inline">
                                    <input type="hidden" name="id_san_pham" value="<?php echo $item['id_san_pham']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">Giỏ hàng của bạn đang trống.</div>
            <a href="<?php echo getBaseUrl(); ?>/sanpham.php" class="btn btn-primary">Tiếp tục mua sắm</a>
        <?php endif; ?>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Tổng kết</h5>
            </div>
            <div class="card-body">
                <p class="d-flex justify-content-between">
                    <span>Tổng tiền:</span>
                    <strong class="text-danger"><?php echo number_format($tongTien, 0, ',', '.'); ?> đ</strong>
                </p>
                <?php if ($cartItems && $cartItems->rowCount() > 0): ?>
                    <a href="<?php echo getBaseUrl(); ?>/checkout.php" class="btn btn-primary w-100">Thanh toán</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/views/layout/footer.php'; ?>

