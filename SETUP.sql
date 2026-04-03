-- ============================================
-- BABY SHOP DATABASE - HOÀN CHỈNH
-- ============================================
-- Tạo database, bảng và dữ liệu từ đầu
-- Chạy trên phpMyAdmin hoặc MySQL command line:
-- mysql -u root -p < SETUP.sql
-- ============================================

-- ============================================
-- BƯỚC 1: TẠO DATABASE
-- ============================================
DROP DATABASE IF EXISTS `baby_shop`;
CREATE DATABASE IF NOT EXISTS `baby_shop` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `baby_shop`;

-- ============================================
-- BƯỚC 2: TẠO BẢNG USERS
-- ============================================
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(255) UNIQUE NOT NULL,
  `full_name` VARCHAR(255),
  `name` VARCHAR(255),
  `email` VARCHAR(255) UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20),
  `role` VARCHAR(50) DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_username` (`username`),
  INDEX `idx_email` (`email`),
  INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BƯỚC 3: TẠO BẢNG PRODUCTS (SẢN PHẨM)
-- ============================================
CREATE TABLE `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` LONGTEXT,
  `price` DECIMAL(10, 2) NOT NULL,
  `discount_percent` DECIMAL(5, 2) DEFAULT 0,
  `image` VARCHAR(255),
  `category` VARCHAR(100),
  `gender` VARCHAR(50) DEFAULT 'unisex',
  `stock` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_category` (`category`),
  INDEX `idx_gender` (`gender`),
  INDEX `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BƯỚC 4: TẠO BẢNG CART (GIỎ HÀNG)
-- ============================================
CREATE TABLE `cart` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `quantity` INT(11) DEFAULT 1,
  `price` DECIMAL(10, 2),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BƯỚC 5: TẠO BẢNG ORDERS (ĐƠN HÀNG)
-- ============================================
CREATE TABLE `orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT(11) NOT NULL,
  `order_number` VARCHAR(50) UNIQUE NOT NULL,
  `total_price` DECIMAL(10, 2) NOT NULL,
  `status` VARCHAR(50) DEFAULT 'pending',
  `payment_method` VARCHAR(100),
  `shipping_address` TEXT,
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_order_number` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BƯỚC 6: TẠO BẢNG ORDER_ITEMS (CHI TIẾT ĐƠN HÀNG)
-- ============================================
CREATE TABLE `order_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  INDEX `idx_order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BƯỚC 7: TẠO BẢNG CATEGORIES (DANH MỤC)
-- ============================================
CREATE TABLE `categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` LONGTEXT,
  `image` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BƯỚC 8: THÊM DỮ LIỆU DANH MỤC
-- ============================================
INSERT INTO `categories` (name, description) VALUES
('0-3 Tháng', 'Sản phẩm cho trẻ 0-3 tháng tuổi'),
('3-6 Tháng', 'Sản phẩm cho trẻ 3-6 tháng tuổi'),
('6-12 Tháng', 'Sản phẩm cho trẻ 6-12 tháng tuổi'),
('Phụ kiện', 'Các phụ kiện và đồ dùng hỗ trợ');

-- ============================================
-- BƯỚC 9: THÊM DỮ LIỆU SẢN PHẨM MẪU
-- ============================================
INSERT INTO `products` (name, price, description, category, stock) VALUES
-- 0-3 Tháng
('Bộ Cơm Sơ Sinh Coton 100% - Xanh Dương', 189000, 'Bộ 5 chiếc cơm mềm, êm ái, thích hợp cho da nhạy cảm của trẻ sơ sinh', '0-3', 50),
('Bộ Quần Áo Sơ Sinh Coton - Ngắn Tay', 149000, 'Bộ 3 chiếc áo quần chất liệu cotton tự nhiên, an toàn cho bé', '0-3', 45),
('Khăn Tắm Em Bé Coton Mềm - 70x140cm', 89000, 'Khăn tắm mềm, thấm nước tốt, dùng được lâu dài', '0-3', 60),
('Nón Sơ Sinh - Nhiều Màu', 29000, 'Nón bảo vệ đầu bé, chất liệu cotton mềm mại', '0-3', 80),
('Bộ Giường Ngủ Cho Em Bé - Xám', 299000, 'Giường sơ sinh cao cấp, an toàn, dễ dàng vệ sinh', '0-3', 20),

-- 3-6 Tháng
('Bộ Cơm Sơ Sinh Coton 100% - Hồng', 189000, 'Cơm mềm cho bé gái, dễ chịu và an toàn', '3-6', 40),
('Túi Ngủ Cho Em Bé - Mẫu Chòm Sao', 249000, 'Túi ngủ ấm áp, thiết kế dễ thương cho bé', '3-6', 35),
('Bộ Xúc Xắc - 6 Chiếc', 99000, 'Đồ chơi phát triển các giác quan cho bé 3-6 tháng', '3-6', 70),
('Gối Lót Cho Giường Bé - Trắng', 79000, 'Gối mềm, lót giường thoáng khí', '3-6', 55),
('Đệm Chống Trào Ngược - Size M', 159000, 'Đệm thoáng khí, giúp bé ngủ tốt', '3-6', 30),

-- 6-12 Tháng
('Bộ Quần Áo Bé Gái - Hoa Nhí', 199000, 'Bộ quần áo đáng yêu cho bé gái 6-12 tháng', '6-12', 48),
('Bộ Quần Áo Bé Trai - Hoạ Tiết', 199000, 'Bộ quần áo thoải mái cho bé trai', '6-12', 52),
('Giầy Tập Đi Cho Bé - Hỗ Trợ Bàn Chân', 149000, 'Giầy mềm giúp bé tập đi, bảo vệ bàn chân', '6-12', 65),
('Bộ Đồ Chơi Xếp Hình - 10 Chi Tiết', 129000, 'Đồ chơi phát triển trí tuệ, an toàn 100%', '6-12', 75),
('Bát Ăn Cơm Cho Bé - Có Chân', 39000, 'Bát melamin an toàn, không dễ vỡ', '6-12', 100),

-- Phụ kiện
('Chai Sữa Coton 125ml - Không BPA', 49000, 'Chai sữa chất lượng cao, dễ vệ sinh', 'phu-kien', 120),
('Núm Ty Silicone - Size M & L (2 cái)', 59000, 'Núm ty mềm, giống điều kiện tự nhiên', 'phu-kien', 90),
('Chổi Rửa Chai Sữa - Đầu Silicone', 19000, 'Chổi rửa vệ sinh, không gây hại cho chai', 'phu-kien', 150),
('Bộ Quần Áo Bảo Vệ Bé - Tấm Dã Ngoại', 89000, 'Thảm chơi di động, dễ mang theo', 'phu-kien', 40),
('Kem Chống Hăm Cho Bé - 50ml', 79000, 'Kem an toàn, thành phần thiên nhiên', 'phu-kien', 85);

-- ============================================
-- BƯỚC 10: THÊM TÀI KHOẢN ADMIN MẶC ĐỊNH
-- ============================================
-- Username: admin
-- Password: buithilan1995 (plain text đơn giản)
INSERT INTO `users` (username, email, name, full_name, phone, password, role) 
VALUES (
  'admin', 
  'admin@shopme.vn', 
  'Admin', 
  'Người Quản Lý', 
  '0866021711', 
  'buithilan1995', 
  'admin'
);

-- ============================================
-- BƯỚC 11: KIỂM TRA CẤU TRÚC BẢNG
-- ============================================
-- Hiển thị tất cả bảng
SHOW TABLES;

-- Hiển thị cấu trúc bảng users
DESCRIBE users;
DESCRIBE products;
DESCRIBE categories;

-- ============================================
-- BƯỚC 12: HIỂN THỊ DỮ LIỆU
-- ============================================
-- Xem tất cả sản phẩm
SELECT 
  `id`,
  `name`,
  `price`,
  `category`,
  `stock`
FROM `products`
ORDER BY `category` ASC;

-- Xem tất cả danh mục
SELECT * FROM `categories`;

-- Xem tất cả người dùng
SELECT 
  `id`,
  `username`,
  `role`,
  `full_name`,
  `email`,
  `phone`,
  `created_at`
FROM `users`
ORDER BY `id` DESC;

-- ============================================
-- BƯỚC 13: THỐNG KÊ
-- ============================================
-- Thống kê tổng sản phẩm theo danh mục
SELECT 
  `category`,
  COUNT(*) as `total`,
  SUM(`stock`) as `stock_total`,
  AVG(`price`) as `avg_price`
FROM `products`
GROUP BY `category`;

-- Thống kê số lượng user
SELECT 
  `role`,
  COUNT(*) as `count`
FROM `users`
GROUP BY `role`;

-- ============================================
-- NOTE: 
-- ============================================
-- Admin Password: buithilan1995
-- Username: admin
-- Role: admin
-- 
-- Sau khi chạy, đăng nhập tại /tai-khoan.php
-- Tên đăng nhập: admin
-- Mật khẩu: buithilan1995
-- ============================================
