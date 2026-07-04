<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../app/Helpers/functions.php';
require_once __DIR__ . '/../app/Helpers/news_data.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'categories':
            echo json_encode([
                'categories' => getNewsCategories(),
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'articles':
            $section = $_GET['section'] ?? 'new';

            echo json_encode([
                'articles' => getArticlesBySection($section),
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'recommendations':
            echo json_encode([
                'recommendations' => getRecommendations(3),
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'search':
            $keyword = $_GET['q'] ?? '';

            echo json_encode([
                'articles' => searchNewsArticles($keyword),
            ], JSON_UNESCAPED_UNICODE);
            break;

        default:
            http_response_code(400);

            echo json_encode([
                'message' => 'Action không hợp lệ.',
            ], JSON_UNESCAPED_UNICODE);
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'message' => 'Có lỗi xảy ra khi xử lý API.',
    ], JSON_UNESCAPED_UNICODE);
}