<?php
session_start();
require_once 'config.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header('Location: cua-hang.php');
    exit;
}

try {
    $sql = "SELECT id, name, price, discount_percent, description, category, stock, image, created_at FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if (!$product) {
        header('Location: cua-hang.php');
        exit;
    }
    
    $sql_related = "SELECT id, name, price, discount_percent, image, stock FROM products WHERE category = ? AND id != ? LIMIT 6";
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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="css-kieu-dang/kieu-dang.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; background: #f5f5f5; padding-top: 80px; }
        
        .container-detail {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 50% 50%;
            gap: 0;
            background: white;
            min-height: calc(100vh - 80px);
        }

        .left-panel {
            padding: 30px;
            background: #f5f5f5;
            display: flex;
            flex-direction: column;
            gap: 15px;
            border-right: 1px solid #eee;
        }

        .main-image {
            width: 100%;
            aspect-ratio: 1;
            background: white;
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
            gap: 10px;
            overflow-x: auto;
        }

        .thumbnail {
            width: 70px;
            height: 70px;
            min-flex-shrink: 0;
            background: white;
            border: 2px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            overflow: hidden;
            transition: border 0.2s;
        }

        .thumbnail:hover, .thumbnail.active {
            border-color: #FF6B35;
        }

        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .right-panel {
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            overflow-y: auto;
        }

        .product-title {
            font-size: 1.5rem;
            font-weight: 600;
            line-height: 1.4;
            color: #000;
        }

        .product-meta {
            display: flex;
            gap: 20px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
            color: #666;
        }

        .rating {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .stars { color: #FFD700; }
        .rating-num { color: #000; font-weight: 600; }
        .rating-count { color: #0088FF; }

        .price-box {
            background: #FFF3E0;
            padding: 15px;
            border-radius: 6px;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .price-original {
            font-size: 1rem;
            text-decoration: line-through;
            color: #999;
        }

        .price-current {
            font-size: 2.2rem;
            color: #FF6B35;
            font-weight: 700;
        }

        .discount-tag {
            background: #FF6B35;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 1rem;
        }

        .color-section {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .section-label {
            font-weight: 600;
            margin-bottom: 10px;
            color: #000;
            font-size: 0.95rem;
        }

        .color-options {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .color-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #ddd;
            cursor: pointer;
            transition: all 0.2s;
        }

        .color-btn.active {
            border-color: #FF6B35;
            box-shadow: 0 0 0 2px rgba(255, 107, 53, 0.2);
        }

        .size-section {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .size-options {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .size-btn {
            background: white;
            border: 1px solid #ddd;
            padding: 8px 14px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
            min-width: 40px;
            text-align: center;
        }

        .size-btn:hover {
            border-color: #FF6B35;
        }

        .size-btn.active {
            background: #FF6B35;
            color: white;
            border-color: #FF6B35;
        }

        .qty-section {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .qty-control {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: fit-content;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            background: #f5f5f5;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
        }

        .qty-input {
            width: 50px;
            height: 36px;
            border: none;
            text-align: center;
            font-weight: 600;
        }

        .voucher-section {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .voucher-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            font-size: 0.9rem;
            color: #333;
        }

        .voucher-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .voucher-text {
            flex: 1;
        }

        .voucher-label {
            color: #FF6B35;
            font-weight: 600;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin: 15px 0;
            padding: 10px 0;
        }

        .btn {
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            text-transform: uppercase;
        }

        .btn-cart {
            background: white;
            color: #FF6B35;
            border: 2px solid #FF6B35;
        }

        .btn-cart:hover {
            background: #FFF0E8;
        }

        .btn-buy {
            background: #FF6B35;
            color: white;
        }

        .btn-buy:hover {
            background: #E55A24;
        }

        .shop-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 4px;
            margin-top: 15px;
        }

        .shop-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .shop-avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #FF6B35, #FF8A50);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .shop-name {
            font-weight: 600;
            color: #000;
        }

        .shop-stat {
            font-size: 0.8rem;
            color: #666;
        }

        .tabs {
            border-top: 1px solid #eee;
            margin-top: 20px;
            padding-top: 15px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-left: -30px;
            margin-right: -30px;
            padding-left: 30px;
            padding-right: 30px;
            border-bottom: 2px solid #eee;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            text-align: center;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }

        .tab-btn.active {
            color: #FF6B35;
            border-bottom-color: #FF6B35;
        }

        .tab-content {
            display: none;
            padding: 20px 0;
        }

        .tab-content.active {
            display: block;
        }

        .description {
            line-height: 1.6;
            color: #666;
            font-size: 0.95rem;
            white-space: pre-wrap;
            word-break: break-word;
        }

        @media (max-width: 768px) {
            .container-detail {
                grid-template-columns: 1fr;
            }

            .left-panel {
                border-right: none;
                border-bottom: 1px solid #eee;
            }

            .price-box {
                flex-wrap: wrap;
            }

            .tabs {
                margin-left: -30px;
                margin-right: -30px;
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
                </div>
            </div>
            <div class="header-search">
                <input type="text" placeholder="Tìm kiếm..." class="search-input">
                <button class="search-btn">🔍</button>
            </div>
            <div class="header-right">
                <a href="gio-hang.php" class="header-icon-btn cart-btn">
                    🛒 <span class="icon-badge" id="cart-count">0</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-detail">
        <!-- LEFT: Images -->
        <div class="left-panel">
            <div class="main-image" id="mainImage">
                <?php if ($product['image']): ?>
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    <div style="font-size: 4rem;">📦</div>
                <?php endif; ?>
            </div>
            <div class="thumbnails">
                <?php if ($product['image']): ?>
                    <div class="thumbnail active" onclick="changeImage(this)">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT: Product Info -->
        <div class="right-panel">
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>

            <div class="product-meta">
                <div class="rating">
                    <span class="stars">★★★★★</span>
                    <span class="rating-num">4.9</span>
                    <span class="rating-count">(3.7k)</span>
                </div>
                <div>129 Đã bán</div>
            </div>

            <div class="price-box">
                <div>
                    <?php if ($discount_percent > 0): ?>
                        <div class="price-original">₫<?php echo number_format($product['price'], 0, ',', '.'); ?></div>
                    <?php endif; ?>
                    <div class="price-current">₫<?php echo number_format($final_price, 0, ',', '.'); ?></div>
                </div>
                <?php if ($discount_percent > 0): ?>
                    <div class="discount-tag">-<?php echo intval($discount_percent); ?>%</div>
                <?php endif; ?>
            </div>

            <div class="color-section">
                <div class="section-label">Màu sắc: Vàng</div>
                <div class="color-options">
                    <div class="color-btn active" style="background: #FFD700;" title="Vàng"></div>
                </div>
            </div>

            <div class="size-section">
                <div class="section-label">Kích thước: 6</div>
                <div class="size-options">
                    <button class="size-btn active" onclick="selectSize(this, 'S')">S</button>
                    <button class="size-btn" onclick="selectSize(this, 'M')">7</button>
                    <button class="size-btn" onclick="selectSize(this, 'L')">8</button>
                </div>
            </div>

            <div class="qty-section">
                <div style="font-weight: 600;">Số lượng</div>
                <div class="qty-control">
                    <button class="qty-btn" onclick="decreaseQty()">−</button>
                    <input type="number" id="quantity" class="qty-input" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    <button class="qty-btn" onclick="increaseQty()">+</button>
                </div>
            </div>

            <div class="voucher-section">
                <div class="voucher-item">
                    <input type="checkbox" checked>
                    <div class="voucher-text">
                        <span class="voucher-label">100K - 100K</span> Giảm tối đa 100K
                    </div>
                </div>
                <div class="voucher-item">
                    <input type="checkbox">
                    <div class="voucher-text">
                        <span class="voucher-label">100K - 100K</span> Giảm tối đa 100K
                    </div>
                </div>
                <div class="voucher-item">
                    <input type="checkbox">
                    <div class="voucher-text">
                        <span class="voucher-label">100K - 100K</span> Giảm tối đa 100K
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <button class="btn btn-cart" onclick="addToCart(<?php echo $product['id']; ?>)">
                    THÊM VÀO GIỎ HÀNG
                </button>
                <button class="btn btn-buy" onclick="buyNow(<?php echo $product['id']; ?>)">
                    MUA NGAY
                </button>
            </div>

            <div class="shop-section">
                <div class="shop-info">
                    <div class="shop-avatar">🏪</div>
                    <div>
                        <div class="shop-name">Shop Mẹ và Bé Đông Lan</div>
                        <div class="shop-stat">Rating: 4.9 ⭐ | 2500+ Products</div>
                    </div>
                </div>
                <button style="background: none; border: none; color: #0088FF; cursor: pointer; font-weight: 600; padding: 8px 12px; border: 1px solid #0088FF; border-radius: 4px;">CHAT</button>
            </div>

            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab(this, 'description')">Thêm Thông Tin</button>
                <button class="tab-btn" onclick="switchTab(this, 'policy')">Chính Sách Vận Chuyển</button>
                <button class="tab-btn" onclick="switchTab(this, 'return')">Chính Sách Đổi Trả</button>
            </div>

            <div id="description" class="tab-content active">
                <div class="description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></div>
            </div>

            <div id="policy" class="tab-content">
                <div class="description">
• Vận chuyển nhanh chóng trên toàn quốc
• Miễn phí vận chuyển từ 80.000₫
• Giao trong 1-3 ngày làm việc
                </div>
            </div>

            <div id="return" class="tab-content">
                <div class="description">
• Đổi trả miễn phí trong 30 ngày
• Hỗ trợ 24/7 từ shop
• Hàng chính hãng 100% được kiểm duyệt
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = './api-dieu-khien';

        function changeImage(el) {
            const src = el.querySelector('img').src;
            document.querySelector('#mainImage img').src = src;
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
        }

        function selectSize(btn, size) {
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }

        function increaseQty() {
            const input = document.getElementById('quantity');
            input.value = parseInt(input.value) + 1;
        }

        function decreaseQty() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
        }

        function switchTab(btn, tabName) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(tabName).classList.add('active');
        }

        async function addToCart(productId) {
            const qty = parseInt(document.getElementById('quantity').value);
            alert('✅ Thêm ' + qty + ' sản phẩm vào giỏ hàng');
        }

        async function buyNow(productId) {
            alert('✅ Chuyển tới thanh toán');
            window.location.href = 'thanh-toan.php';
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('cart-count').textContent = '0';
        });
    </script>
</body>
</html>
