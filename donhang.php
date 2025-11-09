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

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Đặt hàng thành công!</div>
<?php endif; ?>

<?php if ($id && $donHang): ?>
    <!-- Order Detail -->
    <div class="row">
        <div class="col-md-8">
            <h2>Chi tiết đơn hàng: <?php echo htmlspecialchars($donHang->id); ?></h2>
            <div class="card">
                <div class="card-body">
                    <p><strong>Trạng thái:</strong> 
                        <span class="badge bg-warning"><?php echo htmlspecialchars($donHang->trang_thai_don_hang); ?></span>
                    </p>
                    <p><strong>Trạng thái thanh toán:</strong> 
                        <span class="badge bg-info"><?php echo htmlspecialchars($donHang->trang_thai_thanh_toan); ?></span>
                    </p>
                    <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($donHang->ngay_dat)); ?></p>
                    <p><strong>Tổng tiền:</strong> 
                        <span class="text-danger fw-bold"><?php echo number_format($donHang->tong_tien, 0, ',', '.'); ?> đ</span>
                    </p>
                    <p><strong>Địa chỉ giao hàng:</strong> <?php echo htmlspecialchars($donHang->dia_chi_giao); ?></p>
                    <p><strong>SĐT nhận hàng:</strong> <?php echo htmlspecialchars($donHang->sdt_nhan); ?></p>
                    
                    <h5 class="mt-4">Sản phẩm</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $details = $donHang->docChiTiet();
                            while ($item = $details->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['ten_san_pham']); ?></td>
                                    <td><?php echo $item['so_luong']; ?></td>
                                    <td><?php echo number_format($item['don_gia'], 0, ',', '.'); ?> đ</td>
                                    <td><?php echo number_format($item['thanh_tien'], 0, ',', '.'); ?> đ</td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Order List -->
    <h2>Đơn hàng của tôi</h2>
    <?php if ($donHangs): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thanh toán</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($dh = $donHangs->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($dh['id']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($dh['ngay_dat'])); ?></td>
                            <td><?php echo number_format($dh['tong_tien'], 0, ',', '.'); ?> đ</td>
                            <td>
                                <span class="badge bg-warning"><?php echo htmlspecialchars($dh['trang_thai']); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($dh['trang_thai_thanh_toan']); ?></span>
                            </td>
                            <td>
                                <a href="<?php echo getBaseUrl(); ?>/donhang.php?id=<?php echo $dh['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
    <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/views/layout/footer.php'; ?>

