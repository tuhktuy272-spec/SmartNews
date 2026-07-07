USE smartnews;
-- Seed 30 bài viết mẫu cho SmartNews. Nội dung được viết mới để demo dữ liệu, không sao chép nguyên văn từ báo.
-- Chạy sau: schema.sql, schema_task2_posts.sql, schema_task5_editor_cms.sql, schema_role_permissions.sql.

INSERT INTO roles (id, name, description) VALUES
(1, 'admin', 'Quản trị viên - quản lý hệ thống, duyệt bài, quản lý user'),
(2, 'user', 'Người dùng thường - xem tin tức và bình luận'),
(3, 'editor', 'Biên tập viên - viết bài, lưu nháp và gửi duyệt')
ON DUPLICATE KEY UPDATE description = VALUES(description);

SET @author_id := COALESCE(
    (SELECT id FROM users WHERE email = 'trannguyen123@gmail.com' LIMIT 1),
    (SELECT id FROM users WHERE email = 'tuhktuy272@gmail.com' LIMIT 1),
    (SELECT id FROM users ORDER BY id ASC LIMIT 1)
);

-- Nếu @author_id bị NULL nghĩa là bảng users chưa có tài khoản nào.
-- Khi đó hãy đăng ký 1 tài khoản trước, rồi chạy lại file này.

INSERT IGNORE INTO categories (id, name, description) VALUES
(1, 'Công nghệ', 'Tin tức công nghệ, AI, phần mềm và thiết bị số'),
(2, 'Giải trí', 'Tin tức phim ảnh, âm nhạc và đời sống giải trí'),
(3, 'Kinh doanh', 'Tin thị trường, tài chính và doanh nghiệp'),
(4, 'Thể thao', 'Tin tức thể thao trong nước và quốc tế'),
(5, 'Sức khỏe', 'Tin sức khỏe, lối sống và dinh dưỡng'),
(6, 'Giáo dục', 'Tin giáo dục, tuyển sinh và học tập');

INSERT IGNORE INTO tags (id, name) VALUES
(1, 'AI'), (2, 'Website'), (3, 'Tin nóng'), (4, 'Xu hướng'), (5, 'Phân tích'), (6, 'Đời sống'),
(7, 'Mới nhất'), (8, 'Nổi bật'), (14, 'Cập nhật');

-- 01. Công nghệ
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 1, 'AI tạo sinh bước vào giai đoạn hỗ trợ công việc văn phòng', '<p>Các công cụ AI tạo sinh đang được nhiều nhóm nội dung, chăm sóc khách hàng và lập trình sử dụng như một trợ lý số. Điểm quan trọng không phải là thay thế hoàn toàn con người mà là rút ngắn thời gian tìm ý tưởng, tóm tắt tài liệu và kiểm tra lỗi cơ bản. Với doanh nghiệp nhỏ, AI giúp tự động hóa những thao tác lặp lại, nhưng vẫn cần người biên tập kiểm chứng thông tin trước khi xuất bản.</p><p>Bài viết thuộc chuyên mục Công nghệ, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 30 HOUR), DATE_SUB(NOW(), INTERVAL 30 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'AI tạo sinh bước vào giai đoạn hỗ trợ công việc văn phòng');

-- 02. Công nghệ
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 1, 'Thiết bị đeo thông minh ngày càng chú trọng sức khỏe cá nhân', '<p>Đồng hồ thông minh và vòng đeo sức khỏe đang được cải tiến theo hướng theo dõi giấc ngủ, nhịp tim, vận động và cảnh báo thói quen sinh hoạt. Người dùng có thể xem dữ liệu hằng ngày để điều chỉnh lối sống khoa học hơn. Tuy nhiên, dữ liệu từ thiết bị đeo chỉ nên xem là thông tin tham khảo, không thay thế chẩn đoán y tế chuyên môn.</p><p>Bài viết thuộc chuyên mục Công nghệ, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 29 HOUR), DATE_SUB(NOW(), INTERVAL 29 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Thiết bị đeo thông minh ngày càng chú trọng sức khỏe cá nhân');

-- 03. Công nghệ
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 1, 'An toàn dữ liệu trở thành ưu tiên của các website hiện đại', '<p>Khi người dùng đăng ký tài khoản, bình luận và lưu lịch sử đọc, hệ thống tin tức cần bảo vệ dữ liệu cá nhân bằng mật khẩu băm, phân quyền truy cập và kiểm tra dữ liệu đầu vào. Một website nhỏ vẫn có thể gặp rủi ro nếu bỏ qua xác thực phiên đăng nhập hoặc để lộ lỗi truy vấn. Vì vậy, bảo mật nên được thiết kế ngay từ đầu thay vì sửa sau khi xảy ra sự cố.</p><p>Bài viết thuộc chuyên mục Công nghệ, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 28 HOUR), DATE_SUB(NOW(), INTERVAL 28 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'An toàn dữ liệu trở thành ưu tiên của các website hiện đại');

-- 04. Công nghệ
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 1, 'Giao diện responsive giúp độc giả đọc tin thuận tiện hơn', '<p>Độc giả hiện nay truy cập tin tức bằng nhiều thiết bị khác nhau, từ điện thoại đến laptop. Giao diện responsive giúp bố cục tự co giãn, chữ dễ đọc và nút bấm dễ thao tác. Với một trang tin, trải nghiệm đọc mượt mà có thể làm tăng thời gian ở lại trang và giảm cảm giác khó chịu khi phải phóng to hoặc kéo ngang màn hình.</p><p>Bài viết thuộc chuyên mục Công nghệ, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 27 HOUR), DATE_SUB(NOW(), INTERVAL 27 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Giao diện responsive giúp độc giả đọc tin thuận tiện hơn');

-- 05. Công nghệ
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 1, 'Chuyển đổi số thúc đẩy các tòa soạn quản lý nội dung hiệu quả', '<p>Các hệ thống CMS giúp biên tập viên soạn bài, lưu nháp, gửi duyệt và quản lý trạng thái xuất bản trên cùng một nền tảng. Quy trình này hạn chế thất lạc nội dung và giúp admin kiểm soát chất lượng trước khi bài hiển thị công khai. Đây cũng là hướng phù hợp cho các website tin tức vừa và nhỏ khi muốn vận hành chuyên nghiệp hơn.</p><p>Bài viết thuộc chuyên mục Công nghệ, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 26 HOUR), DATE_SUB(NOW(), INTERVAL 26 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Chuyển đổi số thúc đẩy các tòa soạn quản lý nội dung hiệu quả');

