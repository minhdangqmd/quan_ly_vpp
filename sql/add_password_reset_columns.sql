-- Thêm cột để hỗ trợ chức năng reset mật khẩu
ALTER TABLE `taikhoan` 
ADD COLUMN `reset_token` VARCHAR(100) NULL DEFAULT NULL AFTER `ngay_tao`,
ADD COLUMN `reset_token_expiry` DATETIME NULL DEFAULT NULL AFTER `reset_token`;

