# Hệ Thống Quản Lý Nhân Sự & Tiền Lương (PHP MVC)

Dự án Bài tập lớn môn PHP - Hệ thống quản lý nhân sự, chấm công và tính lương cho doanh nghiệp.

## 🛠 Chức năng chính
- **Quản lý nhân sự:** Thêm, sửa, xóa, tìm kiếm nhân viên; quản lý hồ sơ chi tiết.
- **Phòng ban & Chức vụ:** Quản lý cơ cấu tổ chức công ty.
- **Chấm công:** Theo dõi đi làm hằng ngày, quản lý đơn nghỉ phép, tính ngày công thực tế.
- **Quản lý Tiền lương:** Tính lương tự động dựa trên mức lương cơ bản, ngày công, phụ cấp và khen thưởng/kỷ luật.
- **Hợp đồng lao động:** Quản lý thời hạn và loại hợp đồng của từng nhân viên.
- **Hệ thống phân quyền:** Admin, Nhân sự (HR), Kế toán, Nhân viên.
- **Nhật ký hệ thống:** Lưu lại lịch sử hoạt động để kiểm soát bảo mật.

## 🚀 Hướng dẫn cài đặt

### 1. Yêu cầu hệ thống
- **XAMPP** (PHP >= 7.4, MySQL).
- Thư mục dự án đặt tại `C:\xampp\htdocs\BaitaplonPHP`.

### 2. Các bước cài đặt
1. Giải nén/Clone mã nguồn vào thư mục `htdocs` của XAMPP.
2. Khởi động **Apache** và **MySQL** trong XAMPP Control Panel.
3. Mở trình duyệt và truy cập đường dẫn cài đặt tự động:
   ```
   http://localhost/BaitaplonPHP/setup.php
   ```
4. Nhấn nút **"Bắt đầu cài đặt"**. Hệ thống sẽ tự động tạo Database `ql_nhansu_luong` và các bảng dữ liệu mẫu.

### 3. Thông tin đăng nhập mặc định
| Vai trò | Username | Password |
| :--- | :--- | :--- |
| **Quản trị (Admin)** | admin | admin123 |
| **Nhân sự (HR)** | hr01 | hr123 |
| **Kế toán** | ketoan01 | ketoan123 |
| **Nhân viên** | nhanvien01 | nhanvien123 |

---
**Lưu ý:** Sau khi cài đặt thành công, bạn nên xóa file `setup.php` để đảm bảo an toàn cho hệ thống.
