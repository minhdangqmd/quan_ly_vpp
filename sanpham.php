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
    <style>
        .product-detail-container {
            padding: 40px 0;
            margin-top: 80px;
        }
        .product-detail-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        .product-image-section {
            position: sticky;
            top: 100px;
            height: fit-content;
        }
        .product-image-main {
            width: 100%;
            aspect-ratio: 1;
            object-fit: contain;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .product-image-placeholder {
            width: 100%;
            aspect-ratio: 1;
            background: #f5f5f5;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 48px;
        }
        .product-info-section {
            padding: 20px 0;
        }
        .product-title {
            font-size: 3.2rem;
            font-weight: 700;
            color: #171100;
            margin-bottom: 20px;
            line-height: 1.3;
        }
        .product-price {
            font-size: 3.6rem;
            font-weight: 700;
            color: #ffb900;
            margin-bottom: 20px;
        }
        .product-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #eee;
        }
        .product-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #5f5b53;
            font-size: 1.4rem;
        }
        .product-meta-item i {
            color: #ffb900;
        }
        .product-meta-item strong {
            color: #171100;
            margin-right: 5px;
        }
        .product-description {
            margin-bottom: 30px;
        }
        .product-description h3 {
            font-size: 2rem;
            font-weight: 600;
            color: #171100;
            margin-bottom: 15px;
        }
        .product-description p {
            font-size: 1.6rem;
            line-height: 1.75;
            color: #5f5b53;
            white-space: pre-wrap;
        }
        .product-actions {
            background: #fffcf4;
            padding: 30px;
            border-radius: 12px;
            margin-top: 30px;
        }
        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        .quantity-selector label {
            font-size: 1.6rem;
            font-weight: 600;
            color: #171100;
            min-width: 100px;
        }
        .quantity-input {
            display: flex;
            align-items: center;
            border: 2px solid #e2dfda;
            border-radius: 8px;
            overflow: hidden;
            width: 150px;
        }
        .quantity-input button {
            background: #fff;
            border: none;
            padding: 12px 16px;
            cursor: pointer;
            font-size: 1.8rem;
            color: #171100;
            transition: background 0.2s;
        }
        .quantity-input button:hover {
            background: #f5f5f5;
        }
        .quantity-input input {
            border: none;
            width: 60px;
            text-align: center;
            font-size: 1.6rem;
            font-weight: 600;
            padding: 12px 0;
        }
        .quantity-input input:focus {
            outline: none;
        }
        .stock-status {
            margin-bottom: 20px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 1.4rem;
        }
        .stock-status.in-stock {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .stock-status.out-of-stock {
            background: #ffebee;
            color: #c62828;
        }
        .add-to-cart-btn {
            width: 100%;
            padding: 18px;
            background: #ffb900;
            color: #171100;
            border: none;
            border-radius: 8px;
            font-size: 1.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .add-to-cart-btn:hover {
            background: #ffa000;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 185, 0, 0.3);
        }
        .add-to-cart-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        .login-prompt {
            text-align: center;
            padding: 30px;
            background: #fffcf4;
            border-radius: 12px;
        }
        .login-prompt p {
            font-size: 1.6rem;
            color: #5f5b53;
            margin-bottom: 20px;
        }
        .breadcrumb {
            margin-bottom: 30px;
            font-size: 1.4rem;
            color: #5f5b53;
        }
        .breadcrumb a {
            color: #5f5b53;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            color: #ffb900;
        }
        .breadcrumb span {
            margin: 0 10px;
        }
        @media (max-width: 768px) {
            .product-detail-wrapper {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .product-image-section {
                position: static;
            }
            .product-title {
                font-size: 2.4rem;
            }
            .product-price {
                font-size: 2.8rem;
            }
        }
    </style>
    
    <div class="main-content product-detail-container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="<?php echo getBaseUrl(); ?>/index.php">Trang chủ</a>
            <span>/</span>
            <a href="<?php echo getBaseUrl(); ?>/sanpham.php">Sản phẩm</a>
            <?php if ($product->danhMuc): ?>
                <span>/</span>
                <a href="<?php echo getBaseUrl(); ?>/sanpham.php?danh_muc=<?php echo $product->danhMuc->id; ?>">
                    <?php echo htmlspecialchars($product->danhMuc->ten_danh_muc); ?>
                </a>
            <?php endif; ?>
            <span>/</span>
            <span><?php echo htmlspecialchars($product->ten_san_pham); ?></span>
        </div>

        <div class="product-detail-wrapper">
            <!-- Product Image -->
            <div class="product-image-section">
                <?php if ($product->hinh_anh): ?>
                    <img 
                        src="<?php echo getBaseUrl(); ?>/<?php echo htmlspecialchars($product->hinh_anh); ?>" 
                        alt="<?php echo htmlspecialchars($product->ten_san_pham); ?>"
                        class="product-image-main"
                    />
                <?php else: ?>
                    <div class="product-image-placeholder">
                        <i class="fa-solid fa-image"></i>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div class="product-info-section">
                <h1 class="product-title"><?php echo htmlspecialchars($product->ten_san_pham); ?></h1>
                
                <div class="product-price">
                    <?php echo number_format($product->gia_ban, 0, ',', '.'); ?> đ
                </div>

                <div class="product-meta">
                    <?php if ($product->danhMuc): ?>
                        <div class="product-meta-item">
                            <i class="fa-solid fa-tag"></i>
                            <strong>Danh mục:</strong>
                            <a href="<?php echo getBaseUrl(); ?>/sanpham.php?danh_muc=<?php echo $product->danhMuc->id; ?>" style="color: #5f5b53; text-decoration: none;">
                                <?php echo htmlspecialchars($product->danhMuc->ten_danh_muc); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($product->nhaCungCap): ?>
                        <div class="product-meta-item">
                            <i class="fa-solid fa-truck"></i>
                            <strong>Nhà cung cấp:</strong>
                            <span><?php echo htmlspecialchars($product->nhaCungCap->ten_nha_cung_cap); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="product-meta-item">
                        <i class="fa-solid fa-box"></i>
                        <strong>Mã sản phẩm:</strong>
                        <span><?php echo htmlspecialchars($product->id); ?></span>
                    </div>
                    
                    <?php if ($product->han_su_dung): ?>
                        <div class="product-meta-item">
                            <i class="fa-solid fa-calendar"></i>
                            <strong>Hạn sử dụng:</strong>
                            <span><?php echo date('d/m/Y', strtotime($product->han_su_dung)); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($product->mo_ta): ?>
                    <div class="product-description">
                        <h3>Mô tả sản phẩm</h3>
                        <p><?php echo nl2br(htmlspecialchars($product->mo_ta)); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Product Actions -->
                <div class="product-actions">
                    <?php if ($product->so_luong_ton > 0): ?>
                        <div class="stock-status in-stock">
                            <i class="fa-solid fa-check-circle"></i>
                            Còn hàng (<?php echo $product->so_luong_ton; ?> sản phẩm)
                        </div>
                    <?php else: ?>
                        <div class="stock-status out-of-stock">
                            <i class="fa-solid fa-times-circle"></i>
                            Hết hàng
                        </div>
                    <?php endif; ?>

                    <?php if (isLoggedIn()): ?>
                        <form method="POST" action="<?php echo getBaseUrl(); ?>/giohang.php?action=add">
                            <input type="hidden" name="id_san_pham" value="<?php echo $product->id; ?>">
                            
                            <div class="quantity-selector">
                                <label for="so_luong">Số lượng:</label>
                                <div class="quantity-input">
                                    <button type="button" onclick="decreaseQuantity()">-</button>
                                    <input 
                                        type="number" 
                                        id="so_luong" 
                                        name="so_luong" 
                                        value="1" 
                                        min="1" 
                                        max="<?php echo $product->so_luong_ton; ?>" 
                                        required
                                        onchange="validateQuantity()"
                                    >
                                    <button type="button" onclick="increaseQuantity()">+</button>
                                </div>
                            </div>
                            
                            <button 
                                type="submit" 
                                class="add-to-cart-btn"
                                <?php echo $product->so_luong_ton <= 0 ? 'disabled' : ''; ?>
                            >
                                <i class="fa-solid fa-cart-shopping"></i>
                                Thêm vào giỏ hàng
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="login-prompt">
                            <p>Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng</p>
                            <a href="<?php echo getBaseUrl(); ?>/login.php" class="btn" style="min-width: 200px;">
                                Đăng nhập
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function increaseQuantity() {
            const input = document.getElementById('so_luong');
            const max = parseInt(input.getAttribute('max'));
            const current = parseInt(input.value) || 1;
            if (current < max) {
                input.value = current + 1;
            }
        }

        function decreaseQuantity() {
            const input = document.getElementById('so_luong');
            const current = parseInt(input.value) || 1;
            if (current > 1) {
                input.value = current - 1;
            }
        }

        function validateQuantity() {
            const input = document.getElementById('so_luong');
            const max = parseInt(input.getAttribute('max'));
            const min = parseInt(input.getAttribute('min'));
            let value = parseInt(input.value) || 1;
            
            if (value < min) value = min;
            if (value > max) value = max;
            
            input.value = value;
        }
    </script>
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

