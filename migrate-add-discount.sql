-- ============================================
-- MIGRATION: Thêm trường giảm giá cho products
-- ============================================
-- Chạy lệnh này nếu bảng products đã tồn tại
-- mysql -u root -p baby_shop < migrate-add-discount.sql

ALTER TABLE `products` ADD COLUMN `discount_percent` DECIMAL(5, 2) DEFAULT 0;

-- Nếu cột đã tồn tại, lệnh trên sẽ báo lỗi nhưng không ảnh hưởng
-- Bạn cũng có thể chạy lệnh này để an toàn:
-- ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `discount_percent` DECIMAL(5, 2) DEFAULT 0;
