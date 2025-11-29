<?php
require_once __DIR__ . '/utils/session.php';
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Thông tin cá nhân';
requireLogin();

$database = new Database();
$conn = $database->getConnection();
$user = getCurrentUser();

// Check and create table if not exists
try {
    $checkTable = "SHOW TABLES LIKE 'nguoi_dung_chi_tiet'";
    $stmt = $conn->query($checkTable);
    if ($stmt->rowCount() == 0) {
        // Create table
        $createTable = "CREATE TABLE `nguoi_dung_chi_tiet` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_taikhoan` int(11) NOT NULL,
            `ho_ten` varchar(255) DEFAULT NULL,
            `dien_thoai` varchar(50) DEFAULT NULL,
            `dia_chi` text DEFAULT NULL,
            `avatar` varchar(255) DEFAULT NULL,
            `ngay_cap_nhat` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `id_taikhoan` (`id_taikhoan`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $conn->exec($createTable);
    }
} catch (PDOException $e) {
    // Table might already exist, continue
}

// Get user details
$query = "SELECT t.*, n.ho_ten, n.dien_thoai, n.dia_chi, n.avatar 
          FROM taikhoan t 
          LEFT JOIN nguoi_dung_chi_tiet n ON n.id_taikhoan = t.id 
          WHERE t.id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $user['id']);
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

$thongBao = '';
$thongBaoType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['luu_thong_tin'])) {
    $hoTen = trim($_POST['ho_ten'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dienThoai = trim($_POST['dien_thoai'] ?? '');
    $diaChi = trim($_POST['dia_chi'] ?? '');
    $avatarPath = $userData['avatar'] ?? null;
    
    // Handle avatar upload
    if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === 0) {
        $uploadDir = __DIR__ . '/uploads/avatars';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed = ['png', 'jpg', 'jpeg', 'gif'];
        
        if (in_array($ext, $allowed)) {
            $filename = 'user_' . $user['id'] . '_' . time() . '.' . $ext;
            $dest = $uploadDir . '/' . $filename;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                $avatarPath = 'uploads/avatars/' . $filename;
            }
        }
    }
    
    try {
        // Update email in taikhoan
        if ($email !== $userData['email']) {
            $updateEmail = "UPDATE taikhoan SET email = :email WHERE id = :id";
            $stmt = $conn->prepare($updateEmail);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $user['id']);
            $stmt->execute();
        }
        
        // Check if details exist
        $checkQuery = "SELECT id FROM nguoi_dung_chi_tiet WHERE id_taikhoan = :id";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(':id', $user['id']);
        $checkStmt->execute();
        $detailExists = $checkStmt->fetch();
        
        if ($detailExists) {
            // Update
            $updateQuery = "UPDATE nguoi_dung_chi_tiet 
                           SET ho_ten = :ho_ten, dien_thoai = :dien_thoai, 
                               dia_chi = :dia_chi, avatar = :avatar, ngay_cap_nhat = NOW() 
                           WHERE id_taikhoan = :id";
            $stmt = $conn->prepare($updateQuery);
        } else {
            // Insert
            $insertQuery = "INSERT INTO nguoi_dung_chi_tiet 
                           (id_taikhoan, ho_ten, dien_thoai, dia_chi, avatar, ngay_cap_nhat) 
                           VALUES (:id, :ho_ten, :dien_thoai, :dia_chi, :avatar, NOW())";
            $stmt = $conn->prepare($insertQuery);
        }
        
        $stmt->bindParam(':id', $user['id']);
        $stmt->bindParam(':ho_ten', $hoTen);
        $stmt->bindParam(':dien_thoai', $dienThoai);
        $stmt->bindParam(':dia_chi', $diaChi);
        $stmt->bindParam(':avatar', $avatarPath);
        $stmt->execute();
        
        // Reload data
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $user['id']);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $thongBao = 'Cập nhật thông tin thành công!';
        $thongBaoType = 'success';
    } catch (Exception $e) {
        $thongBao = 'Có lỗi xảy ra: ' . $e->getMessage();
        $thongBaoType = 'error';
    }
}

include __DIR__ . '/views/layout/header.php';
?>

<div class="main-content">
    <?php if ($thongBao): ?>
        <div class="alert-<?php echo $thongBaoType; ?>">
            <i class="fa-solid fa-<?php echo $thongBaoType === 'success' ? 'circle-check' : 'circle-exclamation'; ?>"></i>
            <?php echo htmlspecialchars($thongBao); ?>
        </div>
    <?php endif; ?>
    
    <div class="profile-container">
        <div class="profile-sidebar">
            <div class="profile-avatar-section">
                <?php if (!empty($userData['avatar'])): ?>
                    <img src="<?php echo getBaseUrl(); ?>/<?php echo htmlspecialchars($userData['avatar']); ?>" 
                         alt="Avatar" class="profile-avatar">
                <?php else: ?>
                    <div class="profile-avatar-placeholder">
                        <i class="fa-solid fa-user"></i>
                    </div>
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($userData['ten_dang_nhap'] ?? 'Người dùng'); ?></h3>
                <p><?php echo htmlspecialchars($userData['email'] ?? ''); ?></p>
            </div>
            
            <div class="profile-menu">
                <a href="#" class="profile-menu-item active">
                    <i class="fa-solid fa-user"></i> Thông tin cá nhân
                </a>
                <a href="<?php echo getBaseUrl(); ?>/donhang.php" class="profile-menu-item">
                    <i class="fa-solid fa-clipboard-list"></i> Đơn hàng của tôi
                </a>
                <a href="<?php echo getBaseUrl(); ?>/giohang.php" class="profile-menu-item">
                    <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng
                </a>
                <a href="<?php echo getBaseUrl(); ?>/dangXuat.php" class="profile-menu-item logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất
                </a>
            </div>
        </div>
        
        <div class="profile-main">
            <div class="profile-card">
                <h2><i class="fa-solid fa-user-pen"></i> Chỉnh sửa thông tin</h2>
                
                <form method="POST" enctype="multipart/form-data" class="profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="ho_ten">
                                <i class="fa-solid fa-user"></i> Họ và tên
                            </label>
                            <input type="text" id="ho_ten" name="ho_ten" 
                                   value="<?php echo htmlspecialchars($userData['ho_ten'] ?? ''); ?>"
                                   placeholder="Nhập họ và tên">
                        </div>
                        <div class="form-group">
                            <label for="email">
                                <i class="fa-solid fa-envelope"></i> Email
                            </label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>"
                                   placeholder="email@example.com" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="dien_thoai">
                                <i class="fa-solid fa-phone"></i> Số điện thoại
                            </label>
                            <input type="tel" id="dien_thoai" name="dien_thoai" 
                                   value="<?php echo htmlspecialchars($userData['dien_thoai'] ?? ''); ?>"
                                   placeholder="0123456789">
                        </div>
                        <div class="form-group">
                            <label for="avatar">
                                <i class="fa-solid fa-image"></i> Ảnh đại diện
                            </label>
                            <input type="file" id="avatar" name="avatar" accept="image/*">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="dia_chi">
                            <i class="fa-solid fa-location-dot"></i> Địa chỉ
                        </label>
                        <textarea id="dia_chi" name="dia_chi" rows="3" 
                                  placeholder="Nhập địa chỉ của bạn"><?php echo htmlspecialchars($userData['dia_chi'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="luu_thong_tin" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
                        </button>
                        <a href="<?php echo getBaseUrl(); ?>/index.php" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/views/layout/footer.php'; ?>