-- 06. Giải trí
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 2, 'Nội dung ngắn tiếp tục giữ sức hút với khán giả trẻ', '<p>Video ngắn và các bài viết giải trí súc tích đang phù hợp với thói quen xem nhanh trên thiết bị di động. Người dùng thường ưu tiên nội dung dễ hiểu, có điểm nhấn và có thể chia sẻ ngay. Đối với trang tin, chuyên mục giải trí cần tiêu đề hấp dẫn nhưng vẫn tránh giật tít quá mức để giữ uy tín lâu dài.</p><p>Bài viết thuộc chuyên mục Giải trí, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 25 HOUR), DATE_SUB(NOW(), INTERVAL 25 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Nội dung ngắn tiếp tục giữ sức hút với khán giả trẻ');

-- 07. Giải trí
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 2, 'Âm nhạc trực tuyến thay đổi cách nghệ sĩ tiếp cận người nghe', '<p>Nền tảng nghe nhạc số giúp nghệ sĩ phát hành sản phẩm nhanh hơn và đo phản hồi khán giả qua lượt nghe, chia sẻ, bình luận. Thay vì chỉ dựa vào quảng bá truyền thống, nhiều ê-kíp dùng dữ liệu để chọn thời điểm ra mắt và xây dựng chiến dịch truyền thông. Điều này mở ra cơ hội cho nghệ sĩ mới nhưng cũng tạo cạnh tranh lớn hơn.</p><p>Bài viết thuộc chuyên mục Giải trí, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 24 HOUR), DATE_SUB(NOW(), INTERVAL 24 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Âm nhạc trực tuyến thay đổi cách nghệ sĩ tiếp cận người nghe');

-- 08. Giải trí
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 2, 'Phim chiếu rạp chú trọng trải nghiệm cộng đồng', '<p>Dù dịch vụ xem phim trực tuyến phát triển, rạp chiếu vẫn có lợi thế về âm thanh, màn hình lớn và cảm giác xem cùng đám đông. Nhiều bộ phim được truyền thông mạnh qua mạng xã hội trước ngày công chiếu. Với độc giả, các bài đánh giá phim nên cân bằng giữa cảm nhận cá nhân và thông tin không tiết lộ nội dung quan trọng.</p><p>Bài viết thuộc chuyên mục Giải trí, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 23 HOUR), DATE_SUB(NOW(), INTERVAL 23 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Phim chiếu rạp chú trọng trải nghiệm cộng đồng');

-- 09. Giải trí
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 2, 'Trò chơi điện tử trở thành ngành nội dung sáng tạo lớn', '<p>Game không chỉ là hoạt động giải trí mà còn gắn với thể thao điện tử, livestream và cộng đồng người hâm mộ. Nhiều studio chú trọng cốt truyện, đồ họa và trải nghiệm người chơi để giữ chân người dùng. Khi đưa tin về game, trang tin cần phân loại rõ đánh giá sản phẩm, tin cập nhật và thông tin sự kiện.</p><p>Bài viết thuộc chuyên mục Giải trí, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 22 HOUR), DATE_SUB(NOW(), INTERVAL 22 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Trò chơi điện tử trở thành ngành nội dung sáng tạo lớn');

-- 10. Giải trí
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 2, 'Sự kiện văn hóa giải trí được quảng bá mạnh trên mạng xã hội', '<p>Concert, lễ hội âm nhạc và chương trình giao lưu thường tạo hiệu ứng lan tỏa nhanh nhờ hình ảnh và video hậu trường. Mạng xã hội giúp khán giả cập nhật lịch diễn, giá vé và khoảnh khắc nổi bật. Tuy nhiên, người đọc cũng cần kiểm tra thông tin chính thức để tránh tin giả về lịch tổ chức hoặc vé không hợp lệ.</p><p>Bài viết thuộc chuyên mục Giải trí, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 21 HOUR), DATE_SUB(NOW(), INTERVAL 21 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Sự kiện văn hóa giải trí được quảng bá mạnh trên mạng xã hội');

-- 11. Kinh doanh
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 3, 'Doanh nghiệp nhỏ tăng bán hàng nhờ kênh trực tuyến', '<p>Nhiều cửa hàng nhỏ sử dụng website, sàn thương mại điện tử và mạng xã hội để tiếp cận khách hàng ngoài khu vực địa phương. Việc đo lường lượt xem, đơn hàng và phản hồi giúp chủ shop điều chỉnh sản phẩm nhanh hơn. Kinh doanh trực tuyến hiệu quả cần kết hợp nội dung rõ ràng, chăm sóc khách hàng tốt và quản lý tồn kho chính xác.</p><p>Bài viết thuộc chuyên mục Kinh doanh, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 20 HOUR), DATE_SUB(NOW(), INTERVAL 20 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Doanh nghiệp nhỏ tăng bán hàng nhờ kênh trực tuyến');

-- 12. Kinh doanh
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 3, 'Thanh toán số trở thành thói quen trong mua sắm hằng ngày', '<p>Ví điện tử, chuyển khoản nhanh và mã QR giúp giao dịch tiện lợi hơn, đặc biệt với người trẻ. Doanh nghiệp có thêm dữ liệu để theo dõi doanh thu và giảm phụ thuộc vào tiền mặt. Tuy vậy, người dùng cần bảo vệ mã OTP, mật khẩu và chỉ thanh toán qua kênh đáng tin cậy.</p><p>Bài viết thuộc chuyên mục Kinh doanh, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 19 HOUR), DATE_SUB(NOW(), INTERVAL 19 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Thanh toán số trở thành thói quen trong mua sắm hằng ngày');

