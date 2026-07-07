<?php

$pageTitle = $pageTitle ?? APP_NAME;

?>

<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($pageTitle) ?></title>

    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >

    <link rel="stylesheet" href="<?= asset('/css/style.css') ?>">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="<?= url('/index.php') ?>">
            SmartNews
        </a>

        <button 
            class="navbar-toggler" 
            type="button" 
            data-bs-toggle="collapse" 
            data-bs-target="#navbarMain"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/index.php') ?>">
                        Trang chủ
                    </a>
                </li>

                <?php if (isLoggedIn()): ?>

                    <li class="nav-item ms-lg-3">
                        <span class="small text-muted">
                            Xin chào, <?= e($_SESSION['user']['full_name']) ?>
                        </span>
                    </li>

                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-danger btn-sm" href="<?= url('/logout.php') ?>">
                            Đăng xuất
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-primary btn-sm" href="<?= url('/login.php') ?>">
                            Đăng nhập
                        </a>
                    </li>

                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-primary btn-sm" href="<?= url('/register.php') ?>">
                            Đăng ký
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main>