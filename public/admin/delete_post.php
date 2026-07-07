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

if ($postId <= 0) {
    setFlash('flash_error', 'ID bài viết không hợp lệ.');
    redirect('/admin/posts.php');
}

$stmt = $pdo->prepare("
    UPDATE posts
    SET status = 'deleted'
    WHERE id = :id
");

$stmt->execute([
    'id' => $postId,
]);

setFlash('flash_success', 'Đã chuyển bài viết vào trạng thái đã xóa.');
redirect('/admin/posts.php');