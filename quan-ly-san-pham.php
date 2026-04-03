<?php
session_start();
require_once 'config.php';

// Kiểm tra admin access
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Bạn không có quyền truy cập';
    header('Location: tai-khoan.php');
    exit;
}

// Lấy danh sách sản phẩm
$products = [];
$sql = "SELECT id, name, price, category, stock FROM products ORDER BY id DESC LIMIT 100";
$result = $conn->query($sql);
if ($result) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm - Admin</title>
    <link rel="stylesheet" href="css-kieu-dang/kieu-dang.css">
    <link rel="stylesheet" href="css-kieu-dang/mobile.css" media="(max-width: 768px)">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 100px auto 20px;
            padding: 20px;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #E74C3C;
            padding-bottom: 15px;
        }

        .admin-header h1 {
            color: #333;
            font-size: 24px;
        }

        .admin-header a {
            background: #E74C3C;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s;
        }

        .admin-header a:hover {
            background: #C0392B;
        }

        /* Form thêm/sửa sản phẩm */
        .product-form {
            background: white;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .product-form h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-primary {
            background: #E74C3C;
            color: white;
        }

        .btn-primary:hover {
            background: #C0392B;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        /* Bảng sản phẩm */
        .products-table {
            background: white;
            border-collapse: collapse;
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .products-table thead {
            background: #34495e;
            color: white;
        }

        .products-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .products-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ecf0f1;
        }

        .products-table tbody tr:hover {
            background: #f8f9fa;
        }

        .products-table .action-btns {
            display: flex;
            gap: 8px;
        }

        .btn-edit, .btn-delete {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-edit {
            background: #3498db;
            color: white;
        }

        .btn-edit:hover {
            background: #2980b9;
        }

        .btn-delete {
            background: #e74c3c;
            color: white;
        }

        .btn-delete:hover {
            background: #c0392b;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: none;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        @media (max-width: 768px) {
            .admin-container {
                margin-top: 80px;
                padding: 10px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .products-table {
                font-size: 12px;
            }

            .products-table th,
            .products-table td {
                padding: 8px;
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
            <div class="header-right">
                <span style="color: white; margin-right: 15px;">👤 <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
                <a href="tai-khoan.php?logout=1" style="background: #E74C3C; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none;">Đăng xuất</a>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <div class="admin-header">
            <h1>⚙️ Quản Lý Sản Phẩm</h1>
            <a href="trang-chu.php">← Quay lại trang chủ</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Form thêm/sửa sản phẩm -->
        <div class="product-form">
            <h2>➕ Thêm Sản Phẩm Mới</h2>
            <form id="productForm" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Tên Sản Phẩm *</label>
                        <input type="text" id="name" name="name" required placeholder="Ví dụ: Bộ quần áo bé gái">
                    </div>
                    <div class="form-group">
                        <label for="category">Danh Mục *</label>
                        <select id="category" name="category" required>
                            <option value="">-- Chọn danh mục --</option>
                            <option value="0-3">0-3 Tháng</option>
                            <option value="3-6">3-6 Tháng</option>
                            <option value="6-12">6-12 Tháng</option>
                            <option value="phu-kien">Phụ kiện</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Giá Gốc (VND) *</label>
                        <input type="number" id="price" name="price" required min="1000" placeholder="50000" onchange="calculateDiscountedPrice()">
                    </div>
                    <div class="form-group">
                        <label for="discount_percent">Giảm Giá (%)</label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="number" id="discount_percent" name="discount_percent" min="0" max="100" value="0" placeholder="0" step="0.5" onchange="calculateDiscountedPrice()" style="flex: 1;">
                            <div style="padding: 10px; background: #f0f0f0; border-radius: 5px; font-weight: bold; min-width: 100px;">
                                Giá: <span id="discounted-price" style="color: #E74C3C;">0 ₫</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="stock">Số Lượng *</label>
                        <input type="number" id="stock" name="stock" required min="0" placeholder="100">
                    </div>
                    <div class="form-group">
                        <label for="image">Ảnh Sản Phẩm (JPG/PNG) *</label>
                        <input type="file" id="image" name="image" required accept=".jpg,.jpeg,.png" onchange="previewImage(this)">
                        <small style="color: #666; display: block; margin-top: 5px;">Chỉ hỗ trợ JPG và PNG</small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Xem trước ảnh:</label>
                    <img id="image-preview" src="" alt="Xem trước" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 5px; display: none;">
                </div>

                <div class="form-group">
                    <label for="description">Mô Tả Sản Phẩm</label>
                    <textarea id="description" name="description" placeholder="Mô tả chi tiết về sản phẩm..."></textarea>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">💾 Thêm Sản Phẩm</button>
                    <button type="reset" class="btn btn-secondary" onclick="resetForm()">↻ Làm mới</button>
                </div>
            </form>
        </div>

        <!-- Danh sách sản phẩm -->
        <h2 style="margin-top: 30px; margin-bottom: 15px; color: #333;">📦 Danh Sách Sản Phẩm (<?php echo count($products); ?>)</h2>
        
        <div style="overflow-x: auto;">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Sản Phẩm</th>
                        <th>Danh Mục</th>
                        <th>Giá Gốc</th>
                        <th>Giảm Giá</th>
                        <th>Giá Bán</th>
                        <th>Số Lượng</th>
                        <th>Ngày Tạo</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): 
                        // Tính giá sau giảm
                        $discount_percent = isset($product['discount_percent']) ? $product['discount_percent'] : 0;
                        $discount_amount = $product['price'] * ($discount_percent / 100);
                        $final_price = $product['price'] - $discount_amount;
                    ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td>₫<?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                            <td><?php echo $discount_percent > 0 ? '<span style="color: #E74C3C; font-weight: bold;">' . number_format($discount_percent, 2) . '%</span>' : '-'; ?></td>
                            <td style="<?php echo $discount_percent > 0 ? 'color: #27ae60; font-weight: bold;' : ''; ?>">₫<?php echo number_format($final_price, 0, ',', '.'); ?></td>
                            <td>
                                <span style="background: <?php echo $product['stock'] > 20 ? '#d4edda' : '#f8d7da'; ?>; padding: 4px 8px; border-radius: 3px;">
                                    <?php echo $product['stock']; ?>
                                </span>
                            </td>
                            <td><?php echo isset($product['created_at']) ? date('d/m/Y H:i', strtotime($product['created_at'])) : 'N/A'; ?></td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-edit" onclick="editProduct(<?php echo $product['id']; ?>)">✏️ Sửa</button>
                                    <button class="btn-delete" onclick="deleteProduct(<?php echo $product['id']; ?>)">🗑️ Xóa</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Tính giá sau giảm
        function calculateDiscountedPrice() {
            const price = parseFloat(document.getElementById('price').value) || 0;
            const discountPercent = parseFloat(document.getElementById('discount_percent').value) || 0;
            const discountedPrice = price - (price * discountPercent / 100);
            document.getElementById('discounted-price').textContent = 
                parseFloat(discountedPrice).toLocaleString('vi-VN') + ' ₫';
        }

        // Xem trước ảnh
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        // Reset form
        function resetForm() {
            document.getElementById('productForm').reset();
            document.getElementById('image-preview').style.display = 'none';
            document.getElementById('discounted-price').textContent = '0 ₫';
            calculateDiscountedPrice();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            calculateDiscountedPrice();
            
            // Add real-time calculation on input change
            document.getElementById('price').addEventListener('input', calculateDiscountedPrice);
            document.getElementById('discount_percent').addEventListener('input', calculateDiscountedPrice);
        });

        // Submit form thêm sản phẩm
        document.getElementById('productForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData();
            formData.append('name', document.getElementById('name').value);
            formData.append('price', parseFloat(document.getElementById('price').value));
            formData.append('discount_percent', parseFloat(document.getElementById('discount_percent').value) || 0);
            formData.append('description', document.getElementById('description').value);
            formData.append('category', document.getElementById('category').value);
            formData.append('stock', parseInt(document.getElementById('stock').value));
            
            const imageFile = document.getElementById('image').files[0];
            if (imageFile) {
                formData.append('image', imageFile);
            }

            try {
                const response = await fetch('./api-dieu-khien/san-pham.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.status === 'success') {
                    alert('✅ Thêm sản phẩm thành công!');
                    resetForm();
                    location.reload();
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Lỗi khi thêm sản phẩm: ' + error.message);
            }
        });

        // Sửa sản phẩm
        function editProduct(id) {
            const newPrice = prompt('Nhập giá mới:');
            if (newPrice === null) return;

            const newStock = prompt('Nhập số lượng mới:');
            if (newStock === null) return;

            const newName = prompt('Nhập tên mới:');
            if (newName === null) return;

            const newDiscount = prompt('Nhập % giảm giá (0-100):');
            if (newDiscount === null) return;

            const formData = {
                id: id,
                name: newName,
                price: parseFloat(newPrice),
                stock: parseInt(newStock),
                discount_percent: parseFloat(newDiscount)
                stock: parseInt(newStock)
            };

            fetch('./api-dieu-khien/san-pham.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('✅ Cập nhật thành công!');
                    location.reload();
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            });
        }

        // Xóa sản phẩm
        function deleteProduct(id) {
            if (!confirm('Bạn chắc chắn muốn xóa sản phẩm này?')) return;

            fetch('./api-dieu-khien/san-pham.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('✅ Xóa thành công!');
                    location.reload();
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            });
        }
    </script>
</body>
</html>
