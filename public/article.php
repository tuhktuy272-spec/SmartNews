<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../app/Helpers/functions.php';
require_once __DIR__ . '/../app/Helpers/news_data.php';

$articleId = $_GET['id'] ?? '';

if ($articleId === '') {
    redirect('/index.php');
}

$article = getArticleById($articleId);

if (!$article) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>404 - Không tìm thấy bài viết</title>
        <link rel="stylesheet" href="<?= asset('/css/news.css') ?>" />
    </head>

    <body>
        <main class="container section">
            <div class="article-detail">
                <h1>Không tìm thấy bài viết</h1>

                <p>Bài viết bạn đang tìm không tồn tại hoặc đã bị xóa.</p>

                <a class="btn btn-primary" href="<?= url('/index.php') ?>">
                    Quay lại trang chủ
                </a>
            </div>
        </main>
    </body>
    </html>
    <?php
    exit;
}

$recommendations = getRecommendations(4, (int) $article['id']);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title><?= e($article['title']) ?> - SmartNews</title>

    <meta name="description" content="<?= e($article['summary']) ?>" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <link 
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" 
        rel="stylesheet" 
    />

    <link rel="stylesheet" href="<?= asset('/css/news.css') ?>" />
</head>

<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="logo" href="<?= url('/index.php') ?>">
                <span class="logo-mark"></span>
                SmartNews
            </a>

            <button 
                class="nav-toggle" 
                type="button" 
                aria-label="Mở menu" 
                aria-expanded="false"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="nav-links" aria-label="Điều hướng chính">
                <a href="<?= url('/index.php#new-articles') ?>">Mới nhất</a>
                <a href="<?= url('/index.php#featured') ?>">Nổi bật</a>
                <a href="<?= url('/index.php#categories') ?>">Chuyên mục</a>

                <?php if (isLoggedIn()): ?>
                    <a href="<?= url('/dashboard.php') ?>">Dashboard</a>
                    <a href="<?= url('/logout.php') ?>">Đăng xuất</a>
                <?php else: ?>
                    <a href="<?= url('/login.php') ?>">Đăng nhập</a>
                    <a href="<?= url('/register.php') ?>">Đăng ký</a>
                <?php endif; ?>
            </nav>

            <button 
                class="theme-toggle" 
                type="button" 
                aria-label="Chuyển đổi chế độ tối"
            >
                <span class="theme-toggle-icon">☀️</span>
            </button>
        </div>
    </header>

    <main class="container article-page">
        <article class="article-detail reveal">
            <header class="article-header">
                <p class="article-category">
                    Chuyên mục: <?= e($article['category_name']) ?>
                </p>

                <h1><?= e($article['title']) ?></h1>

                <div class="article-meta">
                    <span>Tác giả: <?= e($article['author']) ?></span>

                    <span>
                        Ngày đăng: 
                        <?= date('d/m/Y', strtotime($article['publish_date'])) ?>
                    </span>
                </div>
            </header>

            <?php if (!empty($article['image'])): ?>
                <img 
                    class="article-hero-image" 
                    src="<?= e($article['image']) ?>" 
                    alt="<?= e($article['image_alt'] ?: $article['title']) ?>" 
                />
            <?php endif; ?>

            <div class="article-content" id="article-content">
                <?= $article['content'] ?>
            </div>
        </article>

        <aside class="sidebar reveal">
            <div class="sidebar-box">
                <h2>Bài viết đề xuất cho bạn</h2>

                <ul>
                    <?php foreach ($recommendations as $item): ?>
                        <li>
                            <a href="<?= e($item['link']) ?>">
                                <?= e($item['title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="sidebar-box accent-box">
                <h2>Quay lại trang chủ</h2>

                <p>
                    Tiếp tục khám phá thêm các bài viết mới nhất trên SmartNews.
                </p>

                <a class="btn btn-primary btn-full" href="<?= url('/index.php') ?>">
                    Về trang chủ
                </a>
            </div>
        </aside>
    </main>

    <button class="back-to-top" type="button" aria-label="Quay về đầu trang">
        ↑
    </button>

    <script src="<?= asset('/js/news.js') ?>"></script>
</body>
</html>