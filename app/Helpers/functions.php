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
 * Ví dụ: asset('/css/news.css') => /SmartNews/public/assets/css/news.css
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