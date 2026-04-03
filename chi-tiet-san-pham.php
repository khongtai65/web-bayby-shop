<?php
session_start();
require_once 'config.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header('Location: cua-hang.php');
    exit;
}

// Fetch product details with all info
try {
    $sql = "SELECT id, name, price, COALESCE(discount_percent, 0) as discount_percent, description, category, stock, image, gender, created_at FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if (!$product) {
        header('Location: cua-hang.php');
        exit;
    }
    
    // Fetch related products from same category
    $sql_related = "SELECT id, name, price, COALESCE(discount_percent, 0) as discount_percent, image, stock FROM products WHERE category = ? AND id != ? LIMIT 12";
    $stmt_related = $conn->prepare($sql_related);
    $stmt_related->bind_param("si", $product['category'], $product_id);
    $stmt_related->execute();
    $related_result = $stmt_related->get_result();
    $related_products = $related_result->fetch_all(MYSQLI_ASSOC);
    
    // Best selling products
    $sql_bestsell = "SELECT id, name, price, COALESCE(discount_percent, 0) as discount_percent, image, stock FROM products WHERE category != ? ORDER BY id DESC LIMIT 10";
    $stmt_bestsell = $conn->prepare($sql_bestsell);
    $stmt_bestsell->bind_param("s", $product['category']);
    $stmt_bestsell->execute();
    $bestsell_result = $stmt_bestsell->get_result();
    $bestsell_products = $bestsell_result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Lỗi: " . $e->getMessage());
}

