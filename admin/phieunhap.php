<?php
require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../controllers/PhieuNhapController.php';

$pageTitle = 'Quản lý Nhập kho';
requireRole('Admin');

$controller = new PhieuNhapController();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

if ($action == 'create' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller->create();
}

$formData = null;
if ($action == 'create') {
    $formData = $controller->create();
}

$detailData = null;
if ($action == 'detail' && $id) {
    $detailData = $controller->detail($id);
}

$list = null;
if ($action == 'list') {
    $list = $controller->index();
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
        <h2><i class="fa-solid fa-warehouse"></i> Quản lý Nhập kho</h2>
        <?php if ($action == 'list'): ?>
            <a href="<?php echo getBaseUrl(); ?>/admin/phieunhap.php?action=create" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Tạo Phiếu Nhập
            </a>
        <?php else: ?>
            <a href="<?php echo getBaseUrl(); ?>/admin/phieunhap.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
        <?php endif; ?>
    </div>

    <?php if ($action == 'create'): ?>
        <!-- Create Form -->
        <form method="POST" class="admin-form-container" id="importForm">
            <div class="admin-form-card" style="max-width: 1000px;">
                <h3>Lập Phiếu Nhập Kho</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nhà Cung Cấp *</label>
                        <select name="id_nha_cung_cap" required>
                            <option value="">-- Chọn Nhà Cung Cấp --</option>
                            <?php while ($ncc = $formData['nhaCungCaps']->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $ncc['id']; ?>"><?php echo htmlspecialchars($ncc['ten_nha_cung_cap']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kho Nhập *</label>
                        <select name="id_kho" required>
                            <option value="">-- Chọn Kho --</option>
                            <?php while ($k = $formData['khos']->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['ten_kho']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea name="ghi_chu" rows="2" placeholder="Ghi chú phiếu nhập..."></textarea>
                </div>

                <hr style="margin: 20px 0; border-top: 1px solid #eee;">
                
                <h4>Chi tiết sản phẩm</h4>
                <table class="admin-table" id="productTable">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th width="150">Số lượng</th>
                            <th width="200">Đơn giá nhập</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody id="productBody">
                        <!-- Product rows will be added here -->
                    </tbody>
                </table>
                
                <button type="button" class="btn btn-secondary" id="addProductBtn" style="margin-top: 10px;">
                    <i class="fa-solid fa-plus"></i> Thêm sản phẩm
                </button>

                <div class="form-actions" style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Hoàn tất phiếu nhập
                    </button>
                </div>
            </div>
        </form>

        <!-- Template for product options -->
        <script>
            const products = [
                <?php 
                // Rewind or re-fetch logic might be needed if using PDOStatement directly multiple times? 
                // Actually we can just fetchAll in controller or here.
                // Let's dump them to JS array.
                $allProducts = $formData['sanPhams']->fetchAll(PDO::FETCH_ASSOC);
                foreach ($allProducts as $p) {
                    echo "{id: '" . $p['id'] . "', name: '" . addslashes($p['ten_san_pham']) . "'},";
                }
                ?>
            ];

            document.getElementById('addProductBtn').addEventListener('click', function() {
                const tbody = document.getElementById('productBody');
                const index = tbody.children.length;
                
                const tr = document.createElement('tr');
                
                let options = '<option value="">-- Chọn sản phẩm --</option>';
                products.forEach(p => {
                    options += `<option value="${p.id}">${p.name}</option>`;
                });

                tr.innerHTML = `
                    <td>
                        <select name="products[${index}][id_san_pham]" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            ${options}
                        </select>
                    </td>
                    <td>
                        <input type="number" name="products[${index}][so_luong]" min="1" required placeholder="SL" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </td>
                    <td>
                        <input type="number" name="products[${index}][don_gia]" min="0" step="100" required placeholder="Giá nhập" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </td>
                    <td>
                        <button type="button" class="btn-delete" onclick="this.closest('tr').remove()" style="border: none; background: none; cursor: pointer; color: red;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                `;
                
                tbody.appendChild(tr);
            });

            // Add first row by default
            document.getElementById('addProductBtn').click();
        </script>

    <?php elseif ($action == 'detail' && $detailData): ?>
        <!-- Detail View -->
        <div class="admin-form-container">
            <div class="admin-form-card" style="max-width: 1000px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3>Chi tiết phiếu nhập: <?php echo htmlspecialchars($detailData['info']['id']); ?></h3>
                    <span style="color: #666;"><?php echo $detailData['info']['ngay_nhap']; ?></span>
                </div>
                
                <div class="info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0; background: #f9f9f9; padding: 15px; border-radius: 8px;">
                    <div>
                        <p><strong>Nhà cung cấp:</strong> <?php echo htmlspecialchars($detailData['info']['ten_nha_cung_cap']); ?></p>
                        <p><strong>Nhân viên nhập:</strong> <?php echo htmlspecialchars($detailData['info']['ten_nhan_vien']); ?></p>
                    </div>
                    <div>
                        <p><strong>Kho:</strong> <?php echo htmlspecialchars($detailData['info']['ten_kho']); ?></p>
                        <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($detailData['info']['ghi_chu']); ?></p>
                    </div>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Hình ảnh</th>
                            <th>Số lượng</th>
                            <th>Đơn giá nhập</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        while ($item = $detailData['details']->fetch(PDO::FETCH_ASSOC)): 
                            $total += $item['thanh_tien'];
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['ten_san_pham']); ?></td>
                                <td>
                                    <?php if($item['hinh_anh']): ?>
                                        <img src="/<?php echo $item['hinh_anh']; ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $item['so_luong']; ?></td>
                                <td><?php echo number_format($item['don_gia_nhap'], 0, ',', '.'); ?> đ</td>
                                <td><strong><?php echo number_format($item['thanh_tien'], 0, ',', '.'); ?> đ</strong></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right; font-weight: bold;">Tổng cộng:</td>
                            <td style="font-weight: bold; color: #d32f2f; font-size: 1.1em;">
                                <?php echo number_format($total, 0, ',', '.'); ?> đ
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    <?php else: ?>
        <!-- List View -->
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã Phiếu</th>
                        <th>Ngày nhập</th>
                        <th>Nhà cung cấp</th>
                        <th>Nhân viên</th>
                        <th>Tổng tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($list && $list->rowCount() > 0): ?>
                        <?php while ($row = $list->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['id']); ?></strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['ngay_nhap'])); ?></td>
                                <td><?php echo htmlspecialchars($row['ten_nha_cung_cap']); ?></td>
                                <td><?php echo htmlspecialchars($row['ten_nhan_vien']); ?></td>
                                <td style="color: #d32f2f; font-weight: 500;"><?php echo number_format($row['tong_tien'], 0, ',', '.'); ?> đ</td>
                                <td>
                                    <a href="<?php echo getBaseUrl(); ?>/admin/phieunhap.php?action=detail&id=<?php echo $row['id']; ?>" 
                                       class="btn-edit">
                                        <i class="fa-solid fa-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="no-data">
                                <i class="fa-solid fa-inbox"></i>
                                <p>Chưa có phiếu nhập nào</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>

