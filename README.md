# SmartNews

SmartNews là website tin tức được xây dựng bằng PHP nguyên bản và MySQL.

## Công nghệ sử dụng

- PHP nguyên bản
- MySQL
- Bootstrap 5
- HTML/CSS
- XAMPP
- Git/GitHub

## Chức năng hiện tại

### Module 1: Quản lý tài khoản

- Đăng ký tài khoản
- Kiểm tra trùng email trước khi thêm vào database
- Mã hóa mật khẩu bằng `password_hash()`
- Đăng nhập bằng email và mật khẩu
- Xác thực mật khẩu bằng `password_verify()`
- Khởi tạo session sau khi đăng nhập
- Đăng xuất
- Phân quyền cơ bản bằng bảng `roles`

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
│   └── assets/
├── storage/
├── index.php
├── README.md
└── .gitignore