<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

$pdo = require __DIR__ . '/../config/db_connect.php';

ensureInteractionTables($pdo);

$postId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$postId) {
    redirect('/index.php');
}

$article = findPublishedPost($pdo, $postId);

if (!$article) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>404 - Không tìm thấy bài viết</title>

        <link rel="stylesheet" href="<?= asset('/css/news.css') ?>">
    </head>

    <body>
        <main class="container section">
            <div class="article-detail">
                <h1>Không tìm thấy bài viết</h1>

                <p>
                    Bài viết bạn đang tìm không tồn tại, chưa được duyệt hoặc đã bị xóa.
                </p>

                <a class="btn btn-primary" href="<?= url('/index.php') ?>">
                    Quay lại trang chủ
                </a>
            </div>
        </main>
    </body>
    </html>
    <?php
    exit;
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'toggle_like'
) {
    requireAuth();

    togglePostLike($pdo, $postId, (int) $_SESSION['user']['id']);

    redirect('/article.php?id=' . $postId);
}

$plainSummary = mb_substr(
    trim(strip_tags($article['content'] ?? '')),
    0,
    160
);

$likeCount = countPostLikes($pdo, $postId);
$userLiked = isPostLikedByCurrentUser($pdo, $postId);

$mainImage = getMainPostImage($pdo, $postId);
$comments = getVisibleComments($pdo, $postId);
$recommendations = getRecommendedPostsForSidebar($pdo, $postId, (int) $article['category_id']);

