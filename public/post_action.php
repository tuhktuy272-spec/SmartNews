<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

requireAuth();

$pdo = require __DIR__ . '/../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/user_dashboard.php');
}

$action = $_POST['action'] ?? '';
$userId = (int) $_SESSION['user']['id'];

/**
 * Gán danh sách tag cho bài viết.
 * Cách làm: xóa tag cũ rồi thêm tag mới.
 */
function syncPostTags(PDO $pdo, int $postId, array $tagIds): void
{
    $deleteStmt = $pdo->prepare("
        DELETE FROM post_tags
        WHERE post_id = :post_id
    ");

    $deleteStmt->execute([
        'post_id' => $postId,
    ]);

    if (empty($tagIds)) {
        return;
    }

    $insertStmt = $pdo->prepare("
        INSERT INTO post_tags (post_id, tag_id)
        VALUES (:post_id, :tag_id)
    ");

    foreach ($tagIds as $tagId) {
        $insertStmt->execute([
            'post_id' => $postId,
            'tag_id' => (int) $tagId,
        ]);
    }
}

switch ($action) {
    case 'draft':
    case 'pending':
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $tagIds = $_POST['tag_ids'] ?? [];

        if ($title === '' || $content === '' || $categoryId <= 0) {
            setFlash('flash_error', 'Vui lòng nhập đầy đủ tiêu đề, nội dung và chuyên mục.');
            redirect('/user_dashboard.php');
        }

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
            'status' => $action,
        ]);

        $postId = (int) $pdo->lastInsertId();

        syncPostTags($pdo, $postId, $tagIds);

        if ($action === 'draft') {
            setFlash('flash_success', 'Đã lưu bài viết dưới dạng bản nháp.');
        } else {
            setFlash('flash_success', 'Đã gửi bài viết chờ Admin duyệt.');
        }

        redirect('/user_dashboard.php');

    case 'delete':
        $postId = (int) ($_POST['post_id'] ?? 0);

        if ($postId <= 0) {
            setFlash('flash_error', 'ID bài viết không hợp lệ.');
            redirect('/user_dashboard.php');
        }

        /**
         * Chỉ cho phép xóa bài của chính user hiện tại
         * và chỉ xóa khi bài đang là draft hoặc rejected.
         * Ở đây dùng xóa mềm: status = deleted.
         */
        $stmt = $pdo->prepare("
            UPDATE posts
            SET status = 'deleted'
            WHERE id = :id
              AND user_id = :user_id
              AND status IN ('draft', 'rejected')
        ");

        $stmt->execute([
            'id' => $postId,
            'user_id' => $userId,
        ]);

        if ($stmt->rowCount() > 0) {
            setFlash('flash_success', 'Đã chuyển bài viết vào thùng rác.');
        } else {
            setFlash('flash_error', 'Không thể xóa bài viết ở trạng thái hiện tại.');
        }

        redirect('/user_dashboard.php');

    default:
        setFlash('flash_error', 'Hành động không hợp lệ.');
        redirect('/user_dashboard.php');
}