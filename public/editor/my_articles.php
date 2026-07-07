<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../app/Helpers/functions.php';

requireEditor();

$pdo = require __DIR__ . '/../../config/db_connect.php';

$userId = (int) $_SESSION['user']['id'];

$stmt = $pdo->prepare("
    SELECT
        posts.id,
        posts.title,
        posts.status,
        posts.reject_reason,
        posts.created_at,
        categories.name AS category_name
    FROM posts
    INNER JOIN categories ON categories.id = posts.category_id
    WHERE posts.user_id = :user_id
      AND posts.status <> 'deleted'
    ORDER BY posts.created_at DESC
");

$stmt->execute([
    'user_id' => $userId,
]);

$posts = $stmt->fetchAll();

$pageTitle = 'Bài viết của tôi - SmartNews';

require_once __DIR__ . '/../../app/Views/layout/header.php';

?>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-1">Bài viết của tôi</h1>
                <p class="text-muted mb-0">
                    Theo dõi trạng thái bài viết: nháp, chờ duyệt, đã xuất bản, bị từ chối.
                </p>
            </div>

            <a href="<?= url('/editor/compose.php') ?>" class="btn btn-primary">
                + Soạn bài mới
            </a>
        </div>

        <?php renderFlashMessages(); ?>

        <div class="card auth-card">
            <div class="card-body p-4">
                <?php if (empty($posts)): ?>
                    <p class="text-muted mb-0">
                        Bạn chưa có bài viết nào.
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Tiêu đề</th>
                                    <th>Chuyên mục</th>
                                    <th>Ngày tạo</th>
                                    <th>Trạng thái</th>
                                    <th>Lý do từ chối</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($posts as $post): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($post['title']) ?></strong>
                                        </td>

                                        <td><?= e($post['category_name']) ?></td>

                                        <td>
                                            <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?>
                                        </td>

                                        <td>
                                            <?= postStatusBadge($post['status']) ?>
                                        </td>

                                        <td>
                                            <?php if ($post['status'] === 'rejected'): ?>
                                                <span class="text-danger">
                                                    <?= e($post['reject_reason'] ?: 'Không có lý do') ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">--</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if (in_array($post['status'], ['draft', 'rejected'], true)): ?>
                                                <a
                                                    href="<?= url('/editor/edit.php?id=' . (int) $post['id']) ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    Chỉnh sửa
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">Đã khóa sửa</span>
                                            <?php endif; ?>
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