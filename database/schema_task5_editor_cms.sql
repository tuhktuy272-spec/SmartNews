USE smartnews;

-- ============================================================
-- NHIỆM VỤ 5 - EDITOR CMS
-- Bổ sung bảng ảnh bài viết và dữ liệu chuyên mục/thẻ mở rộng
-- ============================================================

CREATE TABLE IF NOT EXISTS post_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    post_id BIGINT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_post_images_posts
        FOREIGN KEY (post_id)
        REFERENCES posts(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT IGNORE INTO categories (id, name, description) VALUES
(7, 'Thời sự', 'Tin tức thời sự trong ngày'),
(8, 'Chính trị', 'Tin chính trị và chính sách'),
(9, 'Thế giới', 'Tin tức quốc tế'),
(10, 'Tài chính', 'Tin tài chính, ngân hàng, chứng khoán'),
(11, 'AI & Chuyển đổi số', 'Tin AI, công nghệ số và chuyển đổi số'),
(12, 'Khoa học', 'Tin khoa học và nghiên cứu'),
(13, 'Đời sống', 'Tin đời sống xã hội'),
(14, 'Du lịch', 'Tin du lịch và trải nghiệm'),
(15, 'Văn hóa', 'Tin văn hóa'),
(16, 'Pháp luật', 'Tin pháp luật'),
(17, 'Bất động sản', 'Tin bất động sản');

INSERT IGNORE INTO tags (id, name) VALUES
(7, 'Mới nhất'),
(8, 'Nổi bật'),
(9, 'Sự kiện'),
(10, 'Bình luận'),
(11, 'Phỏng vấn'),
(12, 'Chuyên đề'),
(13, 'Độc quyền'),
(14, 'Cập nhật'),
(15, 'Đáng chú ý');