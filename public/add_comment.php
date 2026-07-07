<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

requireAuth();

$pdo = require __DIR__ . '/../config/db_connect.php';

ensureCommentSentimentColumns($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/index.php');
}

$postId = (int) ($_POST['post_id'] ?? 0);
$content = trim($_POST['content'] ?? '');

if ($postId <= 0 || $content === '') {
    setFlash('flash_error', 'Vui lòng nhập nội dung bình luận.');
    redirect('/article.php?id=' . $postId);
}

$checkStmt = $pdo->prepare("
    SELECT id
    FROM posts
    WHERE id = :id
      AND status = 'published'
    LIMIT 1
");

$checkStmt->execute([
    'id' => $postId,
]);

if (!$checkStmt->fetch()) {
    setFlash('flash_error', 'Bài viết không tồn tại hoặc chưa được xuất bản.');
    redirect('/index.php');
}

$sentiment = detectCommentSentiment($content);

$sentimentScore = match ($sentiment) {
    'positive' => 1.00,
    'negative' => -1.00,
    default => 0.00,
};

$stmt = $pdo->prepare("
    INSERT INTO post_comments
        (post_id, user_id, content, status, sentiment, sentiment_score)
    VALUES
        (:post_id, :user_id, :content, 'visible', :sentiment, :sentiment_score)
");

$stmt->execute([
    'post_id' => $postId,
    'user_id' => (int) $_SESSION['user']['id'],
    'content' => $content,
    'sentiment' => $sentiment,
    'sentiment_score' => $sentimentScore,
]);

setFlash('flash_success', 'Đã gửi bình luận thành công.');
redirect('/article.php?id=' . $postId);

function ensureCommentSentimentColumns(PDO $pdo): void
{
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

function detectCommentSentiment(string $content): string
{
    $text = mb_strtolower($content, 'UTF-8');

    $positiveWords = [
        'hay',
        'rất hay',
        'tốt',
        'thích',
        'hữu ích',
        'tuyệt',
        'tuyệt vời',
        'dễ hiểu',
        'bổ ích',
        'ấn tượng',
        'ok',
        'ổn',
        'chất lượng',
        'đáng đọc',
        'rõ ràng',
        'cần thiết',
        'ý nghĩa',
    ];

    $negativeWords = [
        'dở',
        'tệ',
        'chán',
        'khó hiểu',
        'sai',
        'không hay',
        'không thích',
        'lỗi',
        'kém',
        'thất vọng',
        'nhàm',
        'thiếu',
        'không rõ',
        'không đúng',
        'vô ích',
    ];

    foreach ($negativeWords as $word) {
        if (mb_strpos($text, $word) !== false) {
            return 'negative';
        }
    }

    foreach ($positiveWords as $word) {
        if (mb_strpos($text, $word) !== false) {
            return 'positive';
        }
    }

    return 'neutral';
}