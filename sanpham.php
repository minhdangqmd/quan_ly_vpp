<?php
require_once __DIR__ . '/utils/session.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/SanPhamController.php';

$pageTitle = 'Sản phẩm';
$sanPhamController = new SanPhamController();

$id = $_GET['id'] ?? null;
if ($id) {
    $product = $sanPhamController->show($id);
    $pageTitle = $product ? $product->ten_san_pham : 'Sản phẩm';
} else {
    $products = $sanPhamController->index();
}

include __DIR__ . '/views/layout/header.php';
?>

<?php if ($id && $product): ?>
    <!-- Product Detail -->
    <div class="row">
        <div class="col-md-5">
            <?php if ($product->hinh_anh): ?>
                <img src="/<?php echo htmlspecialchars($product->hinh_anh); ?>" 
                     class="img-fluid rounded" alt="<?php echo htmlspecialchars($product->ten_san_pham); ?>">
            <?php else: ?>
                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                     style="height: 400px;">
                    <i class="bi bi-image fs-1 text-muted"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-7">
            <h2><?php echo htmlspecialchars($product->ten_san_pham); ?></h2>
            <p class="text-danger fs-3 fw-bold">
                <?php echo number_format($product->gia_ban, 0, ',', '.'); ?> đ
            </p>
            <p class="text-muted">Tồn kho: <?php echo $product->so_luong_ton; ?></p>
            <?php if ($product->mo_ta): ?>
                <div class="mb-3">
                    <h5>Mô tả:</h5>
                    <p><?php echo nl2br(htmlspecialchars($product->mo_ta)); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (isLoggedIn()): ?>
                <form method="POST" action="<?php echo getBaseUrl(); ?>/giohang.php?action=add">
                    <input type="hidden" name="id_san_pham" value="<?php echo $product->id; ?>">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="so_luong" class="form-label">Số lượng</label>
                            <input type="number" class="form-control" id="so_luong" name="so_luong" 
                                   value="1" min="1" max="<?php echo $product->so_luong_ton; ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                </form>
            <?php else: ?>
                <a href="<?php echo getBaseUrl(); ?>/login.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-box-arrow-in-right"></i> Đăng nhập để mua hàng
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <!-- Product List -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Sản phẩm</h2>
        </div>
        <div class="col-md-6">
            <form method="GET" class="d-flex">
                <input type="text" class="form-control me-2" name="keyword" 
                       placeholder="Tìm kiếm sản phẩm..." value="<?php echo $_GET['keyword'] ?? ''; ?>">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search"></i> Tìm
                </button>
            </form>
        </div>
    </div>
    
    <div class="row">
        <?php if ($products): ?>
            <?php while ($product = $products->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="col-md-3 mb-4">
                    <div class="card product-card h-100">
                        <?php if ($product['hinh_anh']): ?>
                            <img src="/<?php echo htmlspecialchars($product['hinh_anh']); ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($product['ten_san_pham']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['ten_san_pham']); ?></h5>
                            <p class="card-text text-danger fw-bold">
                                <?php echo number_format($product['gia_ban'], 0, ',', '.'); ?> đ
                            </p>
                            <div class="mt-auto">
                                <a href="<?php echo getBaseUrl(); ?>/sanpham.php?id=<?php echo $product['id']; ?>" 
                                   class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-eye"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">Không tìm thấy sản phẩm nào.</div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/views/layout/footer.php'; ?>

