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
                        <li class="dropdown <?php echo (basename($_SERVER['PHP_SELF']) == 'sanpham.php' && isset($_GET['danh_muc'])) ? 'active' : ''; ?>">
                            <a href="#" class="dropdown-toggle">Danh mục <i class="fa-solid fa-angle-down"></i></a>
                            <ul class="dropdown-menu">
                                <?php
                                require_once __DIR__ . '/../../config/database.php';
                                require_once __DIR__ . '/../../models/DanhMucSanPham.php';
                                $db = new Database();
                                $conn = $db->getConnection();
                                $danhMucObj = new DanhMucSanPham($conn);
                                $danhMucs = $danhMucObj->docTatCa();
                                while ($dm = $danhMucs->fetch(PDO::FETCH_ASSOC)):
                                ?>
                                    <li><a href="<?php echo $baseUrl; ?>/sanpham.php?danh_muc=<?php echo $dm['id']; ?>"><?php echo htmlspecialchars($dm['ten_danh_muc']); ?></a></li>
                                <?php endwhile; ?>
                            </ul>
                        </li>
                        <?php if (isLoggedIn()): ?>
                            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'donhang.php') ? 'active' : ''; ?>">
                                <a href="<?php echo $baseUrl; ?>/donhang.php">Đơn hàng</a>
                            </li>
                            <li>
                                <a href="https://docs.google.com/forms/d/e/1FAIpQLSdS3hvyx6UZAha0QOgh1h7Y7zI4vo7Ow21rn2wcnPHSRKAzJA/viewform" target="_blank">Đặt hàng custom</a>
                            </li>
                            <li>
                                <a href="<?php echo $baseUrl; ?>/giohang.php">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    Giỏ hàng
                                </a>
                            </li>
                            
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
                            
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'Admin'): ?>
                                <li class="profile-dropdown-list-item">
                                    <a href="<?php echo $baseUrl; ?>/admin/index.php">
                                        <i class="fa-solid fa-gauge"></i>
                                        Dashboard
                                    </a>
                                </li>
                                <li class="profile-dropdown-list-item">
                                    <a href="<?php echo $baseUrl; ?>/admin/baocao.php">
                                        <i class="fa-solid fa-chart-line"></i>
                                        Báo cáo thống kê
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
    <?php
    // Check if we're in admin area
    $isAdminPage = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
    $currentAdminPage = basename($_SERVER['PHP_SELF']);
    ?>
    
    <?php if ($isAdminPage && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'Admin'): ?>
        <style>
            .admin-layout {
                display: flex;
                min-height: calc(100vh - 80px);
                margin-top: 80px;
            }
            .admin-sidebar {
                width: 260px;
                background: #fff;
                box-shadow: 2px 0 5px rgba(0,0,0,0.05);
                padding: 20px 0;
                position: fixed;
                left: 0;
                top: 90px;
                height: calc(100vh - 80px);
                overflow-y: auto;
                z-index: 100;
            }
            .admin-sidebar .sidebar-header {
                padding: 0 20px 20px;
                border-bottom: 1px solid #eee;
                margin-bottom: 20px;
            }
            .admin-sidebar .sidebar-header h3 {
                margin: 0;
                font-size: 18px;
                font-weight: 600;
                color: #333;
            }
            .admin-sidebar .menu-item {
                display: block;
                padding: 12px 20px;
                color: #666;
                text-decoration: none;
                transition: all 0.2s;
                border-left: 3px solid transparent;
            }
            .admin-sidebar .menu-item:hover {
                background: #f5f5f5;
                color: #1976d2;
                border-left-color: #1976d2;
            }
            .admin-sidebar .menu-item.active {
                background: #e3f2fd;
                color: #1976d2;
                border-left-color: #1976d2;
                font-weight: 600;
            }
            .admin-sidebar .menu-item i {
                width: 20px;
                margin-right: 10px;
                text-align: center;
            }
            .admin-content-wrapper {
                margin-left: 260px;
                flex: 1;
                padding: 20px;
            }
            .admin-content-wrapper .main-content {
                width: 100%;
                max-width: 100%;
            }
            @media (max-width: 768px) {
                .admin-sidebar {
                    transform: translateX(-100%);
                    transition: transform 0.3s;
                    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
                }
                .admin-sidebar.open {
                    transform: translateX(0);
                }
                .admin-content-wrapper {
                    margin-left: 0;
                }
                .sidebar-toggle {
                    display: block;
                    position: fixed;
                    top: 90px;
                    left: 10px;
                    z-index: 101;
                    background: #1976d2;
                    color: white;
                    border: none;
                    padding: 10px 15px;
                    border-radius: 5px;
                    cursor: pointer;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                }
                .sidebar-overlay {
                    display: none;
                    position: fixed;
                    top: 80px;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0,0,0,0.5);
                    z-index: 99;
                }
                .sidebar-overlay.active {
                    display: block;
                }
            }
            @media (min-width: 769px) {
                .sidebar-toggle {
                    display: none;
                }
            }
        </style>
        <div class="admin-layout">
            <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <aside class="admin-sidebar" id="adminSidebar">
                <div class="sidebar-header">
                    <h3><i class="fa-solid fa-bars"></i> Menu quản lý</h3>
                </div>
                <nav class="sidebar-nav">
                    <a href="<?php echo $baseUrl; ?>/admin/index.php" class="menu-item <?php echo $currentAdminPage == 'index.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-gauge"></i>
                        Dashboard
                    </a>
                    <a href="<?php echo $baseUrl; ?>/admin/sanpham.php" class="menu-item <?php echo $currentAdminPage == 'sanpham.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-box"></i>
                        Sản phẩm
                    </a>
                    <a href="<?php echo $baseUrl; ?>/admin/danhmuc.php" class="menu-item <?php echo $currentAdminPage == 'danhmuc.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-folder"></i>
                        Danh mục
                    </a>
                    <a href="<?php echo $baseUrl; ?>/admin/donhang.php" class="menu-item <?php echo $currentAdminPage == 'donhang.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-cart-shopping"></i>
                        Đơn hàng
                    </a>
                    <a href="<?php echo $baseUrl; ?>/admin/khachhang.php" class="menu-item <?php echo $currentAdminPage == 'khachhang.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-users"></i>
                        Khách hàng
                    </a>
                    <a href="<?php echo $baseUrl; ?>/admin/nhacungcap.php" class="menu-item <?php echo $currentAdminPage == 'nhacungcap.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-truck"></i>
                        Nhà cung cấp
                    </a>
                    <a href="<?php echo $baseUrl; ?>/admin/phieunhap.php" class="menu-item <?php echo $currentAdminPage == 'phieunhap.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-file-import"></i>
                        Phiếu nhập
                    </a>
                    <a href="<?php echo $baseUrl; ?>/admin/baocao.php" class="menu-item <?php echo $currentAdminPage == 'baocao.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-chart-line"></i>
                        Báo cáo thống kê
                    </a>
                </nav>
            </aside>
            <script>
                function toggleSidebar() {
                    const sidebar = document.getElementById('adminSidebar');
                    const overlay = document.getElementById('sidebarOverlay');
                    sidebar.classList.toggle('open');
                    if (overlay) {
                        overlay.classList.toggle('active');
                    }
                }
            </script>
            <div class="admin-content-wrapper">
    <?php endif; ?>

