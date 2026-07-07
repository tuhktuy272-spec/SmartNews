USE smartnews;

CREATE TABLE IF NOT EXISTS post_likes (
    post_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (post_id, user_id),

    CONSTRAINT fk_post_likes_posts
        FOREIGN KEY (post_id)
        REFERENCES posts(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_post_likes_users
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

SET @database_name = DATABASE();

SET @has_sentiment = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @database_name
      AND TABLE_NAME = 'post_comments'
      AND COLUMN_NAME = 'sentiment'
);

SET @sql_add_sentiment = IF(
    @has_sentiment = 0,
    'ALTER TABLE post_comments ADD COLUMN sentiment ENUM(''positive'', ''neutral'', ''negative'') NOT NULL DEFAULT ''neutral'' AFTER status',
    'SELECT ''sentiment column already exists'' AS message'
);

PREPARE stmt_add_sentiment FROM @sql_add_sentiment;
EXECUTE stmt_add_sentiment;
DEALLOCATE PREPARE stmt_add_sentiment;

SET @has_sentiment_score = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @database_name
      AND TABLE_NAME = 'post_comments'
      AND COLUMN_NAME = 'sentiment_score'
);

SET @sql_add_sentiment_score = IF(
    @has_sentiment_score = 0,
    'ALTER TABLE post_comments ADD COLUMN sentiment_score DECIMAL(4,2) NOT NULL DEFAULT 0.00 AFTER sentiment',
    'SELECT ''sentiment_score column already exists'' AS message'
);

PREPARE stmt_add_sentiment_score FROM @sql_add_sentiment_score;
EXECUTE stmt_add_sentiment_score;
DEALLOCATE PREPARE stmt_add_sentiment_score;

UPDATE post_comments
SET
    sentiment = CASE
        WHEN LOWER(content) LIKE '%hay%'
          OR LOWER(content) LIKE '%rất hay%'
          OR LOWER(content) LIKE '%tốt%'
          OR LOWER(content) LIKE '%thích%'
          OR LOWER(content) LIKE '%hữu ích%'
          OR LOWER(content) LIKE '%tuyệt%'
          OR LOWER(content) LIKE '%dễ hiểu%'
          OR LOWER(content) LIKE '%bổ ích%'
          OR LOWER(content) LIKE '%ấn tượng%'
          OR LOWER(content) LIKE '%đáng đọc%'
        THEN 'positive'

        WHEN LOWER(content) LIKE '%dở%'
          OR LOWER(content) LIKE '%tệ%'
          OR LOWER(content) LIKE '%chán%'
          OR LOWER(content) LIKE '%khó hiểu%'
          OR LOWER(content) LIKE '%sai%'
          OR LOWER(content) LIKE '%không hay%'
          OR LOWER(content) LIKE '%không thích%'
          OR LOWER(content) LIKE '%lỗi%'
          OR LOWER(content) LIKE '%kém%'
          OR LOWER(content) LIKE '%thất vọng%'
        THEN 'negative'

        ELSE 'neutral'
    END,

    sentiment_score = CASE
        WHEN LOWER(content) LIKE '%hay%'
          OR LOWER(content) LIKE '%rất hay%'
          OR LOWER(content) LIKE '%tốt%'
          OR LOWER(content) LIKE '%thích%'
          OR LOWER(content) LIKE '%hữu ích%'
          OR LOWER(content) LIKE '%tuyệt%'
          OR LOWER(content) LIKE '%dễ hiểu%'
          OR LOWER(content) LIKE '%bổ ích%'
          OR LOWER(content) LIKE '%ấn tượng%'
          OR LOWER(content) LIKE '%đáng đọc%'
        THEN 1.00

        WHEN LOWER(content) LIKE '%dở%'
          OR LOWER(content) LIKE '%tệ%'
          OR LOWER(content) LIKE '%chán%'
          OR LOWER(content) LIKE '%khó hiểu%'
          OR LOWER(content) LIKE '%sai%'
          OR LOWER(content) LIKE '%không hay%'
          OR LOWER(content) LIKE '%không thích%'
          OR LOWER(content) LIKE '%lỗi%'
          OR LOWER(content) LIKE '%kém%'
          OR LOWER(content) LIKE '%thất vọng%'
        THEN -1.00

        ELSE 0.00
    END;

INSERT IGNORE INTO post_likes (post_id, user_id)
SELECT
    p.id,
    u.id
FROM (
    SELECT id
    FROM posts
    WHERE status = 'published'
    ORDER BY id DESC
    LIMIT 8
) AS p
JOIN (
    SELECT id
    FROM users
    ORDER BY id ASC
    LIMIT 2
) AS u;

INSERT INTO post_comments
    (post_id, user_id, content, status, sentiment, sentiment_score)
SELECT
    p.id,
    u.id,
    CASE
        WHEN MOD(p.id, 3) = 0 THEN 'Bài viết rất hay, nội dung dễ hiểu và hữu ích.'
        WHEN MOD(p.id, 3) = 1 THEN 'Nội dung ổn, có thể tham khảo thêm.'
        ELSE 'Một số phần còn hơi khó hiểu, cần trình bày rõ hơn.'
    END AS content,
    'visible' AS status,
    CASE
        WHEN MOD(p.id, 3) = 0 THEN 'positive'
        WHEN MOD(p.id, 3) = 1 THEN 'neutral'
        ELSE 'negative'
    END AS sentiment,
    CASE
        WHEN MOD(p.id, 3) = 0 THEN 1.00
        WHEN MOD(p.id, 3) = 1 THEN 0.00
        ELSE -1.00
    END AS sentiment_score
FROM (
    SELECT id
    FROM posts
    WHERE status = 'published'
    ORDER BY id DESC
    LIMIT 9
) AS p
JOIN (
    SELECT id
    FROM users
    ORDER BY id ASC
    LIMIT 1
) AS u;