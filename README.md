# QLNhanSu — Quản lý người dùng PHP/MySQL

Ứng dụng quản lý người dùng phục vụ bài kiểm tra giữa học phần Công nghệ và Lập trình web.

- Thêm người dùng, lưu mật khẩu dưới dạng băm và tải ảnh đại diện.
- Hiển thị danh sách người dùng ở frontend.
- Sửa, xóa người dùng tại trang quản trị.
- Tìm nhanh theo email hoặc mã ID; xem trước ảnh và hiện/ẩn mật khẩu.
- Giao diện responsive trên điện thoại, máy tính bảng và máy tính.

## Cấu trúc dữ liệu

Cơ sở dữ liệu `QLNhanSu`, bảng `Users` có đúng 4 trường:

| Trường | Kiểu dữ liệu | Nội dung |
| --- | --- | --- |
| `admin_id` | `INT(10) UNSIGNED` | Khóa chính, tự động tăng |
| `admin_email` | `VARCHAR(255)` | Địa chỉ email |
| `admin_password` | `VARCHAR(255)` | Mật khẩu đã băm |
| `images` | `VARCHAR(255)` | Đường dẫn ảnh đại diện |

`database.sql` chỉ chứa cấu trúc cơ sở dữ liệu, không chứa dữ liệu người dùng.

## Chạy với XAMPP trên Windows

Yêu cầu PHP 7.4 trở lên, MySQL/MariaDB, các extension PHP `pdo_mysql` và `fileinfo`.

1. Clone repo vào thư mục web:

   ```powershell
   cd C:\xampp\htdocs
   git clone https://github.com/nguyen26012007-boop/giuakyphp.git
   ```

2. Bật **Apache** và **MySQL** trong XAMPP Control Panel.
3. Mở `http://localhost/phpmyadmin/`, chọn **Nhập (Import)** và nhập file `database.sql`.
4. Kiểm tra thông tin kết nối trong `config.php`. Mặc định: host `127.0.0.1`, database `QLNhanSu`, tài khoản `root`, mật khẩu trống. Có thể cấu hình qua biến môi trường `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`; file `.env` không được tự động đọc.
5. Đảm bảo thư mục `uploads` có quyền ghi.
6. Truy cập:
   - Danh sách: `http://localhost/giuakyphp/index.php`
   - Quản trị: `http://localhost/giuakyphp/admin.php`

Cũng có thể chạy `php -S 127.0.0.1:8000` trong thư mục dự án khi PHP và MySQL đã sẵn sàng, sau đó mở `http://127.0.0.1:8000`.

## Các tệp chính

| Tệp | Chức năng |
| --- | --- |
| `index.php` | Danh sách người dùng |
| `admin.php` | Thêm, sửa, xóa người dùng |
| `config.php` | Kết nối MySQL, CSRF, xử lý ảnh |
| `view.php` | Bố cục và biểu tượng dùng chung |
| `style.css` | Giao diện responsive |
| `app.js` | Tìm kiếm, xem trước ảnh, hiện/ẩn mật khẩu |
| `database.sql` | Tạo database và bảng 4 trường |

Ảnh tải lên, dữ liệu MySQL và skill AI cài trên máy không được đưa vào repo. Sau khi clone, thêm người dùng tại trang quản trị để có dữ liệu demo.

Ứng dụng dùng cho bài thực hành cục bộ. Trang quản trị hiện chưa có chức năng đăng nhập; cần bổ sung kiểm soát truy cập trước khi triển khai công khai.
