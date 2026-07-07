<?php

declare(strict_types=1);

/**
 * Escape dữ liệu trước khi in ra HTML.
 * Giúp hạn chế lỗi XSS.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Tạo đường dẫn URL đúng cho XAMPP.
 * Ví dụ: url('/login.php') => /SmartNews/public/login.php
 */
function url(string $path = ''): string
{
    return BASE_URL . $path;
}

/**
 * Tạo đường dẫn asset.
 * Ví dụ: asset('/css/style.css') => /SmartNews/public/assets/css/style.css
 */
function asset(string $path = ''): string
{
    return BASE_URL . '/assets' . $path;
}

/**
 * Chuyển hướng trang.
 */
function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/**
 * Gán flash message vào session.
 */
function setFlash(string $key, string $message): void
{
    $_SESSION[$key] = $message;
}

/**
 * Lấy và xóa flash message khỏi session.
 */
function getFlash(string $key): ?string
{
    if (!isset($_SESSION[$key])) {
        return null;
    }

    $message = $_SESSION[$key];
    unset($_SESSION[$key]);

    return $message;
}

/**
 * Hiển thị flash message bằng Bootstrap.
 */
function renderFlashMessages(): void
{
    $success = getFlash('flash_success');
    $error = getFlash('flash_error');

    if ($success) {
        echo '<div class="alert alert-success">' . e($success) . '</div>';
    }

    if ($error) {
        echo '<div class="alert alert-danger">' . e($error) . '</div>';
    }
}

/**
 * Hiển thị trạng thái bài viết.
 */
function postStatusBadge(string $status): string
{
    return match ($status) {
        'draft' => '<span class="badge bg-secondary">Bản nháp</span>',
        'pending' => '<span class="badge bg-warning text-dark">Chờ duyệt</span>',
        'published' => '<span class="badge bg-success">Đã xuất bản</span>',
        'rejected' => '<span class="badge bg-danger">Bị từ chối</span>',
        'deleted' => '<span class="badge bg-dark">Đã xóa</span>',
        default => '<span class="badge bg-light text-dark">Không rõ</span>',
    };
}