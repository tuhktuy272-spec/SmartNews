USE smartnews;

-- ============================================================
-- PHÂN QUYỀN TÀI KHOẢN SMARTNEWS
-- admin  : quản trị hệ thống, duyệt bài, quản lý user
-- editor : viết bài, lưu nháp, gửi duyệt
-- user   : xem tin tức, bình luận
-- ============================================================

-- 1. Đảm bảo có đủ 3 quyền chính trong hệ thống
INSERT INTO roles (id, name, description)
VALUES
(1, 'admin', 'Quản trị viên - quản lý hệ thống, duyệt bài, quản lý user'),
(2, 'user', 'Người dùng thường - xem tin tức và bình luận'),
(3, 'editor', 'Biên tập viên - viết bài, lưu nháp và gửi duyệt')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description);

-- ============================================================
-- 2. Cấp quyền mẫu cho các tài khoản test
-- Em có thể sửa email bên dưới theo tài khoản thật trong máy em.
-- Sau khi chạy SQL, nhớ đăng xuất rồi đăng nhập lại.
-- ============================================================

-- Tài khoản admin
UPDATE users
SET role_id = 1
WHERE email = 'tuhktuy272@gmail.com';

-- Tài khoản editor
UPDATE users
SET role_id = 3
WHERE email = 'trannguyen123@gmail.com';

-- Nếu muốn tạo thêm user thường, thay email bên dưới rồi bỏ dấu --
-- UPDATE users
-- SET role_id = 2
-- WHERE email = 'email_user@example.com';

-- ============================================================
-- 3. Kiểm tra danh sách user và quyền hiện tại
-- ============================================================

SELECT
    users.id,
    users.full_name,
    users.email,
    users.status,
    roles.name AS role_name
FROM users
INNER JOIN roles ON roles.id = users.role_id
ORDER BY users.id ASC;