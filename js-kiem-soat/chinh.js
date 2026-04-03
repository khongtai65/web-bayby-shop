// ===== CONFIGURATION =====
const API_BASE = './api-dieu-khien';

// ===== HIGHLIGHT ACTIVE NAV LINK =====
function highlightActiveNavLink() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    const currentURL = window.location.href;
    
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.classList.remove('active');
        
        // Highlight "TRANG CHỦ" on homepage
        if ((currentPage === 'trang-chu.php' || currentPage === '') && link.href.includes('trang-chu.php')) {
            link.classList.add('active');
        }
        // Highlight category links on shop page
        else if (currentURL.includes(link.href.split('?')[1])) {
            link.classList.add('active');
        }
    });
}

// Run on page load
document.addEventListener('DOMContentLoaded', highlightActiveNavLink);

// ===== CHECK LOGIN =====
async function checkLogin() {
    try {
        // Check nếu user_id có trong document (được set bởi server)
        const response = await fetch(`${API_BASE}/xac-thuc.php?action=checkLogin`);
        const data = await response.json();
        return data.status === 'success';
    } catch (error) {
        console.error('Error checking login:', error);
        return false;
    }
}

// Hành động "Mua Sắm Ngay" - kiểm tra đăng nhập
async function goToShop() {
    const isLoggedIn = await checkLogin();
    if (!isLoggedIn) {
        showLoginModal();
    } else {
        window.location.href = 'cua-hang.php';
    }
}

// Hiện modal đăng nhập/đăng ký
function showLoginModal() {
    const modal = document.getElementById('login-modal');
    if (modal) {
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
        document.body.style.overflow = 'hidden';
    }
}

// Đóng modal
function closeLoginModal() {
    const modal = document.getElementById('login-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
        document.body.style.overflow = 'auto';
    }
}

// ===== HELPER FUNCTIONS =====
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        currency: 'VND',
        style: 'currency',
        maximumFractionDigits: 0
    }).format(price);
}

// Update cart count in navbar
async function updateCartCount() {
    try {
        const response = await fetch(`${API_BASE}/gio-hang.php`);
        const data = await response.json();
        
        if (data.status === 'success') {
            const count = data.data.reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById('cart-count').textContent = count;
        } else {
            document.getElementById('cart-count').textContent = '0';
        }
    } catch (error) {
        console.error('Error updating cart count:', error);
        document.getElementById('cart-count').textContent = '0';
    }
}

// ===== PRODUCT FUNCTIONS =====

// Create product card HTML
function createProductCard(product) {
    return `
        <div class="product-card">
            <div class="product-image">${product.image_path ? `<img src="${product.image_path}" alt="${product.name}">` : '📦'}</div>
            <div class="product-info">
                <div class="product-name">${product.name}</div>
                <div class="product-description">${product.description}</div>
                <div class="product-price">${formatPrice(product.price)}</div>
                <div class="product-actions">
                    <input type="number" id="qty-${product.id}" value="1" min="1" class="quantity-input">
                    <button onclick="addToCart(${product.id})" class="btn btn-primary">🛒 Thêm</button>
                </div>
            </div>
        </div>
    `;
}

// Load featured products (homepage)
async function loadFeaturedProducts() {
    try {
        const response = await fetch(`${API_BASE}/san-pham.php`);
        const data = await response.json();
        
        if (data.status === 'success') {
            const featured = data.data.slice(0, 4);
            const html = featured.map(p => createProductCard(p)).join('');
            document.getElementById('featured-products').innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading featured products:', error);
    }
    
    updateCartCount();
}

// Load shop products
async function loadProducts(category = '') {
    try {
        const url = category ? `${API_BASE}/san-pham.php?category=${category}` : `${API_BASE}/san-pham.php`;
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.status === 'success') {
            const html = data.data.map(p => createProductCard(p)).join('');
            document.getElementById('products-container').innerHTML = html || '<p>Không có sản phẩm nào</p>';
        }
    } catch (error) {
        console.error('Error loading products:', error);
        document.getElementById('products-container').innerHTML = '<p>Lỗi khi tải sản phẩm</p>';
    }
    
    updateCartCount();
}

