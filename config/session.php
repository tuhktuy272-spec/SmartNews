<?php

declare(strict_types=1);

require_once __DIR__ . '/app.php';

/**
 * File quản lý session chung cho toàn hệ thống.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Kiểm tra người dùng đã đăng nhập hay chưa.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

/**
 * Chặn người chưa đăng nhập truy cập trang riêng tư.
 */
function requireAuth(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

/**
 * Chặn người đã đăng nhập quay lại trang login/register.
 */
function requireGuest(): void
{
    if (isLoggedIn()) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    }
}