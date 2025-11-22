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

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Cập nhật thành công!</div>
<?php endif; ?>

<?php if ($id && $donHang): ?>
    <!-- Order Detail -->
    <div class="row">
        <div class="col-md-8">
            <h2>Chi tiết đơn hàng: <?php echo htmlspecialchars($donHang->id); ?></h2>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?php echo getBaseUrl(); ?>/admin/donhang.php?action=update_status&id=<?php echo $donHang->id; ?>">
                        <div class="mb-3">
                            <label for="trang_thai" class="form-label">Trạng thái đơn hàng</label>
                            <select class="form-select" id="trang_thai" name="trang_thai" onchange="this.form.submit()">
                                <option value="Đang xử lý" <?php echo $donHang->trang_thai_don_hang == 'Đang xử lý' ? 'selected' : ''; ?>>Đang xử lý</option>
                                <option value="Đã xác nhận" <?php echo $donHang->trang_thai_don_hang == 'Đã xác nhận' ? 'selected' : ''; ?>>Đã xác nhận</option>
                                <option value="Đang giao" <?php echo $donHang->trang_thai_don_hang == 'Đang giao' ? 'selected' : ''; ?>>Đang giao</option>
                                <option value="Đã giao" <?php echo $donHang->trang_thai_don_hang == 'Đã giao' ? 'selected' : ''; ?>>Đã giao</option>
                                <option value="Đã hủy" <?php echo $donHang->trang_thai_don_hang == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                            </select>
                        </div>
                    </form>
                    
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
    <h2>Quản lý đơn hàng</h2>
    <div class="table-responsive">
        <table class="table table-striped">
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
                            <td><?php echo htmlspecialchars($dh['id']); ?></td>
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
                            <td><?php echo number_format($dh['tong_tien'], 0, ',', '.'); ?> đ</td>
                            <td>
                                <span class="badge bg-warning"><?php echo htmlspecialchars($dh['trang_thai']); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($dh['id_hinh_thuc_tt']); ?></span>
                            </td>
                            <td>
                                <a href="<?php echo getBaseUrl(); ?>/admin/donhang.php?id=<?php echo $dh['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Chưa có đơn hàng nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <style>
table {
  border-collapse: separate;
  border-spacing: 20px 0;
}

th {
  padding: 10px;
  text-align: left;
}
</style>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>

