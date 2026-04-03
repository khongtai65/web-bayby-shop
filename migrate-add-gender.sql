-- ============================================
-- MIGRATION: Thêm cột GENDER vào bảng products
-- ============================================
-- Chạy trên phpMyAdmin hoặc MySQL command line nếu cột chưa tồn tại
-- ============================================

-- Kiểm tra và thêm cột gender nếu chưa tồn tại
ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `gender` VARCHAR(50) DEFAULT 'unisex';

-- Cập nhật các cột CREATE INDEX nếu cần
ALTER TABLE `products` ADD INDEX IF NOT EXISTS `idx_gender` (`gender`);

-- Hiển thị kết quả
SELECT 
    id, 
    name, 
    category, 
    gender, 
    price, 
    discount_percent, 
    stock 
FROM products 
ORDER BY created_at DESC;
