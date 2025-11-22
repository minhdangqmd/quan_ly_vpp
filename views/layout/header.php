<?php
if (!function_exists('getBaseUrl')) {
    require_once __DIR__ . '/../../utils/session.php';
}
$baseUrl = getBaseUrl();
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Cửa hàng văn phòng phẩm'; ?></title>
    
    <!-- Reset CSS -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/reset.css" />
    
    <!-- Embed fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sen:wght@700&display=swap"
        rel="stylesheet"
    />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/styles.css" />
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/profile.css">
    
    <!-- JS -->
    <script src="<?php echo $baseUrl; ?>/assets/js/profile.js" defer></script>
</head>
<body>
    <!-- Header -->
    <header class="header fixed">
        <div class="main-content">
            <div class="body">
                <!-- Logo -->
                <a href="<?php echo $baseUrl; ?>/index.php">
                    <img
                        src="<?php echo $baseUrl; ?>/assets/img/logo.svg"
                        alt="Logo"
                        class="logo"
                        style="height: 40px;"
                    />
                </a>
                
                <!-- Nav -->
                <nav class="nav">
                    <ul>
                        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                            <a href="<?php echo $baseUrl; ?>/index.php">Home</a>
                        </li>
                        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'sanpham.php') ? 'active' : ''; ?>">
                            <a href="<?php echo $baseUrl; ?>/sanpham.php">Sản phẩm</a>
                        </li>
                        <?php if (isLoggedIn()): ?>
                            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'donhang.php') ? 'active' : ''; ?>">
                                <a href="<?php echo $baseUrl; ?>/donhang.php">Đơn hàng</a>
                            </li>
                            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'yeucaucustom.php') ? 'active' : ''; ?>">
                                <a href="<?php echo $baseUrl; ?>/yeucaucustom.php">Đặt hàng custom</a>
                            </li>
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'Admin'): ?>
                                <li class="<?php echo (strpos($_SERVER['PHP_SELF'], 'admin') !== false) ? 'active' : ''; ?>">
                                    <a href="<?php echo $baseUrl; ?>/admin/sanpham.php">Quản lý</a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </nav>
                
                <!-- Action -->
                <?php if (isLoggedIn()): ?>
                    <div class="profile-dropdown">
                        <div onclick="toggle()" class="profile-dropdown-btn">
                            <div class="profile-img">
                                <i class="fa-solid fa-circle"></i>
                            </div>
                            <span>
                                <?php echo htmlspecialchars($currentUser['ten_dang_nhap']); ?>
                                <i class="fa-solid fa-angle-down"></i>
                            </span>
                        </div>
                        
                        <ul class="profile-dropdown-list">
                            <li class="profile-dropdown-list-item">
                                <a href="<?php echo $baseUrl; ?>/profile.php">
                                    <i class="fa-regular fa-user"></i>
                                    Thông tin cá nhân
                                </a>
                            </li>
                            
                            <li class="profile-dropdown-list-item">
                                <a href="<?php echo $baseUrl; ?>/giohang.php">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    Giỏ hàng
                                </a>
                            </li>
                            
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'Admin'): ?>
                                <li class="profile-dropdown-list-item">
                                    <a href="<?php echo $baseUrl; ?>/admin/sanpham.php">
                                        <i class="fa-solid fa-sliders"></i>
                                        Quản lý sản phẩm
                                    </a>
                                </a>
                            </li>
                            
                            
                                <li class="profile-dropdown-list-item">
                                    <a href="<?php echo $baseUrl; ?>/admin/baocaothongke.php">
                                        <i class="fa-solid fa-sliders"></i>
                                        Báo cáo thống kê
                                    </a>
                                </li>
                                <li class="profile-dropdown-list-item">
                                    <a href="<?php echo $baseUrl; ?>/admin/donhang.php">
                                        <i class="fa-regular fa-circle-question"></i>
                                        Quản lý đơn hàng
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <hr />
                            
                            <li class="profile-dropdown-list-item">
                                <a href="<?php echo $baseUrl; ?>/logout.php">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="action">
                        <a href="<?php echo $baseUrl; ?>/login.php" class="btn sign-up-btn">Đăng nhập</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <main>


