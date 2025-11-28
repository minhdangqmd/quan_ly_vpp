<?php
require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../controllers/NhaCungCapController.php';

$pageTitle = 'Quản lý Nhà Cung Cấp';
requireRole('Admin');

$controller = new NhaCungCapController();
$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? 'list';

$supplier = null;

if ($action == 'create' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller->create();
}

if ($action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST' && $id) {
    $controller->edit($id);
}

if ($action == 'delete' && $id) {
    $controller->delete($id);
}

if ($action == 'edit' && $id) {
    $supplier = $controller->edit($id);
}

$suppliers = $controller->index();

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
            <i class="fa-solid fa-circle-exclamation"></i> Có lỗi xảy ra! Không thể xóa nhà cung cấp.
        </div>
    <?php endif; ?>
    
    <div class="admin-header">
        <h2><i class="fa-solid fa-truck"></i> Quản lý Nhà Cung Cấp</h2>
        <a href="<?php echo getBaseUrl(); ?>/admin/nhacungcap.php?action=create" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm Nhà Cung Cấp
        </a>
    </div>

<?php if ($action == 'create' || ($action == 'edit' && $id)): ?>
    <!-- Create/Edit Form -->
    <div class="admin-form-container">
        <div class="admin-form-card">
            <h3><?php echo $action == 'create' ? 'Thêm Nhà Cung Cấp Mới' : 'Chỉnh Sửa Nhà Cung Cấp'; ?></h3>
            
            <form method="POST" class="admin-form">
                <?php if ($action == 'edit' && $supplier): ?>
                    <input type="hidden" name="id" value="<?php echo $supplier->id; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="id"><i class="fa-solid fa-barcode"></i> Mã Nhà Cung Cấp *</label>
                    <input type="text" id="id" name="id" 
                           value="<?php echo $supplier->id ?? ''; ?>" 
                           <?php echo $action == 'edit' ? 'readonly' : 'required'; ?>
                           placeholder="VD: NCC01">
                </div>
                
                <div class="form-group">
                    <label for="ten_nha_cung_cap"><i class="fa-solid fa-tag"></i> Tên Nhà Cung Cấp *</label>
                    <input type="text" id="ten_nha_cung_cap" name="ten_nha_cung_cap" 
                           value="<?php echo htmlspecialchars($supplier->ten_nha_cung_cap ?? ''); ?>" 
                           required placeholder="Nhập tên nhà cung cấp">
                </div>

                <div class="form-group">
                    <label for="dia_chi"><i class="fa-solid fa-location-dot"></i> Địa chỉ</label>
                    <input type="text" id="dia_chi" name="dia_chi" 
                           value="<?php echo htmlspecialchars($supplier->dia_chi ?? ''); ?>" 
                           placeholder="Nhập địa chỉ">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="so_dien_thoai"><i class="fa-solid fa-phone"></i> Số điện thoại</label>
                        <input type="text" id="so_dien_thoai" name="so_dien_thoai" 
                               value="<?php echo htmlspecialchars($supplier->sdt ?? ''); ?>" 
                               placeholder="Nhập số điện thoại">
                    </div>
                    <div class="form-group">
                        <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($supplier->email ?? ''); ?>" 
                               placeholder="Nhập email">
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu
                    </button>
                    <a href="<?php echo getBaseUrl(); ?>/admin/nhacungcap.php" class="btn btn-secondary">
                        <i class="fa-solid fa-xmark"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <!-- Supplier List -->
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Mã NCC</th>
                    <th>Tên Nhà Cung Cấp</th>
                    <th>Địa chỉ</th>
                    <th>Điện thoại</th>
                    <th>Email</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($suppliers && $suppliers->rowCount() > 0): ?>
                    <?php while ($row = $suppliers->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['id']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['ten_nha_cung_cap']); ?></td>
                            <td><?php echo htmlspecialchars($row['dia_chi']); ?></td>
                            <td><?php echo htmlspecialchars($row['sdt']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <a href="<?php echo getBaseUrl(); ?>/admin/nhacungcap.php?action=edit&id=<?php echo $row['id']; ?>" 
                                   class="btn-edit">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>
                                <a href="<?php echo getBaseUrl(); ?>/admin/nhacungcap.php?action=delete&id=<?php echo $row['id']; ?>" 
                                   class="btn-delete"
                                   onclick="return confirm('Bạn có chắc muốn xóa nhà cung cấp này?');">
                                    <i class="fa-solid fa-trash"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-data">
                            <i class="fa-solid fa-inbox"></i>
                            <p>Chưa có nhà cung cấp nào</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
</div>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>