-- 13. Kinh doanh
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 3, 'Dữ liệu khách hàng hỗ trợ cá nhân hóa khuyến mãi', '<p>Khi doanh nghiệp hiểu lịch sử mua hàng và sở thích của khách, chương trình khuyến mãi có thể được thiết kế đúng nhu cầu hơn. Cá nhân hóa giúp giảm lãng phí quảng cáo đại trà và tăng khả năng quay lại mua hàng. Điều quan trọng là doanh nghiệp phải minh bạch cách sử dụng dữ liệu và tôn trọng quyền riêng tư.</p><p>Bài viết thuộc chuyên mục Kinh doanh, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 18 HOUR), DATE_SUB(NOW(), INTERVAL 18 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Dữ liệu khách hàng hỗ trợ cá nhân hóa khuyến mãi');

-- 14. Kinh doanh
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 3, 'Xu hướng làm việc linh hoạt ảnh hưởng đến thị trường dịch vụ', '<p>Làm việc từ xa và mô hình kết hợp khiến nhu cầu về phần mềm quản lý, không gian làm việc chung và thiết bị cá nhân tăng lên. Nhiều doanh nghiệp điều chỉnh chính sách nhân sự để giữ hiệu quả nhưng vẫn đáp ứng mong muốn linh hoạt của nhân viên. Đây là cơ hội cho các dịch vụ hỗ trợ năng suất và đào tạo kỹ năng số.</p><p>Bài viết thuộc chuyên mục Kinh doanh, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 17 HOUR), DATE_SUB(NOW(), INTERVAL 17 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Xu hướng làm việc linh hoạt ảnh hưởng đến thị trường dịch vụ');

-- 15. Kinh doanh
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 3, 'Thương hiệu chú trọng trải nghiệm sau bán hàng', '<p>Khách hàng không chỉ quan tâm giá mà còn đánh giá cách doanh nghiệp hỗ trợ đổi trả, phản hồi khiếu nại và chăm sóc sau mua. Một trải nghiệm tốt có thể biến khách hàng thành người giới thiệu tự nhiên cho thương hiệu. Vì vậy, quản trị quan hệ khách hàng đang trở thành một phần quan trọng trong chiến lược kinh doanh.</p><p>Bài viết thuộc chuyên mục Kinh doanh, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 16 HOUR), DATE_SUB(NOW(), INTERVAL 16 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Thương hiệu chú trọng trải nghiệm sau bán hàng');

-- 16. Thể thao
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 4, 'Phong trào chạy bộ đô thị thu hút nhiều người trẻ', '<p>Các giải chạy cộng đồng ngày càng phổ biến vì dễ tham gia, phù hợp nhiều lứa tuổi và tạo động lực rèn luyện. Người mới bắt đầu thường chọn cự ly ngắn, sau đó tăng dần theo thể lực. Để tránh chấn thương, người chạy nên khởi động kỹ, chọn giày phù hợp và có kế hoạch nghỉ ngơi hợp lý.</p><p>Bài viết thuộc chuyên mục Thể thao, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 15 HOUR), DATE_SUB(NOW(), INTERVAL 15 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Phong trào chạy bộ đô thị thu hút nhiều người trẻ');

-- 17. Thể thao
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 4, 'Bóng đá trẻ cần đầu tư dài hạn từ đào tạo cơ bản', '<p>Thành công của một đội bóng không chỉ phụ thuộc vào cầu thủ đội một mà còn đến từ hệ thống đào tạo trẻ. Các học viện cần chú trọng kỹ thuật, thể lực, tư duy chiến thuật và giáo dục kỷ luật. Khi nền tảng trẻ tốt, câu lạc bộ có thể phát triển lực lượng ổn định và giảm chi phí chuyển nhượng.</p><p>Bài viết thuộc chuyên mục Thể thao, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 14 HOUR), DATE_SUB(NOW(), INTERVAL 14 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Bóng đá trẻ cần đầu tư dài hạn từ đào tạo cơ bản');

-- 18. Thể thao
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 4, 'Thể thao điện tử mở rộng tệp khán giả mới', '<p>Esports thu hút lượng lớn người xem qua livestream và các giải đấu trực tuyến. Người chơi chuyên nghiệp cần luyện tập chiến thuật, phản xạ và phối hợp đội không khác nhiều môn thể thao truyền thống về tính kỷ luật. Với truyền thông, esports là mảng nội dung hấp dẫn vì kết hợp công nghệ, giải trí và cạnh tranh.</p><p>Bài viết thuộc chuyên mục Thể thao, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 13 HOUR), DATE_SUB(NOW(), INTERVAL 13 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Thể thao điện tử mở rộng tệp khán giả mới');

-- 19. Thể thao
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 4, 'Dinh dưỡng đóng vai trò quan trọng trong tập luyện', '<p>Người tập thể thao cần cân bằng năng lượng, nước uống và thời gian phục hồi. Một chế độ ăn phù hợp giúp cải thiện sức bền và giảm mệt mỏi sau buổi tập. Thay vì chạy theo thực đơn cực đoan, người tập nên chọn mục tiêu rõ ràng và điều chỉnh theo thể trạng cá nhân.</p><p>Bài viết thuộc chuyên mục Thể thao, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 12 HOUR), DATE_SUB(NOW(), INTERVAL 12 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Dinh dưỡng đóng vai trò quan trọng trong tập luyện');

-- 20. Thể thao
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 4, 'Các ứng dụng theo dõi luyện tập giúp người dùng duy trì thói quen', '<p>Ứng dụng thể thao có thể ghi lại quãng đường, nhịp tim, lượng calo và tiến độ luyện tập. Việc nhìn thấy dữ liệu tiến bộ theo thời gian giúp người dùng có thêm động lực. Tuy nhiên, dữ liệu chỉ là công cụ hỗ trợ, còn yếu tố quyết định vẫn là lịch tập đều đặn và lối sống lành mạnh.</p><p>Bài viết thuộc chuyên mục Thể thao, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 11 HOUR), DATE_SUB(NOW(), INTERVAL 11 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Các ứng dụng theo dõi luyện tập giúp người dùng duy trì thói quen');

-- 21. Sức khỏe
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 5, 'Giấc ngủ chất lượng giúp cải thiện hiệu suất học tập và làm việc', '<p>Ngủ đủ và đúng giờ giúp cơ thể phục hồi, tăng khả năng tập trung và ổn định cảm xúc. Nhiều người trẻ có thói quen dùng điện thoại trước khi ngủ, làm giảm chất lượng nghỉ ngơi. Việc hạn chế màn hình, giữ phòng ngủ yên tĩnh và duy trì lịch ngủ cố định có thể tạo thay đổi tích cực.</p><p>Bài viết thuộc chuyên mục Sức khỏe, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 10 HOUR), DATE_SUB(NOW(), INTERVAL 10 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Giấc ngủ chất lượng giúp cải thiện hiệu suất học tập và làm việc');

