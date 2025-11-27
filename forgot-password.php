<?php
require_once __DIR__ . '/utils/session.php';
require_once __DIR__ . '/controllers/AuthController.php';

$pageTitle = 'Quên mật khẩu';
$error = null;
$result = null;

if (isLoggedIn()) {
    $baseUrl = getBaseUrl();
    header("Location: " . $baseUrl . "/index.php");
    exit();
}

$authController = new AuthController();

// Xử lý form quên mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $authController->quenMatKhau();
    
    if (is_array($result) && isset($result['success']) && $result['success']) {
        // Thành công
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
    <title>Quên mật khẩu - Cửa hàng văn phòng phẩm</title>
    
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
                <?php if (is_array($result) && isset($result['success']) && $result['success']): ?>
                    <!-- Hiển thị thông báo thành công và link reset -->
                    <div style="text-align: center;">
                        <img src="<?php echo $baseUrl; ?>/assets/img/logo.svg" alt="logo" style="height: 40px; margin-bottom: 20px;">
                        <h1 style="margin-bottom: 20px;">Email đã được gửi</h1>
                        <p style="color: #666; margin-bottom: 20px;">
                            Nếu email <strong><?php echo htmlspecialchars($result['email']); ?></strong> tồn tại trong hệ thống, 
                            một link reset mật khẩu đã được gửi đến email của bạn.
                        </p>
                        
                        <?php if ($result['token']): ?>
                            <!-- Chỉ hiển thị trong môi trường dev/local -->
                            <div style="background: #f0f0f0; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: left;">
                                <p style="font-size: 12px; color: #666; margin-bottom: 10px;">
                                    <strong>Chế độ Development:</strong> Link reset password của bạn:
                                </p>
                                <a href="<?php echo $baseUrl; ?>/reset-password.php?token=<?php echo $result['token']; ?>" 
                                   style="word-break: break-all; font-size: 12px; color: #512da8;">
                                    <?php echo $baseUrl; ?>/reset-password.php?token=<?php echo $result['token']; ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <a href="<?php echo $baseUrl; ?>/dangNhap.php" style="display: inline-block; margin-top: 20px; color: #512da8; text-decoration: none;">
                            <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Form nhập email -->
                    <form method="post" action="<?php echo $baseUrl; ?>/forgot-password.php">
                        <img src="<?php echo $baseUrl; ?>/assets/img/logo.svg" alt="logo" style="height: 40px;">
                        <h1>Quên mật khẩu</h1>
                        <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
                            Nhập email của bạn và chúng tôi sẽ gửi link để reset mật khẩu.
                        </p>
                        
                        <?php if ($error): ?>
                            <div style="color: red; font-size: 12px; margin: 10px 0; background: #ffe6e6; padding: 10px; border-radius: 5px;">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <input type="email" placeholder="Email" name="email" required>
                        <button type="submit">Gửi link reset mật khẩu</button>
                        
                        <a href="<?php echo $baseUrl; ?>/dangNhap.php" style="display: inline-block; margin-top: 20px; color: #512da8; font-size: 14px; text-decoration: none;">
                            <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
                        </a>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

