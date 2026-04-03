# 🛠️ HƯỚNG DẪN FIX LỖI & KIỂM TRA HỆ THỐNG

## ⚠️ Vấn đề hiện tại:
1. ❌ Sản phẩm không hiển thị ở gian hàng
2. ❌ Không thể bấm vào sản phẩm để xem chi tiết
3. ❌ Lỗi 500 từ API

---

## ✅ GIẢI PHÁP - 3 BƯỚC ĐƠN GIẢN

### **BƯỚC 1: Khởi động Migration (1 lần đầu)**

**Truy cập trang này:**
```
http://localhost/baby-shop/auto-setup.php
```

Trang này sẽ tự động:
- ✓ Thêm cột `gender` vào database (nếu chưa có)
- ✓ Tạo index cho tìm kiếm nhanh
- ✓ Hiển thị thông tin database hiện tại
- ✓ Kiểm tra toàn vẹn dữ liệu

**Kết quả mong đợi:**
```
✅ Hoàn thành: 3/3 bước
📦 Tổng sản phẩm: X
```

---

### **BƯỚC 2: Khi cần thêm sản phẩm PHẢI có Giới tính**

**Truy cập:**
```
http://localhost/baby-shop/quan-ly-san-pham.php
```

**Form thêm sản phẩm mới (BẮT BUỘC):**

| Trường | Giá trị | Ghi chú |
|--------|--------|--------|
| Tên sản phẩm | *Nhập tên* | Bắt buộc |
| Danh mục | 0-3 / 3-6 / 6-12 / Phụ kiện | Bắt buộc |
| **👨‍👩‍👧‍👦 Giới tính** | **Bé Trai** / **Bé Gái** / **Unisex** | **🆕 BẮT BUỘC** |
| Giá | Nhập số (VND) | Bắt buộc |
| Giảm giá | 0-100 (%) | Tính tự động |
| Số lượng | Nhập số | Bắt buộc |
| Ảnh | JPG/PNG | Bắt buộc |

---

### **BƯỚC 3: Kiểm tra sản phẩm**

#### **A. Ở Gian Hàng (Cửa Hàng)**
```
http://localhost/baby-shop/cua-hang.php
```

✓ Chọn danh mục (0-3 Tháng, etc.)
✓ Sản phẩm sẽ hiển thị

#### **B. Bấm vào sản phẩm**
✓ Click vào card sản phẩm
✓ Modal sẽ hiển thị đầy đủ thông tin:
  - Ảnh sản phẩm
  - Danh mục
  - **📅 Ngày tạo (DD/MM/YYYY HH:MM:SS)**
  - Giá gốc + Giá bán + % Giảm
  - Mô tả
  - Thêm vào giỏ

---

## 🧪 KIỂM TRA HỆ THỐNG

### **Test API trực tiếp:**
```
http://localhost/baby-shop/test-api.html
```

**Kiểm tra:**
1. ✓ GET tất cả sản phẩm
2. ✓ GET sản phẩm theo danh mục
3. ✓ GET chi tiết 1 sản phẩm
4. ✓ GET giỏ hàng

Kết quả phải là `Status: 200` (Success)

---

## 🔍 NẾU VẪN CÓ LỖI

### **1. Lỗi: Sản phẩm không hiển thị**
```
Kiểm tra:
1. F12 → Console → Có lỗi JS không?
2. F12 → Network → API trả dữ liệu không?
3. Kiểm tra auto-setup.php xem database có sản phẩm không
```

### **2. Lỗi: Không bấm vào được sản phẩm**
```
Kiểm tra:
1. DevTools Console → viewProductDetail() có chạy không?
2. Network tab → API response có lỗi không?
3. ProductID có truyền vào không?
```

### **3. Lỗi: API 500 Error**
```
Kiểm tra:
1. Truy cập: auto-setup.php → xem DB structure
2. Tên cột có đúng không? (image không phải image_path)
3. Server logs: /xampp/apache/logs/error.log
```

---

## 📝 QUICK CHECKLIST

- [ ] Truy cập `auto-setup.php` - hoàn thành migration
- [ ] Thêm 2-3 sản phẩm test với đầy đủ thông tin (bao gồm giới tính)
- [ ] Vào `cua-hang.php` - chọn danh mục - thấy sản phẩm không?
- [ ] Click vào sản phẩm - modal hiện không?
- [ ] Test API tại `test-api.html` - tất cả 200 OK?

---

## 📞 LIÊN HỆ HỖ TRỢ

Nếu vẫn có vấn đề:
1. Kiểm tra file logs
2. Đảm bảo form được submit đầy đủ
3. Kiểm tra console DevTools
4. Update cache browser (Ctrl+Shift+Delete)

---

## 📚 FILE QUAN TRỌNG

| File | Dùng để |
|------|---------|
| `auto-setup.php` | 🔧 Kiểm tra & migration DB |
| `test-api.html` | 🧪 Test API |
| `quan-ly-san-pham.php` | 📦 Thêm sản phẩm |
| `cua-hang.php` | 🛒 Xem sản phẩm |
| `api-dieu-khien/san-pham.php` | 🔌 API sản phẩm |

---

**✅ Chúc bạn thành công!**
