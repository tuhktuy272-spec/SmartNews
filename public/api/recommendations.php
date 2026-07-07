<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/Helpers/api_functions.php';

$pdo = require __DIR__ . '/../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_json_response([
        'success' => false,
        'message' => 'Chỉ hỗ trợ phương thức GET.',
    ], 405);
}

$limit = filter_var($_GET['limit'] ?? 5, FILTER_VALIDATE_INT) ?: 5;
$limit = max(1, min($limit, 20));

$user = api_current_user();
$userId = $user['id'] ?? null;
$sessionId = $_GET['session_id'] ?? null;

try {
    $params = [];
    $whereReader = '';

    if ($userId) {
        $whereReader = 'reading_logs.user_id = ?';
        $params[] = $userId;
    } elseif ($sessionId) {
        $whereReader = 'reading_logs.session_id = ?';
        $params[] = $sessionId;
    }

    $readArticleIds = [];
    $categoryAffinity = [];

    if ($whereReader !== '') {
        $sql = "
            SELECT
                reading_logs.article_id,
                reading_logs.time_spent,
                reading_logs.scroll_percentage,
                articles.category_id
            FROM reading_logs
            INNER JOIN articles ON articles.id = reading_logs.article_id
            WHERE $whereReader
              AND reading_logs.created_at >= (NOW() - INTERVAL 90 DAY)
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        foreach ($stmt->fetchAll() as $log) {
            $readArticleIds[$log['article_id']] = true;

            $timeScore = min((int) $log['time_spent'], 300) / 300;
            $scrollScore = ((float) $log['scroll_percentage']) / 100;

            $weight = $timeScore * 0.6 + $scrollScore * 0.4;

            $categoryId = (int) $log['category_id'];

            $categoryAffinity[$categoryId] = ($categoryAffinity[$categoryId] ?? 0) + $weight;
        }
    }

    /**
     * Cold-start:
     * Nếu chưa có dữ liệu đọc, trả về bài viết phổ biến.
     */
    if (empty($categoryAffinity)) {
        $stmt = $pdo->prepare("
            SELECT
                articles.id,
                articles.title,
                articles.slug,
                articles.summary,
                articles.thumbnail,
                articles.views,
                articles.created_at,
                categories.name AS category_name
            FROM articles
            INNER JOIN categories ON categories.id = articles.category_id
            WHERE articles.status = 'published'
            ORDER BY articles.views DESC, articles.created_at DESC
            LIMIT ?
        ");

        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        api_json_response([
            'success' => true,
            'mode' => 'popular_fallback',
            'message' => 'Chưa đủ dữ liệu hành vi đọc, trả về bài viết phổ biến.',
            'data' => $stmt->fetchAll(),
        ]);
    }

    $maxAffinity = max($categoryAffinity);

    foreach ($categoryAffinity as $categoryId => $score) {
        $categoryAffinity[$categoryId] = $maxAffinity > 0 ? $score / $maxAffinity : 0;
    }

    $categoryIds = array_keys($categoryAffinity);
    $categoryPlaceholders = implode(',', array_fill(0, count($categoryIds), '?'));

    $excludeIds = array_keys($readArticleIds);
    $excludeClause = '';

    if (!empty($excludeIds)) {
        $excludePlaceholders = implode(',', array_fill(0, count($excludeIds), '?'));
        $excludeClause = "AND articles.id NOT IN ($excludePlaceholders)";
    }

    $sql = "
        SELECT
            articles.id,
            articles.title,
            articles.slug,
            articles.summary,
            articles.thumbnail,
            articles.views,
            articles.category_id,
            articles.created_at,
            categories.name AS category_name
        FROM articles
        INNER JOIN categories ON categories.id = articles.category_id
        WHERE articles.status = 'published'
          AND articles.category_id IN ($categoryPlaceholders)
          $excludeClause
        ORDER BY articles.created_at DESC
        LIMIT 200
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge($categoryIds, $excludeIds));

    $candidates = $stmt->fetchAll();

    if (empty($candidates)) {
        $stmt = $pdo->prepare("
            SELECT
                articles.id,
                articles.title,
                articles.slug,
                articles.summary,
                articles.thumbnail,
                articles.views,
                articles.category_id,
                articles.created_at,
                categories.name AS category_name
            FROM articles
            INNER JOIN categories ON categories.id = articles.category_id
            WHERE articles.status = 'published'
            ORDER BY articles.views DESC, articles.created_at DESC
            LIMIT ?
        ");

        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        api_json_response([
            'success' => true,
            'mode' => 'popular_fallback',
            'message' => 'Không còn bài phù hợp, trả về bài viết phổ biến.',
            'data' => $stmt->fetchAll(),
        ]);
    }

    $maxViews = max(array_column($candidates, 'views')) ?: 1;
    $now = time();

    foreach ($candidates as &$candidate) {
        $affinity = $categoryAffinity[$candidate['category_id']] ?? 0;
        $viewScore = $candidate['views'] / $maxViews;

        $ageDays = max(0, ($now - strtotime($candidate['created_at'])) / 86400);
        $recencyScore = max(0, 1 - ($ageDays / 30));

        $candidate['final_score'] = round(
            $affinity * 0.7 + $viewScore * 0.2 + $recencyScore * 0.1,
            4
        );
    }

    unset($candidate);

    usort($candidates, function (array $a, array $b) {
        return $b['final_score'] <=> $a['final_score'];
    });

    $result = array_slice($candidates, 0, $limit);

    api_json_response([
        'success' => true,
        'mode' => 'personalized',
        'data' => $result,
    ]);
} catch (PDOException $e) {
    api_json_response([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
    ], 500);
}