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
    UPDATE post_comments
    SET status = 'deleted'
    WHERE id = :id
");

$stmt->execute([
    'id' => $commentId,
]);

setFlash('flash_success', 'Đã xóa mềm bình luận.');
redirect('/admin/comments.php');