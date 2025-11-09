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

<div class="row">
    <div class="col-md-8">
        <h2>Thông tin đơn hàng</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="dia_chi_giao" class="form-label">Địa chỉ giao hàng *</label>
                <input type="text" class="form-control" id="dia_chi_giao" name="dia_chi_giao" 
                       value="<?php echo htmlspecialchars($customer['dia_chi'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="sdt_nhan" class="form-label">Số điện thoại nhận hàng *</label>
                <input type="tel" class="form-control" id="sdt_nhan" name="sdt_nhan" 
                       value="<?php echo htmlspecialchars($customer['so_dien_thoai'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="id_hinh_thuc_tt" class="form-label">Phương thức thanh toán *</label>
                <select class="form-select" id="id_hinh_thuc_tt" name="id_hinh_thuc_tt" required>
                    <option value="">Chọn phương thức thanh toán</option>
                    <?php foreach ($paymentMethods as $method): ?>
                        <option value="<?php echo $method['id']; ?>">
                            <?php echo htmlspecialchars($method['ten_hinh_thuc']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="ghi_chu" class="form-label">Ghi chú</label>
                <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3"></textarea>
            </div>
            
            <h4 class="mt-4">Sản phẩm</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $cartItems->execute();
                    while ($item = $cartItems->fetch(PDO::FETCH_ASSOC)): 
                        $thanhTien = $item['so_luong'] * $item['gia_ban'];
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['ten_san_pham']); ?></td>
                            <td><?php echo $item['so_luong']; ?></td>
                            <td><?php echo number_format($item['gia_ban'], 0, ',', '.'); ?> đ</td>
                            <td><?php echo number_format($thanhTien, 0, ',', '.'); ?> đ</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-circle"></i> Đặt hàng
                </button>
            </div>
        </form>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Tổng kết</h5>
            </div>
            <div class="card-body">
                <p class="d-flex justify-content-between">
                    <span>Tổng tiền:</span>
                    <strong class="text-danger fs-4"><?php echo number_format($tongTien, 0, ',', '.'); ?> đ</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/views/layout/footer.php'; ?>

