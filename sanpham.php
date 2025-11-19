<?php
require_once __DIR__ . '/utils/session.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/SanPhamController.php';
require_once __DIR__ . '/models/DanhMucSanPham.php';

$pageTitle = 'Sản phẩm';
$sanPhamController = new SanPhamController();

// Get database connection for categories
$database = new Database();
$conn = $database->getConnection();
$danhMucObj = new DanhMucSanPham($conn);
$danhMucs = $danhMucObj->docTatCa();

// Get selected category name
$selectedDanhMucId = $_GET['danh_muc'] ?? null;
$selectedDanhMucName = '';
if ($selectedDanhMucId) {
    $danhMucTemp = new DanhMucSanPham($conn);
    $danhMucTemp->id = $selectedDanhMucId;
    if ($danhMucTemp->docTheoId()) {
        $selectedDanhMucName = $danhMucTemp->ten_danh_muc;
        $pageTitle = $danhMucTemp->ten_danh_muc . ' - Sản phẩm';
    }
}

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
    <!-- Product List with Sidebar -->
    <div class="main-content">
        <div class="product-list-container">
            <!-- Category Sidebar -->
            <aside class="category-sidebar">
                <h3>Danh mục</h3>
                <ul class="category-list">
                    <li class="<?php echo !$selectedDanhMucId ? 'active' : ''; ?>">
                        <a href="<?php echo getBaseUrl(); ?>/sanpham.php">
                            <i class="fa-solid fa-border-all"></i> Tất cả sản phẩm
                        </a>
                    </li>
                    <?php
                    $danhMucs = $danhMucObj->docTatCa(); // Reset pointer
                    while ($dm = $danhMucs->fetch(PDO::FETCH_ASSOC)):
                    ?>
                        <li class="<?php echo ($selectedDanhMucId == $dm['id']) ? 'active' : ''; ?>">
                            <a href="<?php echo getBaseUrl(); ?>/sanpham.php?danh_muc=<?php echo $dm['id']; ?>">
                                <i class="fa-solid fa-tag"></i> <?php echo htmlspecialchars($dm['ten_danh_muc']); ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </aside>
            
            <!-- Products Grid -->
            <div class="products-main">
                <div class="products-header">
                    <div>
                        <h2><?php echo $selectedDanhMucName ? htmlspecialchars($selectedDanhMucName) : 'Tất cả sản phẩm'; ?></h2>
                    </div>
                    <form method="GET" class="search-form">
                        <?php if ($selectedDanhMucId): ?>
                            <input type="hidden" name="danh_muc" value="<?php echo $selectedDanhMucId; ?>">
                        <?php endif; ?>
                        <input type="text" name="keyword" 
                               placeholder="Tìm kiếm sản phẩm..." 
                               value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>">
                        <button type="submit">
                            <i class="fa-solid fa-search"></i> Tìm
                        </button>
                    </form>
                </div>
                
                <div class="course-list">
                    <?php if ($products): ?>
                        <?php 
                        $hasProducts = false;
                        while ($product = $products->fetch(PDO::FETCH_ASSOC)): 
                            $hasProducts = true;
                        ?>
                            <div class="course-item">
                                <a href="<?php echo getBaseUrl(); ?>/sanpham.php?id=<?php echo $product['id']; ?>">
                                    <?php if ($product['hinh_anh']): ?>
                                        <img 
                                            src="<?php echo getBaseUrl(); ?>/<?php echo htmlspecialchars($product['hinh_anh']); ?>" 
                                            alt="<?php echo htmlspecialchars($product['ten_san_pham']); ?>" 
                                            class="thumb"
                                        />
                                    <?php else: ?>
                                        <img 
                                            src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='278'%3E%3Crect fill='%23e2dfda' width='300' height='278'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='%235f5b53' font-family='Arial' font-size='16'%3EKhông có ảnh%3C/text%3E%3C/svg%3E" 
                                            alt="<?php echo htmlspecialchars($product['ten_san_pham']); ?>" 
                                            class="thumb"
                                        />
                                    <?php endif; ?>
                                </a>
                                <div class="info">
                                    <div class="head">
                                        <h3 class="title">
                                            <a href="<?php echo getBaseUrl(); ?>/sanpham.php?id=<?php echo $product['id']; ?>" class="line-clamp break-all">
                                                <?php echo htmlspecialchars($product['ten_san_pham']); ?>
                                            </a>
                                        </h3>
                                    </div>
                                    <p class="desc line-clamp line-2 break-all">
                                        <?php echo htmlspecialchars($product['mo_ta'] ?? 'Sản phẩm chất lượng cao'); ?>
                                    </p>
                                    <p class="p-list">
                                        Tồn kho: <?php echo $product['so_luong_ton']; ?>
                                    </p>
                                    <div class="foot">
                                        <span class="price"><?php echo number_format($product['gia_ban'], 0, ',', '.'); ?> đ</span>
                                        <a href="<?php echo getBaseUrl(); ?>/sanpham.php?id=<?php echo $product['id']; ?>" class="btn book-btn">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        
                        <?php if (!$hasProducts): ?>
                            <div class="no-products">
                                <i class="fa-solid fa-box-open"></i>
                                <p>Không tìm thấy sản phẩm nào.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="no-products">
                            <i class="fa-solid fa-box-open"></i>
                            <p>Không tìm thấy sản phẩm nào.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/views/layout/footer.php'; ?>

