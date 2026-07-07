USE smartnews;

-- ============================================================
-- NHIỆM VỤ 6 - ADMIN DASHBOARD & COMMENT MANAGEMENT
-- ============================================================

CREATE TABLE IF NOT EXISTS post_comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    post_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,

    content TEXT NOT NULL,

    status ENUM('visible', 'hidden', 'deleted')
        NOT NULL DEFAULT 'visible',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_post_comments_posts
        FOREIGN KEY (post_id)
        REFERENCES posts(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_post_comments_users
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);