# 🛍️ Shop Mẹ và Bé Đông Lan - Hướng Dẫn Thiết Lập Admin

## 📋 Yêu Cầu
- PHP 7.4+
- MySQL/MariaDB
- phpMyAdmin (hoặc MySQL CLI)

---

## 🚀 Bước 1: Thiết Lập Database

### Cách 1: Sử dụng phpMyAdmin (Khuyên Dùng)

1. **Mở phpMyAdmin**
   - Truy cập: `http://localhost/phpmyadmin`
   - Đăng nhập với user root

2. **Chọn database `baby_shop`**
   - Click vào tab SQL
   - Copy nội dung từ file `DATABASE_SETUP.sql`
   - Dán vào khung nhập SQL
   - Click nút **Thực hiện** (Execute)

### Cách 2: Sử dụng MySQL Command Line

```bash
mysql -u root -p baby_shop < DATABASE_SETUP.sql
```

---

## 👨‍💼 Bước 2: Xác Nhận Tài Khoản Admin

Sau khi chạy SQL, kiểm tra:

```sql
SELECT id, username, email, full_name, role FROM users WHERE role='admin';
```

**Kết quả mong muốn:**
```
| id | username | email            | full_name          | role  |
|----|----------|------------------|--------------------|-------|
| X  | admin    | admin@shopme.vn  | Người Quản Lý      | admin |
```

---

## 🔐 Bước 3: Đăng Nhập Admin

1. Truy cập trang đăng nhập:
   ```
   http://localhost/baby-shop/tai-khoan.php
   hoặc
   https://your-railway-domain.up.railway.app/tai-khoan.php
   ```

2. Điền thông tin:
   - **Tên đăng nhập:** `admin`
   - **Mật khẩu:** `admin123`

3. Click **Đăng Nhập**

---

## 📊 Bước 4: Truy Cập Quản Lý Sản Phẩm

Sau khi đăng nhập với tài khoản admin:

```
http://localhost/baby-shop/quan-ly-san-pham.php
hoặc
https://your-railway-domain.up.railway.app/quan-ly-san-pham.php
```

### Chức Năng Quản Lý:
- ➕ **Thêm sản phẩm mới** - Nhập tên, giá, mô tả, danh mục, số lượng
- ✏️ **Sửa sản phẩm** - Cập nhật tên, giá, số lượng
- 🗑️ **Xóa sản phẩm** - Xóa sản phẩm khỏi hệ thống
- 📦 **Xem tồn kho** - Theo dõi số lượng sản phẩm

---

## 🔄 Thay Đổi Sau Này

### Chỉnh Sửa Admin qua phpMyAdmin:

1. Mở phpMyAdmin → Database `baby_shop`
2. Click vào bảng `users`
3. Tìm dòng với username = "admin"
4. Click **Edit** để sửa:
   - `full_name`: Tên người quản lý
   - `email`: Email liên hệ
   - `phone`: Số điện thoại
   - `role`: Để "admin" (không đổi)

### Tạo Admin Mới:

1. Trong phpMyAdmin, click **Insert** (Thêm mới)
2. Điền dữ liệu:
   ```
   username: admin2 (or another username)
   email: admin2@shopme.vn
   name: Admin Name
   full_name: Full Name
   phone: Phone Number
   password: Để trống (sẽ tạo sau)
   role: admin
   ```

3. Để đặt mật khẩu có hash:
   - Chạy PHP code để hash:
   ```php
   echo password_hash('password123', PASSWORD_BCRYPT, ['cost' => 12]);
   ```
   - Copy hash vào cột `password`

---

## ⚠️ Lưu Ý Bảo Mật

1. **Đổi mật khẩu mặc định:**
   - Đăng nhập với admin/admin123
   - Vào trang tai-khoan.php
   - Đổi mật khẩu ngay lập tức

2. **Database:**
   - Bảo vệ phpmyadmin bằng mật khẩu mạnh
   - Không để trống mật khẩu root
   - Xóa user test/anonymous

3. **File:**
   - Không để DATABASE_SETUP.sql công khai trên web

---

## 🆘 Khắc Phục Sự Cố

### Lỗi: "Bạn không có quyền truy cập"
- Kiểm tra role trong database có phải "admin" không
- Refresh trang sau khi đăng nhập

### Lỗi: "Cột role không tồn tại"
- Chạy SQL migration trong DATABASE_SETUP.sql
- Refresh database

### Quên mật khẩu admin
- Sử dụng SQL để set mật khẩu mới:
```sql
UPDATE users SET password='$2y$12$Qa.qQsgTJy5y6P//9WS52OZtqUbqAu/1bPMqnZvZ89vKXZLcMBOUm' WHERE username='admin';
```
- Mật khẩu sẽ reset thành `admin123`

---

## 📞 Liên Hệ Hỗ Trợ

Hotline: 0866.021.711
Email: support@shopme.vn

---

*Cập nhật lần cuối: 03/04/2026*
