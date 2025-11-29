<?php
require_once __DIR__ . '/utils/session.php';

$pageTitle = 'Đặt hàng custom';
requireLogin();

include __DIR__ . '/views/layout/header.php';
?>

<div class="main-content">
    <div class="placeholder-container" style="text-align: center; padding: 100px 20px;">
        <i class="fa-solid fa-tools" style="font-size: 8rem; color: #e2dfda; margin-bottom: 30px;"></i>
        <h2 style="font-size: 3rem; color: #171100; margin-bottom: 20px;">Đặt hàng custom</h2>
        <p style="font-size: 1.8rem; color: #5f5b53; margin-bottom: 30px;">
            Tính năng đang được phát triển. Vui lòng quay lại sau!
        </p>
        <a href="<?php echo getBaseUrl(); ?>/sanpham.php" class="btn" style="display: inline-block; padding: 15px 30px; background: var(--primary-color); color: #fff; border-radius: 8px; font-size: 1.6rem; text-decoration: none; transition: background 0.3s;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại sản phẩm
        </a>
    </div>
</div>

<?php include __DIR__ . '/views/layout/footer.php'; ?>