-- 22. Sức khỏe
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 5, 'Ăn uống cân bằng quan trọng hơn các xu hướng giảm cân nhanh', '<p>Các chế độ ăn kiêng cực đoan có thể tạo kết quả ngắn hạn nhưng khó duy trì và có nguy cơ thiếu chất. Một bữa ăn cân bằng nên có tinh bột phù hợp, đạm, rau củ và chất béo lành mạnh. Người muốn giảm cân nên ưu tiên thay đổi thói quen bền vững thay vì phụ thuộc vào mẹo lan truyền trên mạng.</p><p>Bài viết thuộc chuyên mục Sức khỏe, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 9 HOUR), DATE_SUB(NOW(), INTERVAL 9 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Ăn uống cân bằng quan trọng hơn các xu hướng giảm cân nhanh');

-- 23. Sức khỏe
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 5, 'Sức khỏe tinh thần được quan tâm nhiều hơn trong đời sống hiện đại', '<p>Áp lực học tập, công việc và mạng xã hội có thể ảnh hưởng đến tâm trạng của nhiều người. Việc trò chuyện với người thân, nghỉ ngơi hợp lý và tìm hỗ trợ chuyên môn khi cần là rất quan trọng. Truyền thông về sức khỏe tinh thần nên dùng ngôn ngữ tôn trọng, tránh kỳ thị và không đơn giản hóa vấn đề.</p><p>Bài viết thuộc chuyên mục Sức khỏe, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 8 HOUR), DATE_SUB(NOW(), INTERVAL 8 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Sức khỏe tinh thần được quan tâm nhiều hơn trong đời sống hiện đại');

-- 24. Sức khỏe
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 5, 'Vận động nhẹ mỗi ngày giúp giảm tác hại của ngồi lâu', '<p>Ngồi nhiều trước máy tính có thể gây mỏi cổ, đau lưng và giảm năng lượng. Những khoảng nghỉ ngắn để đi lại, kéo giãn hoặc tập vài động tác đơn giản giúp cơ thể linh hoạt hơn. Với học sinh, sinh viên và nhân viên văn phòng, thói quen vận động nhỏ nhưng đều đặn có thể tạo lợi ích rõ rệt.</p><p>Bài viết thuộc chuyên mục Sức khỏe, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 7 HOUR), DATE_SUB(NOW(), INTERVAL 7 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Vận động nhẹ mỗi ngày giúp giảm tác hại của ngồi lâu');

-- 25. Sức khỏe
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 5, 'Thông tin y tế trên mạng cần được kiểm chứng cẩn thận', '<p>Người dùng thường tìm kiếm triệu chứng và cách chăm sóc sức khỏe trên internet. Tuy nhiên, không phải nội dung nào cũng chính xác hoặc phù hợp với từng trường hợp. Khi gặp dấu hiệu bất thường, người đọc nên tham khảo nguồn đáng tin cậy và liên hệ nhân viên y tế thay vì tự điều trị theo bài viết lan truyền.</p><p>Bài viết thuộc chuyên mục Sức khỏe, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 6 HOUR), DATE_SUB(NOW(), INTERVAL 6 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Thông tin y tế trên mạng cần được kiểm chứng cẩn thận');

-- 26. Giáo dục
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 6, 'Học trực tuyến trở thành kỹ năng quan trọng của sinh viên', '<p>Các nền tảng học trực tuyến giúp sinh viên tiếp cận bài giảng, tài liệu và bài tập ở bất kỳ đâu. Để học hiệu quả, người học cần tự quản lý thời gian, ghi chú và đặt mục tiêu rõ ràng. Mô hình kết hợp giữa học trực tiếp và trực tuyến có thể tận dụng ưu điểm của cả hai hình thức.</p><p>Bài viết thuộc chuyên mục Giáo dục, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 5 HOUR), DATE_SUB(NOW(), INTERVAL 5 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Học trực tuyến trở thành kỹ năng quan trọng của sinh viên');

-- 27. Giáo dục
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 6, 'Kỹ năng số là lợi thế khi tham gia thị trường lao động', '<p>Nhà tuyển dụng ngày càng đánh giá cao khả năng sử dụng công cụ số, phân tích dữ liệu cơ bản và giao tiếp trực tuyến chuyên nghiệp. Sinh viên có thể bắt đầu từ những kỹ năng gần gũi như quản lý file, làm việc nhóm trên nền tảng số và trình bày dữ liệu. Đây là nền tảng giúp thích nghi nhanh với môi trường làm việc hiện đại.</p><p>Bài viết thuộc chuyên mục Giáo dục, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 4 HOUR), DATE_SUB(NOW(), INTERVAL 4 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Kỹ năng số là lợi thế khi tham gia thị trường lao động');

-- 28. Giáo dục
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 6, 'Dự án nhóm giúp sinh viên rèn kỹ năng phân tích hệ thống', '<p>Khi làm dự án, sinh viên phải xác định yêu cầu, phân chia nhiệm vụ, thiết kế cơ sở dữ liệu và kiểm thử chức năng. Quá trình này giúp kết nối lý thuyết với sản phẩm thực tế. Nếu nhóm ghi chép rõ use case, sơ đồ và quy trình làm việc, việc báo cáo cuối kỳ sẽ dễ thuyết phục hơn.</p><p>Bài viết thuộc chuyên mục Giáo dục, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 3 HOUR), DATE_SUB(NOW(), INTERVAL 3 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Dự án nhóm giúp sinh viên rèn kỹ năng phân tích hệ thống');

