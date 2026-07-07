<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

$pdo = require __DIR__ . '/../config/db_connect.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

try {
    /*
     * API chính cho trang tin tức.
     * Bản sửa tập trung vào 3 phần:
     * 1. Tìm kiếm bài viết theo tiêu đề/nội dung/tác giả/chuyên mục.
     * 2. Lọc bài theo chuyên mục khi bấm vào category.
     * 3. Trả link chi tiết để bấm vào bài sẽ mở article.php?id=...
     */
    ensureBaseCategories($pdo);
    ensureMinimumPublishedPostForEachCategory($pdo);

    switch ($action) {
        case 'categories':
            jsonResponse([
                'categories' => getCategoriesFromDatabase($pdo),
            ]);
            break;

        case 'articles':
            $section = $_GET['section'] ?? 'new';
            $categoryId = filter_var($_GET['category_id'] ?? null, FILTER_VALIDATE_INT);

            jsonResponse([
                'articles' => getPublishedPosts($pdo, $section, $categoryId ?: null),
            ]);
            break;

        case 'recommendations':
            jsonResponse([
                'recommendations' => getRecommendedPosts($pdo, 6),
            ]);
            break;

        case 'search':
            $keyword = trim((string) ($_GET['q'] ?? ''));

            jsonResponse([
                'articles' => searchPublishedPosts($pdo, $keyword),
            ]);
            break;

        default:
            jsonResponse([
                'message' => 'Action không hợp lệ.',
            ], 400);
    }
} catch (Throwable $e) {
    jsonResponse([
        'message' => 'Có lỗi xảy ra khi xử lý API.',
        'error' => $e->getMessage(),
    ], 500);
}

function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function ensureBaseCategories(PDO $pdo): void
{
    $stmt = $pdo->prepare("\n        INSERT IGNORE INTO categories (id, name, description)\n        VALUES\n            (1, 'Công nghệ', 'Tin tức công nghệ, AI, phần mềm và thiết bị số'),\n            (2, 'Giải trí', 'Tin tức phim ảnh, âm nhạc và đời sống giải trí'),\n            (3, 'Kinh doanh', 'Tin thị trường, kinh tế, tài chính và doanh nghiệp'),\n            (4, 'Thể thao', 'Tin tức thể thao trong nước và quốc tế'),\n            (5, 'Sức khỏe', 'Tin sức khỏe, lối sống và dinh dưỡng'),\n            (6, 'Giáo dục', 'Tin giáo dục, tuyển sinh và học tập'),\n            (7, 'Thời sự', 'Tin tức thời sự trong ngày'),\n            (8, 'Chính trị', 'Tin chính trị và chính sách'),\n            (9, 'Thế giới', 'Tin tức quốc tế'),\n            (10, 'Tài chính', 'Tin tài chính, ngân hàng, chứng khoán và kinh tế'),\n            (11, 'AI & Chuyển đổi số', 'Tin AI, công nghệ số và chuyển đổi số'),\n            (12, 'Khoa học', 'Tin khoa học và nghiên cứu'),\n            (13, 'Đời sống', 'Tin đời sống xã hội'),\n            (14, 'Du lịch', 'Tin du lịch và trải nghiệm'),\n            (15, 'Văn hóa', 'Tin văn hóa'),\n            (16, 'Pháp luật', 'Tin pháp luật'),\n            (17, 'Bất động sản', 'Tin bất động sản')\n    ");

    $stmt->execute();
}

function getSeedAuthorId(PDO $pdo): ?int
{
    $stmt = $pdo->query("\n        SELECT users.id\n        FROM users\n        LEFT JOIN roles ON roles.id = users.role_id\n        ORDER BY\n            CASE\n                WHEN roles.name = 'admin' THEN 1\n                WHEN roles.name = 'editor' THEN 2\n                ELSE 3\n            END,\n            users.id ASC\n        LIMIT 1\n    ");

    $id = $stmt->fetchColumn();

    return $id ? (int) $id : null;
}

