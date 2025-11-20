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

<div class="main-content">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i> Thao tác thành công!
        </div>
    <?php endif; ?>
    
    <div class="admin-header">
        <h2><i class="fa-solid fa-box"></i> Quản lý sản phẩm</h2>
        <a href="<?php echo getBaseUrl(); ?>/admin/sanpham.php?action=create" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm sản phẩm
        </a>
    </div>

<?php if ($action == 'create' || ($action == 'edit' && $id)): ?>
    <!-- Create/Edit Form -->
    <div class="admin-form-container">
        <div class="admin-form-card">
            <h3><?php echo $action == 'create' ? 'Thêm sản phẩm mới' : 'Chỉnh sửa sản phẩm'; ?></h3>
            
            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <?php if ($action == 'edit' && $product): ?>
                    <input type="hidden" name="id" value="<?php echo $product->id; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="id"><i class="fa-solid fa-barcode"></i> Mã sản phẩm *</label>
                    <input type="text" id="id" name="id" 
                           value="<?php echo $product->id ?? ''; ?>" 
                           <?php echo $action == 'edit' ? 'readonly' : 'required'; ?>
                           placeholder="VD: SP001">
                </div>
                
                <div class="form-group">
                    <label for="ten_san_pham"><i class="fa-solid fa-tag"></i> Tên sản phẩm *</label>
                    <input type="text" id="ten_san_pham" name="ten_san_pham" 
                           value="<?php echo htmlspecialchars($product->ten_san_pham ?? ''); ?>" 
                           required placeholder="Nhập tên sản phẩm">
                </div>
                
                <div class="form-group">
                    <label for="mo_ta"><i class="fa-solid fa-align-left"></i> Mô tả</label>
                    <textarea id="mo_ta" name="mo_ta" rows="3" 
                              placeholder="Mô tả sản phẩm..."><?php echo htmlspecialchars($product->mo_ta ?? ''); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="gia_ban"><i class="fa-solid fa-money-bill"></i> Giá bán (đ) *</label>
                        <input type="number" id="gia_ban" name="gia_ban" 
                               value="<?php echo $product->gia_ban ?? 0; ?>" 
                               min="0" step="1000" required placeholder="0">
                    </div>
                    <div class="form-group">
                        <label for="so_luong_ton"><i class="fa-solid fa-boxes-stacked"></i> Số lượng tồn *</label>
                        <input type="number" id="so_luong_ton" name="so_luong_ton" 
                               value="<?php echo $product->so_luong_ton ?? 0; ?>" 
                               min="0" required placeholder="0">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="id_danh_muc"><i class="fa-solid fa-folder"></i> Danh mục</label>
                        <select id="id_danh_muc" name="id_danh_muc">
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
                    <div class="form-group">
                        <label for="id_dvt"><i class="fa-solid fa-ruler"></i> Đơn vị tính</label>
                        <?php
                        require_once __DIR__ . '/../config/database.php';
                        $database = new Database();
                        $conn = $database->getConnection();
                        $query = "SELECT * FROM donvitinh";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();
                        $dvt = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <select id="id_dvt" name="id_dvt">
                            <option value="">Chọn đơn vị</option>
                            <?php foreach ($dvt as $unit): ?>
                                <option value="<?php echo $unit['id']; ?>" 
                                        <?php echo ($product->id_dvt ?? '') == $unit['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($unit['ten_dvt']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="id_nha_cung_cap"><i class="fa-solid fa-truck"></i> Nhà cung cấp</label>
                        <select id="id_nha_cung_cap" name="id_nha_cung_cap">
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
                    <div class="form-group">
                        <label for="han_su_dung"><i class="fa-solid fa-calendar"></i> Hạn sử dụng</label>
                        <input type="date" id="han_su_dung" name="han_su_dung" 
                               value="<?php echo $product->han_su_dung ?? ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="hinh_anh"><i class="fa-solid fa-image"></i> Hình ảnh</label>
                    <input type="file" id="hinh_anh" name="hinh_anh" accept="image/*">
                    <?php if ($action == 'edit' && $product && $product->hinh_anh): ?>
                        <small class="file-hint">
                            Hình hiện tại: <a href="/<?php echo $product->hinh_anh; ?>" target="_blank">Xem ảnh</a>
                        </small>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu
                    </button>
                    <a href="<?php echo getBaseUrl(); ?>/admin/sanpham.php" class="btn btn-secondary">
                        <i class="fa-solid fa-xmark"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <!-- Product List -->
    <div class="admin-table-container">
        <table class="admin-table">
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
                            <td><strong><?php echo htmlspecialchars($product['id']); ?></strong></td>
                            <td><?php echo htmlspecialchars($product['ten_san_pham']); ?></td>
                            <td class="price"><?php echo number_format($product['gia_ban'], 0, ',', '.'); ?> đ</td>
                            <td>
                                <span class="stock-badge <?php echo $product['so_luong_ton'] > 0 ? 'in-stock' : 'out-stock'; ?>">
                                    <?php echo $product['so_luong_ton']; ?>
                                </span>
                            </td>
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
                                    echo $dm ? '<span class="category-tag">' . htmlspecialchars($dm['ten_danh_muc']) . '</span>' : '-';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="<?php echo getBaseUrl(); ?>/admin/sanpham.php?action=edit&id=<?php echo $product['id']; ?>" 
                                   class="btn-edit">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-data">
                            <i class="fa-solid fa-inbox"></i>
                            <p>Chưa có sản phẩm nào</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
</div>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>

