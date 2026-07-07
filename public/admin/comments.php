<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../app/Helpers/functions.php';

requireAdmin();

$pdo = require __DIR__ . '/../../config/db_connect.php';

$status = $_GET['status'] ?? '';

$where = ["post_comments.status <> 'deleted'"];
$params = [];

if (in_array($status, ['visible', 'hidden'], true)) {
    $where[] = "post_comments.status = :status";
    $params['status'] = $status;
}

$whereSql = implode(' AND ', $where);

$stmt = $pdo->prepare("
    SELECT
        post_comments.id,
        post_comments.content,
        post_comments.status,
        post_comments.created_at,
        posts.title AS post_title,
        users.full_name AS user_name,
        users.email AS user_email
    FROM post_comments
    INNER JOIN posts ON posts.id = post_comments.post_id
    LEFT JOIN users ON users.id = post_comments.user_id
    WHERE $whereSql
    ORDER BY post_comments.created_at DESC
");

foreach ($params as $key => $value) {
    $stmt->bindValue(':' . $key, $value);
}

$stmt->execute();

$comments = $stmt->fetchAll();

$pageTitle = 'Quản lý bình luận - SmartNews';

require_once __DIR__ . '/../../app/Views/layout/header.php';

?>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-1">Quản lý bình luận</h1>

                <p class="text-muted mb-0">
                    Admin có thể ẩn, hiện hoặc xóa mềm bình luận.
                </p>
            </div>

            <a href="<?= url('/admin/dashboard.php') ?>" class="btn btn-outline-secondary">
                Về Admin Dashboard
            </a>
        </div>

        <?php renderFlashMessages(); ?>

        <div class="card auth-card mb-4">
            <div class="card-body p-4">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <select name="status" class="form-select">
                            <option value="">Tất cả bình luận</option>
                            <option value="visible" <?= $status === 'visible' ? 'selected' : '' ?>>Đang hiển thị</option>
                            <option value="hidden" <?= $status === 'hidden' ? 'selected' : '' ?>>Đang ẩn</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <button class="btn btn-primary w-100">
                            Lọc bình luận
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card auth-card">
            <div class="card-body p-4">
                <?php if (empty($comments)): ?>
                    <p class="text-muted mb-0">
                        Chưa có bình luận nào.
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Nội dung</th>
                                    <th>Bài viết</th>
                                    <th>Người bình luận</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($comments as $comment): ?>
                                    <tr>
                                        <td style="min-width: 260px;">
                                            <?= e($comment['content']) ?>
                                        </td>

                                        <td>
                                            <?= e($comment['post_title']) ?>
                                        </td>

                                        <td>
                                            <?= e($comment['user_name'] ?? 'Khách') ?>

                                            <?php if (!empty($comment['user_email'])): ?>
                                                <div class="small text-muted">
                                                    <?= e($comment['user_email']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if ($comment['status'] === 'visible'): ?>
                                                <span class="badge bg-success">Đang hiển thị</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Đang ẩn</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column gap-2">
                                                <form action="<?= url('/admin/toggle_comment.php') ?>" method="POST">
                                                    <input type="hidden" name="comment_id" value="<?= (int) $comment['id'] ?>">

                                                    <button class="btn btn-sm btn-outline-primary w-100">
                                                        <?= $comment['status'] === 'visible' ? 'Ẩn bình luận' : 'Hiện bình luận' ?>
                                                    </button>
                                                </form>

                                                <form
                                                    action="<?= url('/admin/delete_comment.php') ?>"
                                                    method="POST"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này không?')"
                                                >
                                                    <input type="hidden" name="comment_id" value="<?= (int) $comment['id'] ?>">

                                                    <button class="btn btn-sm btn-outline-danger w-100">
                                                        Xóa mềm
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php

require_once __DIR__ . '/../../app/Views/layout/footer.php';

?>