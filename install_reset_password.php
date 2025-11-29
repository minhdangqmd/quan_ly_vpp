<?php
/**
 * Script để cài đặt chức năng quên mật khẩu
 * Chạy file này một lần để thêm các cột cần thiết vào database
 */

require_once __DIR__ . '/config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Kiểm tra xem cột đã tồn tại chưa
    $check = $conn->query("SHOW COLUMNS FROM taikhoan LIKE 'reset_token'");
    
    if ($check->rowCount() > 0) {
        echo "✓ Các cột cho chức năng quên mật khẩu đã tồn tại trong database.<br>";
        echo "<a href='login.php'>Quay lại trang đăng nhập</a>";
        exit;
    }
    
    // Thêm cột reset_token và reset_token_expiry
    $sql = "ALTER TABLE taikhoan 
            ADD COLUMN reset_token VARCHAR(100) NULL DEFAULT NULL AFTER ngay_tao,
            ADD COLUMN reset_token_expiry DATETIME NULL DEFAULT NULL AFTER reset_token";
    
    $conn->exec($sql);
    
    echo "<h2>✓ Cài đặt thành công!</h2>";
    echo "<p>Đã thêm các cột cần thiết cho chức năng quên mật khẩu vào database.</p>";
    echo "<ul>";
    echo "<li>Cột <strong>reset_token</strong> - Lưu token reset password</li>";
    echo "<li>Cột <strong>reset_token_expiry</strong> - Lưu thời gian hết hạn của token</li>";
    echo "</ul>";
    echo "<p><a href='login.php'>Quay lại trang đăng nhập</a></p>";
    echo "<p style='color: #999; font-size: 12px;'>Bạn có thể xóa file install_reset_password.php này sau khi cài đặt.</p>";
    
} catch(PDOException $e) {
    echo "<h2>✗ Lỗi khi cài đặt:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<p>Vui lòng chạy SQL sau trong phpMyAdmin:</p>";
    echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
    echo "ALTER TABLE taikhoan \n";
    echo "ADD COLUMN reset_token VARCHAR(100) NULL DEFAULT NULL AFTER ngay_tao,\n";
    echo "ADD COLUMN reset_token_expiry DATETIME NULL DEFAULT NULL AFTER reset_token;";
    echo "</pre>";
}
?>

