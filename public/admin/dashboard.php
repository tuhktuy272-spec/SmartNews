<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../app/Helpers/functions.php';

requireAdmin();

$pdo = require __DIR__ . '/../../config/db_connect.php';

ensureAdminInteractionTables($pdo);
updateExistingCommentSentiments($pdo);

function countValue(PDO $pdo, string $sql, array $params = []): int
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}

function ensureAdminInteractionTables(PDO $pdo): void
{
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

function updateExistingCommentSentiments(PDO $pdo): void
{
    $pdo->exec("
        UPDATE post_comments
        SET
            sentiment = CASE
                WHEN LOWER(content) LIKE '%hay%'
                  OR LOWER(content) LIKE '%rất hay%'
                  OR LOWER(content) LIKE '%tốt%'
                  OR LOWER(content) LIKE '%thích%'
                  OR LOWER(content) LIKE '%hữu ích%'
                  OR LOWER(content) LIKE '%tuyệt%'
                  OR LOWER(content) LIKE '%dễ hiểu%'
                  OR LOWER(content) LIKE '%bổ ích%'
                  OR LOWER(content) LIKE '%ấn tượng%'
                  OR LOWER(content) LIKE '%đáng đọc%'
                THEN 'positive'

                WHEN LOWER(content) LIKE '%dở%'
                  OR LOWER(content) LIKE '%tệ%'
                  OR LOWER(content) LIKE '%chán%'
                  OR LOWER(content) LIKE '%khó hiểu%'
                  OR LOWER(content) LIKE '%sai%'
                  OR LOWER(content) LIKE '%không hay%'
                  OR LOWER(content) LIKE '%không thích%'
                  OR LOWER(content) LIKE '%lỗi%'
                  OR LOWER(content) LIKE '%kém%'
                  OR LOWER(content) LIKE '%thất vọng%'
                THEN 'negative'

                ELSE 'neutral'
            END,

            sentiment_score = CASE
                WHEN LOWER(content) LIKE '%hay%'
                  OR LOWER(content) LIKE '%rất hay%'
                  OR LOWER(content) LIKE '%tốt%'
                  OR LOWER(content) LIKE '%thích%'
                  OR LOWER(content) LIKE '%hữu ích%'
                  OR LOWER(content) LIKE '%tuyệt%'
                  OR LOWER(content) LIKE '%dễ hiểu%'
                  OR LOWER(content) LIKE '%bổ ích%'
                  OR LOWER(content) LIKE '%ấn tượng%'
                  OR LOWER(content) LIKE '%đáng đọc%'
                THEN 1.00

                WHEN LOWER(content) LIKE '%dở%'
                  OR LOWER(content) LIKE '%tệ%'
                  OR LOWER(content) LIKE '%chán%'
                  OR LOWER(content) LIKE '%khó hiểu%'
                  OR LOWER(content) LIKE '%sai%'
                  OR LOWER(content) LIKE '%không hay%'
                  OR LOWER(content) LIKE '%không thích%'
                  OR LOWER(content) LIKE '%lỗi%'
                  OR LOWER(content) LIKE '%kém%'
                  OR LOWER(content) LIKE '%thất vọng%'
                THEN -1.00

                ELSE 0.00
            END
        WHERE status <> 'deleted'
    ");
}

$totalUsers = countValue($pdo, "
    SELECT COUNT(*)
    FROM users
");

$totalPosts = countValue($pdo, "
    SELECT COUNT(*)
    FROM posts
    WHERE status <> 'deleted'
");

$pendingPosts = countValue($pdo, "
    SELECT COUNT(*)
    FROM posts
    WHERE status = 'pending'
");

$publishedPosts = countValue($pdo, "
    SELECT COUNT(*)
    FROM posts
    WHERE status = 'published'
");

$rejectedPosts = countValue($pdo, "
    SELECT COUNT(*)
    FROM posts
    WHERE status = 'rejected'
");

$totalComments = countValue($pdo, "
    SELECT COUNT(*)
    FROM post_comments
    WHERE status <> 'deleted'
");

$totalLikes = countValue($pdo, "
    SELECT COUNT(*)
    FROM post_likes
");

$positiveComments = countValue($pdo, "
    SELECT COUNT(*)
    FROM post_comments
    WHERE status <> 'deleted'
      AND sentiment = 'positive'
");

$neutralComments = countValue($pdo, "
    SELECT COUNT(*)
    FROM post_comments
    WHERE status <> 'deleted'
      AND sentiment = 'neutral'
");

$negativeComments = countValue($pdo, "
    SELECT COUNT(*)
    FROM post_comments
    WHERE status <> 'deleted'
      AND sentiment = 'negative'
");

$recentStmt = $pdo->query("
    SELECT
        posts.id,
        posts.title,
        posts.status,
        posts.created_at,
        users.full_name AS author_name,
        categories.name AS category_name
    FROM posts
    INNER JOIN users ON users.id = posts.user_id
    INNER JOIN categories ON categories.id = posts.category_id
    WHERE posts.status <> 'deleted'
    ORDER BY posts.created_at DESC
    LIMIT 8
");

$recentPosts = $recentStmt->fetchAll();

$interactionStmt = $pdo->query("
    SELECT
        posts.id,
        posts.title,
        COALESCE(likes.likes_count, 0) AS likes_count,
        COALESCE(comments.comments_count, 0) AS comments_count,
        COALESCE(comments.positive_count, 0) AS positive_count,
        COALESCE(comments.neutral_count, 0) AS neutral_count,
        COALESCE(comments.negative_count, 0) AS negative_count
    FROM posts
    LEFT JOIN (
        SELECT
            post_id,
            COUNT(*) AS likes_count
        FROM post_likes
        GROUP BY post_id
    ) likes ON likes.post_id = posts.id
    LEFT JOIN (
        SELECT
            post_id,
            COUNT(*) AS comments_count,
            COALESCE(SUM(sentiment = 'positive'), 0) AS positive_count,
            COALESCE(SUM(sentiment = 'neutral'), 0) AS neutral_count,
            COALESCE(SUM(sentiment = 'negative'), 0) AS negative_count
        FROM post_comments
        WHERE status <> 'deleted'
        GROUP BY post_id
    ) comments ON comments.post_id = posts.id
    WHERE posts.status <> 'deleted'
    ORDER BY
        COALESCE(likes.likes_count, 0) DESC,
        COALESCE(comments.comments_count, 0) DESC,
        posts.created_at DESC
    LIMIT 10
");

$interactionPosts = $interactionStmt->fetchAll();

$pageTitle = 'Admin Dashboard - SmartNews';

require_once __DIR__ . '/../../app/Views/layout/header.php';

?>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-1">
                    Admin Dashboard
                </h1>

                <p class="text-muted mb-0">
                    Quản lý tổng quan hệ thống SmartNews.
                </p>
            </div>

            <a href="<?= url('/dashboard.php') ?>" class="btn btn-outline-secondary">
                Về Dashboard
            </a>
        </div>

        <?php renderFlashMessages(); ?>

        <div class="row g-4 mb-4">
            <div class="col-md-4 col-lg-2">
                <div class="card auth-card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Người dùng</div>
                        <div class="fs-3 fw-bold"><?= $totalUsers ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-2">
                <div class="card auth-card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Bài viết</div>
                        <div class="fs-3 fw-bold"><?= $totalPosts ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-2">
                <div class="card auth-card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Chờ duyệt</div>
                        <div class="fs-3 fw-bold text-warning"><?= $pendingPosts ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-2">
                <div class="card auth-card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Đã xuất bản</div>
                        <div class="fs-3 fw-bold text-success"><?= $publishedPosts ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-2">
                <div class="card auth-card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Bị từ chối</div>
                        <div class="fs-3 fw-bold text-danger"><?= $rejectedPosts ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-2">
                <div class="card auth-card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Bình luận</div>
                        <div class="fs-3 fw-bold"><?= $totalComments ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card auth-card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Lượt thích</div>
                        <div class="fs-3 fw-bold text-primary"><?= $totalLikes ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card auth-card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Bình luận tích cực</div>
                        <div class="fs-3 fw-bold text-success"><?= $positiveComments ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card auth-card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Bình luận trung lập</div>
                        <div class="fs-3 fw-bold text-secondary"><?= $neutralComments ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card auth-card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Bình luận tiêu cực</div>
                        <div class="fs-3 fw-bold text-danger"><?= $negativeComments ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <a href="<?= url('/admin/posts.php') ?>" class="text-decoration-none text-dark">
                    <div class="p-4 bg-light rounded-4 h-100">
                        <h5 class="fw-bold">Quản lý bài viết</h5>

                        <p class="text-muted mb-0">
                            Duyệt, từ chối hoặc xóa mềm bài viết.
                        </p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?= url('/admin/comments.php') ?>" class="text-decoration-none text-dark">
                    <div class="p-4 bg-light rounded-4 h-100">
                        <h5 class="fw-bold">Quản lý bình luận</h5>

                        <p class="text-muted mb-0">
                            Ẩn, hiện hoặc xóa mềm bình luận.
                        </p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?= url('/admin/users.php') ?>" class="text-decoration-none text-dark">
                    <div class="p-4 bg-light rounded-4 h-100">
                        <h5 class="fw-bold">Quản lý người dùng</h5>

                        <p class="text-muted mb-0">
                            Xem user, khóa tài khoản và phân quyền.
                        </p>
                    </div>
                </a>
            </div>
        </div>

        <div class="card auth-card mb-4">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-3">
                    Mức độ cảm xúc và tương tác của người đọc
                </h4>

                <p class="text-muted">
                    Bảng này thống kê lượt thích và sắc thái bình luận theo từng bài viết.
                    Sắc thái được phân loại đơn giản dựa trên từ khóa trong nội dung bình luận.
                </p>

                <?php if (empty($interactionPosts)): ?>
                    <p class="text-muted mb-0">
                        Chưa có dữ liệu lượt thích hoặc bình luận.
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Bài viết</th>
                                    <th>Lượt thích</th>
                                    <th>Bình luận</th>
                                    <th>Tích cực</th>
                                    <th>Trung lập</th>
                                    <th>Tiêu cực</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($interactionPosts as $item): ?>
                                    <tr>
                                        <td>
                                            <a
                                                href="<?= url('/article.php?id=' . (int) $item['id']) ?>"
                                                class="text-decoration-none fw-semibold"
                                            >
                                                <?= e($item['title']) ?>
                                            </a>
                                        </td>

                                        <td class="fw-bold text-primary">
                                            <?= (int) $item['likes_count'] ?>
                                        </td>

                                        <td>
                                            <?= (int) $item['comments_count'] ?>
                                        </td>

                                        <td class="text-success fw-bold">
                                            <?= (int) $item['positive_count'] ?>
                                        </td>

                                        <td class="text-secondary fw-bold">
                                            <?= (int) $item['neutral_count'] ?>
                                        </td>

                                        <td class="text-danger fw-bold">
                                            <?= (int) $item['negative_count'] ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card auth-card">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-3">Bài viết mới nhất</h4>

                <?php if (empty($recentPosts)): ?>
                    <p class="text-muted mb-0">
                        Chưa có bài viết nào.
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Tiêu đề</th>
                                    <th>Tác giả</th>
                                    <th>Chuyên mục</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($recentPosts as $post): ?>
                                    <tr>
                                        <td>
                                            <a
                                                href="<?= url('/article.php?id=' . (int) $post['id']) ?>"
                                                class="text-decoration-none fw-semibold"
                                            >
                                                <?= e($post['title']) ?>
                                            </a>
                                        </td>

                                        <td><?= e($post['author_name']) ?></td>

                                        <td><?= e($post['category_name']) ?></td>

                                        <td><?= postStatusBadge($post['status']) ?></td>

                                        <td>
                                            <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?>
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