function ensureMinimumPublishedPostForEachCategory(PDO $pdo): void
{
    $authorId = getSeedAuthorId($pdo);

    if (!$authorId) {
        return;
    }

    $categoryStmt = $pdo->query("\n        SELECT id, name\n        FROM categories\n        ORDER BY id ASC\n    ");

    foreach ($categoryStmt->fetchAll() as $category) {
        $countStmt = $pdo->prepare("\n            SELECT COUNT(*)\n            FROM posts\n            WHERE category_id = :category_id\n              AND status = 'published'\n        ");

        $countStmt->execute([
            'category_id' => (int) $category['id'],
        ]);

        if ((int) $countStmt->fetchColumn() > 0) {
            continue;
        }

        $seed = getSeedPostByCategoryName((string) $category['name']);

        $insertStmt = $pdo->prepare("\n            INSERT INTO posts\n                (user_id, category_id, title, content, status, created_at, updated_at)\n            VALUES\n                (:user_id, :category_id, :title, :content, 'published', NOW(), NOW())\n        ");

        $insertStmt->execute([
            'user_id' => $authorId,
            'category_id' => (int) $category['id'],
            'title' => $seed['title'],
            'content' => $seed['content'],
        ]);
    }
}

function getSeedPostByCategoryName(string $categoryName): array
{
    $templates = [
        'Công nghệ' => [
            'title' => 'Công nghệ bảo mật dữ liệu được quan tâm trong các website',
            'body' => 'Công nghệ đang trở thành nền tảng quan trọng trong hầu hết các website hiện đại. Khi người dùng đăng ký tài khoản, đọc tin, bình luận và tương tác với bài viết, hệ thống cần có giải pháp bảo vệ dữ liệu cá nhân.',
        ],
        'Giải trí' => [
            'title' => 'Nội dung giải trí ngắn tiếp tục thu hút người xem trẻ',
            'body' => 'Các nội dung giải trí ngắn đang phù hợp với thói quen sử dụng điện thoại của nhiều người trẻ. Phim ảnh, âm nhạc, video ngắn và các sự kiện văn hóa thường được chia sẻ nhanh trên mạng xã hội.',
        ],
        'Kinh doanh' => [
            'title' => 'Kinh tế số tạo động lực mới cho doanh nghiệp Việt',
            'body' => 'Kinh tế số đang tạo ra nhiều thay đổi trong cách doanh nghiệp vận hành. Bán hàng trực tuyến, thanh toán không tiền mặt, quản lý dữ liệu khách hàng và quảng bá trên nền tảng số giúp doanh nghiệp tiếp cận thị trường nhanh hơn.',
        ],
        'Thể thao' => [
            'title' => 'Thể thao cộng đồng lan tỏa lối sống năng động',
            'body' => 'Các hoạt động thể thao như chạy bộ, bóng đá phong trào, cầu lông và đạp xe đang được nhiều người quan tâm. Thể thao không chỉ giúp cải thiện sức khỏe mà còn tạo môi trường kết nối cộng đồng.',
        ],
        'Sức khỏe' => [
            'title' => 'Thói quen sống lành mạnh được nhiều người trẻ quan tâm',
            'body' => 'Sức khỏe là chủ đề gắn liền với đời sống hằng ngày. Nhiều người trẻ bắt đầu quan tâm hơn đến giấc ngủ, dinh dưỡng, vận động và sức khỏe tinh thần.',
        ],
        'Giáo dục' => [
            'title' => 'Giáo dục trực tuyến giúp người học chủ động hơn',
            'body' => 'Giáo dục trực tuyến đang giúp học sinh, sinh viên và người đi làm tiếp cận kiến thức linh hoạt hơn. Người học có thể xem lại bài giảng, làm bài kiểm tra và theo dõi tiến độ học tập ngay trên nền tảng số.',
        ],
        'Thời sự' => [
            'title' => 'Thời sự trong ngày cần được cập nhật nhanh và chính xác',
            'body' => 'Thời sự là nhóm tin tức phản ánh các sự kiện đang diễn ra trong xã hội. Người đọc thường cần thông tin nhanh, rõ ràng và có bối cảnh để hiểu đúng vấn đề.',
        ],
        'Chính trị' => [
            'title' => 'Chính sách công được quan tâm vì ảnh hưởng đến đời sống',
            'body' => 'Các chính sách mới trong giáo dục, y tế, giao thông, kinh tế và chuyển đổi số có thể tác động trực tiếp đến người dân. Vì vậy, tin chính trị và chính sách cần được diễn giải dễ hiểu.',
        ],
        'Thế giới' => [
            'title' => 'Tin thế giới giúp độc giả hiểu bối cảnh quốc tế',
            'body' => 'Các sự kiện quốc tế có thể ảnh hưởng đến kinh tế, giáo dục, du lịch và đời sống trong nước. Người đọc ngày càng quan tâm đến tin thế giới để hiểu rõ hơn các xu hướng toàn cầu.',
        ],
        'Tài chính' => [
            'title' => 'Thị trường tài chính chú trọng quản trị rủi ro',
            'body' => 'Thị trường tài chính luôn thay đổi theo lãi suất, dòng tiền, hoạt động đầu tư và tâm lý người tiêu dùng. Trong bối cảnh kinh tế biến động, cá nhân và doanh nghiệp cần quan tâm đến quản trị rủi ro.',
        ],
        'AI & Chuyển đổi số' => [
            'title' => 'AI được ứng dụng rộng hơn trong xử lý nội dung số',
            'body' => 'Trí tuệ nhân tạo đang hỗ trợ nhiều công việc như tóm tắt văn bản, gợi ý tiêu đề, phân loại nội dung và phân tích hành vi người đọc. Với website tin tức, AI có thể hỗ trợ đề xuất bài viết phù hợp hơn.',
        ],
        'Khoa học' => [
            'title' => 'Nghiên cứu khoa học được phổ biến theo cách dễ hiểu hơn',
            'body' => 'Khoa học không chỉ nằm trong phòng thí nghiệm mà còn xuất hiện trong y tế, môi trường, công nghệ và giáo dục. Việc phổ biến khoa học theo cách dễ hiểu giúp người đọc tiếp cận kiến thức chính xác hơn.',
        ],
        'Đời sống' => [
            'title' => 'Đời sống đô thị thay đổi theo nhịp công nghệ',
            'body' => 'Đời sống đô thị hiện đại gắn liền với các dịch vụ số như đặt xe, giao hàng, thanh toán trực tuyến và mua sắm qua ứng dụng. Những tiện ích này giúp con người tiết kiệm thời gian.',
        ],
        'Du lịch' => [
            'title' => 'Du lịch trải nghiệm trở thành xu hướng được yêu thích',
            'body' => 'Nhiều người không chỉ đi du lịch để nghỉ ngơi mà còn muốn trải nghiệm văn hóa, ẩm thực và đời sống địa phương. Các điểm đến gần gũi thiên nhiên đang được quan tâm.',
        ],
        'Văn hóa' => [
            'title' => 'Văn hóa đọc thay đổi trong môi trường số',
            'body' => 'Trong môi trường số, người đọc có nhiều lựa chọn hơn như báo điện tử, mạng xã hội, sách điện tử và video kiến thức. Văn hóa đọc hiện đại đòi hỏi khả năng chọn lọc thông tin.',
        ],
        'Pháp luật' => [
            'title' => 'Quy định pháp luật cần được truyền tải dễ hiểu',
            'body' => 'Pháp luật ảnh hưởng đến nhiều hoạt động trong đời sống như giao thông, lao động, kinh doanh, môi trường mạng và quyền lợi người tiêu dùng.',
        ],
        'Bất động sản' => [
            'title' => 'Bất động sản chú trọng nhu cầu ở thực và hạ tầng',
            'body' => 'Thị trường bất động sản không chỉ phụ thuộc vào giá bán mà còn liên quan đến vị trí, pháp lý, hạ tầng giao thông và nhu cầu ở thực.',
        ],
    ];

    $seed = $templates[$categoryName] ?? [
        'title' => 'Cập nhật mới trong chuyên mục ' . $categoryName,
        'body' => 'Chuyên mục này đang được SmartNews cập nhật thêm nội dung nhằm giúp người đọc có nhiều lựa chọn hơn khi theo dõi tin tức.',
    ];

    return [
        'title' => $seed['title'],
        'content' => '
            <p>' . $seed['body'] . '</p>
            <p>Bài viết được biên tập theo hướng ngắn gọn, dễ hiểu và phù hợp với nhu cầu đọc tin nhanh trên website hiện đại.</p>
            <h2>Bối cảnh</h2>
            <p>Trong thời đại thông tin số, người đọc không chỉ cần xem tiêu đề mà còn muốn hiểu thêm bối cảnh, nguyên nhân và tác động của vấn đề. Vì vậy, SmartNews trình bày bài viết theo hướng rõ ý, dễ đọc và phù hợp với nhiều nhóm độc giả.</p>
            <h2>Nhận định</h2>
            <p>Nội dung cho thấy việc phân loại bài viết theo chuyên mục giúp người đọc tìm thông tin nhanh hơn. Đây cũng là phần quan trọng khi xây dựng một website tin tức có hệ thống.</p>
            <h2>Kết luận</h2>
            <p>SmartNews sẽ tiếp tục cập nhật thêm các bài viết liên quan để từng chuyên mục đều có nội dung rõ ràng, có thể đọc chi tiết và tương tác bằng lượt thích hoặc bình luận.</p>
        ',
    ];
}

