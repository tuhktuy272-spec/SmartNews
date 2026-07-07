<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../app/Helpers/functions.php';

requireAdmin();

$pdo = require __DIR__ . '/../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/comments.php');
}

$commentId = (int) ($_POST['comment_id'] ?? 0);

if ($commentId <= 0) {
    setFlash('flash_error', 'ID bình luận không hợp lệ.');
    redirect('/admin/comments.php');
}

$stmt = $pdo->prepare("
    SELECT status
    FROM post_comments
    WHERE id = :id
    LIMIT 1
");

$stmt->execute([
    'id' => $commentId,
]);

$comment = $stmt->fetch();

if (!$comment) {
    setFlash('flash_error', 'Không tìm thấy bình luận.');
    redirect('/admin/comments.php');
}

$newStatus = $comment['status'] === 'visible' ? 'hidden' : 'visible';

$updateStmt = $pdo->prepare("
    UPDATE post_comments
    SET status = :status
    WHERE id = :id
");

$updateStmt->execute([
    'status' => $newStatus,
    'id' => $commentId,
]);

setFlash('flash_success', 'Đã cập nhật trạng thái bình luận.');
redirect('/admin/comments.php');