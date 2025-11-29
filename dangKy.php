<?php
require_once __DIR__ . '/utils/session.php';
require_once __DIR__ . '/controllers/AuthController.php';

$pageTitle = 'Đăng ký';
$error = null;

if (isLoggedIn()) {
    $baseUrl = getBaseUrl();
    header("Location: " . $baseUrl . "/index.php");
    exit();
}

$authController = new AuthController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error = $authController->dangKy();
    if ($error) {
        // Redirect về dangNhap.php với error message
        $baseUrl = getBaseUrl();
        header("Location: " . $baseUrl . "/dangNhap.php?register_error=" . urlencode($error));
        exit();
    }
} else {
    // Nếu không phải POST thì redirect về dangNhap.php
    $baseUrl = getBaseUrl();
    header("Location: " . $baseUrl . "/dangNhap.php#register");
    exit();
}

// Code dưới này không chạy nữa vì đã redirect hết
include __DIR__ . '/views/layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="bi bi-person-plus"></i> Đăng ký tài khoản</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ten_dang_nhap" class="form-label">Tên đăng nhập *</label>
                            <input type="text" class="form-control" id="ten_dang_nhap" name="ten_dang_nhap" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="mat_khau" class="form-label">Mật khẩu *</label>
                        <input type="password" class="form-control" id="mat_khau" name="mat_khau" required>
                    </div>
                    <div class="mb-3">
                        <label for="ho_ten" class="form-label">Họ và tên *</label>
                        <input type="text" class="form-control" id="ho_ten" name="ho_ten" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="so_dien_thoai" name="so_dien_thoai">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dia_chi" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" id="dia_chi" name="dia_chi">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Đăng ký</button>
                </form>
                <hr>
                <p class="text-center mb-0">
                    Đã có tài khoản? <a href="<?php echo getBaseUrl(); ?>/dangNhap.php">Đăng nhập ngay</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/views/layout/footer.php'; ?>

