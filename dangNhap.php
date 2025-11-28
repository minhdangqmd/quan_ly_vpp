<?php
require_once __DIR__ . '/utils/session.php';
require_once __DIR__ . '/controllers/AuthController.php';

$pageTitle = 'Đăng nhập';
$error = null;
$register_error = null;

if (isLoggedIn()) {
    $baseUrl = getBaseUrl();
    header("Location: " . $baseUrl . "/index.php");
    exit();
}

$authController = new AuthController();

// Kiểm tra error từ URL (redirect từ dangky.php)
if (isset($_GET['register_error'])) {
    $register_error = $_GET['register_error'];
}

// Xử lý đăng ký
if (isset($_POST['register'])) {
    $register_error = $authController->dangKy();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Xử lý đăng nhập
    $error = $authController->dangNhap();
}

$baseUrl = getBaseUrl();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng nhập - Cửa hàng văn phòng phẩm</title>
    
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
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/login.css">
    
    <!-- JS -->
    <script src="<?php echo $baseUrl; ?>/assets/js/login.js" defer></script>
    <?php if ($register_error || isset($_GET['register_error'])): ?>
    <script>
        // Tự động mở panel đăng ký khi có lỗi
        window.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('container');
            if (container) {
                container.classList.add('active');
            }
        });
    </script>
    <?php endif; ?>
</head>
<body>
    <!-- Login -->
    <div class="blur-bg-overlay">
        <div class="container" id="container">
            <div class="form-container sign-in">
                <form method="post" action="<?php echo $baseUrl; ?>/dangNhap.php">
                    <img src="<?php echo $baseUrl; ?>/assets/img/logo.svg" alt="logo" style="height: 40px;">
                    <h1>Đăng nhập</h1>
                    
                    
                    <?php if ($error): ?>
                        <div style="color: red; font-size: 12px; margin: 10px 0;"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['success'])): ?>
                        <div style="color: green; font-size: 12px; margin: 10px 0;">Đăng ký thành công! Vui lòng đăng nhập.</div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['reset_success'])): ?>
                        <div style="color: green; font-size: 12px; margin: 10px 0; background: #e6ffe6; padding: 10px; border-radius: 5px;">
                            Đặt lại mật khẩu thành công! Vui lòng đăng nhập với mật khẩu mới.
                        </div>
                    <?php endif; ?>
                    
                    <input type="text" placeholder="Tên đăng nhập" name="ten_dang_nhap" required>
                    <input type="password" placeholder="Mật khẩu" name="mat_khau" required>
                    <a href="<?php echo $baseUrl; ?>/forgot-password.php" style="font-size: 12px; margin: 10px 0;">Quên mật khẩu?</a>
                    <button type="submit">Đăng nhập</button>
                </form>
            </div>
            
            <div class="form-container sign-up">
                <form action="<?php echo $baseUrl; ?>/dangNhap.php" method="post">
                    <img src="<?php echo $baseUrl; ?>/assets/img/logo.svg" alt="logo" style="height: 40px;">
                    <h1>Đăng ký</h1>
                    
                    
                    
                    <?php if ($register_error): ?>
                        <div style="color: red; font-size: 12px; margin: 10px 0;"><?php echo htmlspecialchars($register_error); ?></div>
                    <?php endif; ?>
                    
                    <input type="email" placeholder="Email" name="email" required>
                    <input type="text" placeholder="Tên đăng nhập" name="ten_dang_nhap" required>
                    <input type="password" placeholder="Mật khẩu" name="mat_khau" required>
                    <input type="text" placeholder="Họ và tên" name="ho_ten" required>
                    <input type="tel" placeholder="Số điện thoại" name="so_dien_thoai">
                    <input type="text" placeholder="Địa chỉ" name="dia_chi">
                    <button type="submit" name="register">Đăng ký</button>
                </form>
            </div>
            
            <div class="toggle-container">
                <div class="toggle">
                    <div class="toggle-panel toggle-left">
                        <h1>Chào mừng trở lại!</h1>
                        <p>Nhập thông tin cá nhân của bạn để sử dụng tất cả các tính năng của trang web</p>
                        <button class="hidden" id="login">Đăng nhập</button>
                    </div>
                    <div class="toggle-panel toggle-right">
                        <h1>Xin chào!</h1>
                        <p>Đăng ký với thông tin cá nhân của bạn để sử dụng tất cả các tính năng của trang web</p>
                        <button class="hidden" id="register">Đăng ký</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