function findPublishedPost(PDO $pdo, int $postId): ?array
{
    $stmt = $pdo->prepare("
        SELECT
            posts.id,
            posts.category_id,
            posts.title,
            posts.content,
            posts.created_at,
            users.full_name AS author_name,
            categories.name AS category_name
        FROM posts
        INNER JOIN users ON users.id = posts.user_id
        INNER JOIN categories ON categories.id = posts.category_id
        WHERE posts.id = :id
          AND posts.status = 'published'
        LIMIT 1
    ");

    $stmt->execute([
        'id' => $postId,
    ]);

    $article = $stmt->fetch();

    return $article ?: null;
}

function ensureInteractionTables(PDO $pdo): void
{
    /*
     * Bảng ảnh bài viết. Nếu em chưa chạy schema_task5_editor_cms.sql
     * thì trang chi tiết vẫn không bị lỗi khi gọi getMainPostImage().
     */
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS post_images (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            post_id BIGINT UNSIGNED NOT NULL,
            image_path VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_post_images_posts
                FOREIGN KEY (post_id)
                REFERENCES posts(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
        )
    " );

    /*
     * Bảng lưu lượt thích bài viết.
     */
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS post_likes (
            post_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,

            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

            PRIMARY KEY (post_id, user_id),

            CONSTRAINT fk_post_likes_posts
                FOREIGN KEY (post_id)
                REFERENCES posts(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,

            CONSTRAINT fk_post_likes_users
                FOREIGN KEY (user_id)
                REFERENCES users(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
        )
    ");

    /*
     * Nếu bảng bình luận chưa có thì tạo.
     * Nếu đã có rồi thì CREATE IF NOT EXISTS sẽ không ảnh hưởng.
     */
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS post_comments (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

            post_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NULL,

            content TEXT NOT NULL,

            status ENUM('visible', 'hidden', 'deleted')
                NOT NULL
                DEFAULT 'visible',

            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP,

            CONSTRAINT fk_post_comments_posts
                FOREIGN KEY (post_id)
                REFERENCES posts(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,

            CONSTRAINT fk_post_comments_users
                FOREIGN KEY (user_id)
                REFERENCES users(id)
                ON DELETE SET NULL
                ON UPDATE CASCADE
        )
    ");

    /*
     * Bổ sung cột sắc thái nếu chưa có.
     */
    if (!columnExists($pdo, 'post_comments', 'sentiment')) {
        $pdo->exec("
            ALTER TABLE post_comments
            ADD COLUMN sentiment ENUM('positive', 'neutral', 'negative')
            NOT NULL DEFAULT 'neutral'
            AFTER status
        ");
    }

    if (!columnExists($pdo, 'post_comments', 'sentiment_score')) {
        $pdo->exec("
            ALTER TABLE post_comments
            ADD COLUMN sentiment_score DECIMAL(4,2)
            NOT NULL DEFAULT 0.00
            AFTER sentiment
        ");
    }
}

function columnExists(PDO $pdo, string $tableName, string $columnName): bool
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = :table_name
          AND COLUMN_NAME = :column_name
    ");

    $stmt->execute([
        'table_name' => $tableName,
        'column_name' => $columnName,
    ]);

    return (int) $stmt->fetchColumn() > 0;
}

function togglePostLike(PDO $pdo, int $postId, int $userId): void
{
    $checkStmt = $pdo->prepare("
        SELECT 1
        FROM post_likes
        WHERE post_id = :post_id
          AND user_id = :user_id
        LIMIT 1
    ");

    $checkStmt->execute([
        'post_id' => $postId,
        'user_id' => $userId,
    ]);

    if ($checkStmt->fetchColumn()) {
        $deleteStmt = $pdo->prepare("
            DELETE FROM post_likes
            WHERE post_id = :post_id
              AND user_id = :user_id
        ");

        $deleteStmt->execute([
            'post_id' => $postId,
            'user_id' => $userId,
        ]);

        setFlash('flash_success', 'Đã bỏ thích bài viết.');

        return;
    }

    $insertStmt = $pdo->prepare("
        INSERT INTO post_likes (post_id, user_id)
        VALUES (:post_id, :user_id)
    ");

    $insertStmt->execute([
        'post_id' => $postId,
        'user_id' => $userId,
    ]);

    setFlash('flash_success', 'Đã thích bài viết.');
}

function countPostLikes(PDO $pdo, int $postId): int
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM post_likes
        WHERE post_id = :post_id
    ");

    $stmt->execute([
        'post_id' => $postId,
    ]);

    return (int) $stmt->fetchColumn();
}

function isPostLikedByCurrentUser(PDO $pdo, int $postId): bool
{
    if (!isLoggedIn()) {
        return false;
    }

    $stmt = $pdo->prepare("
        SELECT 1
        FROM post_likes
        WHERE post_id = :post_id
          AND user_id = :user_id
        LIMIT 1
    ");

    $stmt->execute([
        'post_id' => $postId,
        'user_id' => (int) $_SESSION['user']['id'],
    ]);

    return (bool) $stmt->fetchColumn();
}

function getMainPostImage(PDO $pdo, int $postId): ?array
{
    $stmt = $pdo->prepare("
        SELECT image_path
        FROM post_images
        WHERE post_id = :post_id
        ORDER BY id ASC
        LIMIT 1
    ");

    $stmt->execute([
        'post_id' => $postId,
    ]);

    $image = $stmt->fetch();

    return $image ?: null;
}

function getVisibleComments(PDO $pdo, int $postId): array
{
    $stmt = $pdo->prepare("
        SELECT
            post_comments.id,
            post_comments.content,
            post_comments.created_at,
            post_comments.sentiment,
            post_comments.sentiment_score,
            COALESCE(users.full_name, 'Khách') AS user_name
        FROM post_comments
        LEFT JOIN users ON users.id = post_comments.user_id
        WHERE post_comments.post_id = :post_id
          AND post_comments.status = 'visible'
        ORDER BY post_comments.created_at DESC
    ");

    $stmt->execute([
        'post_id' => $postId,
    ]);

    return $stmt->fetchAll();
}

function getRecommendedPostsForSidebar(PDO $pdo, int $currentPostId, int $categoryId): array
{
    /*
     * Ưu tiên bài cùng chuyên mục để đúng yêu cầu "bài liên quan".
     * Nếu cùng chuyên mục chưa đủ 4 bài, lấy thêm bài mới ở chuyên mục khác.
     */
    $relatedStmt = $pdo->prepare("
        SELECT
            posts.id,
            posts.title,
            categories.name AS category_name
        FROM posts
        INNER JOIN categories ON categories.id = posts.category_id
        WHERE posts.status = 'published'
          AND posts.id <> :id
          AND posts.category_id = :category_id
        ORDER BY posts.created_at DESC
        LIMIT 4
    ");

    $relatedStmt->execute([
        'id' => $currentPostId,
        'category_id' => $categoryId,
    ]);

    $related = $relatedStmt->fetchAll();

    if (count($related) >= 4) {
        return $related;
    }

    $usedIds = array_map(fn (array $item): int => (int) $item['id'], $related);
    $usedIds[] = $currentPostId;

    $placeholders = [];
    $params = [];

    foreach ($usedIds as $index => $id) {
        $key = ':id_' . $index;
        $placeholders[] = $key;
        $params[$key] = $id;
    }

    $limit = 4 - count($related);

    $sql = "
        SELECT
            posts.id,
            posts.title,
            categories.name AS category_name
        FROM posts
        INNER JOIN categories ON categories.id = posts.category_id
        WHERE posts.status = 'published'
          AND posts.id NOT IN (" . implode(', ', $placeholders) . ")
        ORDER BY posts.created_at DESC
        LIMIT :limit
    ";

    $moreStmt = $pdo->prepare($sql);

    foreach ($params as $key => $id) {
        $moreStmt->bindValue($key, $id, PDO::PARAM_INT);
    }

    $moreStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $moreStmt->execute();

    return array_merge($related, $moreStmt->fetchAll());
}

function renderArticleContent(?string $content): string
{
    $content = trim($content ?? '');

    if ($content === '') {
        return '<p>Nội dung bài viết đang được cập nhật.</p>';
    }

    $hasHtml = $content !== strip_tags($content);

    if ($hasHtml) {
        return strip_tags(
            $content,
            '<p><br><strong><b><em><i><u><h2><h3><ul><ol><li><blockquote>'
        );
    }

    return nl2br(e($content));
}

function sentimentLabel(?string $sentiment): string
{
    return match ($sentiment) {
        'positive' => 'Tích cực',
        'negative' => 'Tiêu cực',
        default => 'Trung lập',
    };
}

function sentimentColor(?string $sentiment): string
{
    return match ($sentiment) {
        'positive' => '#16a34a',
        'negative' => '#dc2626',
        default => '#6b7280',
    };
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($article['title']) ?> - SmartNews</title>

    <meta name="description" content="<?= e($plainSummary) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="<?= asset('/css/news.css') ?>">
</head>

<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="logo" href="<?= url('/index.php') ?>">
                <span class="logo-mark"></span>
                SmartNews
            </a>

            <button
                class="nav-toggle"
                type="button"
                aria-label="Mở menu"
                aria-expanded="false"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="nav-links" aria-label="Điều hướng chính">
                <a href="<?= url('/index.php#new-articles') ?>">Mới nhất</a>
                <a href="<?= url('/index.php#featured') ?>">Nổi bật</a>
                <a href="<?= url('/index.php#categories') ?>">Chuyên mục</a>

                <?php if (isLoggedIn()): ?>
                    <a href="<?= url('/dashboard.php') ?>">Dashboard</a>
                    <a href="<?= url('/logout.php') ?>">Đăng xuất</a>
                <?php else: ?>
                    <a href="<?= url('/login.php') ?>">Đăng nhập</a>
                    <a href="<?= url('/register.php') ?>">Đăng ký</a>
                <?php endif; ?>
            </nav>

            <button
                class="theme-toggle"
                type="button"
                aria-label="Chuyển đổi chế độ tối"
            >
                <span class="theme-toggle-icon">☀️</span>
            </button>
        </div>
    </header>

    <main class="container article-page">
        <article class="article-detail reveal">
            <header class="article-header">
                <p class="article-category">
                    Chuyên mục: <?= e($article['category_name']) ?>
                </p>

                <h1><?= e($article['title']) ?></h1>

                <div class="article-meta">
                    <span>Tác giả: <?= e($article['author_name']) ?></span>

                    <span>
                        Ngày đăng:
                        <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                    </span>

                    <span>
                        <?= $likeCount ?> lượt thích
                    </span>
                </div>
            </header>

            <div style="margin: 18px 0 28px;">
                <?php renderFlashMessages(); ?>

                <?php if (isLoggedIn()): ?>
                    <form method="POST" style="display: inline-block;">
                        <input type="hidden" name="action" value="toggle_like">

                        <button class="btn btn-primary" type="submit">
                            <?= $userLiked ? 'Đã thích' : 'Thích bài viết' ?>
                            · <?= $likeCount ?> lượt thích
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-muted">
                        <?= $likeCount ?> lượt thích ·
                        <a href="<?= url('/login.php') ?>">Đăng nhập</a>
                        để thích bài viết.
                    </p>
                <?php endif; ?>
            </div>

            <?php if (!empty($mainImage['image_path'])): ?>
                <img
                    class="article-hero-image"
                    src="<?= url('/' . $mainImage['image_path']) ?>"
                    alt="<?= e($article['title']) ?>"
                >
            <?php endif; ?>

            <div class="article-content" id="article-content">
                <?= renderArticleContent($article['content']) ?>
            </div>

            <section class="sidebar-box" style="margin-top: 32px;">
                <h2>Bình luận</h2>

                <?php if (isLoggedIn()): ?>
                    <form
                        action="<?= url('/add_comment.php') ?>"
                        method="POST"
                        style="margin-bottom: 24px;"
                    >
                        <input
                            type="hidden"
                            name="post_id"
                            value="<?= (int) $article['id'] ?>"
                        >

                        <textarea
                            name="content"
                            rows="4"
                            placeholder="Nhập bình luận của bạn..."
                            required
                            style="width: 100%; padding: 14px; border-radius: 14px; border: 1px solid #d1d5db; resize: vertical;"
                        ></textarea>

                        <button
                            class="btn btn-primary"
                            type="submit"
                            style="margin-top: 12px;"
                        >
                            Gửi bình luận
                        </button>
                    </form>
                <?php else: ?>
                    <p>
                        Bạn cần
                        <a href="<?= url('/login.php') ?>">đăng nhập</a>
                        để bình luận.
                    </p>
                <?php endif; ?>

                <?php if (empty($comments)): ?>
                    <p>Chưa có bình luận nào.</p>
                <?php else: ?>
                    <div class="comment-list">
                        <?php foreach ($comments as $comment): ?>
                            <div style="padding: 16px 0; border-bottom: 1px solid #e5e7eb;">
                                <strong><?= e($comment['user_name']) ?></strong>

                                <span
                                    style="
                                        margin-left: 8px;
                                        font-size: 13px;
                                        font-weight: 700;
                                        color: <?= sentimentColor($comment['sentiment'] ?? 'neutral') ?>;
                                    "
                                >
                                    <?= sentimentLabel($comment['sentiment'] ?? 'neutral') ?>
                                </span>

                                <div style="font-size: 14px; color: #6b7280; margin: 4px 0 8px;">
                                    <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                                </div>

                                <p style="margin: 0;">
                                    <?= nl2br(e($comment['content'])) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </article>

        <aside class="sidebar reveal">
            <div class="sidebar-box">
                <h2>Bài viết liên quan</h2>

                <?php if (empty($recommendations)): ?>
                    <p>Chưa có bài viết liên quan.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($recommendations as $item): ?>
                            <li>
                                <a href="<?= url('/article.php?id=' . (int) $item['id']) ?>">
                                    <?= e($item['title']) ?>
                                </a>

                                <?php if (!empty($item['category_name'])): ?>
                                    <small style="display:block;color:#6b7280;margin-top:4px;">
                                        <?= e($item['category_name']) ?>
                                    </small>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="sidebar-box accent-box">
                <h2>Quay lại trang chủ</h2>

                <p>
                    Tiếp tục khám phá thêm các bài viết mới nhất trên SmartNews.
                </p>

                <a class="btn btn-primary btn-full" href="<?= url('/index.php') ?>">
                    Về trang chủ
                </a>
            </div>
        </aside>
    </main>

    <button class="back-to-top" type="button" aria-label="Quay về đầu trang">
        ↑
    </button>

    <script>
        window.SMARTNEWS_BASE_URL = "<?= BASE_URL ?>";
    </script>

    <script src="<?= asset('/js/news.js') ?>"></script>
</body>
</html>