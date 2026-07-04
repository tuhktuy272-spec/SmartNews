<?php

declare(strict_types=1);

/**
 * Dữ liệu demo cho Nhiệm vụ 4: hiển thị giao diện tin tức, chi tiết bài viết,
 * tìm kiếm, chuyên mục, bài nổi bật, bài mới và đề xuất.
 *
 * Sau này nhóm có thể thay dữ liệu mảng này bằng dữ liệu lấy từ MySQL.
 */

function getNewsCategories(): array
{
    return [
        [
            'title' => 'Công nghệ',
            'description' => 'Tin tức công nghệ, sản phẩm mới và xu hướng số.',
        ],
        [
            'title' => 'Giải trí',
            'description' => 'Phim ảnh, âm nhạc, người nổi tiếng và sự kiện.',
        ],
        [
            'title' => 'Kinh doanh',
            'description' => 'Thị trường, doanh nghiệp, tài chính và đầu tư.',
        ],
        [
            'title' => 'Thể thao',
            'description' => 'Tin thể thao, lịch thi đấu, kết quả và bình luận.',
        ],
        [
            'title' => 'Sức khỏe',
            'description' => 'Lối sống, dinh dưỡng và chăm sóc sức khỏe.',
        ],
        [
            'title' => 'Giáo dục',
            'description' => 'Học tập, tuyển sinh, kỹ năng và hướng nghiệp.',
        ],
    ];
}

function getAllNewsArticles(): array
{
    return [
        [
            'id' => 1,
            'slug' => 'ai-va-tuong-lai-bao-chi-so',
            'title' => 'AI và tương lai báo chí số',
            'summary' => 'Cách AI thay đổi sản xuất nội dung và trải nghiệm đọc tin.',
            'category_name' => 'Công nghệ',
            'author' => 'SmartNews Team',
            'publish_date' => '2026-07-01',
            'section' => 'featured',
            'image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=1200&q=80',
            'image_alt' => 'Trí tuệ nhân tạo trong báo chí số',
            'content' => '
                <p>Trí tuệ nhân tạo đang làm thay đổi mạnh mẽ cách các tòa soạn sản xuất, phân phối và cá nhân hóa nội dung tin tức.</p>
                <p>Thay vì chỉ hiển thị cùng một nội dung cho tất cả độc giả, hệ thống tin tức hiện đại có thể phân tích hành vi đọc để đề xuất bài viết phù hợp hơn.</p>
                <blockquote>AI không thay thế hoàn toàn nhà báo, nhưng giúp quá trình biên tập, phân loại và đề xuất nội dung trở nên nhanh hơn.</blockquote>
                <p>Trong tương lai, các website tin tức sẽ cần kết hợp dữ liệu, giao diện thân thiện và thuật toán gợi ý để tăng trải nghiệm người đọc.</p>
            ',
        ],
        [
            'id' => 2,
            'slug' => 'thiet-ke-giao-dien-tin-tuc-chuyen-nghiep',
            'title' => 'Thiết kế giao diện tin tức chuyên nghiệp',
            'summary' => 'Mẹo bố cục, màu sắc và trải nghiệm người đọc cho trang tin.',
            'category_name' => 'Công nghệ',
            'author' => 'SmartNews Team',
            'publish_date' => '2026-07-02',
            'section' => 'featured',
            'image' => 'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=1200&q=80',
            'image_alt' => 'Thiết kế giao diện báo điện tử',
            'content' => '
                <p>Một giao diện tin tức chuyên nghiệp cần đảm bảo khả năng đọc tốt, bố cục rõ ràng và điều hướng đơn giản.</p>
                <p>Các thành phần như tiêu đề, ảnh đại diện, chuyên mục và nút đọc tiếp cần được trình bày nhất quán.</p>
                <p>Responsive design cũng là yếu tố quan trọng vì người dùng hiện nay đọc tin trên cả điện thoại, máy tính bảng và laptop.</p>
            ',
        ],
        [
            'id' => 3,
            'slug' => 'toi-uu-toc-do-trang-tin-tuc',
            'title' => 'Tối ưu tốc độ trang tin tức',
            'summary' => 'Các kỹ thuật front-end giúp trang tin tải nhanh và ổn định.',
            'category_name' => 'Công nghệ',
            'author' => 'SmartNews Team',
            'publish_date' => '2026-07-03',
            'section' => 'featured',
            'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80',
            'image_alt' => 'Tối ưu hiệu năng website',
            'content' => '
                <p>Tốc độ tải trang ảnh hưởng trực tiếp đến trải nghiệm người dùng và khả năng giữ chân độc giả.</p>
                <p>Một số cách tối ưu phổ biến gồm nén ảnh, giảm file CSS/JS không cần thiết và sử dụng lazy loading.</p>
                <p>Với website tin tức, tốc độ càng quan trọng vì người dùng thường muốn đọc nhanh và chuyển bài liên tục.</p>
            ',
        ],
        [
            'id' => 4,
            'slug' => 'xu-huong-tim-kiem-2026',
            'title' => 'Xu hướng tìm kiếm 2026',
            'summary' => 'Những chủ đề và từ khóa được độc giả tìm kiếm nhiều nhất.',
            'category_name' => 'Giáo dục',
            'author' => 'SmartNews Team',
            'publish_date' => '2026-07-04',
            'section' => 'new',
            'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80',
            'image_alt' => 'Phân tích xu hướng tìm kiếm',
            'content' => '
                <p>Xu hướng tìm kiếm phản ánh những chủ đề mà độc giả đang quan tâm trong từng giai đoạn.</p>
                <p>Việc phân tích từ khóa giúp hệ thống tin tức xây dựng nội dung phù hợp với nhu cầu thực tế.</p>
            ',
        ],
        [
            'id' => 5,
            'slug' => 'cach-viet-tieu-de-thu-hut',
            'title' => 'Cách viết tiêu đề thu hút',
            'summary' => 'Kỹ thuật tạo tiêu đề khiến người đọc muốn click ngay.',
            'category_name' => 'Giáo dục',
            'author' => 'SmartNews Team',
            'publish_date' => '2026-07-04',
            'section' => 'new',
            'image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=1200&q=80',
            'image_alt' => 'Viết nội dung và tiêu đề',
            'content' => '
                <p>Tiêu đề là yếu tố đầu tiên quyết định người đọc có bấm vào bài viết hay không.</p>
                <p>Một tiêu đề tốt cần rõ ràng, ngắn gọn, đúng nội dung và tạo được sự tò mò hợp lý.</p>
            ',
        ],
        [
            'id' => 6,
            'slug' => 'phan-tich-hanh-vi-nguoi-doc',
            'title' => 'Phân tích hành vi người đọc',
            'summary' => 'Hiểu thói quen đọc để tối ưu độ tương tác và giữ chân người dùng.',
            'category_name' => 'Kinh doanh',
            'author' => 'SmartNews Team',
            'publish_date' => '2026-07-04',
            'section' => 'new',
            'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80',
            'image_alt' => 'Phân tích hành vi người dùng',
            'content' => '
                <p>Phân tích hành vi người đọc giúp website hiểu được chủ đề nào được quan tâm nhiều nhất.</p>
                <p>Dữ liệu như lượt xem, thời gian đọc và lượt tìm kiếm có thể hỗ trợ cá nhân hóa nội dung.</p>
            ',
        ],
        [
            'id' => 7,
            'slug' => 'lam-noi-dung-cho-mobile',
            'title' => 'Làm nội dung cho mobile',
            'summary' => 'Thiết kế và định dạng nội dung phù hợp với người dùng di động.',
            'category_name' => 'Công nghệ',
            'author' => 'SmartNews Team',
            'publish_date' => '2026-07-04',
            'section' => 'new',
            'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80',
            'image_alt' => 'Đọc tin tức trên điện thoại',
            'content' => '
                <p>Người dùng di động thường đọc nhanh, lướt nhanh và cần giao diện rõ ràng.</p>
                <p>Vì vậy bài viết nên có đoạn ngắn, khoảng trắng hợp lý và nút bấm dễ thao tác.</p>
            ',
        ],
        [
            'id' => 8,
            'slug' => 'bao-mat-trang-tin-tuc',
            'title' => 'Bảo mật trang tin tức',
            'summary' => 'Các biện pháp bảo vệ dữ liệu người dùng an toàn.',
            'category_name' => 'Công nghệ',
            'author' => 'SmartNews Team',
            'publish_date' => '2026-07-04',
            'section' => 'new',
            'image' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=1200&q=80',
            'image_alt' => 'Bảo mật website',
            'content' => '
                <p>Bảo mật là yếu tố bắt buộc đối với hệ thống có tài khoản người dùng.</p>
                <p>SmartNews cần mã hóa mật khẩu, dùng prepared statement và kiểm soát session an toàn.</p>
            ',
        ],
        [
            'id' => 9,
            'slug' => 'xay-dung-he-thong-de-xuat',
            'title' => 'Xây dựng hệ thống đề xuất',
            'summary' => 'Giới thiệu logic gợi ý bài viết dựa trên hành vi đọc.',
            'category_name' => 'Công nghệ',
            'author' => 'SmartNews Team',
            'publish_date' => '2026-07-04',
            'section' => 'new',
            'image' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1200&q=80',
            'image_alt' => 'Hệ thống đề xuất nội dung',
            'content' => '
                <p>Hệ thống đề xuất giúp người đọc nhanh chóng tìm thấy các bài viết phù hợp với sở thích.</p>
                <p>Trong giai đoạn đầu, SmartNews có thể dùng dữ liệu chuyên mục và bài viết nổi bật để đề xuất đơn giản.</p>
            ',
        ],
    ];
}

