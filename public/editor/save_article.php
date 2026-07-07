<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../app/Helpers/functions.php';

requireEditor();

$pdo = require __DIR__ . '/../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/editor/compose.php');
}

$userId = (int) $_SESSION['user']['id'];

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$categoryId = (int) ($_POST['category_id'] ?? 0);
$status = $_POST['status'] ?? 'draft';
$tagIds = $_POST['tag_ids'] ?? [];

if (!in_array($status, ['draft', 'pending'], true)) {
    $status = 'draft';
}

if ($title === '' || $content === '' || $categoryId <= 0) {
    setFlash('flash_error', 'Vui lòng nhập đầy đủ tiêu đề, chuyên mục và nội dung.');
    redirect('/editor/compose.php');
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO posts
            (user_id, category_id, title, content, status)
        VALUES
            (:user_id, :category_id, :title, :content, :status)
    ");

    $stmt->execute([
        'user_id' => $userId,
        'category_id' => $categoryId,
        'title' => $title,
        'content' => $content,
        'status' => $status,
    ]);

    $postId = (int) $pdo->lastInsertId();

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
        setFlash('flash_success', 'Đã gửi bài viết chờ Admin duyệt.');
    } else {
        setFlash('flash_success', 'Đã lưu bài viết dưới dạng bản nháp.');
    }

    redirect('/editor/my_articles.php');
} catch (Throwable $e) {
    $pdo->rollBack();

    setFlash('flash_error', 'Lỗi khi lưu bài viết: ' . $e->getMessage());
    redirect('/editor/compose.php');
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