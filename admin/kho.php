<?php
require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Kho.php';

$pageTitle = 'Quản lý Kho';
requireRole('Admin');

$db = new Database();
$conn = $db->getConnection();
$khoModel = new Kho($conn);

$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        $id = trim($_POST['id'] ?? '');
        $ten_kho = trim($_POST['ten_kho'] ?? '');
        $dia_chi = trim($_POST['dia_chi'] ?? '');

        if (empty($id) || empty($ten_kho)) {
            $error = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
        } else {
            // Check if ID already exists
            $stmt = $conn->prepare("SELECT id FROM kho WHERE id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetch()) {
                $error = 'ID kho đã tồn tại.';
            } else {
                $query = "INSERT INTO kho (id, ten_kho, dia_chi) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);
                if ($stmt->execute([$id, $ten_kho, $dia_chi])) {
                    $message = 'Thêm kho thành công!';
                    $action = 'list';
                } else {
                    $error = 'Lỗi khi thêm kho.';
                }
            }
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? '';
        $ten_kho = trim($_POST['ten_kho'] ?? '');
        $dia_chi = trim($_POST['dia_chi'] ?? '');

        if (empty($id) || empty($ten_kho)) {
            $error = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
        } else {
            $query = "UPDATE kho SET ten_kho = ?, dia_chi = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            if ($stmt->execute([$ten_kho, $dia_chi, $id])) {
                $message = 'Cập nhật kho thành công!';
                $action = 'list';
            } else {
                $error = 'Lỗi khi cập nhật kho.';
            }
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        
        // Check if kho is being used
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM phieunhap WHERE id_kho = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            $error = 'Không thể xóa kho này vì nó đang được sử dụng.';
        } else {
            $query = "DELETE FROM kho WHERE id = ?";
            $stmt = $conn->prepare($query);
            if ($stmt->execute([$id])) {
                $message = 'Xóa kho thành công!';
            } else {
                $error = 'Lỗi khi xóa kho.';
            }
            $action = 'list';
        }
    }
}

// Get data for edit form
$editData = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM kho WHERE id = ?");
    $stmt->execute([$id]);
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$editData) {
        $error = 'Không tìm thấy kho.';
        $action = 'list';
    }
}

// Get all khos for list view
$khos = null;
if ($action === 'list') {
    $khos = $khoModel->docTatCa();
}

include __DIR__ . '/../views/layout/header.php';
?>

<div class="main-content">
    <div class="admin-header">
        <h2><i class="fa-solid fa-warehouse"></i> Quản lý Kho</h2>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
        <!-- List View -->
        <div class="form-section">
            <div class="form-header">
                <h3><i class="fa-solid fa-list"></i> Danh sách Kho</h3>
                <a href="?action=add" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Thêm Kho
                </a>
            </div>

            <?php if ($khos && $khos->rowCount() > 0): ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID Kho</th>
                                <th>Tên Kho</th>
                                <th>Địa chỉ</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($kho = $khos->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($kho['id']); ?></td>
                                    <td><?php echo htmlspecialchars($kho['ten_kho']); ?></td>
                                    <td><?php echo htmlspecialchars($kho['dia_chi'] ?? ''); ?></td>
                                    <td>
                                        <a href="?action=edit&id=<?php echo urlencode($kho['id']); ?>" class="btn btn-sm btn-warning">
                                            <i class="fa-solid fa-pen-to-square"></i> Sửa
                                        </a>
                                        <form method="POST" action="?action=delete" style="display:inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa kho này?');">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($kho['id']); ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa-solid fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="empty-message">Chưa có kho nào. <a href="?action=add">Thêm kho mới</a></p>
            <?php endif; ?>
        </div>

    <?php elseif ($action === 'add'): ?>
        <!-- Add Form -->
        <div class="form-section">
            <div class="form-header">
                <h3><i class="fa-solid fa-plus-circle"></i> Thêm Kho</h3>
                <a href="?action=list" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
            </div>

            <form method="POST" action="?action=add" class="form-content">
                <div class="form-group">
                    <label for="id">ID Kho *</label>
                    <input 
                        type="text" 
                        id="id" 
                        name="id" 
                        placeholder="Ví dụ: KHO001" 
                        required
                        maxlength="10"
                    >
                </div>

                <div class="form-group">
                    <label for="ten_kho">Tên Kho *</label>
                    <input 
                        type="text" 
                        id="ten_kho" 
                        name="ten_kho" 
                        placeholder="Nhập tên kho" 
                        required
                        maxlength="100"
                    >
                </div>

                <div class="form-group">
                    <label for="dia_chi">Địa chỉ</label>
                    <textarea 
                        id="dia_chi" 
                        name="dia_chi" 
                        placeholder="Nhập địa chỉ kho"
                        rows="4"
                        maxlength="500"
                    ></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu
                    </button>
                    <a href="?action=list" class="btn btn-secondary">
                        <i class="fa-solid fa-times"></i> Hủy
                    </a>
                </div>
            </form>
        </div>

    <?php elseif ($action === 'edit' && $editData): ?>
        <!-- Edit Form -->
        <div class="form-section">
            <div class="form-header">
                <h3><i class="fa-solid fa-pen-to-square"></i> Sửa Kho</h3>
                <a href="?action=list" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
            </div>

            <form method="POST" action="?action=edit" class="form-content">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($editData['id']); ?>">

                <div class="form-group">
                    <label for="id">ID Kho</label>
                    <input 
                        type="text" 
                        id="id_display" 
                        value="<?php echo htmlspecialchars($editData['id']); ?>"
                        disabled
                    >
                </div>

                <div class="form-group">
                    <label for="ten_kho">Tên Kho *</label>
                    <input 
                        type="text" 
                        id="ten_kho" 
                        name="ten_kho" 
                        value="<?php echo htmlspecialchars($editData['ten_kho']); ?>"
                        required
                        maxlength="100"
                    >
                </div>

                <div class="form-group">
                    <label for="dia_chi">Địa chỉ</label>
                    <textarea 
                        id="dia_chi" 
                        name="dia_chi" 
                        rows="4"
                        maxlength="500"
                    ><?php echo htmlspecialchars($editData['dia_chi'] ?? ''); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu
                    </button>
                    <a href="?action=list" class="btn btn-secondary">
                        <i class="fa-solid fa-times"></i> Hủy
                    </a>
                </div>
            </form>
        </div>

    <?php endif; ?>
</div>

<style>
    .main-content {
        padding: 20px;
    }

    .admin-header {
        margin-bottom: 30px;
    }

    .admin-header h2 {
        font-size: 28px;
        color: #333;
        margin-bottom: 10px;
    }

    .alert {
        padding: 15px 20px;
        margin-bottom: 20px;
        border-radius: 5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .form-section {
        background: white;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 15px;
    }

    .form-header h3 {
        font-size: 20px;
        color: #333;
        margin: 0;
    }

    .form-content {
        max-width: 600px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        font-family: inherit;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
    }

    .form-group input:disabled {
        background-color: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .btn-warning {
        background-color: #ffc107;
        color: #333;
    }

    .btn-warning:hover {
        background-color: #e0a800;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background-color: #f8f9fa;
    }

    .data-table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #dee2e6;
    }

    .data-table td {
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
    }

    .data-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .empty-message {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
        font-size: 16px;
    }

    .empty-message a {
        color: #007bff;
        text-decoration: none;
        font-weight: 600;
    }

    .empty-message a:hover {
        text-decoration: underline;
    }
</style>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>
