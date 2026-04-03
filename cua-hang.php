<?php
session_start();
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
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <!-- Navigation Menu -->
    <nav class="navbar">
        <div class="nav-container">
            <button class="menu-toggle" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="category-menu" id="categoryMenu">
                <a href="trang-chu.php" class="category-link">🏠 TRANG CHỦ</a>
                <a href="cua-hang.php?category=0-3" class="category-link">0-3 THÁNG</a>
                <a href="cua-hang.php?category=3-6" class="category-link">3-6 THÁNG</a>
                <a href="cua-hang.php?category=6-12" class="category-link">6-12 THÁNG</a>
                <a href="cua-hang.php?category=phu-kien" class="category-link">PHỤ KIỆN</a>
            </div>
        </div>
    </nav>

    <!-- Drawer Menu Overlay -->
    <div class="drawer-overlay" id="drawerOverlay"></div>

    <!-- Drawer Menu -->
    <div class="drawer-menu" id="drawerMenu">
        <div class="drawer-section">
            <h3>DANH MỤC</h3>
            <a href="trang-chu.php" class="drawer-link">🏠 Trang chủ</a>
            <a href="cua-hang.php?category=0-3" class="drawer-link">0-3 Tháng</a>
            <a href="cua-hang.php?category=3-6" class="drawer-link">3-6 Tháng</a>
            <a href="cua-hang.php?category=6-12" class="drawer-link">6-12 Tháng</a>
            <a href="cua-hang.php?category=phu-kien" class="drawer-link">Phụ kiện</a>
        </div>
        
        <div class="drawer-section">
            <h3>LIÊN HỆ</h3>
            <div class="drawer-social">
                <a href="https://zalo.me/0866021711" class="zalo" title="Zalo" target="_blank">📱</a>
                <a href="https://www.facebook.com/profile.php?id=61580260876532" class="facebook" title="Facebook" target="_blank">f</a>
                <a href="https://www.facebook.com/profile.php?id=61580260876532" class="messenger" title="Messenger" target="_blank">💬</a>
            </div>
        </div>

        <div class="drawer-section">
            <h3>🔔 HỖ TRỢ 24/7</h3>
            <div class="drawer-support">
                <strong>Liên hệ:</strong>
                <span>📞 0866.021.711</span><br>
                <span>Thứ 2 - Thứ 7 (Trừ lễ)</span>
            </div>
        </div>
    </div>

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
        <div id="products-container" class="product-grid"></div>
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
        
        // Load products
        loadProducts(category, gender, weight);
    });
    </script>
</body>
</html>
