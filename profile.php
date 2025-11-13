<?php
// profile.php - dùng taikhoan + nguoi_dung_chi_tiet (phương án C)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();

// include connection - sửa đường dẫn nếu cần
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
} elseif (file_exists(__DIR__ . '/db.php')) {
    require_once __DIR__ . '/db.php';
} else {
    // fallback: chỉnh tên database/user/password nếu bạn không có file kết nối
    $mysqli = new mysqli('localhost', 'root', '', 'quanly_taphoa');
    if ($mysqli->connect_errno) {
        die("Lỗi kết nối DB: " . $mysqli->connect_error);
    }
}

function getBaseUrl() {
    $d = dirname($_SERVER['SCRIPT_NAME']);
    $d = str_replace('\\','/',$d);
    $d = rtrim($d, '/');
    return ($d === '' || $d === '/') ? '' : $d;
}

// Kiểm tra login
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . (getBaseUrl() ? getBaseUrl() . '/login.php' : 'login.php'));
    exit;
}
$user_id = intval($_SESSION['user_id']);

// Lấy thông tin người dùng (JOIN)
$sql = "SELECT t.id AS taikhoan_id, t.ten_dang_nhap, t.email AS email_tk,
               n.id AS chitiet_id, n.ho_ten, n.dien_thoai, n.dia_chi, n.avatar
        FROM taikhoan t
        LEFT JOIN nguoi_dung_chi_tiet n ON n.id_taikhoan = t.id
        WHERE t.id = ?";
$stmt = $mysqli->prepare($sql);
if (!$stmt) die("Prepare lỗi SELECT user: " . $mysqli->error);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if (!$user) {
    die("Không tìm thấy tài khoản với id = {$user_id} trong bảng taikhoan.");
}

