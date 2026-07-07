<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../app/Helpers/functions.php';

requireEditor();

$pdo = require __DIR__ . '/../../config/db_connect.php';

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

$pageTitle = 'Soạn bài viết - SmartNews';

require_once __DIR__ . '/../../app/Views/layout/header.php';

?>

<section class="py-5">
    <div class="container">
        <div class="mb-4">
            <h1 class="fw-bold">Soạn bài viết</h1>
            <p class="text-muted mb-0">
                Viết bài mới, lưu nháp hoặc gửi Admin duyệt.
            </p>
        </div>

        <?php renderFlashMessages(); ?>

        <div class="card auth-card">
            <div class="card-body p-4">
                <form
                    action="<?= url('/editor/save_article.php') ?>"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    <input type="hidden" name="status" id="postStatus" value="draft">

                    <div class="mb-3">
                        <label class="form-label">Tiêu đề</label>
                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            placeholder="Nhập tiêu đề bài viết"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Chuyên mục</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Chọn chuyên mục --</option>

                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['id'] ?>">
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

                    <div class="mb-3">
                        <label class="form-label">Ảnh bài viết</label>
                        <input
                            type="file"
                            name="images[]"
                            class="form-control"
                            multiple
                            accept="image/*"
                        >

                        <div class="form-text">
                            Có thể chọn nhiều ảnh. Chỉ nên dùng ảnh jpg, png, webp.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Nội dung</label>
                        <textarea
                            name="content"
                            rows="12"
                            class="form-control"
                            placeholder="Nhập nội dung bài viết..."
                            required
                        ></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button
                            type="submit"
                            class="btn btn-outline-secondary"
                            onclick="document.getElementById('postStatus').value='draft'"
                        >
                            Lưu nháp
                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary"
                            onclick="document.getElementById('postStatus').value='pending'"
                        >
                            Gửi duyệt
                        </button>

                        <a href="<?= url('/editor/my_articles.php') ?>" class="btn btn-light">
                            Bài viết của tôi
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