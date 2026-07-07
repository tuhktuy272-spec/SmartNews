<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

requireAuth();

if (isAdmin()) {
    redirect('/admin_dashboard.php');
}

$pdo = require __DIR__ . '/../config/db_connect.php';

$userId = (int) $_SESSION['user']['id'];

$categories = $pdo->query("
    SELECT id, name
    FROM categories
    ORDER BY name ASC
")->fetchAll();

$tags = $pdo->query("
    SELECT id, name
    FROM tags
    ORDER BY name ASC
")->fetchAll();

$stmt = $pdo->prepare("
    SELECT
        posts.id,
        posts.title,
        posts.status,
        posts.reject_reason,
        posts.created_at,
        categories.name AS category_name
    FROM posts
    INNER JOIN categories ON posts.category_id = categories.id
    WHERE posts.user_id = :user_id
      AND posts.status <> 'deleted'
    ORDER BY posts.created_at DESC
");

$stmt->execute([
    'user_id' => $userId,
]);

$myPosts = $stmt->fetchAll();

$postTagsMap = [];

if (!empty($myPosts)) {
    $postIds = array_column($myPosts, 'id');
    $placeholders = implode(',', array_fill(0, count($postIds), '?'));

    $tagStmt = $pdo->prepare("
        SELECT
            post_tags.post_id,
            tags.name
        FROM post_tags
        INNER JOIN tags ON post_tags.tag_id = tags.id
        WHERE post_tags.post_id IN ($placeholders)
    ");

    $tagStmt->execute($postIds);

    foreach ($tagStmt->fetchAll() as $row) {
        $postTagsMap[$row['post_id']][] = $row['name'];
    }
}

$pageTitle = 'Quản lý bài viết - SmartNews';

require_once __DIR__ . '/../app/Views/layout/header.php';

?>

<section class="py-5">
    <div class="container">
        <div class="mb-4">
            <h1 class="fw-bold">Quản lý bài viết của tôi</h1>

            <p class="text-muted mb-0">
                Bạn có thể tạo bài viết, lưu nháp hoặc gửi bài viết để Admin duyệt.
            </p>
        </div>

        <?php renderFlashMessages(); ?>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card auth-card h-100">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">Thêm bài viết mới</h4>

                        <form action="<?= url('/post_action.php') ?>" method="POST">
                            <input type="hidden" name="action" id="formAction" value="draft">

                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    Tiêu đề
                                </label>

                                <input
                                    type="text"
                                    name="title"
                                    id="title"
                                    class="form-control"
                                    placeholder="Nhập tiêu đề bài viết"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">
                                    Nội dung
                                </label>

                                <textarea
                                    name="content"
                                    id="content"
                                    rows="6"
                                    class="form-control"
                                    placeholder="Nhập nội dung bài viết"
                                    required
                                ></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">
                                    Chuyên mục
                                </label>

                                <select
                                    name="category_id"
                                    id="category_id"
                                    class="form-select"
                                    required
                                >
                                    <option value="">-- Chọn chuyên mục --</option>

                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= (int) $category['id'] ?>">
                                            <?= e($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    Thẻ bài viết
                                </label>

                                <div class="border rounded-3 p-3">
                                    <?php foreach ($tags as $tag): ?>
                                        <div class="form-check form-check-inline">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="tag_ids[]"
                                                id="tag_<?= (int) $tag['id'] ?>"
                                                value="<?= (int) $tag['id'] ?>"
                                            >

                                            <label
                                                class="form-check-label"
                                                for="tag_<?= (int) $tag['id'] ?>"
                                            >
                                                <?= e($tag['name']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button
                                    type="submit"
                                    class="btn btn-outline-secondary"
                                    onclick="document.getElementById('formAction').value='draft'"
                                >
                                    Lưu nháp
                                </button>

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    onclick="document.getElementById('formAction').value='pending'"
                                >
                                    Gửi duyệt
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card auth-card">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">Bài viết của tôi</h4>

                        <?php if (empty($myPosts)): ?>
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
                                            <th>Thẻ</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($myPosts as $post): ?>
                                            <?php
                                            $canEdit = in_array(
                                                $post['status'],
                                                ['draft', 'rejected'],
                                                true
                                            );
                                            ?>

                                            <tr>
                                                <td>
                                                    <strong>
                                                        <?= e($post['title']) ?>
                                                    </strong>

                                                    <div class="small text-muted">
                                                        <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?>
                                                    </div>

                                                    <?php if ($post['status'] === 'rejected' && !empty($post['reject_reason'])): ?>
                                                        <div class="small text-danger mt-1">
                                                            <strong>Lý do từ chối:</strong>
                                                            <?= e($post['reject_reason']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <?= e($post['category_name']) ?>
                                                </td>

                                                <td>
                                                    <?php if (!empty($postTagsMap[$post['id']])): ?>
                                                        <?= e(implode(', ', $postTagsMap[$post['id']])) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">--</span>
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <?= postStatusBadge($post['status']) ?>
                                                </td>

                                                <td>
                                                    <?php if ($canEdit): ?>
                                                        <div class="d-flex gap-2">
                                                            <a
                                                                href="<?= url('/edit_post.php?id=' . (int) $post['id']) ?>"
                                                                class="btn btn-sm btn-outline-primary"
                                                            >
                                                                Sửa
                                                            </a>

                                                            <button
                                                                type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="confirmDelete(<?= (int) $post['id'] ?>)"
                                                            >
                                                                Xóa
                                                            </button>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="small text-muted">
                                                            Đã khóa
                                                        </span>
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
        </div>
    </div>
</section>

<form id="deleteForm" action="<?= url('/post_action.php') ?>" method="POST" class="d-none">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="post_id" id="deletePostId">
</form>

<script>
    function confirmDelete(postId) {
        const accepted = confirm('Bạn có chắc muốn xóa bài viết này không?');

        if (!accepted) {
            return;
        }

        document.getElementById('deletePostId').value = postId;
        document.getElementById('deleteForm').submit();
    }
</script>

<?php

require_once __DIR__ . '/../app/Views/layout/footer.php';

?>