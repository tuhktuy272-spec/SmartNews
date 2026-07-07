USE smartnews;

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

SET @author_id := COALESCE(
    (SELECT u.id
     FROM users u
     INNER JOIN roles r ON r.id = u.role_id
     WHERE r.name IN ('admin', 'editor')
     ORDER BY FIELD(r.name, 'admin', 'editor'), u.id ASC
     LIMIT 1),
    (SELECT id FROM users ORDER BY id ASC LIMIT 1)
);

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Kinh tế số tạo động lực mới cho doanh nghiệp Việt',
    '<p>Kinh tế số đang trở thành một hướng phát triển quan trọng khi nhiều doanh nghiệp chuyển sang bán hàng trực tuyến, thanh toán không tiền mặt và quản lý dữ liệu khách hàng bằng phần mềm. Sự thay đổi này giúp doanh nghiệp tiết kiệm chi phí vận hành, mở rộng thị trường và tiếp cận người tiêu dùng nhanh hơn.</p><p>Tuy nhiên, quá trình chuyển đổi cũng đặt ra yêu cầu về kỹ năng số, bảo mật thông tin và khả năng thích ứng với công nghệ mới. Với các doanh nghiệp nhỏ, việc bắt đầu từ website, mạng xã hội và hệ thống quản lý đơn hàng là bước đi phù hợp để tham gia vào nền kinh tế hiện đại.</p><h2>Nhận định</h2><p>Bài viết cho thấy kinh tế không chỉ gắn với sản xuất và thương mại truyền thống mà còn liên quan chặt chẽ đến công nghệ, dữ liệu và trải nghiệm khách hàng.</p>',
    'published',
    NOW(),
    NOW()
FROM categories c
WHERE c.name = 'Kinh doanh'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Kinh tế số tạo động lực mới cho doanh nghiệp Việt'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Thị trường tài chính chú trọng quản trị rủi ro',
    '<p>Thị trường tài chính luôn thay đổi theo lãi suất, dòng tiền, hoạt động đầu tư và tâm lý của người tiêu dùng. Trong bối cảnh kinh tế biến động, các cá nhân và doanh nghiệp cần quan tâm nhiều hơn đến quản trị rủi ro thay vì chỉ chạy theo lợi nhuận ngắn hạn.</p><p>Việc theo dõi thông tin tài chính giúp người đọc hiểu rõ hơn về ngân hàng, chứng khoán, tiết kiệm, vay vốn và xu hướng đầu tư. Đây là nhóm thông tin có tác động trực tiếp đến kế hoạch chi tiêu cũng như quyết định kinh doanh.</p><h2>Nhận định</h2><p>Các bài viết tài chính cần trình bày rõ ràng, dễ hiểu để người đọc phổ thông cũng có thể nắm bắt được nội dung chính.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 1 HOUR),
    DATE_SUB(NOW(), INTERVAL 1 HOUR)
FROM categories c
WHERE c.name = 'Tài chính'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Thị trường tài chính chú trọng quản trị rủi ro'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'AI được ứng dụng rộng hơn trong xử lý nội dung số',
    '<p>Trí tuệ nhân tạo đang hỗ trợ nhiều công việc như tóm tắt văn bản, gợi ý tiêu đề, phân loại nội dung và phân tích hành vi người đọc. Với một website tin tức, AI có thể giúp đề xuất bài viết phù hợp hơn với từng nhóm độc giả.</p><p>Trong quá trình vận hành, AI không thay thế hoàn toàn biên tập viên mà đóng vai trò như công cụ hỗ trợ. Nội dung cuối cùng vẫn cần được kiểm tra bởi con người để đảm bảo tính chính xác, phù hợp và có trách nhiệm.</p><h2>Nhận định</h2><p>AI và chuyển đổi số giúp hệ thống tin tức hoạt động hiện đại hơn, đặc biệt trong cá nhân hóa nội dung và quản lý dữ liệu.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 2 HOUR),
    DATE_SUB(NOW(), INTERVAL 2 HOUR)
FROM categories c
WHERE c.name = 'AI & Chuyển đổi số'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'AI được ứng dụng rộng hơn trong xử lý nội dung số'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Công nghệ bảo mật dữ liệu được quan tâm trong các website',
    '<p>Khi người dùng đăng ký tài khoản, bình luận và tương tác với bài viết, hệ thống cần bảo vệ dữ liệu cá nhân bằng nhiều biện pháp khác nhau. Các kỹ thuật như băm mật khẩu, kiểm tra quyền truy cập và chống nhập dữ liệu nguy hiểm là những phần quan trọng trong một website hiện đại.</p><p>Đối với SmartNews, việc phân quyền admin, editor và user giúp hệ thống rõ ràng hơn. Người dùng thường chỉ đọc và bình luận, editor viết bài, còn admin quản lý toàn bộ dữ liệu.</p><h2>Nhận định</h2><p>Bảo mật không nên xem là phần phụ, mà cần được xây dựng ngay từ giai đoạn thiết kế hệ thống.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 3 HOUR),
    DATE_SUB(NOW(), INTERVAL 3 HOUR)
