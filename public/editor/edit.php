<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../app/Helpers/functions.php';

requireEditor();

$pdo = require __DIR__ . '/../../config/db_connect.php';

$userId = (int) $_SESSION['user']['id'];
$postId = (int) ($_GET['id'] ?? 0);

if ($postId <= 0) {
    redirect('/editor/my_articles.php');
}

$stmt = $pdo->prepare("
    SELECT *
    FROM posts
    WHERE id = :id
      AND user_id = :user_id
      AND status IN ('draft', 'rejected')
    LIMIT 1
");

$stmt->execute([
    'id' => $postId,
    'user_id' => $userId,
]);

$post = $stmt->fetch();

if (!$post) {
    setFlash('flash_error', 'Không tìm thấy bài viết hoặc bài viết không thể chỉnh sửa.');
    redirect('/editor/my_articles.php');
}

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

$tagStmt = $pdo->prepare("
    SELECT tag_id
    FROM post_tags
    WHERE post_id = :post_id
");

$tagStmt->execute([
    'post_id' => $postId,
]);

$selectedTagIds = array_map('intval', array_column($tagStmt->fetchAll(), 'tag_id'));

$imageStmt = $pdo->prepare("
    SELECT id, image_path
    FROM post_images
    WHERE post_id = :post_id
    ORDER BY id DESC
");

$imageStmt->execute([
    'post_id' => $postId,
]);

$images = $imageStmt->fetchAll();

$pageTitle = 'Chỉnh sửa bài viết - SmartNews';

require_once __DIR__ . '/../../app/Views/layout/header.php';

?>

<section class="py-5">
    <div class="container">
        <div class="mb-4">
            <h1 class="fw-bold">Chỉnh sửa bài viết</h1>
            <p class="text-muted mb-0">
                Chỉ có thể sửa bài đang là bản nháp hoặc bị từ chối.
            </p>
        </div>

        <?php renderFlashMessages(); ?>

        <div class="card auth-card">
            <div class="card-body p-4">
                <form
                    action="<?= url('/editor/update_article.php') ?>"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                    <input type="hidden" name="status" id="postStatus" value="<?= e($post['status']) ?>">

                    <div class="mb-3">
                        <label class="form-label">Tiêu đề</label>
                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="<?= e($post['title']) ?>"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Chuyên mục</label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($categories as $category): ?>
                                <option
                                    value="<?= (int) $category['id'] ?>"
                                    <?= (int) $post['category_id'] === (int) $category['id'] ? 'selected' : '' ?>
                                >
                                    <?= e($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Thẻ bài viết</label>

                        <div class="border rounded-3 p-3">
                            <?php foreach ($tags as $tag): ?>
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="tag_ids[]"
                                        id="tag_<?= (int) $tag['id'] ?>"
                                        value="<?= (int) $tag['id'] ?>"
                                        <?= in_array((int) $tag['id'], $selectedTagIds, true) ? 'checked' : '' ?>
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

                    <?php if (!empty($images)): ?>
                        <div class="mb-3">
                            <label class="form-label">Ảnh hiện tại</label>

                            <div class="d-flex flex-wrap gap-3">
                                <?php foreach ($images as $image): ?>
                                    <img
                                        src="<?= url('/' . $image['image_path']) ?>"
                                        alt="Ảnh bài viết"
                                        style="width: 120px; height: 80px; object-fit: cover; border-radius: 8px;"
                                    >
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Thêm ảnh mới</label>
                        <input
                            type="file"
                            name="images[]"
                            class="form-control"
                            multiple
                            accept="image/*"
                        >
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Nội dung</label>
                        <textarea
                            name="content"
                            rows="12"
                            class="form-control"
                            required
                        ><?= e($post['content']) ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button
                            type="submit"
                            class="btn btn-outline-secondary"
                            onclick="document.getElementById('postStatus').value='draft'"
                        >
                            Lưu thay đổi
                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary"
                            onclick="document.getElementById('postStatus').value='pending'"
                        >
                            Gửi duyệt lại
                        </button>

                        <a href="<?= url('/editor/my_articles.php') ?>" class="btn btn-light">
                            Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php

require_once __DIR__ . '/../../app/Views/layout/footer.php';

?>