-- 29. Giáo dục
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 6, 'Đọc hiểu tài liệu chuyên ngành cần phương pháp phù hợp', '<p>Nhiều sinh viên gặp khó khi đọc tài liệu dài hoặc có nhiều thuật ngữ. Một cách hiệu quả là đọc mục tiêu trước, đánh dấu khái niệm chính, sau đó tóm tắt lại bằng ngôn ngữ của mình. Việc liên hệ ví dụ thực tế cũng giúp người học nhớ lâu hơn so với chỉ học thuộc định nghĩa.</p><p>Bài viết thuộc chuyên mục Giáo dục, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 2 HOUR), DATE_SUB(NOW(), INTERVAL 2 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Đọc hiểu tài liệu chuyên ngành cần phương pháp phù hợp');

-- 30. Giáo dục
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 6, 'Tư duy phản biện giúp người học chọn lọc thông tin', '<p>Trong môi trường nhiều nguồn tin, người học cần biết đặt câu hỏi về nguồn, bằng chứng và mục đích của thông tin. Tư duy phản biện không có nghĩa là phủ nhận mọi thứ, mà là kiểm tra trước khi tin và biết so sánh nhiều góc nhìn. Đây là kỹ năng quan trọng cho học tập, nghiên cứu và làm việc.</p><p>Bài viết thuộc chuyên mục Giáo dục, dùng để kiểm thử dữ liệu thật trong trang chủ, tìm kiếm, bài nổi bật và bài mới của SmartNews.</p>', 'published', DATE_SUB(NOW(), INTERVAL 1 HOUR), DATE_SUB(NOW(), INTERVAL 1 HOUR)
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Tư duy phản biện giúp người học chọn lọc thông tin');

-- Gắn thẻ cơ bản cho các bài vừa seed.
INSERT IGNORE INTO post_tags (post_id, tag_id)
SELECT p.id, 7 FROM posts p WHERE p.user_id = @author_id AND p.status = 'published';
INSERT IGNORE INTO post_tags (post_id, tag_id)
SELECT p.id, 8 FROM posts p WHERE p.user_id = @author_id AND p.status = 'published' ORDER BY p.created_at DESC LIMIT 6;

SELECT COUNT(*) AS total_published_posts FROM posts WHERE status = 'published';

USE smartnews;
-- Bản seed bổ sung bài viết cho chức năng tìm kiếm, chuyên mục và chi tiết bài.
-- Chạy file này sau schema.sql, schema_task2_posts.sql và sau khi đã có ít nhất 1 user/admin/editor.
INSERT IGNORE INTO categories (id, name, description) VALUES
(1, 'Công nghệ', 'Tin tức công nghệ, AI, phần mềm và thiết bị số'),
(2, 'Giải trí', 'Tin tức phim ảnh, âm nhạc và đời sống giải trí'),
(3, 'Kinh doanh', 'Tin thị trường, kinh tế, tài chính và doanh nghiệp'),
(4, 'Thể thao', 'Tin tức thể thao trong nước và quốc tế'),
(5, 'Sức khỏe', 'Tin sức khỏe, lối sống và dinh dưỡng'),
(6, 'Giáo dục', 'Tin giáo dục, tuyển sinh và học tập'),
(7, 'Thời sự', 'Tin tức thời sự trong ngày'),
(8, 'Chính trị', 'Tin chính trị và chính sách'),
(9, 'Thế giới', 'Tin tức quốc tế'),
(10, 'Tài chính', 'Tin tài chính, ngân hàng, chứng khoán và kinh tế'),
(11, 'AI & Chuyển đổi số', 'Tin AI, công nghệ số và chuyển đổi số'),
(12, 'Khoa học', 'Tin khoa học và nghiên cứu'),
(13, 'Đời sống', 'Tin đời sống xã hội'),
(14, 'Du lịch', 'Tin du lịch và trải nghiệm'),
(15, 'Văn hóa', 'Tin văn hóa'),
(16, 'Pháp luật', 'Tin pháp luật'),
(17, 'Bất động sản', 'Tin bất động sản');
SET @author_id = (SELECT id FROM users ORDER BY id ASC LIMIT 1);

-- 1. Công nghệ
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 1, 'AI hỗ trợ biên tập tin tức nhanh và chính xác hơn', '<p>AI đang được nhiều tòa soạn và website nội dung sử dụng để gợi ý tiêu đề, tóm tắt bản tin và phân loại chuyên mục. Với SmartNews, việc áp dụng AI ở mức cơ bản có thể giúp người biên tập tiết kiệm thời gian, đồng thời giúp người đọc dễ tìm được nội dung phù hợp hơn.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Công nghệ, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 39 HOUR), DATE_SUB(NOW(), INTERVAL 39 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'AI hỗ trợ biên tập tin tức nhanh và chính xác hơn');

-- 2. Công nghệ
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 1, 'Bảo mật tài khoản người dùng là ưu tiên của website hiện đại', '<p>Khi người dùng đăng ký, đăng nhập, bình luận và lưu lịch sử đọc, hệ thống cần bảo vệ mật khẩu, phiên đăng nhập và dữ liệu cá nhân. Một website tin tức tốt không chỉ có giao diện đẹp mà còn cần quy trình xử lý dữ liệu an toàn.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Công nghệ, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 38 HOUR), DATE_SUB(NOW(), INTERVAL 38 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Bảo mật tài khoản người dùng là ưu tiên của website hiện đại');

-- 3. Giải trí
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 2, 'Video ngắn tiếp tục thay đổi thói quen xem nội dung', '<p>Nội dung giải trí dạng ngắn phù hợp với thói quen sử dụng điện thoại và mạng xã hội. Người xem thường ưu tiên các nội dung nhanh, dễ hiểu và có khả năng chia sẻ cao, khiến các nền tảng giải trí phải thay đổi cách trình bày thông tin.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Giải trí, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 37 HOUR), DATE_SUB(NOW(), INTERVAL 37 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Video ngắn tiếp tục thay đổi thói quen xem nội dung');

-- 4. Giải trí
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 2, 'Âm nhạc trực tuyến mở rộng cơ hội tiếp cận khán giả trẻ', '<p>Các nền tảng nghe nhạc và mạng xã hội giúp ca sĩ, nhà sản xuất và người sáng tạo nội dung tiếp cận khán giả dễ hơn. Xu hướng này cũng tạo ra nhu cầu cập nhật tin giải trí nhanh, chọn lọc và có bối cảnh rõ ràng.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Giải trí, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 36 HOUR), DATE_SUB(NOW(), INTERVAL 36 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Âm nhạc trực tuyến mở rộng cơ hội tiếp cận khán giả trẻ');

