<?php
require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../controllers/KhachHangController.php';

$pageTitle = 'Quản lý khách hàng';
requireRole('Admin');

$controller = new KhachHangController();
$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? 'list';

if ($action == 'create' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller->create();
}

if ($action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST' && $id) {
    $controller->update($id);
}

if ($action == 'delete' && $id) {
    $controller->delete($id);
}

$khachHangs = $controller->index();
$khachHang = null;

if ($action == 'edit' && $id) {
    $khachHang = $controller->show($id);
}

include __DIR__ . '/../views/layout/header.php';
?>

<div class="main-content">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i> Thao tác thành công!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error">
            <i class="fa-solid fa-circle-exclamation"></i> Có lỗi xảy ra! Không thể xóa khách hàng.
        </div>
    <?php endif; ?>
    
    <div class="admin-header">
        <h2><i class="fa-solid fa-users"></i> Quản lý khách hàng</h2>
        <a href="<?php echo getBaseUrl(); ?>/admin/khachhang.php?action=create" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm khách hàng
        </a>
    </div>

<?php if ($action == 'create' || ($action == 'edit' && $khachHang)): ?>
    <!-- Create/Edit Form -->
    <div class="admin-form-container">
        <div class="admin-form-card">
            <h3><?php echo $action == 'create' ? 'Thêm khách hàng mới' : 'Chỉnh sửa khách hàng'; ?></h3>
            
            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label for="ho_ten"><i class="fa-solid fa-user"></i> Họ tên *</label>
                    <input type="text" id="ho_ten" name="ho_ten" 
                           value="<?php echo htmlspecialchars($khachHang->ho_ten ?? ''); ?>" 
                           required placeholder="Nhập họ tên khách hàng">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="so_dien_thoai"><i class="fa-solid fa-phone"></i> Số điện thoại *</label>
                        <input type="tel" id="so_dien_thoai" name="so_dien_thoai" 
                               value="<?php echo htmlspecialchars($khachHang->sdt ?? ''); ?>" 
                               required placeholder="0123456789">
                    </div>
                    
                    <?php if ($action == 'create'): ?>
                    <div class="form-group">
                        <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                        <input type="email" id="email" name="email" 
                               placeholder="email@example.com">
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="dia_chi"><i class="fa-solid fa-location-dot"></i> Địa chỉ</label>
                    <textarea id="dia_chi" name="dia_chi" rows="2" 
                              placeholder="Nhập địa chỉ khách hàng"><?php echo htmlspecialchars($khachHang->dia_chi ?? ''); ?></textarea>
                </div>
                
                <?php if ($action == 'create'): ?>
                <div class="form-group">
                    <label for="id_taikhoan"><i class="fa-solid fa-user-tag"></i> Tài khoản (tùy chọn)</label>
                    <select id="id_taikhoan" name="id_taikhoan">
                        <option value="">Không liên kết tài khoản</option>
                        <?php
                        require_once __DIR__ . '/../config/database.php';
                        $database = new Database();
                        $conn = $database->getConnection();
                        $query = "SELECT id, ten_dang_nhap FROM taikhoan WHERE id NOT IN (SELECT id_taikhoan FROM khachhang WHERE id_taikhoan IS NOT NULL)";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();
                        while ($user = $stmt->fetch(PDO::FETCH_ASSOC)):
                        ?>
                            <option value="<?php echo $user['id']; ?>">
                                <?php echo htmlspecialchars($user['ten_dang_nhap']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu
                    </button>
                    <a href="<?php echo getBaseUrl(); ?>/admin/khachhang.php" class="btn btn-secondary">
                        <i class="fa-solid fa-xmark"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <!-- Customer List -->
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Số điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Tài khoản</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($khachHangs): ?>
                    <?php while ($kh = $khachHangs->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($kh['id']); ?></strong></td>
                            <td><?php echo htmlspecialchars($kh['ho_ten']); ?></td>
                            <td><?php echo htmlspecialchars($kh['sdt']); ?></td>
                            <td><?php echo htmlspecialchars($kh['dia_chi'] ?: '-'); ?></td>
                            <td>
                                <?php if ($kh['ten_dang_nhap']): ?>
                                    <span class="account-tag"><?php echo htmlspecialchars($kh['ten_dang_nhap']); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo getBaseUrl(); ?>/admin/khachhang.php?action=edit&id=<?php echo $kh['id']; ?>" 
                                   class="btn-edit">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>
                                <a href="<?php echo getBaseUrl(); ?>/admin/khachhang.php?action=delete&id=<?php echo $kh['id']; ?>" 
                                   class="btn-delete"
                                   onclick="return confirm('Bạn có chắc muốn xóa khách hàng này?');">
                                    <i class="fa-solid fa-trash"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-data">
                            <i class="fa-solid fa-inbox"></i>
                            <p>Chưa có khách hàng nào</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
</div>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>