FROM categories c
WHERE c.name = 'Công nghệ'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Công nghệ bảo mật dữ liệu được quan tâm trong các website'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Nội dung giải trí ngắn tiếp tục thu hút người xem trẻ',
    '<p>Các video ngắn, bài viết ngắn và nội dung dễ chia sẻ đang phù hợp với thói quen sử dụng điện thoại của nhiều bạn trẻ. Người xem thường ưu tiên nội dung có điểm nhấn nhanh, hình ảnh rõ ràng và thông điệp dễ hiểu.</p><p>Đối với trang tin tức, chuyên mục giải trí cần cân bằng giữa yếu tố hấp dẫn và độ tin cậy. Tiêu đề có thể thu hút nhưng không nên gây hiểu nhầm hoặc giật tít quá mức.</p><h2>Nhận định</h2><p>Giải trí là chuyên mục giúp website tăng tính gần gũi, nhưng vẫn cần kiểm soát chất lượng nội dung.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 4 HOUR),
    DATE_SUB(NOW(), INTERVAL 4 HOUR)
FROM categories c
WHERE c.name = 'Giải trí'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Nội dung giải trí ngắn tiếp tục thu hút người xem trẻ'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Giáo dục trực tuyến giúp người học chủ động hơn',
    '<p>Giáo dục trực tuyến tạo điều kiện để học sinh, sinh viên và người đi làm tiếp cận kiến thức ở bất cứ đâu. Các nền tảng học tập số giúp lưu bài giảng, kiểm tra tiến độ và hỗ trợ tự học hiệu quả hơn.</p><p>Tuy nhiên, học trực tuyến cũng đòi hỏi khả năng tự quản lý thời gian, chọn lọc tài liệu và duy trì sự tập trung. Người học cần kết hợp giữa công nghệ và phương pháp học phù hợp.</p><h2>Nhận định</h2><p>Giáo dục hiện đại không chỉ là học kiến thức, mà còn là rèn luyện kỹ năng tự học và thích nghi.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 5 HOUR),
    DATE_SUB(NOW(), INTERVAL 5 HOUR)
FROM categories c
WHERE c.name = 'Giáo dục'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Giáo dục trực tuyến giúp người học chủ động hơn'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Thói quen sống lành mạnh được nhiều người trẻ quan tâm',
    '<p>Sức khỏe và đời sống đang trở thành chủ đề được quan tâm nhiều hơn khi người trẻ chú ý đến giấc ngủ, vận động, dinh dưỡng và sức khỏe tinh thần. Những thay đổi nhỏ trong sinh hoạt hằng ngày có thể tạo tác động tích cực trong dài hạn.</p><p>Việc đọc thông tin sức khỏe cần dựa trên nguồn đáng tin cậy và không nên tự áp dụng các phương pháp cực đoan. Người đọc nên xem bài viết như thông tin tham khảo và tìm chuyên gia khi có vấn đề nghiêm trọng.</p><h2>Nhận định</h2><p>Chuyên mục sức khỏe cần trình bày dễ hiểu, tránh gây hoang mang và hướng đến lối sống cân bằng.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 6 HOUR),
    DATE_SUB(NOW(), INTERVAL 6 HOUR)
FROM categories c
WHERE c.name = 'Sức khỏe'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Thói quen sống lành mạnh được nhiều người trẻ quan tâm'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Đời sống đô thị thay đổi theo nhịp công nghệ',
    '<p>Đời sống đô thị hiện đại gắn liền với các dịch vụ số như đặt xe, giao hàng, thanh toán trực tuyến và mua sắm qua ứng dụng. Những tiện ích này giúp con người tiết kiệm thời gian nhưng cũng khiến nhịp sống trở nên nhanh hơn.</p><p>Người dân thành thị ngày càng quan tâm đến cân bằng giữa công việc, nghỉ ngơi và các hoạt động cá nhân. Vì vậy, các bài viết đời sống thường tập trung vào thói quen, xu hướng xã hội và trải nghiệm thường ngày.</p><h2>Nhận định</h2><p>Đời sống là chuyên mục gần gũi, giúp người đọc thấy được sự thay đổi xung quanh mình.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 7 HOUR),
    DATE_SUB(NOW(), INTERVAL 7 HOUR)
