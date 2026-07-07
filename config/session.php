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
 * Kiểm tra tài khoản hiện tại có phải admin hay không.
 */
function isAdmin(): bool
{
    return isLoggedIn()
        && isset($_SESSION['user']['role_name'])
        && $_SESSION['user']['role_name'] === 'admin';
}

/**
 * Lấy quyền hiện tại của tài khoản.
 */
function currentUserRole(): ?string
{
    return $_SESSION['user']['role_name'] ?? null;
}

/**
 * Kiểm tra tài khoản hiện tại có phải editor hay không.
 */
function isEditor(): bool
{
    return isLoggedIn()
        && currentUserRole() === 'editor';
}

/**
 * Kiểm tra tài khoản hiện tại có phải user thường hay không.
 */
function isNormalUser(): bool
{
    return isLoggedIn()
        && currentUserRole() === 'user';
}

/**
 * Chỉ editor mới được viết bài.
 * Admin không được viết bài theo yêu cầu phân quyền của nhóm.
 */
function canWriteArticle(): bool
{
    return isEditor();
}

/**
 * Chặn người không phải editor truy cập chức năng viết bài.
 */
function requireEditor(): void
{
    requireAuth();

    if (!canWriteArticle()) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    }
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

/**
 * Chặn người không phải admin truy cập trang quản trị.
 */
function requireAdmin(): void
{
    requireAuth();

    if (!isAdmin()) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    }
}