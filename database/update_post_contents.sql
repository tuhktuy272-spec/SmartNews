USE smartnews;

UPDATE posts p
JOIN categories c ON c.id = p.category_id
SET p.content = CONCAT(
    '<p>',
    p.title,
    ' là bài viết thuộc chuyên mục ',
    c.name,
    ' trên SmartNews. Bài viết này được xây dựng nhằm cung cấp cho người đọc thông tin tổng quan, dễ hiểu và có tính cập nhật về chủ đề đang được quan tâm.',
    '</p>',

    '<h2>Bối cảnh</h2>',
    '<p>Trong thời đại thông tin số, người đọc không chỉ cần biết sự việc đã xảy ra mà còn cần hiểu bối cảnh, nguyên nhân và tác động của sự việc đó. Vì vậy, SmartNews trình bày bài viết theo hướng ngắn gọn, rõ ý nhưng vẫn có đủ nội dung để người đọc theo dõi.</p>',
    '<p>Chuyên mục ',
    c.name,
    ' tập trung vào những vấn đề gần gũi với đời sống, học tập, công việc và nhu cầu cập nhật tin tức hằng ngày của độc giả.</p>',

    '<h2>Nội dung chính</h2>',

    CASE
        WHEN c.name IN ('Công nghệ', 'AI & Chuyển đổi số') THEN
            '<p>Công nghệ đang thay đổi mạnh mẽ cách con người học tập, làm việc và giao tiếp. Các xu hướng như trí tuệ nhân tạo, chuyển đổi số, dữ liệu lớn, tự động hóa và nền tảng trực tuyến đang xuất hiện ngày càng nhiều trong đời sống.</p>
            <p>Đối với người dùng phổ thông, điều quan trọng không chỉ là biết một công nghệ mới xuất hiện, mà còn phải hiểu công nghệ đó có thể ứng dụng như thế nào. Những thay đổi này giúp tiết kiệm thời gian, tăng hiệu quả xử lý thông tin và tạo ra nhiều cơ hội mới cho cá nhân cũng như doanh nghiệp.</p>
            <p>Tuy nhiên, sự phát triển công nghệ cũng đặt ra yêu cầu về bảo mật dữ liệu, kỹ năng sử dụng công cụ số và khả năng chọn lọc thông tin chính xác.</p>'

        WHEN c.name IN ('Giải trí', 'Văn hóa') THEN
            '<p>Lĩnh vực giải trí và văn hóa luôn thay đổi theo thị hiếu của công chúng. Phim ảnh, âm nhạc, chương trình truyền hình, mạng xã hội và các sự kiện văn hóa đều góp phần tạo nên đời sống tinh thần phong phú cho người đọc.</p>
            <p>Sự phát triển của nền tảng số giúp nội dung giải trí lan tỏa nhanh hơn, đồng thời tạo điều kiện cho nhiều nghệ sĩ, nhà sáng tạo và cộng đồng người hâm mộ tương tác trực tiếp với nhau.</p>
            <p>Bên cạnh yếu tố thư giãn, các nội dung văn hóa còn phản ánh thói quen, lối sống và xu hướng tiếp nhận thông tin của xã hội hiện đại.</p>'

        WHEN c.name IN ('Kinh doanh', 'Tài chính', 'Bất động sản') THEN
            '<p>Kinh doanh, tài chính và bất động sản là những lĩnh vực có ảnh hưởng trực tiếp đến doanh nghiệp, người lao động và người tiêu dùng. Các thay đổi về giá cả, thị trường, xu hướng đầu tư hoặc hành vi mua sắm đều có thể tạo ra tác động lớn.</p>
            <p>Trong môi trường cạnh tranh hiện nay, việc cập nhật thông tin kịp thời giúp cá nhân và tổ chức đưa ra quyết định phù hợp hơn. Các doanh nghiệp cũng cần quan tâm đến xu hướng số hóa, thương mại điện tử và trải nghiệm khách hàng.</p>
            <p>Bài viết cung cấp góc nhìn tổng quan để người đọc hiểu rõ hơn vấn đề thay vì chỉ tiếp nhận thông tin ở mức tiêu đề.</p>'

        WHEN c.name = 'Thể thao' THEN
            '<p>Thể thao không chỉ đơn thuần là kết quả thi đấu mà còn là câu chuyện về tinh thần, chiến thuật, sự chuẩn bị và nỗ lực của vận động viên. Mỗi trận đấu hoặc sự kiện thể thao đều có thể tạo nên cảm xúc mạnh mẽ cho người hâm mộ.</p>
            <p>Bên cạnh thành tích, người đọc còn quan tâm đến phong độ, lực lượng, lối chơi và những yếu tố bên lề ảnh hưởng đến kết quả. Đây là lý do tin thể thao luôn có sức hút lớn.</p>
            <p>Thông qua bài viết, SmartNews giúp độc giả có cái nhìn đầy đủ hơn về sự kiện thay vì chỉ theo dõi tỉ số cuối cùng.</p>'

        WHEN c.name IN ('Sức khỏe', 'Đời sống') THEN
            '<p>Sức khỏe và đời sống là những chủ đề gắn liền với thói quen hằng ngày của mỗi người. Việc duy trì lối sống lành mạnh, ăn uống hợp lý, nghỉ ngơi đầy đủ và chăm sóc tinh thần ngày càng được quan tâm.</p>
            <p>Các thông tin trong chuyên mục này giúp người đọc có thêm góc nhìn tham khảo để điều chỉnh sinh hoạt cá nhân, nâng cao chất lượng cuộc sống và phòng tránh những rủi ro thường gặp.</p>
            <p>SmartNews trình bày nội dung theo hướng dễ hiểu, hạn chế dùng thuật ngữ phức tạp để phù hợp với nhiều nhóm độc giả.</p>'

        WHEN c.name = 'Giáo dục' THEN
            '<p>Giáo dục là lĩnh vực có ảnh hưởng lâu dài đến học sinh, sinh viên, phụ huynh và xã hội. Những thay đổi trong phương pháp học tập, tuyển sinh, công nghệ giáo dục và định hướng nghề nghiệp luôn nhận được nhiều sự quan tâm.</p>
            <p>Trong bối cảnh học tập hiện đại, người học cần không chỉ kiến thức chuyên môn mà còn cần kỹ năng tự học, kỹ năng số và khả năng thích nghi với môi trường mới.</p>
            <p>Bài viết giúp người đọc nắm được thông tin chính, đồng thời gợi mở thêm một số vấn đề cần chú ý trong quá trình học tập và phát triển bản thân.</p>'

        WHEN c.name IN ('Thời sự', 'Chính trị', 'Thế giới', 'Pháp luật') THEN
            '<p>Các vấn đề thời sự, chính trị, thế giới và pháp luật thường có tác động rộng đến xã hội. Người đọc cần được cung cấp thông tin rõ ràng, có bối cảnh và dễ theo dõi.</p>
            <p>Một sự kiện xã hội không chỉ có diễn biến trước mắt mà còn có nguyên nhân, ảnh hưởng và những phản ứng liên quan từ nhiều phía. Vì vậy, việc trình bày thông tin cần đảm bảo tính mạch lạc và khách quan.</p>
            <p>SmartNews hướng đến cách viết dễ hiểu để người đọc nhanh chóng nắm bắt nội dung chính mà không bị rối bởi quá nhiều thuật ngữ.</p>'

        WHEN c.name = 'Du lịch' THEN
            '<p>Du lịch là hoạt động giúp con người khám phá địa điểm mới, trải nghiệm văn hóa, ẩm thực và phong cảnh. Các bài viết du lịch mang đến cảm hứng cũng như thông tin tham khảo cho người đọc trước khi lên kế hoạch di chuyển.</p>
            <p>Một chuyến đi tốt không chỉ phụ thuộc vào điểm đến mà còn liên quan đến thời gian, chi phí, phương tiện, lịch trình và những lưu ý cần chuẩn bị trước.</p>
            <p>Chuyên mục du lịch trên SmartNews hướng đến cách trình bày gần gũi, giúp độc giả dễ hình dung và lựa chọn trải nghiệm phù hợp.</p>'

        WHEN c.name = 'Khoa học' THEN
            '<p>Khoa học giúp con người hiểu rõ hơn về tự nhiên, xã hội và các hiện tượng xung quanh. Những phát hiện mới, nghiên cứu mới hoặc ứng dụng khoa học đều có thể tạo ra ảnh hưởng tích cực đến đời sống.</p>
            <p>Việc phổ biến kiến thức khoa học theo cách dễ hiểu giúp người đọc tiếp cận thông tin chính xác hơn, tránh hiểu sai hoặc tiếp nhận các nội dung thiếu căn cứ.</p>
            <p>SmartNews trình bày bài viết khoa học theo hướng đơn giản hóa nhưng vẫn giữ được nội dung cốt lõi của vấn đề.</p>'

        ELSE
            '<p>Chủ đề này có nhiều khía cạnh đáng quan tâm trong đời sống hiện nay. Việc theo dõi thông tin giúp người đọc hiểu rõ hơn về những thay đổi đang diễn ra xung quanh mình.</p>
            <p>Bài viết cung cấp góc nhìn tổng quan, giúp độc giả nắm được nội dung chính và có thêm cơ sở để tiếp tục tìm hiểu sâu hơn.</p>'
    END,

    '<h2>Nhận định</h2>',
    '<p>Nhìn chung, nội dung bài viết cho thấy việc đọc tin tức không nên chỉ dừng lại ở tiêu đề. Khi theo dõi đầy đủ nội dung, người đọc có thể hiểu rõ hơn nguyên nhân, diễn biến và tác động của vấn đề.</p>',
    '<p>SmartNews hướng đến việc xây dựng một website tin tức hiện đại, dễ sử dụng, có phân loại chuyên mục rõ ràng và hỗ trợ người dùng tiếp cận thông tin nhanh chóng hơn.</p>',

    '<h2>Kết luận</h2>',
    '<p>Bài viết đã cung cấp thông tin tổng quan về chủ đề thuộc chuyên mục ',
    c.name,
    '. Trong thời gian tới, SmartNews có thể tiếp tục cập nhật thêm các bài viết liên quan để người đọc có thêm nhiều góc nhìn và thông tin mới.</p>'
)
WHERE p.status = 'published';