FROM categories c
WHERE c.name = 'Đời sống'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Đời sống đô thị thay đổi theo nhịp công nghệ'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Thể thao cộng đồng lan tỏa lối sống năng động',
    '<p>Các hoạt động chạy bộ, bóng đá phong trào, cầu lông và đạp xe đang thu hút nhiều người tham gia. Thể thao không chỉ giúp cải thiện sức khỏe mà còn tạo môi trường kết nối cộng đồng.</p><p>Với các giải đấu chuyên nghiệp, người hâm mộ quan tâm không chỉ kết quả mà còn phong độ vận động viên, chiến thuật và câu chuyện bên lề. Đây là lý do chuyên mục thể thao luôn có lượng người đọc ổn định.</p><h2>Nhận định</h2><p>Thể thao là nội dung phù hợp để tăng tương tác vì người đọc thường bình luận và chia sẻ quan điểm cá nhân.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 8 HOUR),
    DATE_SUB(NOW(), INTERVAL 8 HOUR)
FROM categories c
WHERE c.name = 'Thể thao'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Thể thao cộng đồng lan tỏa lối sống năng động'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Thời sự trong ngày cần được cập nhật nhanh và chính xác',
    '<p>Thời sự là nhóm tin tức phản ánh những sự kiện đang diễn ra trong xã hội. Người đọc thường quan tâm đến thông tin nhanh, rõ ràng và có bối cảnh để hiểu vấn đề.</p><p>Một bài thời sự tốt không chỉ nêu sự việc mà còn cần trình bày thời gian, địa điểm, bên liên quan và tác động chính. Điều này giúp người đọc tránh hiểu sai hoặc tiếp nhận thông tin thiếu đầy đủ.</p><h2>Nhận định</h2><p>Với website tin tức, chuyên mục thời sự là phần quan trọng để thể hiện tính cập nhật của hệ thống.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 9 HOUR),
    DATE_SUB(NOW(), INTERVAL 9 HOUR)
FROM categories c
WHERE c.name = 'Thời sự'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Thời sự trong ngày cần được cập nhật nhanh và chính xác'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Chính sách công được quan tâm vì ảnh hưởng đến đời sống',
    '<p>Các chính sách mới trong giáo dục, y tế, giao thông, kinh tế và chuyển đổi số có thể tác động trực tiếp đến người dân. Vì vậy, tin chính trị và chính sách cần được diễn giải dễ hiểu để độc giả nắm được nội dung chính.</p><p>Việc trình bày thông tin chính trị cần khách quan, tránh suy diễn và ưu tiên các dữ kiện rõ ràng. Điều này giúp website giữ được sự tin cậy trong mắt người đọc.</p><h2>Nhận định</h2><p>Chuyên mục chính trị phù hợp để thể hiện khả năng phân loại tin tức nghiêm túc của SmartNews.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 10 HOUR),
    DATE_SUB(NOW(), INTERVAL 10 HOUR)
FROM categories c
WHERE c.name = 'Chính trị'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Chính sách công được quan tâm vì ảnh hưởng đến đời sống'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Tin thế giới giúp độc giả hiểu bối cảnh quốc tế',
    '<p>Các sự kiện quốc tế có thể ảnh hưởng đến kinh tế, giáo dục, du lịch và đời sống trong nước. Vì vậy, người đọc ngày càng quan tâm đến tin thế giới để hiểu rõ hơn về các xu hướng toàn cầu.</p><p>Khi đưa tin thế giới, nội dung cần giải thích bối cảnh, tránh chỉ dịch ngắn gọn sự kiện. Người đọc phổ thông cần biết sự việc đó có ý nghĩa gì và vì sao đáng quan tâm.</p><h2>Nhận định</h2><p>Chuyên mục thế giới giúp website có phạm vi nội dung rộng hơn và đa dạng hơn.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 11 HOUR),
    DATE_SUB(NOW(), INTERVAL 11 HOUR)
FROM categories c
WHERE c.name = 'Thế giới'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Tin thế giới giúp độc giả hiểu bối cảnh quốc tế'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Nghiên cứu khoa học được phổ biến theo cách dễ hiểu hơn',
    '<p>Khoa học không chỉ nằm trong phòng thí nghiệm mà còn xuất hiện trong y tế, môi trường, công nghệ và giáo dục. Việc phổ biến khoa học theo cách dễ hiểu giúp người đọc tiếp cận kiến thức chính xác hơn.</p><p>Một bài viết khoa học nên giải thích thuật ngữ, nêu ứng dụng thực tế và tránh phóng đại kết quả nghiên cứu. Điều này giúp độc giả hiểu bản chất vấn đề mà không bị nhiễu thông tin.</p><h2>Nhận định</h2><p>Chuyên mục khoa học giúp SmartNews tăng giá trị tri thức và tính giáo dục cho người đọc.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 12 HOUR),
    DATE_SUB(NOW(), INTERVAL 12 HOUR)
