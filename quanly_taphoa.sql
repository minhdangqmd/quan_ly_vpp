-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2025 at 12:36 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quanly_taphoa`
--

-- --------------------------------------------------------

--
-- Table structure for table `chitietdonhang`
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
-- Table structure for table `chitietgiohang`
--

CREATE TABLE `chitietgiohang` (
  `id` int(11) NOT NULL,
  `id_giohang` int(11) NOT NULL,
  `id_san_pham` varchar(10) NOT NULL,
  `so_luong` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chitietgiohang`
--

INSERT INTO `chitietgiohang` (`id`, `id_giohang`, `id_san_pham`, `so_luong`) VALUES
(1, 1, '4', 1),
(2, 2, '4', 1);

-- --------------------------------------------------------

--
-- Table structure for table `chitietphieunhap`
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
-- Table structure for table `chucvu`
--

CREATE TABLE `chucvu` (
  `id` varchar(10) NOT NULL,
  `ten_chuc_vu` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `danhmuc`
--

CREATE TABLE `danhmuc` (
  `id` int(11) NOT NULL,
  `ten_danh_muc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `danhmuc`
--

INSERT INTO `danhmuc` (`id`, `ten_danh_muc`) VALUES
(1, 'Dụng cụ viết và đánh dấu'),
(2, 'Dụng cụ lưu trữ và quản lý hồ sơ'),
(3, 'Thiết bị văn phòng nhỏ'),
(4, 'Đồ dùng học sinh');

-- --------------------------------------------------------

--
-- Table structure for table `donhang`
--

CREATE TABLE `donhang` (
  `id` varchar(10) NOT NULL,
  `id_khach_hang` int(11) DEFAULT NULL,
  `ngay_dat` datetime DEFAULT current_timestamp(),
  `tong_tien` decimal(15,2) NOT NULL,
  `trang_thai_thanh_toan` varchar(50) NOT NULL,
  `dia_chi_giao` varchar(255) NOT NULL,
  `sdt_nhan` varchar(20) NOT NULL,
  `id_hinh_thuc_tt` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donhang`
--

INSERT INTO `donhang` (`id`, `id_khach_hang`, `ngay_dat`, `tong_tien`, `trang_thai_thanh_toan`, `dia_chi_giao`, `sdt_nhan`, `id_hinh_thuc_tt`) VALUES
('1', 1, '2025-11-12 19:06:23', 1000000.00, 'Đang xử lý', 'hai phong', '012456878', 'BANK');

-- --------------------------------------------------------

--
-- Table structure for table `donvitinh`
--

CREATE TABLE `donvitinh` (
  `id` int(11) NOT NULL,
  `ten_dvt` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donvitinh`
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
-- Table structure for table `giohang`
--

CREATE TABLE `giohang` (
  `id` int(11) NOT NULL,
  `id_khach_hang` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `giohang`
--

INSERT INTO `giohang` (`id`, `id_khach_hang`, `created_at`) VALUES
(1, 2, '2025-11-28 07:28:34'),
(2, 4, '2025-11-28 07:35:54');

-- --------------------------------------------------------

--
-- Table structure for table `hinhthucthanhtoan`
--

CREATE TABLE `hinhthucthanhtoan` (
  `id` varchar(10) NOT NULL,
  `ten_hinh_thuc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hinhthucthanhtoan`
--

INSERT INTO `hinhthucthanhtoan` (`id`, `ten_hinh_thuc`) VALUES
('BANK', 'Chuyển khoản ngân hàng'),
('COD', 'Thanh toán khi nhận hàng');

-- --------------------------------------------------------

--
-- Table structure for table `khachhang`
--

CREATE TABLE `khachhang` (
  `id` int(11) NOT NULL,
  `id_taikhoan` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `khachhang`
--

INSERT INTO `khachhang` (`id`, `id_taikhoan`, `ho_ten`, `sdt`, `dia_chi`) VALUES
(1, 1, 'a', '113', 'hn'),
(2, 2, 'Admin', '000000000', 'HN'),
(3, 4, 'Phạm Đức', '0369837234', 'Hải Phòng'),
(4, 3, 'Khách hàng mới', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kho`
--

CREATE TABLE `kho` (
  `id` varchar(10) NOT NULL,
  `ten_kho` varchar(100) NOT NULL,
  `dia_chi` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loaitintuc`
--

CREATE TABLE `loaitintuc` (
  `id` int(11) NOT NULL,
  `ten_loai_tin` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung_chi_tiet`
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
-- Dumping data for table `nguoi_dung_chi_tiet`
--

INSERT INTO `nguoi_dung_chi_tiet` (`id`, `id_taikhoan`, `ho_ten`, `dien_thoai`, `dia_chi`, `avatar`, `ngay_cap_nhat`) VALUES
(1, 2, NULL, NULL, NULL, NULL, NULL),
(2, 1, 'Lê Hiệu', '0395204696', 'PG An Đồng', NULL, '2025-11-12 17:33:29'),
(4, 3, 'Đặng Quang Minh', '', 'Lê Chân - Hải Phòng', NULL, '2025-11-28 03:55:50');

-- --------------------------------------------------------

--
-- Table structure for table `nhacungcap`
--

CREATE TABLE `nhacungcap` (
  `id` varchar(10) NOT NULL,
  `ten_nha_cung_cap` varchar(100) NOT NULL,
  `dia_chi` varchar(500) DEFAULT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nhacungcap`
--

INSERT INTO `nhacungcap` (`id`, `ten_nha_cung_cap`, `dia_chi`, `sdt`, `email`) VALUES
('1', 'thienlong', 'hanoi', '0915486785', 'thienlonghanoi@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `nhanvien`
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
-- Table structure for table `phieunhap`
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
-- Table structure for table `sanpham`
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
-- Dumping data for table `sanpham`
--

INSERT INTO `sanpham` (`id`, `ten_san_pham`, `id_danh_muc`, `id_nha_cung_cap`, `mo_ta`, `gia_ban`, `id_dvt`, `hinh_anh`, `so_luong_ton`, `han_su_dung`) VALUES
('1', 'Bút Mực', 1, '1', 'loai 1', 150000.00, 1, '', 500, NULL),
('2', 'Thước kẻ', 4, '1', 'thienlong', 20000.00, 1, '', 10000, NULL),
('3', 'Kệ đựng tài liệu', 2, '1', 'abc', 500000.00, 1, '', 300, NULL),
('4', 'Bút Chì cao cấp', 1, '1', 'thien long', 500000.00, 1, NULL, 500, NULL),
('5', 'Khung ảnh', 3, NULL, 'Khung ảnh để khách hàng ghép ảnh vào', 50000.00, 1, NULL, 100, '2029-01-28'),
('6', 'Sổ tay', 1, '1', 'Sổ tay viết và ghi chép', 30000.00, 1, NULL, 400, '2025-11-28'),
('7', 'Hộp bút', 2, NULL, 'Hộp bút đựng đồ dùng', 70000.00, 1, NULL, 500, '2026-03-27'),
('8', 'Hộp quà', 2, NULL, '', 20000.00, 1, NULL, 1000, '2025-12-03'),
('9', 'Túi đựng hồ sơ', 2, NULL, 'túi đựng tài liệu', 20000.00, 1, NULL, 6000, '2025-11-29');

-- --------------------------------------------------------

--
-- Table structure for table `taikhoan`
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
-- Dumping data for table `taikhoan`
--

INSERT INTO `taikhoan` (`id`, `ten_dang_nhap`, `mat_khau`, `email`, `id_vai_tro`, `trang_thai`, `ngay_tao`) VALUES
(1, 'hieu1605', 'hoalucx1', 'lenguyenhieu1605@gmail.com', 3, 1, '2025-11-12 23:19:19'),
(2, 'admin', '12345', 'admin@gmail.com', 1, 1, '2025-11-12 23:19:19'),
(3, 'minh123', '1234', 'minhminh@gmail.com', 3, 1, '2025-11-28 10:42:33'),
(4, 'duc2907', 'hoalucx1', 'phamduc290703@gmail.com', 3, 1, '2025-11-28 14:21:42');

-- --------------------------------------------------------

--
-- Table structure for table `tintuc`
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
-- Table structure for table `vaitro`
--

CREATE TABLE `vaitro` (
  `id` int(11) NOT NULL,
  `ten_vai_tro` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vaitro`
--

INSERT INTO `vaitro` (`id`, `ten_vai_tro`) VALUES
(1, 'Admin'),
(3, 'KhachHang'),
(2, 'NhanVien');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_don_hang` (`id_don_hang`),
  ADD KEY `id_san_pham` (`id_san_pham`);

--
-- Indexes for table `chitietgiohang`
--
ALTER TABLE `chitietgiohang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_giohang` (`id_giohang`),
  ADD KEY `idx_sp` (`id_san_pham`);

--
-- Indexes for table `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_phieu_nhap` (`id_phieu_nhap`),
  ADD KEY `id_san_pham` (`id_san_pham`);

--
-- Indexes for table `chucvu`
--
ALTER TABLE `chucvu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_khach_hang` (`id_khach_hang`),
  ADD KEY `id_hinh_thuc_tt` (`id_hinh_thuc_tt`);

--
-- Indexes for table `donvitinh`
--
ALTER TABLE `donvitinh`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ten_dvt` (`ten_dvt`);

--
-- Indexes for table `giohang`
--
ALTER TABLE `giohang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_khach` (`id_khach_hang`);

--
-- Indexes for table `hinhthucthanhtoan`
--
ALTER TABLE `hinhthucthanhtoan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_taikhoan` (`id_taikhoan`);

--
-- Indexes for table `kho`
--
ALTER TABLE `kho`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loaitintuc`
--
ALTER TABLE `loaitintuc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nguoi_dung_chi_tiet`
--
ALTER TABLE `nguoi_dung_chi_tiet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_taikhoan` (`id_taikhoan`);

--
-- Indexes for table `nhacungcap`
--
ALTER TABLE `nhacungcap`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_taikhoan` (`id_taikhoan`),
  ADD KEY `id_chuc_vu` (`id_chuc_vu`);

--
-- Indexes for table `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nha_cung_cap` (`id_nha_cung_cap`),
  ADD KEY `id_nhan_vien` (`id_nhan_vien`),
  ADD KEY `id_kho` (`id_kho`);

--
-- Indexes for table `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_danh_muc` (`id_danh_muc`),
  ADD KEY `id_nha_cung_cap` (`id_nha_cung_cap`),
  ADD KEY `id_dvt` (`id_dvt`);

--
-- Indexes for table `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ten_dang_nhap` (`ten_dang_nhap`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_vai_tro` (`id_vai_tro`);

--
-- Indexes for table `tintuc`
--
ALTER TABLE `tintuc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nhan_vien` (`id_nhan_vien`),
  ADD KEY `id_loai_tin` (`id_loai_tin`);

--
-- Indexes for table `vaitro`
--
ALTER TABLE `vaitro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ten_vai_tro` (`ten_vai_tro`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chitietgiohang`
--
ALTER TABLE `chitietgiohang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `danhmuc`
--
ALTER TABLE `danhmuc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `donvitinh`
--
ALTER TABLE `donvitinh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `giohang`
--
ALTER TABLE `giohang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `loaitintuc`
--
ALTER TABLE `loaitintuc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nguoi_dung_chi_tiet`
--
ALTER TABLE `nguoi_dung_chi_tiet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `taikhoan`
--
ALTER TABLE `taikhoan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tintuc`
--
ALTER TABLE `tintuc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vaitro`
--
ALTER TABLE `vaitro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`id_don_hang`) REFERENCES `donhang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietdonhang_ibfk_2` FOREIGN KEY (`id_san_pham`) REFERENCES `sanpham` (`id`);

--
-- Constraints for table `chitietgiohang`
--
ALTER TABLE `chitietgiohang`
  ADD CONSTRAINT `fk_ctgh_giohang` FOREIGN KEY (`id_giohang`) REFERENCES `giohang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ctgh_sanpham` FOREIGN KEY (`id_san_pham`) REFERENCES `sanpham` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  ADD CONSTRAINT `chitietphieunhap_ibfk_1` FOREIGN KEY (`id_phieu_nhap`) REFERENCES `phieunhap` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietphieunhap_ibfk_2` FOREIGN KEY (`id_san_pham`) REFERENCES `sanpham` (`id`);

--
-- Constraints for table `donhang`
--
ALTER TABLE `donhang`
  ADD CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`id_khach_hang`) REFERENCES `khachhang` (`id`),
  ADD CONSTRAINT `donhang_ibfk_2` FOREIGN KEY (`id_hinh_thuc_tt`) REFERENCES `hinhthucthanhtoan` (`id`);

--
-- Constraints for table `giohang`
--
ALTER TABLE `giohang`
  ADD CONSTRAINT `fk_giohang_khach` FOREIGN KEY (`id_khach_hang`) REFERENCES `khachhang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `khachhang`
--
ALTER TABLE `khachhang`
  ADD CONSTRAINT `khachhang_ibfk_1` FOREIGN KEY (`id_taikhoan`) REFERENCES `taikhoan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nguoi_dung_chi_tiet`
--
ALTER TABLE `nguoi_dung_chi_tiet`
  ADD CONSTRAINT `nguoi_dung_chi_tiet_ibfk_1` FOREIGN KEY (`id_taikhoan`) REFERENCES `taikhoan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD CONSTRAINT `nhanvien_ibfk_1` FOREIGN KEY (`id_taikhoan`) REFERENCES `taikhoan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nhanvien_ibfk_2` FOREIGN KEY (`id_chuc_vu`) REFERENCES `chucvu` (`id`);

--
-- Constraints for table `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD CONSTRAINT `phieunhap_ibfk_1` FOREIGN KEY (`id_nha_cung_cap`) REFERENCES `nhacungcap` (`id`),
  ADD CONSTRAINT `phieunhap_ibfk_2` FOREIGN KEY (`id_nhan_vien`) REFERENCES `nhanvien` (`id`),
  ADD CONSTRAINT `phieunhap_ibfk_3` FOREIGN KEY (`id_kho`) REFERENCES `kho` (`id`);

--
-- Constraints for table `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`id_danh_muc`) REFERENCES `danhmuc` (`id`),
  ADD CONSTRAINT `sanpham_ibfk_2` FOREIGN KEY (`id_nha_cung_cap`) REFERENCES `nhacungcap` (`id`),
  ADD CONSTRAINT `sanpham_ibfk_3` FOREIGN KEY (`id_dvt`) REFERENCES `donvitinh` (`id`);

--
-- Constraints for table `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD CONSTRAINT `taikhoan_ibfk_1` FOREIGN KEY (`id_vai_tro`) REFERENCES `vaitro` (`id`);

--
-- Constraints for table `tintuc`
--
ALTER TABLE `tintuc`
  ADD CONSTRAINT `tintuc_ibfk_1` FOREIGN KEY (`id_nhan_vien`) REFERENCES `nhanvien` (`id`),
  ADD CONSTRAINT `tintuc_ibfk_2` FOREIGN KEY (`id_loai_tin`) REFERENCES `loaitintuc` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