-- 5. Kinh doanh
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 3, 'Doanh nghiệp nhỏ tăng tốc nhờ chuyển đổi số', '<p>Bán hàng trực tuyến, thanh toán điện tử và quản lý dữ liệu khách hàng đang giúp nhiều doanh nghiệp nhỏ hoạt động linh hoạt hơn. Khi biết tận dụng công cụ số, doanh nghiệp có thể giảm chi phí vận hành và tiếp cận khách hàng hiệu quả hơn.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Kinh doanh, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 35 HOUR), DATE_SUB(NOW(), INTERVAL 35 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Doanh nghiệp nhỏ tăng tốc nhờ chuyển đổi số');

-- 6. Kinh doanh
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 3, 'Thương mại điện tử thúc đẩy cạnh tranh dịch vụ khách hàng', '<p>Người mua trực tuyến ngày càng quan tâm đến tốc độ giao hàng, chất lượng tư vấn và chính sách đổi trả. Vì vậy, doanh nghiệp cần đầu tư vào trải nghiệm khách hàng thay vì chỉ tập trung vào giá bán.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Kinh doanh, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 34 HOUR), DATE_SUB(NOW(), INTERVAL 34 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Thương mại điện tử thúc đẩy cạnh tranh dịch vụ khách hàng');

-- 7. Thể thao
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 4, 'Chạy bộ phong trào thu hút nhiều người trẻ tham gia', '<p>Chạy bộ là môn thể thao dễ bắt đầu, chi phí thấp và phù hợp với nhiều độ tuổi. Các câu lạc bộ chạy bộ giúp người tham gia duy trì động lực, rèn luyện sức khỏe và mở rộng mối quan hệ trong cộng đồng.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Thể thao, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 33 HOUR), DATE_SUB(NOW(), INTERVAL 33 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Chạy bộ phong trào thu hút nhiều người trẻ tham gia');

-- 8. Thể thao
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 4, 'Công nghệ hỗ trợ theo dõi hiệu quả luyện tập thể thao', '<p>Đồng hồ thông minh và ứng dụng thể thao giúp người dùng ghi lại quãng đường, nhịp tim, calo và thời gian luyện tập. Dữ liệu này giúp người tập hiểu cơ thể tốt hơn và điều chỉnh kế hoạch tập luyện phù hợp.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Thể thao, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 32 HOUR), DATE_SUB(NOW(), INTERVAL 32 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Công nghệ hỗ trợ theo dõi hiệu quả luyện tập thể thao');

-- 9. Sức khỏe
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 5, 'Ngủ đủ giấc giúp cải thiện khả năng tập trung', '<p>Giấc ngủ có vai trò quan trọng đối với trí nhớ, cảm xúc và hiệu suất học tập. Việc dùng điện thoại quá lâu trước khi ngủ có thể làm giảm chất lượng nghỉ ngơi, vì vậy người đọc nên xây dựng thói quen ngủ ổn định.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Sức khỏe, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 31 HOUR), DATE_SUB(NOW(), INTERVAL 31 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Ngủ đủ giấc giúp cải thiện khả năng tập trung');

-- 10. Sức khỏe
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 5, 'Dinh dưỡng cân bằng quan trọng hơn các xu hướng ăn kiêng nhanh', '<p>Một chế độ ăn hợp lý cần kết hợp tinh bột, đạm, rau củ, chất béo lành mạnh và đủ nước. Các xu hướng giảm cân quá nhanh thường khó duy trì, trong khi thay đổi thói quen bền vững sẽ tốt hơn cho sức khỏe lâu dài.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Sức khỏe, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 30 HOUR), DATE_SUB(NOW(), INTERVAL 30 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Dinh dưỡng cân bằng quan trọng hơn các xu hướng ăn kiêng nhanh');

-- 11. Giáo dục
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 6, 'Học trực tuyến giúp sinh viên chủ động quản lý thời gian', '<p>Nền tảng học trực tuyến cho phép người học xem lại bài giảng, làm bài tập và tự theo dõi tiến độ. Tuy nhiên, để học hiệu quả, sinh viên cần đặt mục tiêu rõ ràng và tránh trì hoãn khi không có người nhắc nhở trực tiếp.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Giáo dục, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 29 HOUR), DATE_SUB(NOW(), INTERVAL 29 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Học trực tuyến giúp sinh viên chủ động quản lý thời gian');

-- 12. Giáo dục
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 6, 'Kỹ năng số trở thành lợi thế quan trọng của sinh viên', '<p>Biết sử dụng công cụ số, làm việc nhóm trực tuyến và trình bày dữ liệu là lợi thế khi sinh viên tham gia thị trường lao động. Đây cũng là nền tảng giúp người học thích nghi nhanh với môi trường làm việc hiện đại.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Giáo dục, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 28 HOUR), DATE_SUB(NOW(), INTERVAL 28 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Kỹ năng số trở thành lợi thế quan trọng của sinh viên');

-- 13. Thời sự
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 7, 'Tin thời sự cần được trình bày nhanh nhưng vẫn có kiểm chứng', '<p>Người đọc thường muốn nắm thông tin mới trong thời gian ngắn, nhưng tốc độ không nên đánh đổi bằng độ chính xác. Một bài thời sự tốt cần nêu rõ sự kiện, bối cảnh và tác động chính đến đời sống xã hội.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Thời sự, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 27 HOUR), DATE_SUB(NOW(), INTERVAL 27 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Tin thời sự cần được trình bày nhanh nhưng vẫn có kiểm chứng');

-- 14. Thời sự
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 7, 'Cập nhật đời sống xã hội giúp người dân theo dõi thay đổi quanh mình', '<p>Các thay đổi về giao thông, giáo dục, dịch vụ công và đời sống đô thị thường ảnh hưởng trực tiếp đến người dân. Tin thời sự nên được trình bày dễ hiểu để người đọc nhanh chóng nắm được vấn đề.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Thời sự, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 26 HOUR), DATE_SUB(NOW(), INTERVAL 26 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Cập nhật đời sống xã hội giúp người dân theo dõi thay đổi quanh mình');