// Calculate final price
$discount_percent = floatval($product['discount_percent']) ?? 0;
$final_price = $product['price'] * (1 - $discount_percent / 100);
$savings = $product['price'] - $final_price;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Shop Mẹ và Bé Đông Lan</title>
    <link rel="stylesheet" href="css-kieu-dang/kieu-dang.css">
    <link rel="stylesheet" href="css-kieu-dang/mobile.css" media="(max-width: 768px)">
    <link rel="stylesheet" href="css/social-icons.css">
    <link rel="stylesheet" href="css/search.css">
    <style>
        .product-detail-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .product-image-gallery {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .product-main-image {
            width: 100%;
            height: 500px;
            background: #f5f5f5;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-main-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .discount-badge-large {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 1.2rem;
            box-shadow: 0 3px 10px rgba(231, 76, 60, 0.3);
        }

        .flash-sale-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .product-thumbnails {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .product-thumbnail {
            width: 80px;
            height: 80px;
            background: #f5f5f5;
            border-radius: 8px;
            cursor: pointer;
            overflow: hidden;
            border: 2px solid transparent;
            transition: all 0.3s;
        }

        .product-thumbnail:hover,
        .product-thumbnail.active {
            border-color: #CD853F;
        }

        .product-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .product-info-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .product-header {
            display: flex;
            flex-direction: column;
            gap: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .product-category {
            color: #CD853F;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .product-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #333;
            line-height: 1.3;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .rating-stars {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stars {
            color: #FFB800;
            font-size: 1rem;
        }

        .rating-score {
            background: #fff0e6;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            color: #CD853F;
            font-size: 0.9rem;
        }

        .review-count {
            color: #666;
            font-size: 0.95rem;
            text-decoration: underline;
            cursor: pointer;
        }

        .price-section {
            background: linear-gradient(135deg, #fff0e6 0%, #ffe8d6 100%);
            padding: 20px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .price-row {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .original-price {
            font-size: 1.1rem;
            color: #999;
            text-decoration: line-through;
        }

        .final-price {
            font-size: 2.5rem;
            color: #E74C3C;
            font-weight: bold;
        }

        .savings {
            background: #E74C3C;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .savings-text {
            color: #E74C3C;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .stock-section {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .stock-label {
            color: #666;
            font-weight: 600;
        }

        .stock-status {
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.95rem;
        }

        .stock-available {
            background: #d4edda;
            color: #155724;
        }

        .stock-low {
            background: #fff3cd;
            color: #856404;
        }

        .stock-out {
            background: #f8d7da;
            color: #721c24;
        }

        .shipping-section {
            background: #f0fffe;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #CD853F;
        }

        .shipping-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 10px;
        }

        .shipping-item:last-child {
            margin-bottom: 0;
        }

        .shipping-icon {
            font-size: 1.3rem;
            min-width: 30px;
            text-align: center;
        }

        .shipping-text {
            flex: 1;
            font-size: 0.95rem;
            color: #666;
            line-height: 1.4;
        }

        .shipping-text strong {
            color: #333;
            display: block;
            margin-bottom: 2px;
        }

        .variant-section {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .variant-label {
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }

        .variant-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .variant-option {
            padding: 12px 16px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            color: #333;
        }

        .variant-option:hover {
            border-color: #CD853F;
        }

        .variant-option.selected {
            border-color: #CD853F;
            background: #fff0e6;
            color: #CD853F;
        }

        .quantity-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quantity-label {
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }

        .quantity-control {
            display: flex;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .quantity-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: #f5f5f5;
            color: #333;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
        }

        .quantity-btn:hover {
            background: #e0e0e0;
        }

        .quantity-input {
            width: 60px;
            height: 40px;
            border: none;
            text-align: center;
            font-size: 1rem;
            font-weight: bold;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 20px;
        }

        .btn-action {
            padding: 16px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-cart {
            background: #fff0e6;
            color: #CD853F;
            border: 2px solid #CD853F;
        }

        .btn-cart:hover {
            background: #fff5er;
        }

        .btn-buy {
            background: #E74C3C;
            color: white;
        }

        .btn-buy:hover {
            background: #C0392B;
        }

        .description-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .description-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            border-bottom: 2px solid #CD853F;
            padding-bottom: 10px;
        }

        .description-content {
            color: #666;
            line-height: 1.8;
            font-size: 0.95rem;
            text-align: justify;
        }

        .related-products {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #eee;
        }

        .related-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .related-product-card {
            background: white;
            border: 1px solid #eee;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s;
            cursor: pointer;
        }

        .related-product-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }

        .related-image {
            width: 100%;
            height: 180px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .related-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .related-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #E74C3C;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.8rem;
        }

        .related-info {
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .related-name {
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
            line-height: 1.3;
            min-height: 40px;
        }

        .related-price {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .related-original {
            text-decoration: line-through;
            color: #999;
            font-size: 0.85rem;
        }

        .related-final {
            color: #E74C3C;
            font-weight: bold;
            font-size: 1rem;
        }

        @media (max-width: 768px) {
            .product-detail-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .product-main-image {
                height: 350px;
            }

            .product-title {
                font-size: 1.4rem;
            }

            .final-price {
                font-size: 2rem;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }

            .related-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="header-container">
            <div class="header-left">
                <a href="trang-chu.php" class="header-logo">
                    <img src="hinh-anh/logo.png" alt="Logo" class="header-logo-img">
                </a>
                <div class="logo-text">
                    <div class="logo-brand">Shop Mẹ và Bé Đông Lan</div>
                    <div class="logo-phone">📱 0866.021.711</div>
                </div>
            </div>
            <div class="header-search">
                <input type="text" placeholder="Bạn cần tìm gì ...?" class="search-input">
                <button class="search-btn">🔍</button>
                <div id="search-results"></div>
            </div>
            <div class="header-right">
                <div class="social-links">
                    <a href="https://zalo.me/0866021711" class="social-btn zalo" title="Zalo" target="_blank"></a>
                    <a href="https://www.facebook.com/profile.php?id=61580260876532" class="social-btn facebook" title="Facebook" target="_blank"></a>
                    <a href="https://www.facebook.com/profile.php?id=61580260876532" class="social-btn messenger" title="Messenger" target="_blank"></a>
                </div>
                <div class="hotline-section">
                    <span class="hotline-label">Hotline</span>
                    <span class="hotline-number">0866.021.711</span>
                </div>
                <a href="tai-khoan.php" class="header-icon-btn" title="Tài khoản">
                    <span>👤</span>
                </a>
                <a href="gio-hang.php" class="header-icon-btn cart-btn" title="Giỏ hàng">
                    <img src="hinh-anh/icons/order.png" alt="Giỏ hàng" class="header-icon-img">
                    <span class="icon-badge" id="cart-count">0</span>
                </a>
                <button class="menu-toggle" id="menuToggle" title="Menu">☰</button>
            </div>
        </div>
    </div>

    <!-- Popup Menu -->
    <div class="popup-menu" id="popupMenu">
        <div class="popup-header">
            <h3>Menu</h3>
            <button class="popup-close" id="popupClose">&times;</button>
        </div>
        <div class="popup-section">
            <h4>DANH MỤC</h4>
            <a href="trang-chu.php" class="popup-link">🏠 Trang chủ</a>
            <a href="cua-hang.php?category=0-3" class="popup-link">0-3 Tháng</a>
            <a href="cua-hang.php?category=3-6" class="popup-link">3-6 Tháng</a>
            <a href="cua-hang.php?category=6-12" class="popup-link">6-12 Tháng</a>
            <a href="cua-hang.php?category=phu-kien" class="popup-link">Phụ kiện</a>
        </div>
        <div class="popup-section">
            <h4>LIÊN HỆ</h4>
            <div class="popup-social">
                <a href="https://zalo.me/0866021711" class="social-btn zalo" title="Zalo" target="_blank"></a>
                <a href="https://www.facebook.com/profile.php?id=61580260876532" class="social-btn facebook" title="Facebook" target="_blank">f</a>
                <a href="https://www.facebook.com/profile.php?id=61580260876532" class="social-btn messenger" title="Messenger" target="_blank">💬</a>
            </div>
        </div>
        <div class="popup-section">
            <h4>🔔 HỖ TRỢ 24/7</h4>
            <div class="popup-support">
                <strong>Liên hệ:</strong> 📞 0866.021.711<br>
                <small>Thứ 2 - Thứ 7</small>
            </div>
        </div>
    </div>

    <!-- Popup Overlay -->
    <div class="popup-overlay" id="popupOverlay"></div>

    <!-- Navigation Menu -->
    <nav class="main-nav">
        <a href="trang-chu.php" class="nav-link">TRANG CHỦ</a>
        <a href="cua-hang.php?category=0-3" class="nav-link">0-3 THÁNG</a>
        <a href="cua-hang.php?category=3-6" class="nav-link">3-6 THÁNG</a>
        <a href="cua-hang.php?category=6-12" class="nav-link">6-12 THÁNG</a>
        <a href="cua-hang.php?category=phu-kien" class="nav-link">PHỤ KIỆN</a>
    </nav>

    <div class="container">
        <!-- Product Detail Main -->
        <div class="product-detail-container">
            <!-- Left: Product Images -->
            <div class="product-image-gallery">
                <div class="product-main-image" id="mainImage">
                    <?php if ($product['image']): ?>
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" id="mainImg">
                    <?php else: ?>
                        <div style="font-size: 3rem;">📦</div>
                    <?php endif; ?>
                    <?php if ($discount_percent > 0): ?>
                        <div class="discount-badge-large">-<?php echo number_format($discount_percent); ?>%</div>
                    <?php endif; ?>
                    <div class="flash-sale-badge">🔥 YÊU THÍCH</div>
                </div>
                
                <!-- Thumbnails -->
                <div class="product-thumbnails">
                    <?php if ($product['image']): ?>
                        <div class="product-thumbnail active" onclick="changeMainImage(this)">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Thumbnail">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Product Info -->
            <div class="product-info-section">
                <!-- Product Header -->
                <div class="product-header">
                    <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                    <div class="product-title"><?php echo htmlspecialchars($product['name']); ?></div>
                    
                    <div class="product-rating">
                        <div class="rating-stars">
                            <span class="stars">★★★★★</span>
                            <span class="rating-score">4.9</span>
                        </div>
                        <span class="review-count">3,7k Đánh giá</span>
                        <span style="color: #666; font-size: 0.95rem;">129 Đã thích</span>
                    </div>
                </div>

                <!-- Price Section -->
                <div class="price-section">
                    <div class="price-row">
                        <?php if ($discount_percent > 0): ?>
                            <span class="original-price">₫<?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                        <?php endif; ?>
                        <span class="final-price">₫<?php echo number_format($final_price, 0, ',', '.'); ?></span>
                        <?php if ($discount_percent > 0): ?>
                            <span class="savings">-<?php echo number_format($discount_percent); ?>%</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($discount_percent > 0): ?>
                        <div class="savings-text">
                            ✨ Tiết kiệm: ₫<?php echo number_format($product['price'] - $final_price, 0, ',', '.'); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Stock Section -->
                <div class="stock-section">
                    <span class="stock-label">📦 Kho hàng:</span>
                    <?php if ($product['stock'] > 20): ?>
                        <span class="stock-status stock-available">✅ Còn <?php echo $product['stock']; ?> sản phẩm</span>
                    <?php elseif ($product['stock'] > 0): ?>
                        <span class="stock-status stock-low">⚠️ Chỉ còn <?php echo $product['stock']; ?> sản phẩm</span>
                    <?php else: ?>
                        <span class="stock-status stock-out">❌ Hết hàng tạm thời</span>
                    <?php endif; ?>
                </div>

                <!-- Shipping Section -->
                <div class="shipping-section">
                    <div class="shipping-item">
                        <div class="shipping-icon">🚚</div>
                        <div class="shipping-text">
                            <strong>Miễn phí vận chuyển</strong>
                            Từ 80.000đ trở lên
                        </div>
                    </div>
                    <div class="shipping-item">
                        <div class="shipping-icon">🔄</div>
                        <div class="shipping-text">
                            <strong>Đổi trả dễ dàng</strong>
                            Trong 30 ngày nếu sản phẩm bị lỗi
                        </div>
                    </div>
                    <div class="shipping-item">
                        <div class="shipping-icon">✅</div>
                        <div class="shipping-text">
                            <strong>Hàng chính hãng 100%</strong>
                            Được kiểm duyệt kỹ trước khi gửi
                        </div>
                    </div>
                </div>

                <!-- Variants Section -->
                <div class="variant-section">
                    <div class="variant-label">👶 Kích cỡ:</div>
                    <div class="variant-options">
                        <div class="variant-option selected" onclick="selectSize(this, 'S(0-3m)')">S(0-3m)</div>
                        <div class="variant-option" onclick="selectSize(this, 'M(3-6m)')">M(3-6m)</div>
                        <div class="variant-option" onclick="selectSize(this, 'L(6-9m)')">L(6-9m)</div>
                        <div class="variant-option" onclick="selectSize(this, 'XL(trên9m)')">XL(trên9m)</div>
                    </div>
                </div>

                <!-- Quantity Section -->
                <div class="quantity-section">
                    <span class="quantity-label">Số lượng:</span>
                    <div class="quantity-control">
                        <button class="quantity-btn" onclick="decreaseQuantity()">−</button>
                        <input type="number" id="quantity" class="quantity-input" value="1" min="1" max="<?php echo $product['stock']; ?>" onchange="validateQuantity()">
                        <button class="quantity-btn" onclick="increaseQuantity()">+</button>
                    </div>
                    <span style="color: #666; font-size: 0.9rem;" id="stockInfo"><?php echo $product['stock']; ?> có sẵn</span>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="btn-action btn-cart" onclick="addToCart(<?php echo $product['id']; ?>)">
                        🛒 Thêm Giỏ Hàng
                    </button>
                    <button class="btn-action btn-buy" onclick="buyNow(<?php echo $product['id']; ?>)">
                        💳 Mua Ngay
                    </button>
                </div>
            </div>
        </div>

        <!-- Description Section -->
        <div class="description-section">
            <div class="description-title">📝 Mô Tả Sản Phẩm</div>
            <div class="description-content">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
        <div class="related-products">
            <div class="related-title">
                🛍️ Sản Phẩm Liên Quan
            </div>
            <div class="related-grid">
                <?php foreach ($related_products as $related): 
                    $rel_discount = floatval($related['discount_percent']) ?? 0;
                    $rel_final = $related['price'] * (1 - $rel_discount / 100);
                ?>
                <div class="related-product-card" onclick="window.location.href='chi-tiet-san-pham.php?id=<?php echo $related['id']; ?>'">
                    <div class="related-image">
                        <?php if ($related['image']): ?>
                            <img src="<?php echo htmlspecialchars($related['image']); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                        <?php else: ?>
                            <div style="font-size: 2rem;">📦</div>
                        <?php endif; ?>
                        <?php if ($rel_discount > 0): ?>
                            <div class="related-badge">-<?php echo number_format($rel_discount); ?>%</div>
                        <?php endif; ?>
                    </div>
                    <div class="related-info">
                        <div class="related-name"><?php echo htmlspecialchars($related['name']); ?></div>
                        <div class="related-price">
                            <?php if ($rel_discount > 0): ?>
                                <span class="related-original">₫<?php echo number_format($related['price'], 0, ',', '.'); ?></span>
                            <?php endif; ?>
                            <span class="related-final">₫<?php echo number_format($rel_final, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 Shop Mẹ và Bé Đông Lan - Tất cả quyền được bảo lưu</p>
    </footer>

    <!-- Login Modal -->
    <div id="login-modal" style="display: none !important; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 9999; align-items: center; justify-content: center; pointer-events: none;">
        <div style="background: white; border-radius: 15px; padding: 2rem; width: 90%; max-width: 500px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); pointer-events: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="margin: 0; color: #CD853F;">🔐 Đăng Nhập / Đăng Ký</h2>
                <button onclick="closeLoginModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <p style="margin: 0 0 1.5rem; color: #666; font-size: 1rem;">
                ⚠️ Bạn cần đăng nhập hoặc đăng ký để tiếp tục mua hàng
            </p>
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                <button onclick="showLoginTab()" id="btn-login-tab" class="modal-tab-btn active" style="flex: 1; padding: 12px; border: 2px solid #CD853F; background: #CD853F; color: white; border-radius: 8px; cursor: pointer; font-weight: bold;">
                    🔓 Đăng Nhập
                </button>
                <button onclick="showRegisterTab()" id="btn-register-tab" class="modal-tab-btn" style="flex: 1; padding: 12px; border: 2px solid #ddd; background: #f5f5f5; color: #333; border-radius: 8px; cursor: pointer; font-weight: bold;">
                    📝 Đăng Ký
                </button>
            </div>
            <div id="modal-login-tab" style="display: block;">
                <form onsubmit="return handleLoginSubmit(event)">
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Tên đăng nhập</label>
                        <input type="text" id="modal-login-username" name="username" required minlength="4" maxlength="12" placeholder="4-12 ký tự" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Mật khẩu</label>
                        <input type="password" id="modal-login-password" name="password" required minlength="6" maxlength="16" placeholder="Tối đa 16 ký tự" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
                    </div>
                    <button type="submit" style="width: 100%; padding: 12px; background: #CD853F; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">
                        ✅ Đăng Nhập
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="js-kiem-soat/header-scroll.js"></script>
    <script>
        const API_BASE = './api-dieu-khien';
        
        // Popup Menu Handler
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const popupMenu = document.getElementById('popupMenu');
            const popupOverlay = document.getElementById('popupOverlay');
            const popupClose = document.getElementById('popupClose');
            
            if (!menuToggle || !popupMenu) return;
            
            // Open menu
            menuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                popupMenu.classList.add('active');
                popupOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
            
            // Close menu
            function closeMenu() {
                popupMenu.classList.remove('active');
                popupOverlay.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
            
            if (popupClose) {
                popupClose.addEventListener('click', closeMenu);
            }
            
            if (popupOverlay) {
                popupOverlay.addEventListener('click', closeMenu);
            }
            
            // Close menu when clicking links
            const popupLinks = popupMenu.querySelectorAll('a');
            popupLinks.forEach(link => {
                link.addEventListener('click', closeMenu);
            });
        });
        

        // Check login status
        async function checkLogin() {
            try {
                const response = await fetch(`${API_BASE}/xac-thuc.php?action=checkLogin`);
                const data = await response.json();
                return data.status === 'success';
            } catch (error) {
                console.error('Error:', error);
                return false;
            }
        }

        // Format price
        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                maximumFractionDigits: 0
            }).format(price);
        }

        // Change main image
        function changeMainImage(element) {
            document.querySelectorAll('.product-thumbnail').forEach(t => t.classList.remove('active'));
            element.classList.add('active');
            const imgSrc = element.querySelector('img').src;
            document.getElementById('mainImg').src = imgSrc;
        }

        // Select size
        let selectedSize = 'S(0-3m)';
        function selectSize(element, size) {
            document.querySelectorAll('.variant-option').forEach(opt => opt.classList.remove('selected'));
            element.classList.add('selected');
            selectedSize = size;
        }

        // Quantity control
        function increaseQuantity() {
            const input = document.getElementById('quantity');
            const max = parseInt(input.max);
            if (parseInt(input.value) < max) {
                input.value = parseInt(input.value) + 1;
            }
        }

        function decreaseQuantity() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        function validateQuantity() {
            const input = document.getElementById('quantity');
            const max = parseInt(input.max);
            if (parseInt(input.value) > max) input.value = max;
            if (parseInt(input.value) < 1) input.value = 1;
        }

        // Add to cart
        async function addToCart(productId) {
            const isLoggedIn = await checkLogin();
            if (!isLoggedIn) {
                showLoginModal();
                return;
            }

            const quantity = parseInt(document.getElementById('quantity').value);
            
            try {
                const response = await fetch(`${API_BASE}/gio-hang.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: productId, quantity: quantity })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    alert('✅ Thêm vào giỏ hàng thành công!');
                    updateCartCount();
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Lỗi khi thêm vào giỏ hàng');
            }
        }

        // Buy now
        async function buyNow(productId) {
            const isLoggedIn = await checkLogin();
            if (!isLoggedIn) {
                showLoginModal();
                return;
            }

            const quantity = parseInt(document.getElementById('quantity').value);
            
            try {
                const response = await fetch(`${API_BASE}/gio-hang.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: productId, quantity: quantity })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    window.location.href = 'thanh-toan.php';
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Lỗi khi thêm vào giỏ hàng');
            }
        }

        // Update cart count
        async function updateCartCount() {
            try {
                const response = await fetch(`${API_BASE}/gio-hang.php`);
                const data = await response.json();
                
                if (data.status === 'success') {
                    const count = data.data.reduce((sum, item) => sum + item.quantity, 0);
                    document.getElementById('cart-count').textContent = count;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Login modal functions
        function showLoginModal() {
            const modal = document.getElementById('login-modal');
            modal.style.display = 'flex';
            modal.style.pointerEvents = 'auto';
        }

        function closeLoginModal() {
            const modal = document.getElementById('login-modal');
            modal.style.display = 'none';
            modal.style.pointerEvents = 'none';
        }

        function handleLoginSubmit(event) {
            event.preventDefault();
            const username = document.getElementById('modal-login-username').value;
            const password = document.getElementById('modal-login-password').value;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'api-dieu-khien/xac-thuc.php';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'login';
            
            const usernameInput = document.createElement('input');
            usernameInput.type = 'hidden';
            usernameInput.name = 'username';
            usernameInput.value = username;
            
            const passwordInput = document.createElement('input');
            passwordInput.type = 'hidden';
            passwordInput.name = 'password';
            passwordInput.value = password;
            
            const returnInput = document.createElement('input');
            returnInput.type = 'hidden';
            returnInput.name = 'returnUrl';
            returnInput.value = window.location.pathname;
            
            form.appendChild(actionInput);
            form.appendChild(usernameInput);
            form.appendChild(passwordInput);
            form.appendChild(returnInput);
            
            document.body.appendChild(form);
            form.submit();
            
            return false;
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', updateCartCount);
    </script>
</body>
</html>
