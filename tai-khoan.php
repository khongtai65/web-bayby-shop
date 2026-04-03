<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài Khoản - Shop Mẹ và Bé Đông Lan</title>
    <link rel="stylesheet" href="css-kieu-dang/kieu-dang.css">
    <link rel="stylesheet" href="css-kieu-dang/mobile.css" media="(max-width: 768px)">
    <link rel="stylesheet" href="css/social-icons.css">
    <link rel="stylesheet" href="css/search.css">
    <style>
        .auth-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .auth-buttons button {
            flex: 1;
            min-width: 150px;
            padding: 12px 20px;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .auth-buttons button.active {
            background-color: #CD853F;
            color: #fff;
            font-weight: bold;
        }
        .auth-buttons button:not(.active) {
            background-color: #F4A460;
            color: #fff;
        }
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
            }
            .auth-buttons {
                gap: 0.5rem;
            }

            .auth-buttons button {
                padding: 10px 15px;
                font-size: 0.9rem;
                min-width: 120px;
            }
            .container {
                padding: 10px;
            }
            h1, h2 {
                font-size: 1.5rem;
            }
            .form-group input {
                font-size: 16px;
            }
        }
        
        @media (max-width: 480px) {
            .auth-buttons {
                flex-direction: column;
            }
            .auth-buttons button {
                width: 100%;
                min-width: auto;
            }
            .form-container {
                padding: 1rem;
            }
            .form-group {
                margin-bottom: 1rem;
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

    <div class="container">
        <h1>👤 Tài Khoản</h1>

        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Logged In Profile -->
            <div class="user-profile" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div>
                        <h2 style="margin: 0; color: white;">👋 Xin chào, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <p style="margin: 0.5rem 0 0; font-size: 0.9rem; opacity: 0.9;">⚙️ Tài khoản Admin</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div style="background: rgba(255,255,255,0.2); padding: 1.5rem; border-radius: 8px; margin: 1rem 0;">
                    <p style="margin: 0.5rem 0;"><strong>👤 Tên đăng nhập:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <p style="margin: 0.5rem 0;"><strong>📝 Tên đầy đủ:</strong> <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['user_name']); ?></p>
                    <p style="margin: 0.5rem 0;"><strong>📞 Số điện thoại:</strong> <?php echo htmlspecialchars($_SESSION['user_phone'] ?? 'Chưa cập nhật'); ?></p>
                </div>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1rem;">
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="quan-ly-san-pham.php" style="background: #FF6347; color: white; padding: 12px 20px; border-radius: 5px; text-decoration: none; font-weight: 600; transition: 0.3s; flex: 1; text-align: center; min-width: 150px;">⚙️ Dashboard Quản Lý</a>
                    <?php endif; ?>
                    <a href="api-dieu-khien/xac-thuc.php?logout=1" style="background: rgba(255,255,255,0.3); color: white; padding: 12px 20px; border-radius: 5px; text-decoration: none; font-weight: 600; transition: 0.3s; flex: 1; text-align: center; min-width: 150px;">🚪 Đăng Xuất</a>
                </div>
            </div>

            <div class="user-orders">
                <h2>📦 Lịch sử đơn hàng</h2>
                <div id="orders-list"></div>
            </div>

        <?php else: ?>
            <!-- 2 Nút Riêng Biệt -->
            <div class="auth-buttons">
                <button id="btn-login" class="active" onclick="showLoginForm()">🔓 Đăng Nhập</button>
                <button id="btn-register" onclick="showRegisterForm()">📝 Đăng Ký</button>
            </div>

            <?php
            // Display error/success messages
            if (isset($_SESSION['login_error'])) {
                echo '<div style="color: #721c24; margin-bottom: 1.5rem; background: #f8d7da; padding: 1.5rem; border: 1px solid #f5c6cb; border-radius: 5px; border-left: 4px solid #f5c6cb;">
                    <strong>❌ Lỗi Đăng Nhập:</strong><br>' . htmlspecialchars($_SESSION['login_error']) . '
                </div>';
                unset($_SESSION['login_error']);
            }
            if (isset($_SESSION['register_error'])) {
                echo '<div style="color: #721c24; margin-bottom: 1.5rem; background: #f8d7da; padding: 1.5rem; border: 1px solid #f5c6cb; border-radius: 5px; border-left: 4px solid #f5c6cb;">
                    <strong>❌ Lỗi Đăng Ký:</strong><br>' . htmlspecialchars($_SESSION['register_error']) . '
                </div>';
                unset($_SESSION['register_error']);
            }
            if (isset($_SESSION['register_success'])) {
                echo '<div style="color: #155724; margin-bottom: 1.5rem; background: #d4edda; padding: 1.5rem; border: 1px solid #c3e6cb; border-radius: 5px; border-left: 4px solid #28a745;">
                    <h3 style="margin-top: 0; color: #155724;">✅ Đăng Ký Thành Công!</h3>
                    <p style="margin: 0.5rem 0;">' . htmlspecialchars($_SESSION['register_success']) . '</p>
                    <p style="margin: 1rem 0 0; font-size: 0.95rem;">👇 Vui lòng đăng nhập bằng tài khoản vừa tạo:</p>
                </div>';
                unset($_SESSION['register_success']);
            }
            ?>

            <!-- Login Form -->
            <div id="login-form" class="form-container" style="display: block;">
                <h2>🔓 Đăng Nhập</h2>
                <form method="POST" action="api-dieu-khien/xac-thuc.php" onsubmit="return validateLoginForm()">
                    <div class="form-group">
                        <label>Tên đăng nhập</label>
                        <input type="text" id="login-username" name="username" required placeholder="6-12 ký tự" minlength="6" maxlength="12">
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password" id="login-password" name="password" required placeholder="6-12 ký tự" minlength="6" maxlength="12">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="remember" value="1"> Ghi nhớ đăng nhập (30 ngày)
                        </label>
                    </div>
                    <input type="hidden" name="action" value="login">
                    <button type="submit" class="btn btn-primary btn-large">✅ Đăng Nhập</button>
                </form>
            </div>

            <!-- Register Form -->
            <div id="register-form" class="form-container" style="display: none;">
                <h2>📝 Đăng Ký Tài Khoản</h2>
                <form method="POST" action="api-dieu-khien/xac-thuc.php" onsubmit="return validateRegisterForm()">
                    <div class="form-group">
                        <label>Tên đăng nhập <span style="color: red;">*</span></label>
                        <input type="text" id="register-username" name="username" required placeholder="6-12 ký tự, không 'admin'" minlength="6" maxlength="12">
                        <small style="color: #666;">⚠️ 6-12 ký tự, không chứa 'admin', không có khoảng trắng</small>
                    </div>
                    <div class="form-group">
                        <label>Họ và tên <span style="color: red;">*</span></label>
                        <input type="text" id="register-fullname" name="full_name" required placeholder="VD: Nguyễn Văn A">
                    </div>
                    <div class="form-group">
                        <label>Tên gọi <span style="color: red;">*</span></label>
                        <input type="text" id="register-name" name="name" required placeholder="VD: Nguyễn">
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu <span style="color: red;">*</span></label>
                        <input type="password" id="register-password" name="password" required placeholder="6-12 ký tự" minlength="6" maxlength="12">
                        <small style="color: #666;">⚠️ 6-12 ký tự, chỉ chứa chữ, số, @, &</small>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="tel" name="phone" placeholder="VD: 0901234567">
                    </div>
                    <input type="hidden" name="action" value="register">
                    <button type="submit" class="btn btn-primary btn-large">✅ Đăng Ký</button>
                </form>
            </div>

        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 Shop Mẹ và Bé Đông Lan</p>
    </footer>

    <script src="js-kiem-soat/chinh.js"></script>
    <script src="js-kiem-soat/header-scroll.js"></script>
    <script src="js-kiem-soat/search.js"></script>
    <script src="js-kiem-soat/sticky-contact.js"></script>
    <script>
    // Hiển thị form đăng nhập
    function showLoginForm() {
        document.getElementById('login-form').style.display = 'block';
        document.getElementById('register-form').style.display = 'none';
        document.getElementById('btn-login').classList.add('active');
        document.getElementById('btn-register').classList.remove('active');
    }

    // Hiển thị form đăng ký
    function showRegisterForm() {
        document.getElementById('login-form').style.display = 'none';
        document.getElementById('register-form').style.display = 'block';
        document.getElementById('btn-login').classList.remove('active');
        document.getElementById('btn-register').classList.add('active');
    }

    // Validate form đăng nhập
    function validateLoginForm() {
        const username = document.getElementById('login-username').value.trim();
        const password = document.getElementById('login-password').value;
        
        if (!username || username.length === 0) {
            alert('❌ Tên đăng nhập không được để trống');
            return false;
        }

        if (username.length < 6 || username.length > 12) {
            alert('❌ Tên đăng nhập phải từ 6-12 ký tự');
            return false;
        }
        
        if (password.length < 6 || password.length > 12) {
            alert('❌ Mật khẩu phải từ 6-12 ký tự');
            return false;
        }
        
        return true;
    }

    // Validate form đăng ký
    function validateRegisterForm() {
        const username = document.getElementById('register-username').value.trim();
        const fullname = document.getElementById('register-fullname').value.trim();
        const name = document.getElementById('register-name').value.trim();
        const password = document.getElementById('register-password').value;

        // Validate username
        if (!username || username.length < 6) {
            alert('❌ Tên đăng nhập tối thiểu 6 ký tự');
            return false;
        }

        if (username.length > 12) {
            alert('❌ Tên đăng nhập tối đa 12 ký tự');
            return false;
        }

        if (username.toLowerCase() === 'admin' || username.toLowerCase().includes('admin')) {
            alert('❌ Tên đăng nhập không được chứa từ "admin"');
            return false;
        }

        if (/\s/.test(username)) {
            alert('❌ Tên đăng nhập không được có khoảng trắng');
            return false;
        }

        // Validate password
        if (password.length < 6) {
            alert('❌ Mật khẩu tối thiểu 6 ký tự');
            return false;
        }

        if (password.length > 12) {
            alert('❌ Mật khẩu tối đa 12 ký tự');
            return false;
        }

        // Check password contains only allowed special chars (@, &)
        const passwordRegex = /^[a-zA-Z0-9@&]*$/;
        if (!passwordRegex.test(password)) {
            alert('❌ Mật khẩu chỉ được chứa chữ, số, @, &');
            return false;
        }

        if (!fullname) {
            alert('❌ Họ và tên không được để trống');
            return false;
        }

        if (!name) {
            alert('❌ Tên gọi không được để trống');
            return false;
        }

        return true;
    }

    <?php if (isset($_SESSION['user_id'])): ?>
        loadUserOrders();
    <?php endif; ?>
    </script>
</body>
</html>
