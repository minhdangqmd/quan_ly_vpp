<?php
require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../controllers/DanhMucController.php';

$pageTitle = 'Quản lý danh mục';
requireRole('Admin');

$controller = new DanhMucController();
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

$danhMucs = $controller->index();
$danhMuc = null;

if ($action == 'edit' && $id) {
    $danhMuc = $controller->show($id);
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
            <i class="fa-solid fa-circle-exclamation"></i> Có lỗi xảy ra! Không thể xóa danh mục đang có sản phẩm.
        </div>
    <?php endif; ?>
    
    <div class="admin-header">
        <h2><i class="fa-solid fa-folder"></i> Quản lý danh mục</h2>
        <a href="<?php echo getBaseUrl(); ?>/admin/danhmuc.php?action=create" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm danh mục
        </a>
    </div>

<?php if ($action == 'create' || ($action == 'edit' && $danhMuc)): ?>
    <!-- Create/Edit Form -->
    <div class="admin-form-container">
        <div class="admin-form-card">
            <h3><?php echo $action == 'create' ? 'Thêm danh mục mới' : 'Chỉnh sửa danh mục'; ?></h3>
            
            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label for="ten_danh_muc"><i class="fa-solid fa-tag"></i> Tên danh mục *</label>
                    <input type="text" id="ten_danh_muc" name="ten_danh_muc" 
                           value="<?php echo htmlspecialchars($danhMuc->ten_danh_muc ?? ''); ?>" 
                           required placeholder="Nhập tên danh mục">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu
                    </button>
                    <a href="<?php echo getBaseUrl(); ?>/admin/danhmuc.php" class="btn btn-secondary">
                        <i class="fa-solid fa-xmark"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <!-- Category List -->
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Số sản phẩm</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($danhMucs): ?>
                    <?php while ($dm = $danhMucs->fetch(PDO::FETCH_ASSOC)): ?>
                        <?php
                        // Count products in this category
                        require_once __DIR__ . '/../config/database.php';
                        $database = new Database();
                        $conn = $database->getConnection();
                        $query = "SELECT COUNT(*) as count FROM sanpham WHERE id_danh_muc = :id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(":id", $dm['id']);
                        $stmt->execute();
                        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($dm['id']); ?></strong></td>
                            <td><?php echo htmlspecialchars($dm['ten_danh_muc']); ?></td>
                            <td>
                                <span class="count-badge"><?php echo $count; ?> sản phẩm</span>
                            </td>
                            <td>
                                <a href="<?php echo getBaseUrl(); ?>/admin/danhmuc.php?action=edit&id=<?php echo $dm['id']; ?>" 
                                   class="btn-edit">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>
                                <a href="<?php echo getBaseUrl(); ?>/admin/danhmuc.php?action=delete&id=<?php echo $dm['id']; ?>" 
                                   class="btn-delete"
                                   onclick="return confirm('Bạn có chắc muốn xóa danh mục này?');">
                                    <i class="fa-solid fa-trash"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="no-data">
                            <i class="fa-solid fa-inbox"></i>
                            <p>Chưa có danh mục nào</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
</div>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>

