<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../app/Helpers/functions.php';

requireEditor();

$pdo = require __DIR__ . '/../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/editor/my_articles.php');
}

$userId = (int) $_SESSION['user']['id'];
$postId = (int) ($_POST['post_id'] ?? 0);

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$categoryId = (int) ($_POST['category_id'] ?? 0);
$status = $_POST['status'] ?? 'draft';
$tagIds = $_POST['tag_ids'] ?? [];

if (!in_array($status, ['draft', 'pending'], true)) {
    $status = 'draft';
}

if ($postId <= 0 || $title === '' || $content === '' || $categoryId <= 0) {
    setFlash('flash_error', 'Dữ liệu bài viết không hợp lệ.');
    redirect('/editor/my_articles.php');
}

try {
    $pdo->beginTransaction();

    $checkStmt = $pdo->prepare("
        SELECT id
        FROM posts
        WHERE id = :id
          AND user_id = :user_id
          AND status IN ('draft', 'rejected')
        LIMIT 1
    ");

    $checkStmt->execute([
        'id' => $postId,
        'user_id' => $userId,
    ]);

    if (!$checkStmt->fetch()) {
        throw new RuntimeException('Bài viết không tồn tại hoặc không thể chỉnh sửa.');
    }

    $updateStmt = $pdo->prepare("
        UPDATE posts
        SET
            title = :title,
            content = :content,
            category_id = :category_id,
            status = :status,
            reject_reason = NULL
        WHERE id = :id
          AND user_id = :user_id
    ");

    $updateStmt->execute([
        'title' => $title,
        'content' => $content,
        'category_id' => $categoryId,
        'status' => $status,
        'id' => $postId,
        'user_id' => $userId,
    ]);

    $deleteTagsStmt = $pdo->prepare("
        DELETE FROM post_tags
        WHERE post_id = :post_id
    ");

    $deleteTagsStmt->execute([
        'post_id' => $postId,
    ]);

    if (!empty($tagIds)) {
        $tagStmt = $pdo->prepare("
            INSERT INTO post_tags (post_id, tag_id)
            VALUES (:post_id, :tag_id)
        ");

        foreach ($tagIds as $tagId) {
            $tagStmt->execute([
                'post_id' => $postId,
                'tag_id' => (int) $tagId,
            ]);
        }
    }

    uploadPostImages($pdo, $postId);

    $pdo->commit();

    if ($status === 'pending') {
        setFlash('flash_success', 'Đã gửi lại bài viết để Admin duyệt.');
    } else {
        setFlash('flash_success', 'Đã lưu thay đổi bài viết.');
    }

    redirect('/editor/my_articles.php');
} catch (Throwable $e) {
    $pdo->rollBack();

    setFlash('flash_error', 'Lỗi cập nhật bài viết: ' . $e->getMessage());
    redirect('/editor/edit.php?id=' . $postId);
}

function uploadPostImages(PDO $pdo, int $postId): void
{
    if (empty($_FILES['images']['name'][0])) {
        return;
    }

    $uploadDir = __DIR__ . '/../uploads/posts';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    $imageStmt = $pdo->prepare("
        INSERT INTO post_images (post_id, image_path)
        VALUES (:post_id, :image_path)
    ");

    foreach ($_FILES['images']['tmp_name'] as $index => $tmpPath) {
        if ($_FILES['images']['error'][$index] !== UPLOAD_ERR_OK) {
            continue;
        }

        $originalName = $_FILES['images']['name'][$index];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            continue;
        }

        $fileName = uniqid('post_', true) . '.' . $extension;
        $targetPath = $uploadDir . '/' . $fileName;

        if (move_uploaded_file($tmpPath, $targetPath)) {
            $imageStmt->execute([
                'post_id' => $postId,
                'image_path' => 'uploads/posts/' . $fileName,
            ]);
        }
    }
}