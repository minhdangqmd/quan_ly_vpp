<?php
require_once __DIR__ . '/utils/session.php';
require_once __DIR__ . '/controllers/YeuCauDatHangCustomController.php';

$pageTitle = 'Đặt hàng custom';
requireLogin();

$controller = new YeuCauDatHangCustomController();

// Handle create
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller->create();
}

$id = $_GET['id'] ?? null;
if ($id) {
    $data = $controller->show($id);
} else {
    $yeuCaus = $controller->index();
}

include __DIR__ . '/views/layout/header.php';
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Gửi yêu cầu thành công!</div>
<?php endif; ?>

<?php if ($id && $data): ?>
    <!-- View Request Detail -->
    <div class="row">
        <div class="col-md-8">
            <h2>Chi tiết yêu cầu</h2>
            <div class="card">
                <div class="card-body">
                    <p><strong>Mã yêu cầu:</strong> <?php echo htmlspecialchars($data['yeuCau']->id); ?></p>
                    <p><strong>Trạng thái:</strong> 
                        <span class="badge bg-info"><?php echo htmlspecialchars($data['yeuCau']->trang_thai_yeu_cau); ?></span>
                    </p>
                    <p><strong>Ngày yêu cầu:</strong> <?php echo date('d/m/Y H:i', strtotime($data['yeuCau']->ngay_yeu_cau)); ?></p>
                    <p><strong>Giá ước tính:</strong> 
                        <?php echo number_format($data['yeuCau']->gia_uoc_tinh, 0, ',', '.'); ?> đ
                    </p>
                    <div class="mb-3">
                        <strong>Mô tả chi tiết:</strong>
                        <p><?php echo nl2br(htmlspecialchars($data['yeuCau']->mo_ta_chi_tiet)); ?></p>
                    </div>
                    
                    <?php if ($data['images']): ?>
                        <div class="mb-3">
                            <strong>Hình ảnh:</strong>
                            <div class="row mt-2">
                                <?php while ($img = $data['images']->fetch(PDO::FETCH_ASSOC)): ?>
                                    <div class="col-md-4 mb-2">
                                        <img src="/<?php echo htmlspecialchars($img['duong_dan_hinh_anh']); ?>" 
                                             class="img-thumbnail" style="width: 100%; height: 200px; object-fit: cover;">
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Create/List Requests -->
    <div class="row">
        <div class="col-md-8">
            <h2>Đặt hàng custom (ảnh theo yêu cầu)</h2>
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tạo yêu cầu mới</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="mo_ta_chi_tiet" class="form-label">Mô tả chi tiết yêu cầu *</label>
                            <textarea class="form-control" id="mo_ta_chi_tiet" name="mo_ta_chi_tiet" 
                                      rows="5" required placeholder="Mô tả chi tiết sản phẩm bạn muốn đặt..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gia_uoc_tinh" class="form-label">Giá ước tính (đ)</label>
                                <input type="number" class="form-control" id="gia_uoc_tinh" name="gia_uoc_tinh" 
                                       min="0" step="1000" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ngay_hoan_thanh_uoc_tinh" class="form-label">Ngày hoàn thành ước tính</label>
                                <input type="date" class="form-control" id="ngay_hoan_thanh_uoc_tinh" 
                                       name="ngay_hoan_thanh_uoc_tinh">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="hinh_anh" class="form-label">Hình ảnh tham khảo</label>
                            <input type="file" class="form-control" id="hinh_anh" name="hinh_anh[]" 
                                   multiple accept="image/*">
                            <small class="form-text text-muted">Có thể chọn nhiều ảnh</small>
                        </div>
                        <button type="submit" class="btn btn-info">
                            <i class="bi bi-send"></i> Gửi yêu cầu
                        </button>
                    </form>
                </div>
            </div>
            
            <h3>Lịch sử yêu cầu</h3>
            <?php if ($yeuCaus): ?>
                <div class="list-group">
                    <?php while ($yc = $yeuCaus->fetch(PDO::FETCH_ASSOC)): ?>
                        <a href="<?php echo getBaseUrl(); ?>/yeucaucustom.php?id=<?php echo $yc['id']; ?>" 
                           class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><?php echo htmlspecialchars($yc['id']); ?></h5>
                                <small>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($yc['trang_thai_yeu_cau']); ?></span>
                                </small>
                            </div>
                            <p class="mb-1"><?php echo substr(htmlspecialchars($yc['mo_ta_chi_tiet']), 0, 100); ?>...</p>
                            <small>Ngày: <?php echo date('d/m/Y H:i', strtotime($yc['ngay_yeu_cau'])); ?></small>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Chưa có yêu cầu nào.</div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/views/layout/footer.php'; ?>

