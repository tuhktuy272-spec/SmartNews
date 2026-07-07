<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../app/Helpers/functions.php';

requireAdmin();

$pdo = require __DIR__ . '/../../config/db_connect.php';

$status = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

$where = ["posts.status <> 'deleted'"];
$params = [];

if (in_array($status, ['draft', 'pending', 'published', 'rejected'], true)) {
    $where[] = "posts.status = :status";
    $params['status'] = $status;
}

if ($search !== '') {
    $where[] = "(posts.title LIKE :search_title OR users.full_name LIKE :search_author)";
    $params['search_title'] = '%' . $search . '%';
    $params['search_author'] = '%' . $search . '%';
}

$whereSql = implode(' AND ', $where);

$stmt = $pdo->prepare("
    SELECT
        posts.id,
        posts.title,
        posts.content,
        posts.status,
        posts.reject_reason,
        posts.created_at,
        users.full_name AS author_name,
        users.email AS author_email,
        categories.name AS category_name
    FROM posts
    INNER JOIN users ON users.id = posts.user_id
    INNER JOIN categories ON categories.id = posts.category_id
    WHERE $whereSql
    ORDER BY posts.created_at DESC
");

foreach ($params as $key => $value) {
    $stmt->bindValue(':' . $key, $value);
}

$stmt->execute();

$posts = $stmt->fetchAll();

$pageTitle = 'Quản lý bài viết - SmartNews';

require_once __DIR__ . '/../../app/Views/layout/header.php';

?>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-1">Quản lý bài viết</h1>

                <p class="text-muted mb-0">
                    Duyệt bài, từ chối bài hoặc xóa mềm bài viết.
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
                    <div class="col-md-5">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Tìm theo tiêu đề hoặc tác giả"
                            value="<?= e($search) ?>"
                        >
                    </div>

                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Bản nháp</option>
                            <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                            <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Đã xuất bản</option>
                            <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Bị từ chối</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">
                            Lọc bài viết
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card auth-card">
            <div class="card-body p-4">
                <?php if (empty($posts)): ?>
                    <p class="text-muted mb-0">
                        Không có bài viết phù hợp.
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Bài viết</th>
                                    <th>Tác giả</th>
                                    <th>Chuyên mục</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($posts as $post): ?>
                                    <tr>
                                        <td style="min-width: 260px;">
                                            <strong><?= e($post['title']) ?></strong>

                                            <div class="small text-muted mt-1">
                                                <?= e(mb_substr(strip_tags($post['content']), 0, 120)) ?>...
                                            </div>

                                            <?php if ($post['status'] === 'rejected' && !empty($post['reject_reason'])): ?>
                                                <div class="small text-danger mt-1">
                                                    <strong>Lý do từ chối:</strong>
                                                    <?= e($post['reject_reason']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= e($post['author_name']) ?>

                                            <div class="small text-muted">
                                                <?= e($post['author_email']) ?>
                                            </div>
                                        </td>

                                        <td><?= e($post['category_name']) ?></td>

                                        <td><?= postStatusBadge($post['status']) ?></td>

                                        <td>
                                            <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?>
                                        </td>

                                        <td style="min-width: 220px;">
                                            <div class="d-flex flex-column gap-2">
                                                <?php if ($post['status'] !== 'published'): ?>
                                                    <form action="<?= url('/admin/approve_post.php') ?>" method="POST">
                                                        <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                                                        <button class="btn btn-sm btn-success w-100">
                                                            Duyệt bài
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <?php if ($post['status'] !== 'rejected'): ?>
                                                    <form action="<?= url('/admin/reject_post.php') ?>" method="POST">
                                                        <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                                                        <input
                                                            type="text"
                                                            name="reject_reason"
                                                            class="form-control form-control-sm mb-1"
                                                            placeholder="Lý do từ chối"
                                                            required
                                                        >
                                                        <button class="btn btn-sm btn-warning w-100">
                                                            Từ chối
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <form
                                                    action="<?= url('/admin/delete_post.php') ?>"
                                                    method="POST"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa mềm bài viết này không?')"
                                                >
                                                    <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
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