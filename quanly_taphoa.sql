-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 22, 2025 lúc 02:41 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `quanly_taphoa`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdonhang`
--

CREATE TABLE `chitietdonhang` (
  `id` int(11) NOT NULL,
  `id_don_hang` varchar(10) NOT NULL,
  `id_san_pham` varchar(10) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `don_gia` decimal(10,2) NOT NULL,
  `thanh_tien` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietphieunhap`
--

CREATE TABLE `chitietphieunhap` (
  `id` int(11) NOT NULL,
  `id_phieu_nhap` varchar(10) NOT NULL,
  `id_san_pham` varchar(10) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `don_gia_nhap` decimal(10,2) NOT NULL,
  `thanh_tien` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chucvu`
--

CREATE TABLE `chucvu` (
  `id` varchar(10) NOT NULL,
  `ten_chuc_vu` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhmuc`
--

CREATE TABLE `danhmuc` (
  `id` int(11) NOT NULL,
  `ten_danh_muc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danhmuc`
--

INSERT INTO `danhmuc` (`id`, `ten_danh_muc`) VALUES
(1, 'Dụng cụ viết và đánh dấu'),
(2, 'Dụng cụ lưu trữ và quản lý hồ sơ'),
(3, 'Thiết bị văn phòng nhỏ'),
(4, 'Đồ dùng học sinh');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donhang`
--

CREATE TABLE `donhang` (
  `id` varchar(10) NOT NULL,
  `id_khach_hang` int(11) DEFAULT NULL,
  `ngay_dat` datetime DEFAULT current_timestamp(),
  `tong_tien` decimal(15,2) NOT NULL,
  `trang_thai` varchar(50) DEFAULT 'Đang xử lý',
  `dia_chi_giao` varchar(255) NOT NULL,
  `sdt_nhan` varchar(20) NOT NULL,
  `id_hinh_thuc_tt` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `donhang`
--

INSERT INTO `donhang` (`id`, `id_khach_hang`, `ngay_dat`, `tong_tien`, `trang_thai`, `dia_chi_giao`, `sdt_nhan`, `id_hinh_thuc_tt`) VALUES
('1', 1, '2025-11-12 19:06:23', 1000000.00, 'Đang xử lý', 'hai phong', '012456878', 'BANK');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donvitinh`
--

CREATE TABLE `donvitinh` (
  `id` int(11) NOT NULL,
  `ten_dvt` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `donvitinh`
--

INSERT INTO `donvitinh` (`id`, `ten_dvt`) VALUES
(1, 'Cái'),
(3, 'Chai'),
(5, 'Gói'),
(2, 'Hộp'),
(6, 'Kg'),
(4, 'Lon');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hinhthucthanhtoan`
--

CREATE TABLE `hinhthucthanhtoan` (
  `id` varchar(10) NOT NULL,
  `ten_hinh_thuc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hinhthucthanhtoan`
--

INSERT INTO `hinhthucthanhtoan` (`id`, `ten_hinh_thuc`) VALUES
('BANK', 'Chuyển khoản ngân hàng'),
('COD', 'Thanh toán khi nhận hàng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

CREATE TABLE `khachhang` (
  `id` int(11) NOT NULL,
  `id_taikhoan` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`id`, `id_taikhoan`, `ho_ten`, `sdt`, `dia_chi`) VALUES
(1, 1, 'a', '113', 'hn');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kho`
--

CREATE TABLE `kho` (
  `id` varchar(10) NOT NULL,
  `ten_kho` varchar(100) NOT NULL,
  `dia_chi` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaitintuc`
--

CREATE TABLE `loaitintuc` (
  `id` int(11) NOT NULL,
  `ten_loai_tin` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung_chi_tiet`
--

CREATE TABLE `nguoi_dung_chi_tiet` (
  `id` int(11) NOT NULL,
  `id_taikhoan` int(11) NOT NULL,
  `ho_ten` varchar(255) DEFAULT NULL,
  `dien_thoai` varchar(50) DEFAULT NULL,
  `dia_chi` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `ngay_cap_nhat` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung_chi_tiet`
--

INSERT INTO `nguoi_dung_chi_tiet` (`id`, `id_taikhoan`, `ho_ten`, `dien_thoai`, `dia_chi`, `avatar`, `ngay_cap_nhat`) VALUES
(1, 2, NULL, NULL, NULL, NULL, NULL),
(2, 1, 'Lê Hiệu', '0395204696', 'PG An Đồng', NULL, '2025-11-12 17:33:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhacungcap`
--

CREATE TABLE `nhacungcap` (
  `id` varchar(10) NOT NULL,
  `ten_nha_cung_cap` varchar(100) NOT NULL,
  `dia_chi` varchar(500) DEFAULT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhacungcap`
--

INSERT INTO `nhacungcap` (`id`, `ten_nha_cung_cap`, `dia_chi`, `sdt`, `email`) VALUES
('1', 'thienlong', 'hanoi', '0915486785', 'thienlonghanoi@gmail.com');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien`
--

CREATE TABLE `nhanvien` (
  `id` varchar(10) NOT NULL,
  `id_taikhoan` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `id_chuc_vu` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieunhap`
--

CREATE TABLE `phieunhap` (
  `id` varchar(10) NOT NULL,
  `id_nha_cung_cap` varchar(10) DEFAULT NULL,
  `id_nhan_vien` varchar(10) DEFAULT NULL,
  `id_kho` varchar(10) DEFAULT NULL,
  `ngay_nhap` datetime DEFAULT current_timestamp(),
  `tong_tien` decimal(15,2) DEFAULT 0.00,
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

CREATE TABLE `sanpham` (
  `id` varchar(10) NOT NULL,
  `ten_san_pham` varchar(255) NOT NULL,
  `id_danh_muc` int(11) DEFAULT NULL,
  `id_nha_cung_cap` varchar(10) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `gia_ban` decimal(10,2) NOT NULL,
  `id_dvt` int(11) DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `so_luong_ton` int(11) NOT NULL DEFAULT 0,
  `han_su_dung` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`id`, `ten_san_pham`, `id_danh_muc`, `id_nha_cung_cap`, `mo_ta`, `gia_ban`, `id_dvt`, `hinh_anh`, `so_luong_ton`, `han_su_dung`) VALUES
('1', 'Bút Mực', 1, '1', 'loai 1', 150000.00, 1, 'uploads/products/1763038582_but-ky-ten-cao-cap-1-ha-noi.jpg', 500, NULL),
('2', 'Thước kẻ', 4, '1', 'thienlong', 20000.00, 1, 'uploads/products/1763038245_OIP.webp', 10000, NULL),
('3', 'Kệ đựng tài liệu', 2, '1', 'abc', 500000.00, 1, 'uploads/products/1763038600_OIP (1).webp', 300, NULL),
('4', 'Bút Chì cao cấp', 1, '1', 'thien long', 500000.00, 1, NULL, 500, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan`
--

CREATE TABLE `taikhoan` (
  `id` int(11) NOT NULL,
  `ten_dang_nhap` varchar(100) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `id_vai_tro` int(11) NOT NULL,
  `trang_thai` tinyint(1) NOT NULL DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoan`
--

INSERT INTO `taikhoan` (`id`, `ten_dang_nhap`, `mat_khau`, `email`, `id_vai_tro`, `trang_thai`, `ngay_tao`) VALUES
(1, 'hieu1605', 'hoalucx1', 'lenguyenhieu1605@gmail.com', 3, 1, '2025-11-12 23:19:19'),
(2, 'admin', '12345', 'admin@gmail.com', 1, 1, '2025-11-12 23:19:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tintuc`
--

CREATE TABLE `tintuc` (
  `id` int(11) NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `noi_dung` text NOT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `ngay_dang` datetime DEFAULT current_timestamp(),
  `id_nhan_vien` varchar(10) DEFAULT NULL,
  `id_loai_tin` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vaitro`
--

CREATE TABLE `vaitro` (
  `id` int(11) NOT NULL,
  `ten_vai_tro` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vaitro`
--

INSERT INTO `vaitro` (`id`, `ten_vai_tro`) VALUES
(1, 'Admin'),
(3, 'KhachHang'),
(2, 'NhanVien');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_don_hang` (`id_don_hang`),
  ADD KEY `id_san_pham` (`id_san_pham`);

--
-- Chỉ mục cho bảng `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_phieu_nhap` (`id_phieu_nhap`),
  ADD KEY `id_san_pham` (`id_san_pham`);

--
-- Chỉ mục cho bảng `chucvu`
--
ALTER TABLE `chucvu`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_khach_hang` (`id_khach_hang`),
  ADD KEY `id_hinh_thuc_tt` (`id_hinh_thuc_tt`);

--
-- Chỉ mục cho bảng `donvitinh`
--
ALTER TABLE `donvitinh`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ten_dvt` (`ten_dvt`);

--
-- Chỉ mục cho bảng `hinhthucthanhtoan`
--
ALTER TABLE `hinhthucthanhtoan`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_taikhoan` (`id_taikhoan`);

--
-- Chỉ mục cho bảng `kho`
--
ALTER TABLE `kho`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `loaitintuc`
--
ALTER TABLE `loaitintuc`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `nguoi_dung_chi_tiet`
--
ALTER TABLE `nguoi_dung_chi_tiet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_taikhoan` (`id_taikhoan`);

--
-- Chỉ mục cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_taikhoan` (`id_taikhoan`),
  ADD KEY `id_chuc_vu` (`id_chuc_vu`);

--
-- Chỉ mục cho bảng `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nha_cung_cap` (`id_nha_cung_cap`),
  ADD KEY `id_nhan_vien` (`id_nhan_vien`),
  ADD KEY `id_kho` (`id_kho`);

--
-- Chỉ mục cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_danh_muc` (`id_danh_muc`),
  ADD KEY `id_nha_cung_cap` (`id_nha_cung_cap`),
  ADD KEY `id_dvt` (`id_dvt`);

--
-- Chỉ mục cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ten_dang_nhap` (`ten_dang_nhap`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_vai_tro` (`id_vai_tro`);

--
-- Chỉ mục cho bảng `tintuc`
--
ALTER TABLE `tintuc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nhan_vien` (`id_nhan_vien`),
  ADD KEY `id_loai_tin` (`id_loai_tin`);

--
-- Chỉ mục cho bảng `vaitro`
--
ALTER TABLE `vaitro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ten_vai_tro` (`ten_vai_tro`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `donvitinh`
--
ALTER TABLE `donvitinh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `loaitintuc`
--
ALTER TABLE `loaitintuc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nguoi_dung_chi_tiet`
--
ALTER TABLE `nguoi_dung_chi_tiet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `tintuc`
--
ALTER TABLE `tintuc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `vaitro`
--
ALTER TABLE `vaitro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`id_don_hang`) REFERENCES `donhang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietdonhang_ibfk_2` FOREIGN KEY (`id_san_pham`) REFERENCES `sanpham` (`id`);

--
-- Các ràng buộc cho bảng `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  ADD CONSTRAINT `chitietphieunhap_ibfk_1` FOREIGN KEY (`id_phieu_nhap`) REFERENCES `phieunhap` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietphieunhap_ibfk_2` FOREIGN KEY (`id_san_pham`) REFERENCES `sanpham` (`id`);

--
-- Các ràng buộc cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`id_khach_hang`) REFERENCES `khachhang` (`id`),
  ADD CONSTRAINT `donhang_ibfk_2` FOREIGN KEY (`id_hinh_thuc_tt`) REFERENCES `hinhthucthanhtoan` (`id`);

--
-- Các ràng buộc cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD CONSTRAINT `khachhang_ibfk_1` FOREIGN KEY (`id_taikhoan`) REFERENCES `taikhoan` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nguoi_dung_chi_tiet`
--
ALTER TABLE `nguoi_dung_chi_tiet`
  ADD CONSTRAINT `nguoi_dung_chi_tiet_ibfk_1` FOREIGN KEY (`id_taikhoan`) REFERENCES `taikhoan` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD CONSTRAINT `nhanvien_ibfk_1` FOREIGN KEY (`id_taikhoan`) REFERENCES `taikhoan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nhanvien_ibfk_2` FOREIGN KEY (`id_chuc_vu`) REFERENCES `chucvu` (`id`);

--
-- Các ràng buộc cho bảng `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD CONSTRAINT `phieunhap_ibfk_1` FOREIGN KEY (`id_nha_cung_cap`) REFERENCES `nhacungcap` (`id`),
  ADD CONSTRAINT `phieunhap_ibfk_2` FOREIGN KEY (`id_nhan_vien`) REFERENCES `nhanvien` (`id`),
  ADD CONSTRAINT `phieunhap_ibfk_3` FOREIGN KEY (`id_kho`) REFERENCES `kho` (`id`);

--
-- Các ràng buộc cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`id_danh_muc`) REFERENCES `danhmuc` (`id`),
  ADD CONSTRAINT `sanpham_ibfk_2` FOREIGN KEY (`id_nha_cung_cap`) REFERENCES `nhacungcap` (`id`),
  ADD CONSTRAINT `sanpham_ibfk_3` FOREIGN KEY (`id_dvt`) REFERENCES `donvitinh` (`id`);

--
-- Các ràng buộc cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD CONSTRAINT `taikhoan_ibfk_1` FOREIGN KEY (`id_vai_tro`) REFERENCES `vaitro` (`id`);

--
-- Các ràng buộc cho bảng `tintuc`
--
ALTER TABLE `tintuc`
  ADD CONSTRAINT `tintuc_ibfk_1` FOREIGN KEY (`id_nhan_vien`) REFERENCES `nhanvien` (`id`),
  ADD CONSTRAINT `tintuc_ibfk_2` FOREIGN KEY (`id_loai_tin`) REFERENCES `loaitintuc` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
