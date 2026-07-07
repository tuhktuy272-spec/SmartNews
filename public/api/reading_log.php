<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/Helpers/api_functions.php';

$pdo = require __DIR__ . '/../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_json_response([
        'success' => false,
        'message' => 'Chỉ hỗ trợ phương thức POST.',
    ], 405);
}

$body = api_get_json_body();

$articleId = filter_var($body['article_id'] ?? null, FILTER_VALIDATE_INT);
$timeSpent = filter_var($body['time_spent'] ?? 0, FILTER_VALIDATE_INT);
$scrollPercentage = filter_var($body['scroll_percentage'] ?? 0, FILTER_VALIDATE_FLOAT);
$device = isset($body['device']) ? substr((string) $body['device'], 0, 50) : null;

if (!$articleId) {
    api_json_response([
        'success' => false,
        'message' => 'Thiếu hoặc sai article_id.',
    ], 422);
}

$timeSpent = max(0, min((int) $timeSpent, 3600 * 6));
$scrollPercentage = max(0, min((float) $scrollPercentage, 100));

$sessionId = api_resolve_reader_session_id($body['session_id'] ?? null);
$user = api_current_user();
$userId = $user['id'] ?? null;

try {
    $checkStmt = $pdo->prepare("
        SELECT id
        FROM articles
        WHERE id = :id
          AND status = 'published'
        LIMIT 1
    ");

    $checkStmt->execute([
        'id' => $articleId,
    ]);

    if (!$checkStmt->fetch()) {
        api_json_response([
            'success' => false,
            'message' => 'Bài viết không tồn tại hoặc chưa được xuất bản.',
        ], 404);
    }

    $stmt = $pdo->prepare("
        INSERT INTO reading_logs
            (user_id, session_id, article_id, time_spent, scroll_percentage, device, ip_address)
        VALUES
            (:user_id, :session_id, :article_id, :time_spent, :scroll_percentage, :device, :ip_address)
    ");

    $stmt->execute([
        'user_id' => $userId,
        'session_id' => $sessionId,
        'article_id' => $articleId,
        'time_spent' => $timeSpent,
        'scroll_percentage' => $scrollPercentage,
        'device' => $device,
        'ip_address' => api_client_ip(),
    ]);

    $readingLogId = (int) $pdo->lastInsertId();

    $pageViewStmt = $pdo->prepare("
        INSERT INTO page_views
            (user_id, article_id, session_id, ip_address, user_agent)
        VALUES
            (:user_id, :article_id, :session_id, :ip_address, :user_agent)
    ");

    $pageViewStmt->execute([
        'user_id' => $userId,
        'article_id' => $articleId,
        'session_id' => $sessionId,
        'ip_address' => api_client_ip(),
        'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
    ]);

    $updateViewsStmt = $pdo->prepare("
        UPDATE articles
        SET views = views + 1
        WHERE id = :id
    ");

    $updateViewsStmt->execute([
        'id' => $articleId,
    ]);

    api_json_response([
        'success' => true,
        'message' => 'Đã ghi nhận nhật ký đọc.',
        'data' => [
            'log_id' => $readingLogId,
            'session_id' => $sessionId,
        ],
    ], 201);
} catch (PDOException $e) {
    api_json_response([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
    ], 500);
}