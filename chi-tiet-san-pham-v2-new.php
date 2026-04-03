<?php
session_start();
require_once 'config.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header('Location: cua-hang.php');
    exit;
}

try {
    $sql = "SELECT id, name, price, COALESCE(discount_percent, 0) as discount_percent, description, category, stock, image, created_at FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if (!$product) {
        header('Location: cua-hang.php');
        exit;
    }
    
    $sql_related = "SELECT id, name, price, COALESCE(discount_percent, 0) as discount_percent, image, stock FROM products WHERE category = ? AND id != ? LIMIT 6";
    $stmt_related = $conn->prepare($sql_related);
    $stmt_related->bind_param("si", $product['category'], $product_id);
    $stmt_related->execute();
    $related_result = $stmt_related->get_result();
    $related_products = $related_result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Lỗi: " . $e->getMessage());
}

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
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding-top: 80px; }
        
        .product-detail {
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 40% 60%;
            gap: 30px;
            margin-bottom: 30px;
        }

        .product-images {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .main-image {
            width: 100%;
            aspect-ratio: 1;
            background: #f5f5f5;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .thumbnails {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 5px 0;
        }

        .thumbnail {
            width: 60px;
            height: 60px;
            min-flex-shrink: 0;
            background: #f5f5f5;
            border: 2px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.2s;
        }

        .thumbnail:hover, .thumbnail.active {
            border-color: #FF6B35;
        }

        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .product-info {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .category-tag {
            display: inline-block;
            background: #f0f0f0;
            color: #666;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            width: fit-content;
        }

        .product-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #000;
            line-height: 1.4;
        }

        .rating-section {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 0.9rem;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .rating-stars {
            color: #FFD700;
            font-size: 1.1rem;
        }

        .rating-number {
            color: #000;
            font-weight: 600;
        }

        .rating-count {
            color: #0088FF;
            cursor: pointer;
        }

        .sold-count {
            color: #666;
        }

        .price-section {
            background: #FFF3E0;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .price-original {
            font-size: 1.1rem;
            color: #999;
            text-decoration: line-through;
        }

        .price-current {
            font-size: 2.5rem;
            color: #FF6B35;
            font-weight: 700;
        }

        .discount-badge {
            background: #FF6B35;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 1rem;
        }

        .savings-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
        }

        .promo-items {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .promo-item {
            display: flex;
            gap: 10px;
            font-size: 0.9rem;
            color: #666;
            align-items: center;
        }

        .promo-icon {
            font-size: 1.2rem;
            min-width: 20px;
        }

        .size-section {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .size-label {
            font-weight: 600;
            margin-bottom: 10px;
            color: #000;
        }

        .size-options {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .size-btn {
            background: white;
            border: 2px solid #ddd;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .size-btn:hover {
            border-color: #FF6B35;
            color: #FF6B35;
        }

        .size-btn.active {
            background: #FFE5E0;
            border-color: #FF6B35;
            color: #FF6B35;
        }

        .quantity-section {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quantity-label {
            font-weight: 600;
            color: #000;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            background: #f5f5f5;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            background: #e8e8e8;
        }

        .qty-input {
            width: 50px;
            height: 36px;
            border: none;
            text-align: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .stock-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #666;
        }

        .stock-dot {
            width: 12px;
            height: 12px;
            background: #27AE60;
            border-radius: 50%;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin: 20px 0;
        }

        .btn {
            padding: 14px;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-cart {
            background: white;
            color: #FF6B35;
            border: 2px solid #FF6B35;
        }

        .btn-cart:hover {
            background: #FFE5E0;
        }

        .btn-buy {
            background: #FF6B35;
            color: white;
        }

        .btn-buy:hover {
            background: #E55A24;
        }

        .shop-section {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
        }

        .shop-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .shop-avatar {
            width: 50px;
            height: 50px;
            background: #FF6B35;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .shop-details h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #000;
        }

        .shop-badge {
            background: #FFE8CB;
            color: #FF6B35;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 8px;
        }

        .shop-contact {
            background: white;
            border: 1px solid #ddd;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            color: #333;
            transition: all 0.2s;
        }

        .shop-contact:hover {
            border-color: #0088FF;
            color: #0088FF;
        }

        .related-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #000;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 12px;
        }

        .related-card {
            background: white;
            border-radius: 4px;
            border: 1px solid #eee;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s;
        }

        .related-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .related-image {
            width: 100%;
            height: 150px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .related-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .related-discount {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #FF6B35;
            color: white;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .related-info {
            padding: 10px;
        }

        .related-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: #000;
            line-height: 1.2;
            margin-bottom: 6px;
            height: 2.4rem;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .related-price {
            font-size: 1.1rem;
            color: #FF6B35;
            font-weight: 700;
        }

        .header { padding-top: 0; }

        @media (max-width: 768px) {
            .detail-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }

            .product-title {
                font-size: 1.3rem;
            }

            .price-current {
                font-size: 1.8rem;
            }

            .related-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
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
            </div>
            <div class="header-right">
                <a href="tai-khoan.php" class="header-icon-btn">👤</a>
                <a href="gio-hang.php" class="header-icon-btn cart-btn">
                    <img src="hinh-anh/icons/order.png" alt="Giỏ hàng" class="header-icon-img">
                    <span class="icon-badge" id="cart-count">0</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div style="background: white; padding: 12px 20px; border-bottom: 1px solid #eee; max-width: 1200px; margin: 0 auto;">
        <a href="trang-chu.php" style="color: #0088FF; text-decoration: none;">🏠 Trang chủ</a>
        <span> / </span>
        <a href="cua-hang.php?category=<?php echo urlencode($product['category']); ?>" style="color: #0088FF; text-decoration: none;"><?php echo htmlspecialchars($product['category']); ?></a>
        <span> / </span>
        <span><?php echo htmlspecialchars(substr($product['name'], 0, 50)); ?></span>
    </div>

    <!-- Main Product Detail -->
    <div class="product-detail">
        <div class="detail-grid">
            <!-- Left: Images -->
            <div class="product-images">
                <div class="main-image" id="mainImage">
                    <?php if ($product['image']): ?>
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                        <div style="font-size: 3rem;">📦</div>
                    <?php endif; ?>
                </div>
                <div class="thumbnails">
                    <?php if (!empty($product['image'])): ?>
                        <div class="thumbnail active" onclick="changeImage(this)">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Thumbnail">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Info & Actions -->
            <div class="product-info">
                <span class="category-tag"><?php echo htmlspecialchars($product['category']); ?></span>
                <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>

                <div class="rating-section">
                    <span class="rating-stars">★★★★★</span>
                    <span class="rating-number">4.9</span>
                    <span class="rating-count">(3.7k Đánh giá)</span>
                    <span class="sold-count">| 129 Đã bán</span>
                </div>

                <div class="price-section">
                    <div>
                        <?php if ($discount_percent > 0): ?>
                            <div class="price-original">₫<?php echo number_format($product['price'], 0, ',', '.'); ?></div>
                        <?php endif; ?>
                        <div class="price-current">₫<?php echo number_format($final_price, 0, ',', '.'); ?></div>
                        <?php if ($discount_percent > 0): ?>
                            <div class="savings-text">Tiết kiệm ₫<?php echo number_format($savings, 0, ',', '.'); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if ($discount_percent > 0): ?>
                        <div class="discount-badge">-<?php echo intval($discount_percent); ?>%</div>
                    <?php endif; ?>
                </div>

                <div class="promo-items">
                    <div class="promo-item">
                        <span class="promo-icon">🚚</span>
                        <span>Miễn phí vận chuyển từ 80.000₫</span>
                    </div>
                    <div class="promo-item">
                        <span class="promo-icon">🔄</span>
                        <span>Trả hàng miễn phí trong 30 ngày</span>
                    </div>
                    <div class="promo-item">
                        <span class="promo-icon">✅</span>
                        <span>Hàng chính hãng 100% được kiểm duyệt</span>
                    </div>
                </div>

                <div class="size-section">
                    <div class="size-label">Kích cỡ: *</div>
                    <div class="size-options">
                        <button class="size-btn active" onclick="selectSize(this, 'S(0-3m)')">S(0-3m)</button>
                        <button class="size-btn" onclick="selectSize(this, 'M(3-6m)')">M(3-6m)</button>
                        <button class="size-btn" onclick="selectSize(this, 'L(6-9m)')">L(6-9m)</button>
                        <button class="size-btn" onclick="selectSize(this, 'XL(trên9m)')">XL(9m+)</button>
                    </div>
                </div>

                <div class="quantity-section">
                    <div class="quantity-label">Số lượng:</div>
                    <div class="quantity-control">
                        <button class="qty-btn" onclick="decreaseQty()">−</button>
                        <input type="number" id="quantity" class="qty-input" value="1" min="1" max="<?php echo $product['stock']; ?>">
                        <button class="qty-btn" onclick="increaseQty()">+</button>
                    </div>
                    <div class="stock-status">
                        <div class="stock-dot"></div>
                        <span><?php echo $product['stock'] > 0 ? 'Còn ' . $product['stock'] . ' sản phẩm' : 'Hết hàng'; ?></span>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-cart" onclick="addToCart(<?php echo $product['id']; ?>)">🛒 THÊM VÀO GIỎ HÀNG</button>
                    <button class="btn btn-buy" onclick="buyNow(<?php echo $product['id']; ?>)">💳 MUA NGAY</button>
                </div>

                <div class="shop-section">
                    <div class="shop-info">
                        <div class="shop-avatar">🏪</div>
                        <div class="shop-details">
                            <h3>Shop Mẹ và Bé Đông Lan <span class="shop-badge">OFFICIAL</span></h3>
                            <p style="margin: 4px 0; font-size: 0.9rem; color: #666;">Rating: 4.9 ⭐ | Sản phẩm: 2500+</p>
                        </div>
                    </div>
                    <button class="shop-contact" onclick="window.open('https://zalo.me/0866021711')">📞 Liên hệ</button>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
        <div class="related-section">
            <h2 class="section-title">🛍️ Sản Phẩm Liên Quan</h2>
            <div class="related-grid">
                <?php foreach ($related_products as $prod):
                    $prod_discount = floatval($prod['discount_percent']) ?? 0;
                    $prod_final = $prod['price'] * (1 - $prod_discount / 100);
                ?>
                <div class="related-card" onclick="window.location.href='chi-tiet-san-pham-v2.php?id=<?php echo $prod['id']; ?>'">
                    <div class="related-image">
                        <?php if ($prod['image']): ?>
                            <img src="<?php echo htmlspecialchars($prod['image']); ?>" alt="">
                        <?php else: ?>
                            <div style="font-size: 2rem;">📦</div>
                        <?php endif; ?>
                        <?php if ($prod_discount > 0): ?>
                            <div class="related-discount">-<?php echo intval($prod_discount); ?>%</div>
                        <?php endif; ?>
                    </div>
                    <div class="related-info">
                        <div class="related-name"><?php echo htmlspecialchars($prod['name']); ?></div>
                        <div class="related-price">₫<?php echo number_format($prod_final, 0, ',', '.'); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer style="background: #333; color: white; padding: 20px; text-align: center; margin-top: 40px;">
        <p>&copy; 2026 Shop Mẹ và Bé Đông Lan - Tất cả quyền được bảo lưu</p>
    </footer>

    <script src="js-kiem-soat/header-scroll.js"></script>
    <script>
        const API_BASE = './api-dieu-khien';

        function changeImage(el) {
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            const src = el.querySelector('img').src;
            document.querySelector('#mainImage img').src = src;
        }

        function selectSize(btn, size) {
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            window.selectedSize = size;
        }

        function increaseQty() {
            const input = document.getElementById('quantity');
            const max = parseInt(input.max);
            if (parseInt(input.value) < max) input.value = parseInt(input.value) + 1;
        }

        function decreaseQty() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
        }

        async function checkLogin() {
            try {
                const response = await fetch(`${API_BASE}/xac-thuc.php?action=checkLogin`);
                const data = await response.json();
                return data.status === 'success';
            } catch (error) {
                return false;
            }
        }

        async function addToCart(productId) {
            const isLoggedIn = await checkLogin();
            if (!isLoggedIn) {
                alert('⚠️ Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng');
                window.location.href = 'tai-khoan.php';
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
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                alert('❌ Lỗi: ' + error.message);
            }
        }

        async function buyNow(productId) {
            const isLoggedIn = await checkLogin();
            if (!isLoggedIn) {
                alert('⚠️ Bạn cần đăng nhập để mua hàng');
                window.location.href = 'tai-khoan.php';
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
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                alert('❌ Lỗi: ' + error.message);
            }
        }

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

        document.addEventListener('DOMContentLoaded', updateCartCount);
        window.selectedSize = 'S(0-3m)';
    </script>
</body>
</html>