function mapArticleCard(array $article): array
{
    return [
        'id' => $article['id'],
        'slug' => $article['slug'],
        'title' => $article['title'],
        'summary' => $article['summary'],
        'link' => 'article.php?id=' . $article['slug'],
    ];
}

function getArticlesBySection(string $section): array
{
    $articles = array_filter(getAllNewsArticles(), function (array $article) use ($section) {
        return $article['section'] === $section;
    });

    return array_map('mapArticleCard', array_values($articles));
}

function getRecommendations(int $limit = 3, ?int $excludeId = null): array
{
    $articles = array_filter(getAllNewsArticles(), function (array $article) use ($excludeId) {
        return $excludeId === null || $article['id'] !== $excludeId;
    });

    return array_slice(array_map('mapArticleCard', array_values($articles)), 0, $limit);
}

function getArticleById(string $id): ?array
{
    foreach (getAllNewsArticles() as $article) {
        if ($article['slug'] === $id || (string) $article['id'] === $id) {
            return $article;
        }
    }

    return null;
}

function searchNewsArticles(string $keyword): array
{
    $keyword = mb_strtolower(trim($keyword));

    if ($keyword === '') {
        return [];
    }

    $results = array_filter(getAllNewsArticles(), function (array $article) use ($keyword) {
        $haystack = mb_strtolower(
            $article['title'] . ' ' .
            $article['summary'] . ' ' .
            $article['category_name']
        );

        return str_contains($haystack, $keyword);
    });

    return array_map('mapArticleCard', array_values($results));
}