// Xử lý POST (cập nhật)
$thong_bao = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['luu_thong_tin'])) {
    $ho_ten = trim($_POST['ho_ten'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dien_thoai = trim($_POST['dien_thoai'] ?? '');
    $dia_chi = trim($_POST['dia_chi'] ?? '');
    $avatar_path = null;

    // Xử lý upload avatar nếu có
    if (!empty($_FILES['avatar']['name'])) {
        $up = $_FILES['avatar'];
        if ($up['error'] !== 0) {
            $thong_bao = "Lỗi upload avatar: code {$up['error']}.";
        } else {
            $ext = strtolower(pathinfo($up['name'], PATHINFO_EXTENSION));
            $allowed = ['png','jpg','jpeg','gif'];
            if (!in_array($ext, $allowed)) {
                $thong_bao = "Định dạng avatar không hợp lệ. Chỉ: " . implode(', ', $allowed);
            } else {
                $targetDir = __DIR__ . '/uploads/avatars';
                if (!is_dir($targetDir)) {
                    if (!mkdir($targetDir, 0755, true)) {
                        $thong_bao = "Không tạo được thư mục lưu avatar: $targetDir";
                    }
                }
                if ($thong_bao === '') {
                    $filename = 'user_'.$user_id.'_'.time().'.'.$ext;
                    $dest = $targetDir . '/' . $filename;
                    if (!move_uploaded_file($up['tmp_name'], $dest)) {
                        $thong_bao = "Lỗi lưu file avatar. Kiểm tra quyền thư mục.";
                    } else {
                        $avatar_path = 'uploads/avatars/' . $filename;
                    }
                }
            }
        }
    }

    // Nếu không có lỗi upload, tiến hành cập nhật DB
    if ($thong_bao === '') {
        // 1) Cập nhật email trên bảng taikhoan nếu khác
        if ($email !== $user['email_tk']) {
            $uq = $mysqli->prepare("UPDATE taikhoan SET email = ? WHERE id = ?");
            if ($uq) {
                $uq->bind_param('si', $email, $user_id);
                if (!$uq->execute()) $thong_bao = "Lỗi khi cập nhật email: " . $uq->error;
                $uq->close();
            } else {
                $thong_bao = "Prepare lỗi update email: " . $mysqli->error;
            }
        }

        // 2) Cập nhật/insert vào nguoi_dung_chi_tiet
        if ($thong_bao === '') {
            if (!empty($user['chitiet_id'])) {
                // đã tồn tại record chi tiết -> UPDATE
                if ($avatar_path) {
                    $sqlu = "UPDATE nguoi_dung_chi_tiet SET ho_ten = ?, dien_thoai = ?, dia_chi = ?, avatar = ?, ngay_cap_nhat = NOW() WHERE id_taikhoan = ?";
                    $stmt2 = $mysqli->prepare($sqlu);
                    if ($stmt2) {
                        $stmt2->bind_param('ssssi', $ho_ten, $dien_thoai, $dia_chi, $avatar_path, $user_id);
                        if (!$stmt2->execute()) $thong_bao = "Lỗi UPDATE chi tiết: " . $stmt2->error;
                        $stmt2->close();
                    } else $thong_bao = "Prepare lỗi UPDATE chi tiết: " . $mysqli->error;
                } else {
                    $sqlu = "UPDATE nguoi_dung_chi_tiet SET ho_ten = ?, dien_thoai = ?, dia_chi = ?, ngay_cap_nhat = NOW() WHERE id_taikhoan = ?";
                    $stmt2 = $mysqli->prepare($sqlu);
                    if ($stmt2) {
                        $stmt2->bind_param('sssi', $ho_ten, $dien_thoai, $dia_chi, $user_id);
                        if (!$stmt2->execute()) $thong_bao = "Lỗi UPDATE chi tiết: " . $stmt2->error;
                        $stmt2->close();
                    } else $thong_bao = "Prepare lỗi UPDATE chi tiết: " . $mysqli->error;
                }
            } else {
                // chưa có record -> INSERT
                $sqli = "INSERT INTO nguoi_dung_chi_tiet (id_taikhoan, ho_ten, dien_thoai, dia_chi, avatar, ngay_cap_nhat) VALUES (?,?,?,?,?,NOW())";
                $st = $mysqli->prepare($sqli);
                if ($st) {
                    $st->bind_param('issss', $user_id, $ho_ten, $dien_thoai, $dia_chi, $avatar_path);
                    if (!$st->execute()) $thong_bao = "Lỗi INSERT chi tiết: " . $st->error;
                    $st->close();
                } else $thong_bao = "Prepare lỗi INSERT chi tiết: " . $mysqli->error;
            }
        }

        // 3) Nếu tất cả OK, load lại dữ liệu user để hiển thị mới
        if ($thong_bao === '') {
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $user = $res->fetch_assoc();
            $stmt->close();
            $thong_bao = "Cập nhật thành công.";
            // cập nhật session info (nếu cần)
            $_SESSION['email'] = $user['email_tk'] ?? $email;
        }
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Thông tin cá nhân</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:Arial,Helvetica,sans-serif;padding:20px;background:#fff}
    .card{max-width:900px;margin:20px auto;padding:20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.06)}
    .left{float:left;width:30%;text-align:center}
    .right{float:right;width:65%}
    img.avatar{width:140px;height:140px;border-radius:50%;object-fit:cover}
    label{display:block;margin:10px 0 4px}
    input, textarea{width:100%;padding:8px;border:1px solid #ddd;border-radius:4px}
    .btn{display:inline-block;padding:10px 16px;background:#f6b21a;color:#fff;border-radius:24px;text-decoration:none;border:none;cursor:pointer}
    .clear{clear:both}
    .msg{margin:10px 0;color:green}
    .err{margin:10px 0;color:red}
  </style>
</head>
<body>
  <div class="card">
    <div class="left">
      <h3><?php echo htmlspecialchars($user['ten_dang_nhap'] ?? ($_SESSION['username'] ?? '')); ?></h3>
      <?php if (!empty($user['avatar'])): ?>
        <img class="avatar" src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="avatar">
      <?php else: ?>
        <div style="width:140px;height:140px;border-radius:50%;background:#eee;display:flex;align-items:center;justify-content:center;color:#999">No Avatar</div>
      <?php endif; ?>
    </div>

    <div class="right">
      <h2>Thông tin cá nhân</h2>
      <?php if ($thong_bao): ?>
        <div class="<?php echo ($thong_bao === 'Cập nhật thành công.' ? 'msg' : 'err'); ?>"><?php echo htmlspecialchars($thong_bao); ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <label>Họ tên</label>
        <input name="ho_ten" value="<?php echo htmlspecialchars($user['ho_ten'] ?? ''); ?>">

        <label>Email</label>
        <input name="email" value="<?php echo htmlspecialchars($user['email_tk'] ?? ''); ?>">

        <label>Điện Thoại / Tel</label>
        <input name="dien_thoai" value="<?php echo htmlspecialchars($user['dien_thoai'] ?? ''); ?>">

        <label>Địa Chỉ</label>
        <textarea name="dia_chi"><?php echo htmlspecialchars($user['dia_chi'] ?? ''); ?></textarea>

        <label>Avatar (ảnh)</label>
        <input type="file" name="avatar" accept="image/*">

        <div style="margin-top:12px">
          <button class="btn" type="submit" name="luu_thong_tin">Lưu thay đổi</button>
          <a href="<?php echo (getBaseUrl() ? getBaseUrl().'/index.php' : 'index.php'); ?>" style="margin-left:12px;text-decoration:none">Quay về</a>
        </div>
      </form>
    </div>

    <div class="clear"></div>
  </div>
</body>
</html>
