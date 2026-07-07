USE smartnews;

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tags (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,

    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,

    status ENUM('draft', 'pending', 'published', 'rejected', 'deleted')
        NOT NULL DEFAULT 'draft',

    reject_reason TEXT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_posts_users
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_posts_categories
        FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS post_tags (
    post_id BIGINT UNSIGNED NOT NULL,
    tag_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (post_id, tag_id),

    CONSTRAINT fk_post_tags_posts
        FOREIGN KEY (post_id)
        REFERENCES posts(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_post_tags_tags
        FOREIGN KEY (tag_id)
        REFERENCES tags(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT IGNORE INTO categories (id, name, description) VALUES
(1, 'Công nghệ', 'Tin tức công nghệ, AI, phần mềm và thiết bị số'),
(2, 'Giải trí', 'Tin tức phim ảnh, âm nhạc và đời sống giải trí'),
(3, 'Kinh doanh', 'Tin thị trường, tài chính và doanh nghiệp'),
(4, 'Thể thao', 'Tin tức thể thao trong nước và quốc tế'),
(5, 'Sức khỏe', 'Tin sức khỏe, lối sống và dinh dưỡng'),
(6, 'Giáo dục', 'Tin giáo dục, tuyển sinh và học tập');

INSERT IGNORE INTO tags (id, name) VALUES
(1, 'AI'),
(2, 'Website'),
(3, 'Tin nóng'),
(4, 'Xu hướng'),
(5, 'Phân tích'),
(6, 'Đời sống');