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
                    <img src="hinh-anh/icons/order.png" alt="Giỏ hàng" class="header-icon-img">
                    <span class="icon-badge" id="cart-count">0</span>
                </a>
                <button class="menu-toggle" id="menuToggle" title="Menu">☰</button>
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
        <a href="trang-chu.php" class="nav-link active">🏠 TRANG CHỦ</a>
        <a href="cua-hang.php?category=0-3" class="nav-link">0-3 THÁNG</a>
        <a href="cua-hang.php?category=3-6" class="nav-link">3-6 THÁNG</a>
        <a href="cua-hang.php?category=6-12" class="nav-link">6-12 THÁNG</a>
        <a href="cua-hang.php?category=phu-kien" class="nav-link">PHỤ KIỆN</a>
    </nav>

    <!-- Hero Banner -->
    <div class="hero" style="background-image: url('hinh-anh/nen-banner.png');">
        <div class="hero-content">
            <h1>Shop Mẹ và Bé Đông Lan</h1>
            <p>Những sản phẩm sơ sinh chất lượng cao, an toàn cho mẹ và bé yêu</p>
            <p>Đặt hàng ngay hôm nay để nhận ưu đãi hấp dẫn!</p>
            <button onclick="window.location.href='cua-hang.php'" class="btn btn-primary"><img src="hinh-anh/icons/cart.svg" alt="Giỏ hàng"> Mua Sắm Ngay</button>
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