-- 15. Chính trị
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 8, 'Chính sách công cần được giải thích bằng ngôn ngữ dễ hiểu', '<p>Nhiều chính sách có tác động trực tiếp đến học tập, lao động, kinh doanh và đời sống. Việc diễn giải chính sách bằng ngôn ngữ rõ ràng giúp người dân hiểu quyền lợi, trách nhiệm và cách áp dụng trong thực tế.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Chính trị, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 25 HOUR), DATE_SUB(NOW(), INTERVAL 25 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Chính sách công cần được giải thích bằng ngôn ngữ dễ hiểu');

-- 16. Chính trị
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 8, 'Truyền thông chính sách góp phần tăng tính minh bạch', '<p>Khi thông tin chính sách được công bố rõ ràng và có giải thích cụ thể, người dân dễ tiếp cận hơn. Các website tin tức có thể đóng vai trò cầu nối bằng cách tóm tắt điểm chính và cung cấp bối cảnh cần thiết.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Chính trị, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 24 HOUR), DATE_SUB(NOW(), INTERVAL 24 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Truyền thông chính sách góp phần tăng tính minh bạch');

-- 17. Thế giới
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 9, 'Các xu hướng quốc tế ảnh hưởng đến kinh tế và giáo dục trong nước', '<p>Những biến động toàn cầu về công nghệ, thương mại và nhân lực có thể tạo tác động gián tiếp đến doanh nghiệp và người học. Theo dõi tin thế giới giúp độc giả hiểu thêm bối cảnh bên ngoài quốc gia.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Thế giới, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 23 HOUR), DATE_SUB(NOW(), INTERVAL 23 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Các xu hướng quốc tế ảnh hưởng đến kinh tế và giáo dục trong nước');

-- 18. Thế giới
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 9, 'Hợp tác quốc tế mở ra nhiều cơ hội học tập và làm việc', '<p>Trao đổi sinh viên, chương trình học bổng và hợp tác doanh nghiệp xuyên biên giới đang tạo thêm lựa chọn cho người trẻ. Tin thế giới vì vậy không chỉ là sự kiện xa xôi mà còn liên quan đến định hướng tương lai.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Thế giới, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 22 HOUR), DATE_SUB(NOW(), INTERVAL 22 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Hợp tác quốc tế mở ra nhiều cơ hội học tập và làm việc');

-- 19. Tài chính
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 10, 'Quản lý chi tiêu cá nhân cần bắt đầu từ thói quen nhỏ', '<p>Ghi chép thu nhập, chi phí cố định và khoản tiết kiệm giúp mỗi người hiểu rõ tình hình tài chính của mình. Khi có kế hoạch rõ ràng, người dùng sẽ dễ tránh chi tiêu cảm tính và chuẩn bị tốt hơn cho mục tiêu dài hạn.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Tài chính, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 21 HOUR), DATE_SUB(NOW(), INTERVAL 21 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Quản lý chi tiêu cá nhân cần bắt đầu từ thói quen nhỏ');

-- 20. Tài chính
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 10, 'Thanh toán số thay đổi cách người dùng quản lý tiền hằng ngày', '<p>Ví điện tử, ngân hàng số và thanh toán không tiền mặt giúp giao dịch nhanh hơn. Tuy nhiên, người dùng cũng cần chú ý bảo mật tài khoản, kiểm tra thông báo giao dịch và tránh chia sẻ mã xác thực.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Tài chính, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 20 HOUR), DATE_SUB(NOW(), INTERVAL 20 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Thanh toán số thay đổi cách người dùng quản lý tiền hằng ngày');

-- 21. AI & Chuyển đổi số
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 11, 'Chuyển đổi số giúp website tin tức cá nhân hóa trải nghiệm đọc', '<p>Khi hệ thống ghi nhận lịch sử đọc, chuyên mục yêu thích và hành vi tương tác, website có thể đề xuất bài viết phù hợp hơn. Đây là bước quan trọng để SmartNews phát triển chức năng gợi ý nội dung thông minh.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục AI & Chuyển đổi số, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 19 HOUR), DATE_SUB(NOW(), INTERVAL 19 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Chuyển đổi số giúp website tin tức cá nhân hóa trải nghiệm đọc');

-- 22. AI & Chuyển đổi số
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 11, 'AI có thể hỗ trợ phân loại bình luận và phát hiện nội dung tiêu cực', '<p>Bình luận là nơi người đọc tương tác với bài viết nhưng cũng cần được quản lý. AI ở mức cơ bản có thể hỗ trợ nhận diện sắc thái tích cực, trung lập hoặc tiêu cực, giúp quản trị viên kiểm soát cộng đồng tốt hơn.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục AI & Chuyển đổi số, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 18 HOUR), DATE_SUB(NOW(), INTERVAL 18 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'AI có thể hỗ trợ phân loại bình luận và phát hiện nội dung tiêu cực');

-- 23. Khoa học
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 12, 'Phổ biến khoa học cần gần gũi với đời sống hằng ngày', '<p>Khoa học xuất hiện trong y tế, môi trường, giáo dục và công nghệ. Khi nội dung khoa học được giải thích bằng ví dụ đơn giản, người đọc sẽ dễ hiểu hơn và có thể ứng dụng vào quyết định hằng ngày.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Khoa học, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 17 HOUR), DATE_SUB(NOW(), INTERVAL 17 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Phổ biến khoa học cần gần gũi với đời sống hằng ngày');

-- 24. Khoa học
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 12, 'Nghiên cứu môi trường được quan tâm trong phát triển đô thị', '<p>Chất lượng không khí, nước, cây xanh và rác thải là những chủ đề gắn với đời sống thành phố. Tin khoa học về môi trường giúp người đọc hiểu vì sao phát triển bền vững cần dựa trên dữ liệu và nghiên cứu.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Khoa học, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 16 HOUR), DATE_SUB(NOW(), INTERVAL 16 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Nghiên cứu môi trường được quan tâm trong phát triển đô thị');

-- 25. Đời sống
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 13, 'Dịch vụ số thay đổi nhịp sống đô thị hiện đại', '<p>Đặt xe, giao hàng, mua sắm trực tuyến và thanh toán số giúp người dân tiết kiệm thời gian. Tuy nhiên, sự tiện lợi này cũng đòi hỏi người dùng biết chọn lọc thông tin và bảo vệ dữ liệu cá nhân.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Đời sống, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 15 HOUR), DATE_SUB(NOW(), INTERVAL 15 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Dịch vụ số thay đổi nhịp sống đô thị hiện đại');

