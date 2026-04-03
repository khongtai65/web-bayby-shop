// Dữ liệu sản phẩm
const products = [
    { id: 1, name: "Áo sơ mi cotton 0-3 tháng", category: "0-3", price: 150000 },
    { id: 2, name: "Áo thun trắng 0-3 tháng", category: "0-3", price: 120000 },
    { id: 3, name: "Quần dài cotton 0-3 tháng", category: "0-3", price: 140000 },
    { id: 4, name: "Bộ 2 áo 0-3 tháng", category: "0-3", price: 250000 },
    
    { id: 5, name: "Áo sơ mi cotton 3-6 tháng", category: "3-6", price: 160000 },
    { id: 6, name: "Áo thun in hình 3-6 tháng", category: "3-6", price: 130000 },
    { id: 7, name: "Quần dài cotton 3-6 tháng", category: "3-6", price: 150000 },
    { id: 8, name: "Áo khoác mỏng 3-6 tháng", category: "3-6", price: 200000 },
    
    { id: 9, name: "Áo sơ mi cotton 6-12 tháng", category: "6-12", price: 170000 },
    { id: 10, name: "Áo thun in hình 6-12 tháng", category: "6-12", price: 140000 },
    { id: 11, name: "Quần dài cotton 6-12 tháng", category: "6-12", price: 160000 },
    { id: 12, name: "Áo khoác mỏng 6-12 tháng", category: "6-12", price: 220000 },
    
    { id: 13, name: "Mũ len trẻ em", category: "phu-kien", price: 80000 },
    { id: 14, name: "Khăn lụa trẻ em", category: "phu-kien", price: 60000 },
    { id: 15, name: "Tất cotton 5 đôi", category: "phu-kien", price: 90000 },
    { id: 16, name: "Nón che nắng trẻ em", category: "phu-kien", price: 100000 },
    { id: 17, name: "Dây đeo balo trẻ em", category: "phu-kien", price: 120000 },
    { id: 18, name: "Cặp xách trẻ em", category: "phu-kien", price: 150000 },
];

// Hàm tìm kiếm sản phẩm
function searchProducts(query) {
    const resultsContainer = document.getElementById('search-results');
    
    if (!query || query.length < 1) {
        resultsContainer.style.display = 'none';
        return;
    }
    
    const filtered = products.filter(product => 
        product.name.toLowerCase().includes(query.toLowerCase())
    );
    
    if (filtered.length === 0) {
        resultsContainer.innerHTML = '<div class="search-result-item">Không tìm thấy sản phẩm</div>';
        resultsContainer.style.display = 'block';
        return;
    }
    
    resultsContainer.innerHTML = filtered.map(product => `
        <div class="search-result-item" onclick="goToCategory('${product.category}')">
            <div class="result-name">${product.name}</div>
            <div class="result-price">${product.price.toLocaleString('vi-VN')} ₫</div>
        </div>
    `).join('');
    
    resultsContainer.style.display = 'block';
}

// Hàm đi tới danh mục
function goToCategory(category) {
    window.location.href = `cua-hang.php?category=${category}`;
}

// Event listener cho search input
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            searchProducts(e.target.value);
        });
        
        // Ẩn kết quả khi click ra ngoài
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.header-search')) {
                const resultsContainer = document.getElementById('search-results');
                if (resultsContainer) {
                    resultsContainer.style.display = 'none';
                }
            }
        });
    }
});

// Hàm load sản phẩm trong cửa hàng
function loadProducts(category = '') {
    const container = document.getElementById('products-container');
    if (!container) return;
    
    let filtered = products;
    if (category) {
        filtered = products.filter(p => p.category === category);
    }
    
    container.innerHTML = filtered.map(product => `
        <div class="product-card">
            <div class="product-image">📦</div>
            <div class="product-info">
                <div class="product-name">${product.name}</div>
                <div class="product-price">${product.price.toLocaleString('vi-VN')} ₫</div>
                <div class="product-actions">
                    <input type="number" value="1" min="1" class="quantity-input">
                    <button class="btn btn-primary">Thêm vào giỏ</button>
                </div>
            </div>
        </div>
    `).join('');
}

// Hàm filter theo category
function filterByCategory() {
    const select = document.getElementById('category-filter');
    if (select) {
        const category = select.value;
        loadProducts(category);
    }
}
