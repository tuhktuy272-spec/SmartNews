# SmartNews

SmartNews là website tin tức xây dựng bằng PHP nguyên bản và MySQL, chạy trên XAMPP.

## Công nghệ sử dụng

- PHP nguyên bản
- MySQL
- Bootstrap 5
- HTML/CSS/JavaScript
- XAMPP
- Git/GitHub

## Chức năng đã hoàn thành

### Module 1: Quản lý tài khoản

- Đăng ký tài khoản
- Kiểm tra trùng email trước khi insert
- Mã hóa mật khẩu bằng `password_hash()`
- Đăng nhập bằng email và mật khẩu
- Xác thực bằng `password_verify()`
- Khởi tạo session
- Đăng xuất
- Phân quyền bằng bảng `roles`

### Module 2: Quản lý bài viết cơ bản

- Người dùng tạo bài viết
- Lưu nháp
- Gửi bài chờ duyệt
- Sửa bài nháp hoặc bài bị từ chối
- Xóa mềm bài viết

### Module 3: API và cá nhân hóa

- API quản lý user cho Admin
- API đọc dữ liệu user dạng JSON
- Ghi nhận hành vi đọc bài
- Gợi ý bài viết

### Module 4: Giao diện tin tức

- Trang chủ tin tức hiện đại
- Hiển thị chuyên mục
- Hiển thị bài mới
- Hiển thị bài nổi bật
- Tìm kiếm bài viết
- Trang chi tiết bài viết

### Module 5: Editor CMS

- Soạn bài viết
- Upload nhiều ảnh
- Gắn chuyên mục
- Gắn thẻ
- Lưu nháp
- Gửi duyệt
- Xem danh sách bài viết của tôi

### Module 6: Admin Dashboard

- Thống kê tổng quan
- Duyệt bài viết
- Từ chối bài viết kèm lý do
- Xóa mềm bài viết
- Quản lý bình luận
- Ẩn/hiện bình luận
- Xóa mềm bình luận
- Quản lý user qua API

## Cấu trúc thư mục

```txt
SmartNews/
├── app/
│   ├── Controllers/
│   ├── Helpers/
│   ├── Models/
│   └── Views/
├── config/
├── database/
├── public/
│   ├── admin/
│   ├── api/
│   ├── assets/
│   ├── editor/
│   └── uploads/
├── index.php
├── README.md
└── .gitignore