function getCategoriesFromDatabase(PDO $pdo): array
{
    $stmt = $pdo->query("\n        SELECT\n            categories.id,\n            categories.name AS title,\n            COALESCE(categories.description, '') AS description,\n            COUNT(posts.id) AS published_count\n        FROM categories\n        LEFT JOIN posts\n            ON posts.category_id = categories.id\n           AND posts.status = 'published'\n        GROUP BY categories.id, categories.name, categories.description\n        ORDER BY categories.id ASC\n    ");

    return array_map(function (array $category): array {
        return [
            'id' => (int) $category['id'],
            'title' => $category['title'],
            'description' => $category['description'],
            'published_count' => (int) $category['published_count'],
            'link' => url('/index.php?category=' . (int) $category['id'] . '#new-articles'),
        ];
    }, $stmt->fetchAll());
}

function getPublishedPosts(PDO $pdo, string $section, ?int $categoryId = null, int $limit = 12): array
{
    $orderBy = $section === 'featured'
        ? 'posts.created_at DESC'
        : 'posts.created_at DESC';

    $sql = "\n        SELECT\n            posts.id,\n            posts.category_id,\n            posts.title,\n            posts.content,\n            posts.created_at,\n            users.full_name AS author_name,\n            categories.name AS category_name\n        FROM posts\n        INNER JOIN users ON users.id = posts.user_id\n        INNER JOIN categories ON categories.id = posts.category_id\n        WHERE posts.status = :status\n    ";

    $params = ['status' => 'published'];

    if ($categoryId !== null) {
        $sql .= " AND posts.category_id = :category_id ";
        $params['category_id'] = $categoryId;
    }

    $sql .= " ORDER BY {$orderBy} LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':status', $params['status'], PDO::PARAM_STR);

    if ($categoryId !== null) {
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return mapPostsForApi($stmt->fetchAll());
}

function getRecommendedPosts(PDO $pdo, int $limit = 6): array
{
    $stmt = $pdo->prepare("\n        SELECT\n            posts.id,\n            posts.category_id,\n            posts.title,\n            posts.content,\n            posts.created_at,\n            users.full_name AS author_name,\n            categories.name AS category_name\n        FROM posts\n        INNER JOIN users ON users.id = posts.user_id\n        INNER JOIN categories ON categories.id = posts.category_id\n        WHERE posts.status = 'published'\n        ORDER BY posts.created_at DESC\n        LIMIT :limit\n    ");

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return mapPostsForApi($stmt->fetchAll());
}

function searchPublishedPosts(PDO $pdo, string $keyword): array
{
    $keyword = trim($keyword);

    if ($keyword === '') {
        return [];
    }

    /*
     * Không dùng lại cùng một named placeholder nhiều lần trong SQL nữa.
     * PDO của project đang tắt emulate prepares, nên cách cũ dễ làm search bị lỗi.
     * Bản này lấy bài published rồi lọc bằng PHP sau khi chuẩn hóa tiếng Việt,
     * nhờ vậy tìm được cả "cong nghe" và "công nghệ".
     */
    $stmt = $pdo->query("\n        SELECT\n            posts.id,\n            posts.category_id,\n            posts.title,\n            posts.content,\n            posts.created_at,\n            users.full_name AS author_name,\n            categories.name AS category_name,\n            COALESCE(categories.description, '') AS category_description\n        FROM posts\n        INNER JOIN users ON users.id = posts.user_id\n        INNER JOIN categories ON categories.id = posts.category_id\n        WHERE posts.status = 'published'\n        ORDER BY posts.created_at DESC\n        LIMIT 1000\n    ");

    $posts = $stmt->fetchAll();
    $normalizedKeyword = normalizeVietnameseText($keyword);
    $words = array_values(array_filter(preg_split('/\s+/', $normalizedKeyword) ?: []));
    $terms = buildSearchTerms($keyword);

    $matches = [];

    foreach ($posts as $post) {
        $haystack = normalizeVietnameseText(
            ($post['title'] ?? '') . ' ' .
            strip_tags((string) ($post['content'] ?? '')) . ' ' .
            ($post['author_name'] ?? '') . ' ' .
            ($post['category_name'] ?? '') . ' ' .
            ($post['category_description'] ?? '')
        );

        $titleHaystack = normalizeVietnameseText((string) ($post['title'] ?? ''));
        $score = 0;

        if ($normalizedKeyword !== '' && str_contains($haystack, $normalizedKeyword)) {
            $score += 20;
        }

        if ($normalizedKeyword !== '' && str_contains($titleHaystack, $normalizedKeyword)) {
            $score += 30;
        }

        foreach ($terms as $term) {
            $normalizedTerm = normalizeVietnameseText($term);

            if ($normalizedTerm !== '' && str_contains($haystack, $normalizedTerm)) {
                $score += 8;
            }

            if ($normalizedTerm !== '' && str_contains($titleHaystack, $normalizedTerm)) {
                $score += 12;
            }
        }

        foreach ($words as $word) {
            if (mb_strlen($word, 'UTF-8') < 2) {
                continue;
            }

            if (str_contains($haystack, $word)) {
                $score += 3;
            }

            if (str_contains($titleHaystack, $word)) {
                $score += 5;
            }
        }

        if ($score > 0) {
            $post['_score'] = $score;
            $matches[] = $post;
        }
    }

    usort($matches, function (array $a, array $b): int {
        if (($a['_score'] ?? 0) !== ($b['_score'] ?? 0)) {
            return ($b['_score'] ?? 0) <=> ($a['_score'] ?? 0);
        }

        return strtotime((string) $b['created_at']) <=> strtotime((string) $a['created_at']);
    });

    return mapPostsForApi(array_slice($matches, 0, 20));
}

function buildSearchTerms(string $keyword): array
{
    $normalized = normalizeVietnameseText($keyword);
    $terms = [$keyword, $normalized];

    $synonyms = [
        'kinh te' => ['kinh tế', 'kinh doanh', 'tài chính', 'thị trường', 'doanh nghiệp'],
        'kinh doanh' => ['kinh doanh', 'kinh tế', 'doanh nghiệp', 'thị trường'],
        'tai chinh' => ['tài chính', 'kinh tế', 'ngân hàng', 'chứng khoán'],
        'bat dong san' => ['bất động sản', 'nhà đất', 'hạ tầng'],
        'cong nghe' => ['công nghệ', 'AI', 'chuyển đổi số', 'phần mềm', 'bảo mật'],
        'ai' => ['AI', 'trí tuệ nhân tạo', 'chuyển đổi số'],
        'giai tri' => ['giải trí', 'phim', 'âm nhạc', 'văn hóa'],
        'the thao' => ['thể thao', 'bóng đá', 'chạy bộ', 'luyện tập'],
        'suc khoe' => ['sức khỏe', 'dinh dưỡng', 'lối sống', 'giấc ngủ'],
        'giao duc' => ['giáo dục', 'học tập', 'tuyển sinh', 'sinh viên'],
        'thoi su' => ['thời sự', 'xã hội', 'cập nhật'],
        'chinh tri' => ['chính trị', 'chính sách', 'quản lý nhà nước'],
        'the gioi' => ['thế giới', 'quốc tế', 'toàn cầu'],
        'du lich' => ['du lịch', 'trải nghiệm', 'điểm đến'],
        'van hoa' => ['văn hóa', 'lễ hội', 'đọc sách'],
        'khoa hoc' => ['khoa học', 'nghiên cứu', 'môi trường'],
        'phap luat' => ['pháp luật', 'quy định', 'xã hội'],
    ];

    foreach ($synonyms as $key => $values) {
        if (str_contains($normalized, $key)) {
            $terms = array_merge($terms, $values);
        }
    }

    return array_values(array_unique(array_filter($terms)));
}

function normalizeVietnameseText(string $text): string
{
    $text = mb_strtolower(trim(strip_tags($text)), 'UTF-8');

    $map = [
        'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
        'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
        'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
        'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
        'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
        'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
        'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
        'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
        'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
        'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
        'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
        'đ' => 'd',
    ];

    $text = strtr($text, $map);
    $text = preg_replace('/[^a-z0-9\s]+/u', ' ', $text) ?? $text;
    $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

    return trim($text);
}

function mapPostsForApi(array $posts): array
{
    return array_map(function (array $post): array {
        $plainContent = trim(strip_tags((string) ($post['content'] ?? '')));
        $summary = mb_substr($plainContent, 0, 180, 'UTF-8');

        return [
            'id' => (int) $post['id'],
            'category_id' => isset($post['category_id']) ? (int) $post['category_id'] : null,
            'title' => $post['title'],
            'summary' => $summary . (mb_strlen($plainContent, 'UTF-8') > 180 ? '...' : ''),
            'author' => $post['author_name'],
            'category_name' => $post['category_name'],
            'publish_date' => $post['created_at'],
            'link' => url('/article.php?id=' . (int) $post['id']),
        ];
    }, $posts);
}
