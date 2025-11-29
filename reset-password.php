<?php
require_once __DIR__ . '/utils/session.php';
require_once __DIR__ . '/controllers/AuthController.php';

$pageTitle = 'Đặt lại mật khẩu';
$error = null;
$result = null;
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header("Location: " . getBaseUrl() . "/login.php");
    exit();
}

if (isLoggedIn()) {
    $baseUrl = getBaseUrl();
    header("Location: " . $baseUrl . "/index.php");
    exit();
}

$authController = new AuthController();

// Xử lý form reset mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $authController->resetPassword();
    
    if (is_array($result) && isset($result['success']) && $result['success']) {
        // Thành công - redirect về login với thông báo
        header("Location: " . getBaseUrl() . "/login.php?reset_success=1");
        exit();
    } else {
        // Lỗi
        $error = $result;
    }
}

$baseUrl = getBaseUrl();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đặt lại mật khẩu - Cửa hàng văn phòng phẩm</title>
    
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
</head>
<body>
    <div class="blur-bg-overlay">
        <div class="container">
            <div class="form-container sign-in" style="width: 100%; max-width: 450px; margin: 0 auto;">
                <form method="post" action="<?php echo $baseUrl; ?>/reset-password.php?token=<?php echo htmlspecialchars($token); ?>">
                    <img src="<?php echo $baseUrl; ?>/assets/img/logo.svg" alt="logo" style="height: 40px;">
                    <h1>Đặt lại mật khẩu</h1>
                    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
                        Nhập mật khẩu mới của bạn.
                    </p>
                    
                    <?php if ($error): ?>
                        <div style="color: red; font-size: 12px; margin: 10px 0; background: #ffe6e6; padding: 10px; border-radius: 5px;">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <input type="password" placeholder="Mật khẩu mới" name="mat_khau_moi" required minlength="6">
                    <input type="password" placeholder="Xác nhận mật khẩu mới" name="xac_nhan_mat_khau" required minlength="6">
                    
                    <button type="submit">Đặt lại mật khẩu</button>
                    
                    <a href="<?php echo $baseUrl; ?>/login.php" style="display: inline-block; margin-top: 20px; color: #512da8; font-size: 14px; text-decoration: none;">
                        <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
                    </a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

