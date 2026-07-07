<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/session.php';

/**
 * Trả JSON cho API.
 */
function api_json_response(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    exit;
}

/**
 * Đọc JSON body từ request.
 */
function api_get_json_body(): array
{
    $raw = file_get_contents('php://input');

    $data = json_decode($raw, true);

    return is_array($data) ? $data : [];
}

/**
 * Lấy user hiện tại từ session SmartNews.
 */
function api_current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

/**
 * Chặn API nếu chưa đăng nhập.
 */
function api_require_login(): array
{
    $user = api_current_user();

    if (!$user) {
        api_json_response([
            'success' => false,
            'message' => 'Bạn cần đăng nhập để thực hiện thao tác này.',
        ], 401);
    }

    return $user;
}

/**
 * Chặn API nếu không phải Admin.
 */
function api_require_admin(): array
{
    $user = api_require_login();

    if (($user['role_name'] ?? '') !== 'admin') {
        api_json_response([
            'success' => false,
            'message' => 'Bạn không có quyền truy cập chức năng này.',
        ], 403);
    }

    if (($user['status'] ?? 'active') === 'locked') {
        api_json_response([
            'success' => false,
            'message' => 'Tài khoản của bạn đã bị khóa.',
        ], 403);
    }

    return $user;
}

/**
 * Lấy hoặc sinh session_id cho người đọc.
 */
function api_resolve_reader_session_id(?string $clientSessionId): string
{
    if (!empty($clientSessionId)) {
        return substr($clientSessionId, 0, 100);
    }

    if (empty($_SESSION['reader_session_id'])) {
        $_SESSION['reader_session_id'] = bin2hex(random_bytes(16));
    }

    return $_SESSION['reader_session_id'];
}

/**
 * Lấy IP client.
 */
function api_client_ip(): string
{
    return $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '';
}

/**
 * Phân tích sắc thái bình luận tiếng Việt dạng đơn giản.
 */
function api_analyze_sentiment(string $text): array
{
    $text = mb_strtolower($text, 'UTF-8');

    $positiveWords = [
        'hay', 'tuyệt', 'tốt', 'thích', 'yêu', 'xuất sắc',
        'tuyệt vời', 'hữu ích', 'chất lượng', 'ấn tượng',
        'cảm ơn', 'đỉnh', 'chính xác', 'nhanh', 'chuẩn',
        'ok', 'ổn', 'ủng hộ', 'đáng đọc', 'bổ ích',
    ];

    $negativeWords = [
        'tệ', 'dở', 'chán', 'kém', 'sai', 'lừa đảo',
        'thất vọng', 'ghét', 'phản cảm', 'nhảm', 'vô lý',
        'tồi', 'rác', 'sai sự thật', 'tào lao', 'nhàm chán',
        'giả', 'xàm', 'bực', 'khó chịu', 'sai lệch',
    ];

    $posCount = 0;
    $negCount = 0;

    foreach ($positiveWords as $word) {
        if (mb_strpos($text, $word) !== false) {
            $posCount++;
        }
    }

    foreach ($negativeWords as $word) {
        if (mb_strpos($text, $word) !== false) {
            $negCount++;
        }
    }

    $total = $posCount + $negCount;

    if ($total === 0) {
        return [
            'label' => 'neutral',
            'score' => 0.0,
        ];
    }

    $score = ($posCount - $negCount) / $total;

    if ($score > 0.15) {
        $label = 'positive';
    } elseif ($score < -0.15) {
        $label = 'negative';
    } else {
        $label = 'neutral';
    }

    return [
        'label' => $label,
        'score' => round($score, 2),
    ];
}