<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

requireAuth();

$pageTitle = 'Dashboard - SmartNews';

require_once __DIR__ . '/../app/Views/layout/header.php';

$userName = $_SESSION['user']['full_name'] ?? 'Người dùng';
$userRole = currentUserRole() ?? 'unknown';

?>

<section class="py-5">
    <div class="container">
        <div class="mb-4">
            <h1 class="fw-bold mb-1">
                Dashboard
            </h1>

            <p class="text-muted mb-0">
                Xin chào, <strong><?= e($userName) ?></strong>.
                Quyền hiện tại:
                <strong><?= e($userRole) ?></strong>
            </p>
        </div>

        <?php renderFlashMessages(); ?>

        <div class="row g-3">
            <div class="col-md-12">
                <a href="<?= url('/index.php') ?>" class="text-decoration-none text-dark">
                    <div class="p-3 bg-light rounded-4 h-100">
                        <strong>Trang chủ tin tức</strong>

                        <div class="small text-muted">
                            Xem các bài viết đã được xuất bản.
                        </div>
                    </div>
                </a>
            </div>

            <?php if (isEditor()): ?>
                <div class="col-md-6">
                    <a href="<?= url('/editor/compose.php') ?>" class="text-decoration-none text-dark">
                        <div class="p-3 bg-light rounded-4 h-100">
                            <strong>Soạn bài viết</strong>

                            <div class="small text-muted">
                                Tạo bài viết mới, lưu nháp hoặc gửi duyệt.
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6">
                    <a href="<?= url('/editor/my_articles.php') ?>" class="text-decoration-none text-dark">
                        <div class="p-3 bg-light rounded-4 h-100">
                            <strong>Bài viết của tôi</strong>

                            <div class="small text-muted">
                                Xem, sửa và gửi duyệt bài viết của mình.
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6">
                    <a href="<?= url('/user_dashboard.php') ?>" class="text-decoration-none text-dark">
                        <div class="p-3 bg-light rounded-4 h-100">
                            <strong>Quản lý bài viết cơ bản</strong>

                            <div class="small text-muted">
                                Chức năng viết bài từ nhiệm vụ 2.
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (isAdmin()): ?>
                <div class="col-md-6">
                    <a href="<?= url('/admin/dashboard.php') ?>" class="text-decoration-none text-dark">
                        <div class="p-3 bg-light rounded-4 h-100">
                            <strong>Admin Dashboard</strong>

                            <div class="small text-muted">
                                Thống kê và quản lý tổng quan hệ thống.
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6">
                    <a href="<?= url('/admin/posts.php') ?>" class="text-decoration-none text-dark">
                        <div class="p-3 bg-light rounded-4 h-100">
                            <strong>Duyệt bài viết</strong>

                            <div class="small text-muted">
                                Duyệt, từ chối hoặc xóa mềm bài viết.
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6">
                    <a href="<?= url('/admin/comments.php') ?>" class="text-decoration-none text-dark">
                        <div class="p-3 bg-light rounded-4 h-100">
                            <strong>Quản lý bình luận</strong>

                            <div class="small text-muted">
                                Ẩn, hiện hoặc xóa mềm bình luận.
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6">
                    <a href="<?= url('/admin/users.php') ?>" class="text-decoration-none text-dark">
                        <div class="p-3 bg-light rounded-4 h-100">
                            <strong>Quản lý người dùng</strong>

                            <div class="small text-muted">
                                Xem user, khóa tài khoản và phân quyền.
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (isNormalUser()): ?>
                <div class="col-md-12">
                    <div class="alert alert-info mb-0">
                        Tài khoản user thường chỉ được xem tin tức và bình luận.
                        User không có quyền viết bài hoặc truy cập trang quản trị.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php

require_once __DIR__ . '/../app/Views/layout/footer.php';

?>