<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Shop Mẹ và Bé Đông Lan - Đồ Sơ Sinh Chất Lượng</title>
    <link rel="stylesheet" href="css-kieu-dang/kieu-dang.css">
    <link rel="stylesheet" href="css-kieu-dang/mobile.css" media="(max-width: 768px)">
    <link rel="stylesheet" href="css/social-icons.css">
    <link rel="stylesheet" href="css/search.css">
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
                    <span>🛒</span>
                    <span class="icon-badge" id="cart-count">0</span>
                </a>
                <button class="menu-toggle" id="menuToggle" title="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
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
                <a href="https://zalo.me/0866021711" class="social-btn zalo" title="Zalo" target="_blank">📱</a>
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
    <nav class="navbar">
        <div class="nav-container">
            <div class="category-menu" id="categoryMenu">
                <a href="trang-chu.php" class="category-link">🏠 TRANG CHỦ</a>
                <a href="cua-hang.php?category=0-3" class="category-link">0-3 THÁNG</a>
                <a href="cua-hang.php?category=3-6" class="category-link">3-6 THÁNG</a>
                <a href="cua-hang.php?category=6-12" class="category-link">6-12 THÁNG</a>
                <a href="cua-hang.php?category=phu-kien" class="category-link">PHỤ KIỆN</a>
            </div>
        </div>
    </nav>

    <!-- Hero Banner -->
    <div class="hero" style="background-image: url('hinh-anh/nen-banner.png');">
        <div class="hero-content">
            <h1>Shop Mẹ và Bé Đông Lan</h1>
            <p>Những sản phẩm sơ sinh chất lượng cao, an toàn cho mẹ và bé yêu</p>
            <p>Đặt hàng ngay hôm nay để nhận ưu đãi hấp dẫn!</p>
            <a href="cua-hang.php" class="btn btn-primary"><img src="hinh-anh/icons/cart.svg" alt="Giỏ hàng"> Mua Sắm Ngay</a>
        </div>
    </div>

    <!-- Categories -->
    <section class="categories">
        <h2>📦 Danh Mục Sản Phẩm</h2>
        <div class="category-grid">
            <div class="category-card" onclick="window.location.href='cua-hang.php?category=0-3'">
                <div class="category-icon"><img src="hinh-anh/danh-muc-so-sinh.png" alt="0-3 Tháng"></div>
                <h3>0-3 Tháng</h3>
            </div>
            <div class="category-card" onclick="window.location.href='cua-hang.php?category=3-6'">
                <div class="category-icon"><img src="hinh-anh/danh-muc-3-6-thang.png" alt="3-6 tháng"></div>
                <h3>3-6 Tháng</h3>
            </div>
            <div class="category-card" onclick="window.location.href='cua-hang.php?category=6-12'">
                <div class="category-icon"><img src="hinh-anh/danh-muc-6-12-thang.png" alt="6-12 tháng"></div>
                <h3>6-12 Tháng</h3>
            </div>
            <div class="category-card" onclick="window.location.href='cua-hang.php?category=phu-kien'">
                <div class="category-icon"><img src="hinh-anh/danh-muc-phu-kien.png" alt="Phụ kiện"></div>
                <h3>Phụ Kiện</h3>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured">
        <h2>⭐ Sản Phẩm Nổi Bật</h2>
        <div id="featured-products" class="product-grid"></div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 Shop Mẹ và Bé Đông Lan - Tất cả quyền được bảo lưu</p>
        <p>Liên hệ: 0866 021 711 | Email: support@babyshop.vn</p>
    </footer>

    <script src="js-kiem-soat/chinh.js"></script>
    <script src="js-kiem-soat/header-scroll.js"></script>
    <script src="js-kiem-soat/search.js"></script>
    <script src="js-kiem-soat/sticky-contact.js"></script>
    <script>
    // Load featured products
    loadFeaturedProducts();
    
    // Menu Toggle
    const menuToggle = document.getElementById('menuToggle');
    const categoryMenu = document.getElementById('categoryMenu');
    
    if (menuToggle && categoryMenu) {
        menuToggle.addEventListener('click', function() {
            categoryMenu.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
        
        // Close menu when a link is clicked
        const categoryLinks = categoryMenu.querySelectorAll('.category-link');
        categoryLinks.forEach(link => {
            link.addEventListener('click', function() {
                categoryMenu.classList.remove('active');
                menuToggle.classList.remove('active');
            });
        });
    }
    </script>
</body>
</html>
