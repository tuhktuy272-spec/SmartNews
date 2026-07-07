USE smartnews;

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS articles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(255) NOT NULL,
    slug VARCHAR(280) NOT NULL UNIQUE,
    summary VARCHAR(500),
    content LONGTEXT NOT NULL,
    thumbnail VARCHAR(255),

    category_id INT UNSIGNED NOT NULL,
    author_id BIGINT UNSIGNED NOT NULL,

    views INT UNSIGNED NOT NULL DEFAULT 0,

    status ENUM('draft', 'published', 'hidden') NOT NULL DEFAULT 'published',

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_articles_categories
        FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_articles_users
        FOREIGN KEY (author_id)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    article_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,

    content TEXT NOT NULL,

    sentiment ENUM('positive', 'neutral', 'negative') DEFAULT NULL,
    sentiment_score FLOAT DEFAULT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_comments_articles
        FOREIGN KEY (article_id)
        REFERENCES articles(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_comments_users
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS reading_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT UNSIGNED NULL,
    session_id VARCHAR(100) NOT NULL,
    article_id BIGINT UNSIGNED NOT NULL,

    time_spent INT UNSIGNED NOT NULL DEFAULT 0,
    scroll_percentage FLOAT NOT NULL DEFAULT 0,

    device VARCHAR(50) DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reading_logs_users
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_reading_logs_articles
        FOREIGN KEY (article_id)
        REFERENCES articles(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_reading_logs_user_article (user_id, article_id),
    INDEX idx_reading_logs_session (session_id),
    INDEX idx_reading_logs_created (created_at)
);

CREATE TABLE IF NOT EXISTS page_views (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT UNSIGNED NULL,
    article_id BIGINT UNSIGNED NULL,

    session_id VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_page_views_users
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_page_views_articles
        FOREIGN KEY (article_id)
        REFERENCES articles(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    INDEX idx_page_views_created (created_at),
    INDEX idx_page_views_session (session_id)
);

INSERT IGNORE INTO categories (id, name, description) VALUES
(1, 'Công nghệ', 'Tin tức công nghệ, AI, phần mềm và thiết bị số'),
(2, 'Giải trí', 'Tin tức phim ảnh, âm nhạc và đời sống giải trí'),
(3, 'Kinh doanh', 'Tin thị trường, tài chính và doanh nghiệp'),
(4, 'Thể thao', 'Tin tức thể thao trong nước và quốc tế'),
(5, 'Sức khỏe', 'Tin sức khỏe, lối sống và dinh dưỡng'),
(6, 'Giáo dục', 'Tin giáo dục, tuyển sinh và học tập');

INSERT IGNORE INTO articles
    (id, title, slug, summary, content, thumbnail, category_id, author_id, views, status)
SELECT
    1,
    'AI và tương lai báo chí số',
    'ai-va-tuong-lai-bao-chi-so',
    'Cách AI thay đổi sản xuất nội dung và trải nghiệm đọc tin.',
    'Trí tuệ nhân tạo đang làm thay đổi mạnh mẽ cách các tòa soạn sản xuất, phân phối và cá nhân hóa nội dung tin tức.',
    'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=1200&q=80',
    1,
    users.id,
    25,
    'published'
FROM users
ORDER BY users.id
LIMIT 1;

INSERT IGNORE INTO articles
    (id, title, slug, summary, content, thumbnail, category_id, author_id, views, status)
SELECT
    2,
    'Thiết kế giao diện tin tức chuyên nghiệp',
    'thiet-ke-giao-dien-tin-tuc-chuyen-nghiep',
    'Mẹo bố cục, màu sắc và trải nghiệm người đọc cho trang tin.',
    'Một giao diện tin tức chuyên nghiệp cần đảm bảo khả năng đọc tốt, bố cục rõ ràng và điều hướng đơn giản.',
    'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=1200&q=80',
    1,
    users.id,
    18,
    'published'
FROM users
ORDER BY users.id
LIMIT 1;

INSERT IGNORE INTO articles
    (id, title, slug, summary, content, thumbnail, category_id, author_id, views, status)
SELECT
    3,
    'Tối ưu tốc độ trang tin tức',
    'toi-uu-toc-do-trang-tin-tuc',
    'Các kỹ thuật front-end giúp trang tin tải nhanh và ổn định.',
    'Tốc độ tải trang ảnh hưởng trực tiếp đến trải nghiệm người dùng và khả năng giữ chân độc giả.',
    'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80',
    1,
    users.id,
    12,
    'published'
FROM users
ORDER BY users.id
LIMIT 1;