<?php
require_once __DIR__ . '/utils/session.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/SanPhamController.php';

$pageTitle = 'Trang chủ';
$sanPhamController = new SanPhamController();
$products = $sanPhamController->index();

// Convert PDO statement to array for easier handling
$productsArray = [];
if ($products) {
    while ($product = $products->fetch(PDO::FETCH_ASSOC)) {
        $productsArray[] = $product;
    }
}

$baseUrl = getBaseUrl();
include __DIR__ . '/views/layout/header.php';
?>

<div id="home">
    <!-- Hero -->
    <div class="hero">
        <div class="main-content">
            <div class="body">
                <!-- Hero left -->
                <div class="media-block">
                    <img
                        src="<?php echo $baseUrl; ?>/assets/img/hero-image.jpg"
                        alt="Cửa hàng văn phòng phẩm"
                        class="img"
                        onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'470\' height=\'685\'%3E%3Crect fill=\'%23e2dfda\' width=\'470\' height=\'685\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%235f5b53\' font-family=\'Arial\' font-size=\'24\'%3EẢnh Hero%3C/text%3E%3C/svg%3E'"
                    />
                </div>

                <!-- Hero right -->
                <div class="content-block">
                    <h1 class="heading lv1">
                        Văn Phòng Phẩm Chất Lượng Cao
                    </h1>
                    <p class="desc">
                        Cung cấp đầy đủ các sản phẩm văn phòng phẩm từ bút, giấy, đến các thiết bị văn phòng hiện đại. 
                        Đáp ứng mọi nhu cầu làm việc của bạn với chất lượng tốt nhất và giá cả hợp lý.
                    </p>
                    <div class="cta-group">
                        <a href="#product" class="btn hero-cta">Xem Sản Phẩm</a>
                    </div>
                    <p class="desc">Tương tác gần đây</p>
                    <p class="desc stats">
                        <strong><?php echo count($productsArray) > 0 ? count($productsArray) : '0'; ?>+</strong> Sản phẩm
                        <strong>1000+</strong> Khách hàng
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Products -->
    <div class="popular" id="product">
        <div class="main-content">
            <div class="popular-top">
                <div class="info">
                    <h2 class="heading lv2">Sản Phẩm Nổi Bật</h2>
                    <p class="desc">Văn phòng phẩm đa dạng, chất lượng cao</p>
                </div>
            </div>

            <div class="course-list">
                <?php if (count($productsArray) > 0): ?>
                    <?php foreach ($productsArray as $product): ?>
                        <div class="course-item">
                            <a href="<?php echo $baseUrl; ?>/sanpham.php?id=<?php echo $product['id']; ?>">
                                <?php if ($product['hinh_anh']): ?>
                                    <img 
                                        src="<?php echo $baseUrl; ?>/<?php echo htmlspecialchars($product['hinh_anh']); ?>" 
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
                                        <a href="<?php echo $baseUrl; ?>/sanpham.php?id=<?php echo $product['id']; ?>" class="line-clamp break-all">
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
                                <div class="foot" style="align-items: flex-end;">
                                    <?php if (isset($product['phan_tram_giam']) && $product['phan_tram_giam'] > 0): 
                                        $giaMoi = $product['gia_ban'] * (1 - $product['phan_tram_giam'] / 100);
                                    ?>
                                        <div style="display:flex; flex-direction:column; align-items: flex-start;">
                                            <span class="price" style="text-decoration: line-through; color: #999; font-size: 1.4rem; font-weight: 400;">
                                                <?php echo number_format($product['gia_ban'], 0, ',', '.'); ?> đ
                                            </span>
                                            <span class="price" style="color: #d84315;">
                                                <?php echo number_format($giaMoi, 0, ',', '.'); ?> đ
                                                <span style="font-size:1.2rem; background:#ffebee; color:#c62828; padding:2px 6px; border-radius:4px; margin-left: 5px;">-<?php echo $product['phan_tram_giam']; ?>%</span>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span class="price"><?php echo number_format($product['gia_ban'], 0, ',', '.'); ?> đ</span>
                                    <?php endif; ?>

                                    <a href="<?php echo $baseUrl; ?>/sanpham.php?id=<?php echo $product['id']; ?>" class="btn book-btn">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                        <p class="desc">Chưa có sản phẩm nào.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/views/layout/footer.php'; ?>

