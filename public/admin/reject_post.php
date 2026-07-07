<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../app/Helpers/functions.php';

requireAdmin();

$pdo = require __DIR__ . '/../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/posts.php');
}

$postId = (int) ($_POST['post_id'] ?? 0);
$rejectReason = trim($_POST['reject_reason'] ?? '');

if ($postId <= 0 || $rejectReason === '') {
    setFlash('flash_error', 'Vui lòng nhập lý do từ chối.');
    redirect('/admin/posts.php');
}

$stmt = $pdo->prepare("
    UPDATE posts
    SET
        status = 'rejected',
        reject_reason = :reject_reason
    WHERE id = :id
      AND status <> 'deleted'
");

$stmt->execute([
    'reject_reason' => $rejectReason,
    'id' => $postId,
]);

setFlash('flash_success', 'Đã từ chối bài viết và gửi lý do cho người viết.');
redirect('/admin/posts.php?status=rejected');