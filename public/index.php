<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

$pageTitle = 'Trang chủ - SmartNews';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <meta 
        name="description" 
        content="SmartNews là trang tin tức hiện đại, tối giản và chuyên nghiệp." 
    />

    <title><?= e($pageTitle) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <link 
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" 
        rel="stylesheet" 
    />

    <link rel="stylesheet" href="<?= asset('/css/news.css') ?>" />
</head>

<body>
    <div class="page-loading" id="page-loading" aria-hidden="true">
        <div class="loading-card">
            <div class="loading-line loading-line-lg"></div>
            <div class="loading-line"></div>
            <div class="loading-line"></div>

            <div class="loading-row">
                <span class="loading-pill"></span>
                <span class="loading-pill"></span>
            </div>
        </div>
    </div>

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
                <a href="#new-articles">Mới nhất</a>
                <a href="#featured">Nổi bật</a>
                <a href="#categories">Chuyên mục</a>

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

    <main>
        <section class="hero">
            <div class="hero-ambient"></div>

            <div class="container hero-grid">
                <div class="hero-copy reveal">
                    <p class="eyebrow">Tin nổi bật</p>

                    <h1>
                        Khám phá tin tức thông minh, nhanh và cực kỳ dễ đọc.
                    </h1>

                    <p>
                        SmartNews được thiết kế tối giản nhưng vẫn giữ được cảm giác 
                        hiện đại, rõ ràng và chuyên nghiệp trên mọi thiết bị.
                    </p>

                    <div class="hero-actions">
                        <a class="btn btn-primary" href="#featured">
                            Xem bài nổi bật
                        </a>

                        <a class="btn btn-secondary" href="#categories">
                            Khám phá chuyên mục
                        </a>
                    </div>

                    <div class="hero-metrics">
                        <div>
                            <strong>24/7</strong>
                            <span>Cập nhật</span>
                        </div>

                        <div>
                            <strong>6+</strong>
                            <span>Chuyên mục</span>
                        </div>

                        <div>
                            <strong>100%</strong>
                            <span>Responsive</span>
                        </div>
                    </div>
                </div>

                <div class="hero-panel reveal">
                    <div class="hero-panel-card">
                        <img 
                            src="https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=900&q=80" 
                            alt="Bài báo và nội dung tin tức" 
                        />

                        <div class="hero-panel-info">
                            <span class="pill">Xu hướng 2026</span>

                            <h2>
                                Giao diện báo chí đổi mới để tăng trải nghiệm đọc
                            </h2>

                            <p>
                                Thiết kế rõ ràng, điều hướng mượt và nội dung được sắp xếp hợp lý.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section search-section">
            <div class="container">
                <form id="search-form" class="search-form reveal">
                    <div class="search-form-icon" aria-hidden="true">⌕</div>

                    <input 
                        type="search" 
                        id="search-input" 
                        placeholder="Tìm bài viết, tác giả, chủ đề..." 
                    />

                    <button type="submit">Tìm kiếm</button>
                </form>
            </div>
        </section>

        <section class="section" id="search-results-section" hidden>
            <div class="container section-header reveal">
                <div>
                    <p class="section-label">Kết quả tìm kiếm</p>

                    <h2>
                        Kết quả cho “<span id="search-query-text"></span>”
                    </h2>
                </div>
            </div>

            <div class="articles-grid container" id="search-results-list"></div>
        </section>

        <section class="section" id="categories">
            <div class="container section-header reveal">
                <div>
                    <p class="section-label">Chuyên mục</p>
                    <h2>Duyệt theo lĩnh vực bạn quan tâm</h2>
                </div>
            </div>

            <div class="category-grid container" id="categories-list"></div>
        </section>

        <section class="section" id="featured">
            <div class="container section-header reveal">
                <div>
                    <p class="section-label">Nổi bật</p>
                    <h2>Bài viết được chú ý nhiều nhất</h2>
                </div>
            </div>

            <div class="articles-grid container" id="featured-list"></div>
        </section>

        <section class="section" id="new-articles">
            <div class="container section-header reveal">
                <div>
                    <p class="section-label">Mới nhất</p>
                    <h2>Tin tức vừa được cập nhật</h2>
                </div>
            </div>

            <div class="articles-grid container" id="new-list"></div>
        </section>

        <section class="section recommendations">
            <div class="container section-header reveal">
                <div>
                    <p class="section-label">Đề xuất</p>

                    <h2>Những bài viết phù hợp với bạn</h2>

                    <p class="section-subtitle">
                        Dữ liệu demo mô phỏng chức năng đề xuất bài viết cho hệ thống SmartNews.
                    </p>
                </div>
            </div>

            <div class="articles-grid container" id="recommendation-list"></div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <a class="logo footer-logo" href="<?= url('/index.php') ?>">
                    <span class="logo-mark"></span>
                    SmartNews
                </a>

                <p>
                    Trang tin tức hiện đại, chuyên nghiệp và dễ tiếp cận,
                    tập trung vào trải nghiệm đọc và nội dung chất lượng.
                </p>
            </div>

            <div>
                <h3>Khám phá</h3>

                <ul>
                    <li><a href="#featured">Bài nổi bật</a></li>
                    <li><a href="#new-articles">Bài mới</a></li>
                    <li><a href="#categories">Chuyên mục</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <button class="back-to-top" type="button" aria-label="Quay về đầu trang">
        ↑
    </button>

    <script>
    window.SMARTNEWS_BASE_URL = "<?= BASE_URL ?>";
    </script>

    <script src="<?= asset('/js/news.js') ?>"></script>
</body>
</html>