<?php
session_start();
require_once 'config.php';

// Get category from URL
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Load products from database
$products = [];
if ($category) {
    $sql = "SELECT id, name, price, COALESCE(discount_percent, 0) as discount_percent, image, stock, created_at FROM products WHERE category = ? ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
} else {
    $sql = "SELECT id, name, price, COALESCE(discount_percent, 0) as discount_percent, image, stock, created_at FROM products ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Shop Mẹ và Bé Đông Lan</title>
    <link rel="stylesheet" href="css-kieu-dang/kieu-dang.css">
    <link rel="stylesheet" href="css-kieu-dang/mobile.css" media="(max-width: 768px)">
    <link rel="stylesheet" href="css/social-icons.css">
    <link rel="stylesheet" href="css/search.css">
    <style>
        /* Shopee Product Card Styles */
        .product-card {
            background: white;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .product-image {
            width: 100%;
            height: 190px;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            overflow: hidden;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-discount-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #FF6B35;
            color: white;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 0.75rem;
            z-index: 2;
        }

        .product-info {
            padding: 1rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.4rem;
            color: #333;
            line-height: 1.3;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            height: 2.6rem;
        }

        .product-rating-sold {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 0.6rem;
            font-size: 0.8rem;
            color: #666;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 0.2rem;
        }

        .product-stars {
            color: #FFD700;
        }

        .product-sold {
            color: #999;
        }

        .product-price-section {
            margin-bottom: 0.6rem;
        }

        .product-price {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
        }

        .product-price-current {
            font-size: 1.2rem;
            color: #FF6B35;
            font-weight: bold;
        }

        .product-price-old {
            font-size: 0.8rem;
            text-decoration: line-through;
            color: #999;
        }

        .product-actions {
            display: flex;
            gap: 0.4rem;
            margin-top: auto;
        }

        .product-actions input {
            width: 50px;
            padding: 0.4rem;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 0.85rem;
        }

        .product-actions button {
            flex: 1;
            padding: 0.4rem;
            border: none;
            background: #FF6B35;
            color: white;
            border-radius: 3px;
            font-weight: bold;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .product-actions button:hover {
            background: #E55A24;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 1rem;
            margin-bottom: 3rem;
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
        <div class="filter-section">
            <h2>🛍️ Cửa Hàng</h2>
            <div class="filter-controls">
                <div class="filter-group">
                    <label>Chọn giới tính:</label>
                    <select id="gender-filter" onchange="updateWeightOptions()">
                        <option value="">Tất cả giới tính</option>
                        <option value="boy">👦 Bé trai</option>
                        <option value="girl">👧 Bé gái</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Chọn cân nặng:</label>
                    <select id="weight-filter" onchange="applyFilters()">
                        <option value="">Tất cả cân nặng</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="product-grid">
            <?php
            if (!empty($products)) {
                foreach ($products as $product) {
                    $discount_percent = floatval($product['discount_percent']) ?? 0;
                    $final_price = $product['price'] * (1 - $discount_percent / 100);
                    $rating = 4.5;
                    $sold = rand(50, 1050);
                    $stars = str_repeat('★', floor($rating)) . (($rating % 1 >= 0.5) ? '☆' : '');
                    $image_src = !empty($product['image']) ? htmlspecialchars($product['image']) : 'hinh-anh/placeholder.png';
                    ?>
                    <div class="product-card" onclick="window.location.href='chi-tiet-san-pham-v2.php?id=<?php echo $product['id']; ?>'">
                        <div class="product-image">
                            <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php if ($discount_percent > 0): ?>
                                <div class="product-discount-badge">-<?php echo intval($discount_percent); ?>%</div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                            <div class="product-rating-sold">
                                <div class="product-rating">
                                    <span class="product-stars"><?php echo $stars; ?></span>
                                    <span><?php echo $rating; ?></span>
                                </div>
                                <span class="product-sold">Đã bán <?php echo $sold; ?></span>
                            </div>
                            <div class="product-price-section">
                                <div class="product-price">
                                    <span class="product-price-current">₫<?php echo number_format($final_price, 0, ',', '.'); ?></span>
                                    <?php if ($discount_percent > 0): ?>
                                        <span class="product-price-old">₫<?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="product-actions">
                                <input type="number" value="1" min="1" onclick="event.stopPropagation()">
                                <button onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>)">🛒 Thêm</button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p style="grid-column: 1/-1; text-align: center; padding: 40px;">Không có sản phẩm nào</p>';
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 Shop Mẹ và Bé Đông Lan - Tất cả quyền được bảo lưu</p>
    </footer>

    <script src="js-kiem-soat/chinh.js"></script>
    <script src="js-kiem-soat/header-scroll.js"></script>
    <script src="js-kiem-soat/search.js"></script>
    <script src="js-kiem-soat/sticky-contact.js"></script>
    <script>
    // Highlight active nav link
    highlightActiveNavLink();
    
    // WHO Growth Standards - Cân nặng trung bình theo độ tuổi
    const weightData = {
        '0-3': {
            boy: [
                { label: 'Sơ sinh (~3.2kg)', value: '3.2' },
                { label: '3 tháng (~5.8kg)', value: '5.8' }
            ],
            girl: [
                { label: 'Sơ sinh (~3.0kg)', value: '3.0' },
                { label: '3 tháng (~5.4kg)', value: '5.4' }
            ]
        },
        '3-6': {
            boy: [
                { label: '3 tháng (~5.8kg)', value: '5.8' },
                { label: '6 tháng (~7.4kg)', value: '7.4' }
            ],
            girl: [
                { label: '3 tháng (~5.4kg)', value: '5.4' },
                { label: '6 tháng (~6.8kg)', value: '6.8' }
            ]
        },
        '6-12': {
            boy: [
                { label: '6 tháng (~7.4kg)', value: '7.4' },
                { label: '12 tháng (~9.8kg)', value: '9.8' }
            ],
            girl: [
                { label: '6 tháng (~6.8kg)', value: '6.8' },
                { label: '12 tháng (~9.2kg)', value: '9.2' }
            ]
        }
    };

    function updateWeightOptions() {
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category') || '';
        const genderFilter = document.getElementById('gender-filter').value;
        const weightFilter = document.getElementById('weight-filter');
        
        // Clear existing options
        weightFilter.innerHTML = '<option value="">Tất cả cân nặng</option>';
        
        // If category and gender are selected, populate weight options
        if (category && genderFilter && weightData[category]) {
            const genderData = weightData[category][genderFilter];
            if (genderData) {
                genderData.forEach(weight => {
                    const option = document.createElement('option');
                    option.value = weight.value;
                    option.textContent = weight.label;
                    weightFilter.appendChild(option);
                });
            }
        }
    }

    function applyFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category') || '';
        const gender = document.getElementById('gender-filter').value;
        const weight = document.getElementById('weight-filter').value;
        
        // Build URL with parameters
        let url = 'cua-hang.php';
        let params = [];
        if (category) params.push('category=' + category);
        if (gender) params.push('gender=' + gender);
        if (weight) params.push('weight=' + weight);
        
        if (params.length > 0) {
            url += '?' + params.join('&');
        }
        
        // Update URL and reload products
        window.history.pushState({}, '', url);
        loadProducts(category, gender, weight);
    }

    // Load initial filters from URL
    document.addEventListener('DOMContentLoaded', function() {
        highlightActiveNavLink();
        
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category') || '';
        const gender = urlParams.get('gender') || '';
        const weight = urlParams.get('weight') || '';
        
        if (gender) document.getElementById('gender-filter').value = gender;
        
        // Update weight options based on selected category and gender
        if (category && gender) {
            updateWeightOptions();
            if (weight) document.getElementById('weight-filter').value = weight;
        } else if (category) {
            // Even if gender not selected, update weight placeholder
            updateWeightOptions();
        }
        
        // Note: Products are already rendered by PHP, no need to loadProducts() here
        updateCartCount();
    });
    </script>

    <!-- Login Modal Popup -->
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
                <button onclick="showLoginTab()" id="btn-login-tab" class="modal-tab-btn active" style="flex: 1; padding: 12px; border: 2px solid #CD853F; background: #CD853F; color: white; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s;">
                    🔓 Đăng Nhập
                </button>
                <button onclick="showRegisterTab()" id="btn-register-tab" class="modal-tab-btn" style="flex: 1; padding: 12px; border: 2px solid #ddd; background: #f5f5f5; color: #333; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s;">
                    📝 Đăng Ký
                </button>
            </div>

            <!-- Login Tab -->
            <div id="modal-login-tab" style="display: block;">
                <form method="POST" action="api-dieu-khien/xac-thuc.php" onsubmit="return validateLoginForm()">
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Tên đăng nhập</label>
                        <input type="text" id="modal-login-username" name="username" required minlength="4" maxlength="12" placeholder="4-12 ký tự" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; font-size: 1rem;">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Mật khẩu</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <input type="password" id="modal-login-password" name="password" required minlength="6" maxlength="16" placeholder="Tối đa 16 ký tự" style="width: 100%; padding: 10px; padding-right: 40px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; font-size: 1rem;">
                            <button type="button" onclick="togglePasswordVisibility('modal-login-password')" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; font-size: 1.2rem;">👁️</button>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="returnUrl" value="cua-hang.php">
                    <button type="submit" style="width: 100%; padding: 12px; background: #CD853F; color: white; border: none; border-radius: 5px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: 0.3s;">
                        ✅ Đăng Nhập
                    </button>
                </form>
            </div>

            <!-- Register Tab -->
            <div id="modal-register-tab" style="display: none;">
                <form method="POST" action="api-dieu-khien/xac-thuc.php" onsubmit="return validateRegisterForm()">
                    <div style="margin-bottom: 0.8rem;">
                        <label style="display: block; margin-bottom: 0.3rem; font-weight: bold; font-size: 0.9rem;">Tên đăng nhập</label>
                        <input type="text" id="modal-register-username" name="username" required minlength="4" maxlength="12" placeholder="4-12 ký tự" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; font-size: 0.95rem;">
                    </div>
                    <div style="margin-bottom: 0.8rem;">
                        <label style="display: block; margin-bottom: 0.3rem; font-weight: bold; font-size: 0.9rem;">Họ và tên</label>
                        <input type="text" id="modal-register-fullname" name="full_name" required placeholder="VD: Nguyễn Văn A" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; font-size: 0.95rem;">
                    </div>
                    <div style="margin-bottom: 0.8rem;">
                        <label style="display: block; margin-bottom: 0.3rem; font-weight: bold; font-size: 0.9rem;">Tên gọi</label>
                        <input type="text" id="modal-register-name" name="name" required placeholder="VD: Nguyễn" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; font-size: 0.95rem;">
                    </div>
                    <div style="margin-bottom: 0.8rem;">
                        <label style="display: block; margin-bottom: 0.3rem; font-weight: bold; font-size: 0.9rem;">Mật khẩu</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <input type="password" id="modal-register-password" name="password" required minlength="6" maxlength="16" placeholder="Tối đa 16 ký tự" style="width: 100%; padding: 8px; padding-right: 40px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; font-size: 0.95rem;">
                            <button type="button" onclick="togglePasswordVisibility('modal-register-password')" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; font-size: 1rem;">👁️</button>
                        </div>
                    </div>
                    <div style="margin-bottom: 0.8rem;">
                        <label style="display: block; margin-bottom: 0.3rem; font-weight: bold; font-size: 0.9rem;">Số điện thoại</label>
                        <input type="tel" name="phone" placeholder="0901234567" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; font-size: 0.95rem;">
                    </div>
                    <input type="hidden" name="action" value="register">
                    <input type="hidden" name="returnUrl" value="cua-hang.php">
                    <button type="submit" style="width: 100%; padding: 10px; background: #CD853F; color: white; border: none; border-radius: 5px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: 0.3s;">
                        ✅ Đăng Ký
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Show/Hide modal tabs
        function showLoginTab() {
            document.getElementById('modal-login-tab').style.display = 'block';
            document.getElementById('modal-register-tab').style.display = 'none';
            document.getElementById('btn-login-tab').style.background = '#CD853F';
            document.getElementById('btn-login-tab').style.color = 'white';
            document.getElementById('btn-login-tab').style.borderColor = '#CD853F';
            document.getElementById('btn-register-tab').style.background = '#f5f5f5';
            document.getElementById('btn-register-tab').style.color = '#333';
            document.getElementById('btn-register-tab').style.borderColor = '#ddd';
        }

        function showRegisterTab() {
            document.getElementById('modal-login-tab').style.display = 'none';
            document.getElementById('modal-register-tab').style.display = 'block';
            document.getElementById('btn-login-tab').style.background = '#f5f5f5';
            document.getElementById('btn-login-tab').style.color = '#333';
            document.getElementById('btn-login-tab').style.borderColor = '#ddd';
            document.getElementById('btn-register-tab').style.background = '#CD853F';
            document.getElementById('btn-register-tab').style.color = 'white';
            document.getElementById('btn-register-tab').style.borderColor = '#CD853F';
        }

        // Toggle password visibility in modal
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }

        // Close modal when clicking outside
        const modal = document.getElementById('login-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeLoginModal();
                }
            });
        }
    </script>
</body>
</html>
