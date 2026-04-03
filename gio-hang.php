<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng - Shop Mẹ và Bé Đông Lan</title>
    <link rel="stylesheet" href="css-kieu-dang/kieu-dang.css">
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
                <a href="gio-hang.php" class="header-icon-btn" title="Giỏ hàng">
                    <span class="icon-badge" id="cart-count">0</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="category-menu">
                <a href="cua-hang.php" class="category-link">📋 DANH MỤC MENU</a>
                <a href="cua-hang.php?category=0-3" class="category-link">0-3 THÁNG</a>
                <a href="cua-hang.php?category=3-6" class="category-link">3-6 THÁNG</a>
                <a href="cua-hang.php?category=6-12" class="category-link">6-12 THÁNG</a>
                <a href="cua-hang.php?category=phu-kien" class="category-link">PHỤ KIỆN</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>🛒 Giỏ Hàng</h1>

        <div id="empty-cart" class="empty-state">
            <p>Giỏ hàng trống</p>
            <a href="cua-hang.php" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>

        <div id="cart-content" style="display: none;">
            <div class="cart-table">
                <table>
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="cart-items"></tbody>
                </table>
            </div>

            <div class="cart-summary">
                <h3>Tóm tắt đơn hàng</h3>
                <div class="summary-row">
                    <span>Tổng cộng:</span>
                    <span id="total-price">0 ₫</span>
                </div>
                <a href="thanh-toan.php" class="btn btn-primary btn-large">Tiến hành thanh toán</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 Shop Mẹ và Bé Đông Lan</p>
    </footer>

    <script src="js-kiem-soat/chinh.js"></script>
    <script src="js-kiem-soat/header-scroll.js"></script>
    <script src="js-kiem-soat/search.js"></script>
    <script>
    loadCart();
    </script>
</body>
</html>
