<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

requireAuth();

$pdo = require __DIR__ . '/../config/db_connect.php';

$latestPublishedPost = null;

try {
    $stmt = $pdo->query("
        SELECT id, title
        FROM posts
        WHERE status = 'published'
        ORDER BY created_at DESC
        LIMIT 1
    ");

    $latestPublishedPost = $stmt->fetch();
} catch (Throwable $e) {
    $latestPublishedPost = null;
}

$pageTitle = 'Trung tâm kiểm thử - SmartNews';

require_once __DIR__ . '/../app/Views/layout/header.php';

$userName = $_SESSION['user']['full_name'] ?? 'Người dùng';
$userRole = currentUserRole() ?? 'unknown';

?>

<section class="py-5">
    <div class="container">
        <div class="mb-4">
            <h1 class="fw-bold">
                Trung tâm kiểm thử SmartNews
            </h1>

            <p class="text-muted mb-0">
                Tài khoản:
                <strong><?= e($userName) ?></strong>
                -
                Quyền:
                <strong><?= e($userRole) ?></strong>
            </p>
        </div>

        <?php renderFlashMessages(); ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card auth-card h-100">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">
                            Chức năng chung
                        </h4>

                        <div class="d-grid gap-2">
                            <a href="<?= url('/index.php') ?>" class="btn btn-outline-primary">
                                Xem trang chủ tin tức
                            </a>

                            <a href="<?= url('/dashboard.php') ?>" class="btn btn-outline-primary">
                                Dashboard
                            </a>

                            <?php if ($latestPublishedPost): ?>
                                <a
                                    href="<?= url('/article.php?id=' . (int) $latestPublishedPost['id']) ?>"
                                    class="btn btn-outline-success"
                                >
                                    Xem bài đã xuất bản mới nhất
                                </a>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary" disabled>
                                    Chưa có bài đã xuất bản
                                </button>
                            <?php endif; ?>
                        </div>

                        <hr>

                        <p class="small text-muted mb-0">
                            Admin, editor và user đều có thể xem tin tức.
                        </p>
                    </div>
                </div>
            </div>

            <?php if (isEditor()): ?>
                <div class="col-lg-4">
                    <div class="card auth-card h-100">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-3">
                                Chức năng Editor
                            </h4>

                            <div class="d-grid gap-2">
                                <a href="<?= url('/editor/compose.php') ?>" class="btn btn-primary">
                                    Soạn bài viết mới
                                </a>

                                <a href="<?= url('/editor/my_articles.php') ?>" class="btn btn-outline-primary">
                                    Bài viết của tôi
                                </a>

                                <a href="<?= url('/user_dashboard.php') ?>" class="btn btn-outline-primary">
                                    Quản lý bài viết cơ bản
                                </a>
                            </div>

                            <hr>

                            <p class="small text-muted mb-0">
                                Editor được viết bài, lưu nháp, gửi duyệt và sửa bài của mình.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isAdmin()): ?>
                <div class="col-lg-4">
                    <div class="card auth-card h-100">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-3">
                                Chức năng Admin
                            </h4>

                            <div class="d-grid gap-2">
                                <a href="<?= url('/admin/dashboard.php') ?>" class="btn btn-danger">
                                    Admin Dashboard
                                </a>

                                <a href="<?= url('/admin/posts.php') ?>" class="btn btn-outline-danger">
                                    Duyệt / từ chối bài viết
                                </a>

                                <a href="<?= url('/admin/posts.php?status=pending') ?>" class="btn btn-outline-warning">
                                    Bài đang chờ duyệt
                                </a>

                                <a href="<?= url('/admin/comments.php') ?>" class="btn btn-outline-danger">
                                    Quản lý bình luận
                                </a>

                                <a href="<?= url('/admin/users.php') ?>" class="btn btn-outline-dark">
                                    Quản lý người dùng
                                </a>
                            </div>

                            <hr>

                            <p class="small text-muted mb-0">
                                Admin quản trị hệ thống nhưng không viết bài.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isNormalUser()): ?>
                <div class="col-lg-4">
                    <div class="card auth-card h-100">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-3">
                                Chức năng User
                            </h4>

                            <div class="alert alert-info mb-0">
                                User chỉ được xem bài viết và bình luận.
                                User không được viết bài và không được vào trang admin.
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="card auth-card mt-4">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-3">
                    Luồng test theo quyền
                </h4>

                <?php if (isAdmin()): ?>
                    <ol class="mb-0">
                        <li>Vào <strong>Admin Dashboard</strong>.</li>
                        <li>Vào <strong>Duyệt / từ chối bài viết</strong>.</li>
                        <li>Duyệt bài hoặc từ chối bài có lý do.</li>
                        <li>Vào <strong>Quản lý bình luận</strong>.</li>
                        <li>Vào <strong>API quản lý user</strong> để kiểm tra user và phân quyền.</li>
                        <li>Thử mở trực tiếp <code>/editor/compose.php</code>, admin phải bị chặn.</li>
                    </ol>
                <?php elseif (isEditor()): ?>
                    <ol class="mb-0">
                        <li>Vào <strong>Soạn bài viết mới</strong>.</li>
                        <li>Tạo bài và bấm <strong>Gửi duyệt</strong>.</li>
                        <li>Vào <strong>Bài viết của tôi</strong>, kiểm tra trạng thái chờ duyệt.</li>
                        <li>Thử mở trực tiếp <code>/admin/dashboard.php</code>, editor phải bị chặn.</li>
                    </ol>
                <?php else: ?>
                    <ol class="mb-0">
                        <li>Vào <strong>Trang chủ tin tức</strong>.</li>
                        <li>Mở một bài đã xuất bản.</li>
                        <li>Gửi bình luận nếu đã có form bình luận.</li>
                        <li>Thử mở trực tiếp <code>/editor/compose.php</code>, user phải bị chặn.</li>
                        <li>Thử mở trực tiếp <code>/admin/dashboard.php</code>, user phải bị chặn.</li>
                    </ol>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php

require_once __DIR__ . '/../app/Views/layout/footer.php';

?>