FROM categories c
WHERE c.name = 'Khoa học'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Nghiên cứu khoa học được phổ biến theo cách dễ hiểu hơn'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Du lịch trải nghiệm trở thành xu hướng được yêu thích',
    '<p>Nhiều người không chỉ đi du lịch để nghỉ ngơi mà còn muốn trải nghiệm văn hóa, ẩm thực và đời sống địa phương. Các điểm đến gần gũi thiên nhiên, chi phí hợp lý và có nhiều hoạt động trải nghiệm đang được quan tâm.</p><p>Trước mỗi chuyến đi, người đọc thường tìm thông tin về thời tiết, phương tiện, lịch trình và những lưu ý cần chuẩn bị. Vì vậy, bài viết du lịch cần thiết thực và dễ áp dụng.</p><h2>Nhận định</h2><p>Chuyên mục du lịch phù hợp để tạo nội dung nhẹ nhàng, giàu hình ảnh và dễ thu hút người đọc.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 13 HOUR),
    DATE_SUB(NOW(), INTERVAL 13 HOUR)
FROM categories c
WHERE c.name = 'Du lịch'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Du lịch trải nghiệm trở thành xu hướng được yêu thích'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Văn hóa đọc thay đổi trong môi trường số',
    '<p>Trong môi trường số, người đọc có nhiều lựa chọn hơn như báo điện tử, mạng xã hội, sách điện tử và video kiến thức. Sự thay đổi này khiến thói quen tiếp nhận thông tin trở nên nhanh hơn nhưng cũng đòi hỏi khả năng chọn lọc tốt hơn.</p><p>Văn hóa đọc hiện đại không chỉ là đọc nhiều, mà còn là đọc có chọn lọc, biết kiểm chứng nguồn tin và hiểu được ngữ cảnh của nội dung.</p><h2>Nhận định</h2><p>Chuyên mục văn hóa giúp website có thêm chiều sâu về lối sống, thói quen và giá trị xã hội.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 14 HOUR),
    DATE_SUB(NOW(), INTERVAL 14 HOUR)
FROM categories c
WHERE c.name = 'Văn hóa'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Văn hóa đọc thay đổi trong môi trường số'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Quy định pháp luật cần được truyền tải dễ hiểu',
    '<p>Pháp luật ảnh hưởng đến nhiều hoạt động trong đời sống như giao thông, lao động, kinh doanh, môi trường mạng và quyền lợi người tiêu dùng. Tuy nhiên, ngôn ngữ pháp lý đôi khi khó hiểu với người đọc phổ thông.</p><p>Vì vậy, các bài viết pháp luật cần giải thích bằng ví dụ gần gũi, nêu rõ hành vi liên quan và hậu quả có thể xảy ra. Cách trình bày rõ ràng giúp người đọc nâng cao ý thức tuân thủ quy định.</p><h2>Nhận định</h2><p>Chuyên mục pháp luật giúp SmartNews tăng tính hữu ích và tính giáo dục xã hội.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 15 HOUR),
    DATE_SUB(NOW(), INTERVAL 15 HOUR)
FROM categories c
WHERE c.name = 'Pháp luật'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Quy định pháp luật cần được truyền tải dễ hiểu'
  );

INSERT INTO posts (user_id, category_id, title, content, status, created_at, updated_at)
SELECT
    @author_id,
    c.id,
    'Bất động sản chú trọng nhu cầu ở thực và hạ tầng',
    '<p>Thị trường bất động sản không chỉ phụ thuộc vào giá bán mà còn liên quan đến vị trí, pháp lý, hạ tầng giao thông và nhu cầu ở thực. Người mua nhà ngày càng quan tâm đến tiện ích xung quanh, khả năng kết nối và chất lượng môi trường sống.</p><p>Trong bối cảnh kinh tế thay đổi, thông tin bất động sản cần được trình bày cẩn trọng, tránh tạo kỳ vọng quá mức. Người đọc nên xem xét nhiều yếu tố trước khi đưa ra quyết định.</p><h2>Nhận định</h2><p>Chuyên mục bất động sản phù hợp để cung cấp thông tin thị trường, xu hướng nhà ở và phân tích nhu cầu người mua.</p>',
    'published',
    DATE_SUB(NOW(), INTERVAL 16 HOUR),
    DATE_SUB(NOW(), INTERVAL 16 HOUR)
FROM categories c
WHERE c.name = 'Bất động sản'
  AND @author_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts WHERE title = 'Bất động sản chú trọng nhu cầu ở thực và hạ tầng'
  );

SELECT
    c.id,
    c.name AS category_name,
    COUNT(p.id) AS published_posts
FROM categories c
LEFT JOIN posts p
    ON p.category_id = c.id
   AND p.status = 'published'
GROUP BY c.id, c.name
ORDER BY c.id;