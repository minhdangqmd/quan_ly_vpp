<?php
require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../controllers/SanPhamController.php';

$pageTitle = 'Quản lý sản phẩm';
requireRole('Admin');

$controller = new SanPhamController();
$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? 'list';

if ($action == 'create' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller->create();
}

if ($action == 'edit' && $id) {
    $product = $controller->update($id);
}

if ($action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST' && $id) {
    $controller->update($id);
}

$products = $controller->index();
$formData = null;

if ($action == 'create') {
    $formData = $controller->create();
}

if ($action == 'edit' && $id) {
    $product = $controller->update($id);
}

include __DIR__ . '/../views/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý sản phẩm</h2>
    <a href="<?php echo getBaseUrl(); ?>/admin/sanpham.php?action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm sản phẩm
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Thao tác thành công!</div>
<?php endif; ?>

<?php if ($action == 'create' || ($action == 'edit' && $id)): ?>
    <!-- Create/Edit Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><?php echo $action == 'create' ? 'Thêm sản phẩm' : 'Sửa sản phẩm'; ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <?php if ($action == 'edit' && $product): ?>
                            <input type="hidden" name="id" value="<?php echo $product->id; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="id" class="form-label">Mã sản phẩm *</label>
                            <input type="text" class="form-control" id="id" name="id" 
                                   value="<?php echo $product->id ?? ''; ?>" 
                                   <?php echo $action == 'edit' ? 'readonly' : 'required'; ?>>
                        </div>
                        <div class="mb-3">
                            <label for="ten_san_pham" class="form-label">Tên sản phẩm *</label>
                            <input type="text" class="form-control" id="ten_san_pham" name="ten_san_pham" 
                                   value="<?php echo htmlspecialchars($product->ten_san_pham ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="mo_ta" name="mo_ta" rows="3"><?php echo htmlspecialchars($product->mo_ta ?? ''); ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="gia_ban" class="form-label">Giá bán *</label>
                                <input type="number" class="form-control" id="gia_ban" name="gia_ban" 
                                       value="<?php echo $product->gia_ban ?? 0; ?>" min="0" step="1000" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="so_luong_ton" class="form-label">Số lượng tồn *</label>
                                <input type="number" class="form-control" id="so_luong_ton" name="so_luong_ton" 
                                       value="<?php echo $product->so_luong_ton ?? 0; ?>" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="id_dvt" class="form-label">Đơn vị tính</label>
                                <?php
                                require_once __DIR__ . '/../config/database.php';
                                $database = new Database();
                                $conn = $database->getConnection();
                                $query = "SELECT * FROM donvitinh";
                                $stmt = $conn->prepare($query);
                                $stmt->execute();
                                $dvt = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <select class="form-select" id="id_dvt" name="id_dvt">
                                    <option value="">Chọn đơn vị tính</option>
                                    <?php foreach ($dvt as $unit): ?>
                                        <option value="<?php echo $unit['id']; ?>" 
                                                <?php echo ($product->id_dvt ?? '') == $unit['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($unit['ten_dvt']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_danh_muc" class="form-label">Danh mục</label>
                                <select class="form-select" id="id_danh_muc" name="id_danh_muc">
                                    <option value="">Chọn danh mục</option>
                                    <?php if ($formData && isset($formData['danhMucs'])): ?>
                                        <?php while ($dm = $formData['danhMucs']->fetch(PDO::FETCH_ASSOC)): ?>
                                            <option value="<?php echo $dm['id']; ?>" 
                                                    <?php echo ($product->id_danh_muc ?? '') == $dm['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($dm['ten_danh_muc']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="id_nha_cung_cap" class="form-label">Nhà cung cấp</label>
                                <select class="form-select" id="id_nha_cung_cap" name="id_nha_cung_cap">
                                    <option value="">Chọn nhà cung cấp</option>
                                    <?php if ($formData && isset($formData['nhaCungCaps'])): ?>
                                        <?php while ($ncc = $formData['nhaCungCaps']->fetch(PDO::FETCH_ASSOC)): ?>
                                            <option value="<?php echo $ncc['id']; ?>" 
                                                    <?php echo ($product->id_nha_cung_cap ?? '') == $ncc['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($ncc['ten_nha_cung_cap']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="han_su_dung" class="form-label">Hạn sử dụng</label>
                            <input type="date" class="form-control" id="han_su_dung" name="han_su_dung" 
                                   value="<?php echo $product->han_su_dung ?? ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="hinh_anh" class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" id="hinh_anh" name="hinh_anh" accept="image/*">
                            <?php if ($action == 'edit' && $product && $product->hinh_anh): ?>
                                <small class="form-text text-muted">
                                    Hình ảnh hiện tại: <a href="/<?php echo $product->hinh_anh; ?>" target="_blank">Xem</a>
                                </small>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Lưu
                        </button>
                        <a href="<?php echo getBaseUrl(); ?>/admin/sanpham.php" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Product List -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Mã SP</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Danh mục</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($products): ?>
                    <?php while ($product = $products->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td><?php echo htmlspecialchars($product['ten_san_pham']); ?></td>
                            <td><?php echo number_format($product['gia_ban'], 0, ',', '.'); ?> đ</td>
                            <td><?php echo $product['so_luong_ton']; ?></td>
                            <td>
                                <?php
                                if ($product['id_danh_muc']) {
                                    require_once __DIR__ . '/../config/database.php';
                                    $database = new Database();
                                    $conn = $database->getConnection();
                                    $query = "SELECT ten_danh_muc FROM danhmuc WHERE id = :id";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bindParam(":id", $product['id_danh_muc']);
                                    $stmt->execute();
                                    $dm = $stmt->fetch(PDO::FETCH_ASSOC);
                                    echo $dm ? htmlspecialchars($dm['ten_danh_muc']) : '-';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="<?php echo getBaseUrl(); ?>/admin/sanpham.php?action=edit&id=<?php echo $product['id']; ?>" 
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Sửa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Chưa có sản phẩm nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>