// Filter by category
function filterByCategory() {
    const select = document.getElementById('category-filter');
    const category = select.value;
    window.location.href = category ? `cua-hang.php?category=${category}` : 'cua-hang.php';
}

// Add to cart
async function addToCart(productId) {
    try {
        // Check login trước
        const isLoggedIn = await checkLogin();
        if (!isLoggedIn) {
            showLoginModal();
            return;
        }
        
        const quantity = parseInt(document.getElementById(`qty-${productId}`).value);
        
        const response = await fetch(`${API_BASE}/gio-hang.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: quantity })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            alert('✅ Đã thêm vào giỏ hàng');
            updateCartCount();
        } else {
            alert(data.message || '❌ Thêm vào giỏ thất bại');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        alert('❌ Lỗi khi thêm vào giỏ');
    }
}

// ===== CART FUNCTIONS =====

// Load cart
async function loadCart() {
    try {
        const response = await fetch(`${API_BASE}/gio-hang.php`);
        const data = await response.json();
        
        if (data.status === 'error') {
            document.getElementById('empty-cart').style.display = 'block';
            document.getElementById('cart-content').style.display = 'none';
            return;
        }
        
        const cart = data.data;
        
        if (cart.length === 0) {
            document.getElementById('empty-cart').style.display = 'block';
            document.getElementById('cart-content').style.display = 'none';
            return;
        }
        
        document.getElementById('empty-cart').style.display = 'none';
        document.getElementById('cart-content').style.display = 'block';
        
        const tbody = document.getElementById('cart-items');
        let total = 0;
        
        const html = cart.map(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            
            return `
                <tr>
                    <td>${item.name}</td>
                    <td>${formatPrice(item.price)}</td>
                    <td>
                        <input type="number" value="${item.quantity}" min="1"
                            onchange="updateQuantity(${item.product_id}, this.value)" class="quantity-input">
                    </td>
                    <td>${formatPrice(subtotal)}</td>
                    <td>
                        <button onclick="removeFromCart(${item.product_id})" class="btn btn-danger">❌ Xóa</button>
                    </td>
                </tr>
            `;
        }).join('');
        
        tbody.innerHTML = html;
        document.getElementById('total-price').textContent = formatPrice(total);
    } catch (error) {
        console.error('Error loading cart:', error);
    }
    
    updateCartCount();
}

// Remove from cart
async function removeFromCart(productId) {
    if (!confirm('Bạn chắc chắn muốn xóa sản phẩm này?')) return;
    
    try {
        const response = await fetch(`${API_BASE}/gio-hang.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            loadCart();
        } else {
            alert('❌ ' + data.message);
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
    }
}

// Update quantity
async function updateQuantity(productId, quantity) {
    const qty = parseInt(quantity);
    
    if (qty < 1) {
        alert('Số lượng phải >= 1');
        loadCart();
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/gio-hang.php`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: qty })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            loadCart();
        } else {
            alert('❌ ' + data.message);
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
    }
}

// ===== CHECKOUT FUNCTIONS =====

// Load checkout
async function loadCheckout() {
    try {
        const response = await fetch(`${API_BASE}/cart.php`);
        const data = await response.json();
        
        if (data.status === 'error' || data.data.length === 0) {
            alert('Giỏ hàng trống');
            window.location.href = 'cua-hang.php';
            return;
        }
        
        const cart = data.data;
        const itemsContainer = document.getElementById('order-items');
        let total = 0;
        
        const html = cart.map(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            
            return `
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid #eee;">
                    <span>${item.name} x${item.quantity}</span>
                    <span>${formatPrice(subtotal)}</span>
                </div>
            `;
        }).join('');
        
        itemsContainer.innerHTML = html;
        document.getElementById('summary-total').textContent = formatPrice(total);
        
        // Store total for later use
        window.cartTotal = total;
    } catch (error) {
        console.error('Error loading checkout:', error);
    }
    
    updateCartCount();
}

// Complete checkout
async function completeCheckout() {
    const fullname = document.getElementById('fullname').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const address = document.getElementById('address').value;
    
    if (!fullname || !email || !phone || !address) {
        alert('Vui lòng điền đầy đủ thông tin');
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/don-hang.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                total_price: window.cartTotal,
                shipping_address: address,
                phone: phone
            })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            alert('✅ Đơn hàng đã được tạo thành công!\nMã đơn: ' + data.data.order_id);
            window.location.href = 'tai-khoan.php';
        } else {
            alert('❌ ' + data.message);
        }
    } catch (error) {
        console.error('Error creating order:', error);
        alert('❌ Lỗi khi tạo đơn hàng');
    }
}

// ===== ACCOUNT FUNCTIONS =====

// Switch tab
function switchTab(tab) {
    const loginTab = document.getElementById('login-tab');
    const registerTab = document.getElementById('register-tab');
    const tabBtns = document.querySelectorAll('.tab-btn');
    
    tabBtns.forEach(btn => btn.classList.remove('active'));
    
    if (tab === 'login') {
        loginTab.style.display = 'block';
        registerTab.style.display = 'none';
        tabBtns[0].classList.add('active');
    } else {
        loginTab.style.display = 'none';
        registerTab.style.display = 'block';
        tabBtns[1].classList.add('active');
    }
}

// Load user orders
async function loadUserOrders() {
    try {
        const response = await fetch(`${API_BASE}/don-hang.php`);
        const data = await response.json();
        
        if (data.status === 'success') {
            const orders = data.data;
            const ordersList = document.getElementById('orders-list');
            
            if (orders.length === 0) {
                ordersList.innerHTML = '<p>Chưa có đơn hàng nào</p>';
                return;
            }
            
            const html = orders.map(order => `
                <div class="order-item">
                    <h3>Đơn #${order.id}</h3>
                    <p><strong>Ngày:</strong> ${new Date(order.created_at).toLocaleDateString('vi-VN')}</p>
                    <p><strong>Tổng tiền:</strong> ${formatPrice(order.total_price)}</p>
                    <p><strong>Trạng thái:</strong> <span style="color: ${order.status === 'completed' ? 'green' : 'orange'}">${order.status}</span></p>
                    <p><strong>Địa chỉ:</strong> ${order.shipping_address}</p>
                </div>
            `).join('');
            
            ordersList.innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading orders:', error);
    }
}

// ===== PAGE LOAD =====
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    initPopupMenu();
});

// ===== POPUP MENU FUNCTIONS =====
function initPopupMenu() {
    const menuToggle = document.getElementById('menuToggle');
    const popupMenu = document.getElementById('popupMenu');
    const popupOverlay = document.getElementById('popupOverlay');
    const popupClose = document.getElementById('popupClose');
    const popupLinks = document.querySelectorAll('.popup-link');
    
    // Debug: Check if elements exist
    if (!menuToggle || !popupMenu) {
        console.warn('Popup menu elements not found');
        return;
    }
    
    console.log('Popup menu initialized');
    
    // Toggle popup menu
    menuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        e.preventDefault();
        console.log('Menu button clicked');
        popupMenu.classList.toggle('active');
        popupOverlay.classList.toggle('active');
        document.body.classList.toggle('popup-active');
    });
    
    // Close popup when clicking close button
    popupClose.addEventListener('click', function() {
        popupMenu.classList.remove('active');
        popupOverlay.classList.remove('active');
        document.body.classList.remove('popup-active');
    });
    
    // Close popup when clicking overlay
    popupOverlay.addEventListener('click', function() {
        popupMenu.classList.remove('active');
        popupOverlay.classList.remove('active');
        document.body.classList.remove('popup-active');
    });
    
    // Close popup when clicking a link
    popupLinks.forEach(link => {
        link.addEventListener('click', function() {
            popupMenu.classList.remove('active');
            popupOverlay.classList.remove('active');
            document.body.classList.remove('popup-active');
        });
    });
    
    // Close popup when clicking outside
    document.addEventListener('click', function(e) {
        if (!popupMenu.contains(e.target) && !menuToggle.contains(e.target)) {
            popupMenu.classList.remove('active');
            popupOverlay.classList.remove('active');
            document.body.classList.remove('popup-active');
        }
    });
}