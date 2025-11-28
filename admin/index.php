<?php
require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/BaoCaoController.php';

$pageTitle = 'Dashboard - Quản trị';
requireRole('Admin');

$db = new Database();
$conn = $db->getConnection();
$reportController = new BaoCaoController();

// Get Statistics
// 1. Total Orders
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM donhang");
$stmt->execute();
$totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// 2. Total Revenue
$stmt = $conn->prepare("SELECT SUM(tong_tien) as total FROM donhang WHERE trang_thai_thanh_toan = 'Đã thanh toán'");
$stmt->execute();
$totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// 3. Total Products
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM sanpham");
$stmt->execute();
$totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// 4. Total Customers
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM khachhang");
$stmt->execute();
$totalCustomers = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Recent Orders
$recentOrders = $reportController->baoCaoDonHang(); 
// Note: baoCaoDonHang returns all orders sorted by date desc. We limit in view.

// Best selling
$bestSelling = $reportController->baoCaoSanPhamBanChay(5);

include __DIR__ . '/../views/layout/header.php';
?>

<div class="main-content">
    <div class="admin-header">
        <h2><i class="fa-solid fa-gauge"></i> Dashboard</h2>
    </div>

    <!-- Stats Cards -->
    <div class="dashboard-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px;">
            <div class="icon" style="background: #e3f2fd; color: #1976d2; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div class="info">
                <h4 style="margin: 0; color: #666; font-size: 14px;">Tổng đơn hàng</h4>
                <p style="margin: 5px 0 0; font-size: 24px; font-weight: bold; color: #333;"><?php echo number_format($totalOrders); ?></p>
            </div>
        </div>

        <div class="stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px;">
            <div class="icon" style="background: #e8f5e9; color: #2e7d32; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div class="info">
                <h4 style="margin: 0; color: #666; font-size: 14px;">Doanh thu</h4>
                <p style="margin: 5px 0 0; font-size: 24px; font-weight: bold; color: #333;"><?php echo number_format($totalRevenue, 0, ',', '.'); ?> đ</p>
            </div>
        </div>

        <div class="stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px;">
            <div class="icon" style="background: #f3e5f5; color: #7b1fa2; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fa-solid fa-box"></i>
            </div>
            <div class="info">
                <h4 style="margin: 0; color: #666; font-size: 14px;">Sản phẩm</h4>
                <p style="margin: 5px 0 0; font-size: 24px; font-weight: bold; color: #333;"><?php echo number_format($totalProducts); ?></p>
            </div>
        </div>

        <div class="stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px;">
            <div class="icon" style="background: #fff3e0; color: #ef6c00; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="info">
                <h4 style="margin: 0; color: #666; font-size: 14px;">Khách hàng</h4>
                <p style="margin: 5px 0 0; font-size: 24px; font-weight: bold; color: #333;"><?php echo number_format($totalCustomers); ?></p>
            </div>
        </div>
    </div>

    <!-- Management Section -->
    <style>
        .management-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
        }
    </style>
    <div class="management-section" style="margin-bottom: 30px;">
        <h3 style="margin-bottom: 20px; color: #333; font-size: 20px; font-weight: 600;">
            <i class="fa-solid fa-gear"></i> Quản lý
        </h3>
        <div class="management-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <a href="<?php echo getBaseUrl(); ?>/admin/sanpham.php" class="management-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; text-align: center; transition: transform 0.2s, box-shadow 0.2s;">
                <div class="icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px;">
                    <i class="fa-solid fa-box"></i>
                </div>
                <h4 style="margin: 0; color: #333; font-size: 16px; font-weight: 600;">Sản phẩm</h4>
                <p style="margin: 5px 0 0; color: #666; font-size: 12px;">Quản lý sản phẩm</p>
            </a>

            <a href="<?php echo getBaseUrl(); ?>/admin/danhmuc.php" class="management-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; text-align: center; transition: transform 0.2s, box-shadow 0.2s;">
                <div class="icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px;">
                    <i class="fa-solid fa-folder"></i>
                </div>
                <h4 style="margin: 0; color: #333; font-size: 16px; font-weight: 600;">Danh mục</h4>
                <p style="margin: 5px 0 0; color: #666; font-size: 12px;">Quản lý danh mục</p>
            </a>

            <a href="<?php echo getBaseUrl(); ?>/admin/donhang.php" class="management-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; text-align: center; transition: transform 0.2s, box-shadow 0.2s;">
                <div class="icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px;">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <h4 style="margin: 0; color: #333; font-size: 16px; font-weight: 600;">Đơn hàng</h4>
                <p style="margin: 5px 0 0; color: #666; font-size: 12px;">Quản lý đơn hàng</p>
            </a>

            <a href="<?php echo getBaseUrl(); ?>/admin/khachhang.php" class="management-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; text-align: center; transition: transform 0.2s, box-shadow 0.2s;">
                <div class="icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px;">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h4 style="margin: 0; color: #333; font-size: 16px; font-weight: 600;">Khách hàng</h4>
                <p style="margin: 5px 0 0; color: #666; font-size: 12px;">Quản lý khách hàng</p>
            </a>

            <a href="<?php echo getBaseUrl(); ?>/admin/nhacungcap.php" class="management-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; text-align: center; transition: transform 0.2s, box-shadow 0.2s;">
                <div class="icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px;">
                    <i class="fa-solid fa-truck"></i>
                </div>
                <h4 style="margin: 0; color: #333; font-size: 16px; font-weight: 600;">Nhà cung cấp</h4>
                <p style="margin: 5px 0 0; color: #666; font-size: 12px;">Quản lý nhà cung cấp</p>
            </a>

            <a href="<?php echo getBaseUrl(); ?>/admin/phieunhap.php" class="management-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; text-align: center; transition: transform 0.2s, box-shadow 0.2s;">
                <div class="icon" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px;">
                    <i class="fa-solid fa-file-import"></i>
                </div>
                <h4 style="margin: 0; color: #333; font-size: 16px; font-weight: 600;">Phiếu nhập</h4>
                <p style="margin: 5px 0 0; color: #666; font-size: 12px;">Quản lý phiếu nhập</p>
            </a>

            <a href="<?php echo getBaseUrl(); ?>/admin/kho.php" class="management-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; text-align: center; transition: transform 0.2s, box-shadow 0.2s;">
                <div class="icon" style="background: linear-gradient(135deg, #fa7921 0%, #ffa500 100%); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px;">
                    <i class="fa-solid fa-warehouse"></i>
                </div>
                <h4 style="margin: 0; color: #333; font-size: 16px; font-weight: 600;">Kho</h4>
                <p style="margin: 5px 0 0; color: #666; font-size: 12px;">Quản lý kho hàng</p>
            </a>

            <a href="<?php echo getBaseUrl(); ?>/admin/baocao.php" class="management-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; text-align: center; transition: transform 0.2s, box-shadow 0.2s;">
                <div class="icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px;">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h4 style="margin: 0; color: #333; font-size: 16px; font-weight: 600;">Báo cáo</h4>
                <p style="margin: 5px 0 0; color: #666; font-size: 12px;">Báo cáo thống kê</p>
            </a>
        </div>
    </div>

    <div class="dashboard-content" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Recent Orders -->
        <div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;">Đơn hàng gần đây</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $count = 0;
                    while ($order = $recentOrders->fetch(PDO::FETCH_ASSOC)): 
                        if ($count++ >= 5) break;
                    ?>
                        <tr>
                            <td><a href="donhang.php?action=detail&id=<?php echo $order['id']; ?>">#<?php echo $order['id']; ?></a></td>
                            <td><?php echo htmlspecialchars($order['ho_ten']); ?></td>
                            <td><?php echo number_format($order['tong_tien'], 0, ',', '.'); ?> đ</td>
                            <td>
                                <span class="status-badge <?php echo strtolower(str_replace(' ', '-', $order['trang_thai_thanh_toan'])); ?>">
                                    <?php echo $order['trang_thai_thanh_toan']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div style="margin-top: 15px; text-align: right;">
                <a href="donhang.php" style="color: #1976d2; text-decoration: none; font-weight: 500;">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>

        <!-- Best Selling -->
        <div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;">Top sản phẩm bán chạy</h3>
            <div class="best-selling-list">
                <?php while ($prod = $bestSelling->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="item" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f5f5f5;">
                        <div>
                            <div style="font-weight: 500;"><?php echo htmlspecialchars($prod['ten_san_pham']); ?></div>
                            <div style="font-size: 12px; color: #888;"><?php echo htmlspecialchars($prod['ten_danh_muc']); ?></div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: bold; color: #2e7d32;"><?php echo number_format($prod['tong_so_luong_ban']); ?> đã bán</div>
                            <div style="font-size: 12px; color: #666;"><?php echo number_format($prod['tong_doanh_thu'], 0, ',', '.'); ?> đ</div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>