-- 26. Đời sống
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 13, 'Không gian công cộng góp phần nâng cao chất lượng sống', '<p>Công viên, thư viện, đường đi bộ và khu sinh hoạt cộng đồng giúp người dân có nơi thư giãn, vận động và kết nối. Đời sống đô thị không chỉ được đo bằng tốc độ phát triển mà còn bằng trải nghiệm của người dân.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Đời sống, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 14 HOUR), DATE_SUB(NOW(), INTERVAL 14 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Không gian công cộng góp phần nâng cao chất lượng sống');

-- 27. Du lịch
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 14, 'Du lịch trải nghiệm được nhiều bạn trẻ lựa chọn', '<p>Thay vì chỉ tham quan điểm nổi tiếng, nhiều người trẻ muốn tìm hiểu văn hóa địa phương, ẩm thực và hoạt động cộng đồng. Xu hướng này giúp chuyến đi có nhiều ý nghĩa hơn và tạo sự kết nối với nơi đến.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Du lịch, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 13 HOUR), DATE_SUB(NOW(), INTERVAL 13 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Du lịch trải nghiệm được nhiều bạn trẻ lựa chọn');

-- 28. Du lịch
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 14, 'Lập kế hoạch kỹ giúp chuyến đi tiết kiệm và an toàn hơn', '<p>Trước khi đi du lịch, người đọc nên kiểm tra phương tiện, nơi ở, lịch trình, chi phí và các quy định địa phương. Chuẩn bị tốt giúp hạn chế rủi ro và tận hưởng chuyến đi thoải mái hơn.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Du lịch, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 12 HOUR), DATE_SUB(NOW(), INTERVAL 12 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Lập kế hoạch kỹ giúp chuyến đi tiết kiệm và an toàn hơn');

-- 29. Văn hóa
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 15, 'Văn hóa đọc thay đổi trong thời đại nội dung số', '<p>Người đọc hiện có thể tiếp cận sách giấy, sách điện tử, báo mạng và video kiến thức. Sự đa dạng này tạo nhiều lựa chọn nhưng cũng đòi hỏi khả năng chọn lọc nguồn đáng tin cậy.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Văn hóa, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 11 HOUR), DATE_SUB(NOW(), INTERVAL 11 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Văn hóa đọc thay đổi trong thời đại nội dung số');

-- 30. Văn hóa
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 15, 'Lễ hội địa phương góp phần giữ gìn bản sắc cộng đồng', '<p>Các lễ hội giúp người dân kết nối với lịch sử, phong tục và đời sống tinh thần. Khi được tổ chức phù hợp, lễ hội không chỉ thu hút du khách mà còn giúp thế hệ trẻ hiểu hơn về văn hóa địa phương.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Văn hóa, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 10 HOUR), DATE_SUB(NOW(), INTERVAL 10 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Lễ hội địa phương góp phần giữ gìn bản sắc cộng đồng');

-- 31. Pháp luật
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 16, 'Hiểu quy định pháp luật giúp người dân bảo vệ quyền lợi', '<p>Những quy định về giao thông, lao động, mua bán trực tuyến và an toàn thông tin đều liên quan đến đời sống. Tin pháp luật nên được diễn giải rõ ràng để người đọc hiểu cách áp dụng vào tình huống thực tế.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Pháp luật, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 9 HOUR), DATE_SUB(NOW(), INTERVAL 9 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Hiểu quy định pháp luật giúp người dân bảo vệ quyền lợi');

-- 32. Pháp luật
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 16, 'An toàn trên môi trường mạng cần sự chủ động của người dùng', '<p>Người dùng nên cẩn trọng với đường link lạ, yêu cầu chuyển tiền và các tin nhắn giả mạo. Bên cạnh quy định pháp luật, thói quen kiểm chứng thông tin là yếu tố quan trọng để phòng tránh rủi ro.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Pháp luật, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 8 HOUR), DATE_SUB(NOW(), INTERVAL 8 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'An toàn trên môi trường mạng cần sự chủ động của người dùng');

-- 33. Bất động sản
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 17, 'Nhu cầu ở thực tác động đến lựa chọn bất động sản', '<p>Người mua nhà ngày càng quan tâm đến vị trí, pháp lý, hạ tầng và tiện ích xung quanh. Một quyết định tốt cần dựa trên khả năng tài chính, nhu cầu sử dụng thật và thông tin minh bạch.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Bất động sản, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 7 HOUR), DATE_SUB(NOW(), INTERVAL 7 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Nhu cầu ở thực tác động đến lựa chọn bất động sản');

-- 34. Bất động sản
INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT @author_id, 17, 'Hạ tầng giao thông ảnh hưởng đến giá trị khu vực nhà ở', '<p>Khi hạ tầng giao thông được cải thiện, việc di chuyển thuận tiện hơn và khu vực xung quanh có thể thu hút thêm dịch vụ. Tuy nhiên, người mua vẫn cần kiểm tra pháp lý và đánh giá rủi ro trước khi quyết định.</p><h2>Bối cảnh</h2><p>Bài viết thuộc chuyên mục Bất động sản, dùng để kiểm thử chức năng lọc chuyên mục, tìm kiếm và đọc chi tiết trong SmartNews.</p><h2>Nhận định</h2><p>Nội dung được trình bày theo hướng rõ ràng, dễ hiểu, phù hợp với website tin tức dành cho người dùng phổ thông.</p><h2>Kết luận</h2><p>Việc có nhiều bài trong cùng một chuyên mục giúp hệ thống hiển thị bài liên quan tốt hơn khi người đọc mở trang chi tiết.</p>', 'published', DATE_SUB(NOW(), INTERVAL 6 HOUR), DATE_SUB(NOW(), INTERVAL 6 HOUR)
WHERE @author_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM posts WHERE title = 'Hạ tầng giao thông ảnh hưởng đến giá trị khu vực nhà ở');

SELECT COUNT(*) AS total_published_posts FROM posts WHERE status = 'published';
