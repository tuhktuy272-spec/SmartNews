USE smartnews;

-- ============================================================
-- THIẾT LẬP TÀI KHOẢN ADMIN CHO MODULE 5 - QUẢN LÝ USER
-- ============================================================
-- Cách dùng:
-- 1. Đăng ký tài khoản bình thường trên website.
-- 2. Thay email bên dưới bằng email muốn cấp quyền admin.
-- 3. Import file này trong phpMyAdmin.
-- ============================================================

SET @admin_email = 'tuhktuy272@gmail.com';

UPDATE users
SET role_id = 1
WHERE email = @admin_email;

SELECT 
    users.id,
    users.full_name,
    users.email,
    users.status,
    roles.name AS role_name
FROM users
INNER JOIN roles ON roles.id = users.role_id
WHERE users.email = @admin_email;