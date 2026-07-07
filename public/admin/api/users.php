<?php

declare(strict_types=1);

/**
 * ============================================================
 * MODULE 5 - API QUẢN LÝ USER DÀNH CHO ADMIN
 * ============================================================
 *
 * GET    users.php?page=1&limit=10&search=&role=&status=
 * GET    users.php?id=1
 * PUT    users.php
 * DELETE users.php?id=2
 */

require_once __DIR__ . '/../../../app/Helpers/api_functions.php';

$admin = api_require_admin();

$pdo = require __DIR__ . '/../../../config/db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGetUsers($pdo);
            break;

        case 'PUT':
            handleUpdateUser($pdo, (int) $admin['id']);
            break;

        case 'DELETE':
            handleDeleteUser($pdo, (int) $admin['id']);
            break;

        default:
            api_json_response([
                'success' => false,
                'message' => 'Phương thức không được hỗ trợ.',
            ], 405);
    }
} catch (PDOException $e) {
    api_json_response([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
    ], 500);
}

/**
 * Xử lý GET:
 * - Nếu có id: trả về chi tiết 1 user.
 * - Nếu không có id: trả về danh sách user có phân trang, tìm kiếm, lọc.
 */
function handleGetUsers(PDO $pdo): void
{
    if (!empty($_GET['id'])) {
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

        if (!$id) {
            api_json_response([
                'success' => false,
                'message' => 'ID user không hợp lệ.',
            ], 422);
        }

        $stmt = $pdo->prepare("
            SELECT
                users.id,
                users.full_name,
                users.email,
                users.status,
                users.created_at,
                users.updated_at,
                roles.name AS role_name
            FROM users
            INNER JOIN roles ON roles.id = users.role_id
            WHERE users.id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id,
        ]);

        $user = $stmt->fetch();

        if (!$user) {
            api_json_response([
                'success' => false,
                'message' => 'Không tìm thấy user.',
            ], 404);
        }

        api_json_response([
            'success' => true,
            'data' => $user,
        ]);
    }

    $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1;
    $limit = filter_var($_GET['limit'] ?? 10, FILTER_VALIDATE_INT) ?: 10;

    $page = max(1, $page);
    $limit = max(1, min(100, $limit));

    $offset = ($page - 1) * $limit;

    $search = trim($_GET['search'] ?? '');
    $roleFilter = trim($_GET['role'] ?? '');
    $statusFilter = trim($_GET['status'] ?? '');

    $where = [];
    $params = [];

    if ($search !== '') {
        $where[] = "(users.full_name LIKE :search_name OR users.email LIKE :search_email)";
        $params['search_name'] = '%' . $search . '%';
        $params['search_email'] = '%' . $search . '%';
    }

    if (in_array($roleFilter, ['admin', 'editor', 'user'], true)) {
        $where[] = "roles.name = :role_name";
        $params['role_name'] = $roleFilter;
    }

    if (in_array($statusFilter, ['active', 'locked'], true)) {
        $where[] = "users.status = :status";
        $params['status'] = $statusFilter;
    }

    $whereSql = '';

    if (!empty($where)) {
        $whereSql = 'WHERE ' . implode(' AND ', $where);
    }

    $countStmt = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM users
        INNER JOIN roles ON roles.id = users.role_id
        $whereSql
    ");

    foreach ($params as $key => $value) {
        $countStmt->bindValue(':' . $key, $value);
    }

    $countStmt->execute();

    $total = (int) $countStmt->fetch()['total'];

    $stmt = $pdo->prepare("
        SELECT
            users.id,
            users.full_name,
            users.email,
            users.status,
            users.created_at,
            users.updated_at,
            roles.name AS role_name
        FROM users
        INNER JOIN roles ON roles.id = users.role_id
        $whereSql
        ORDER BY users.created_at DESC
        LIMIT :limit OFFSET :offset
    ");

    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    api_json_response([
        'success' => true,
        'data' => $stmt->fetchAll(),
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'total_pages' => (int) ceil($total / $limit),
        ],
    ]);
}

/**
 * Xử lý PUT:
 * Body JSON:
 * {
 *   "id": 2,
 *   "action": "toggle_status"
 * }
 *
 * Hoặc:
 * {
 *   "id": 2,
 *   "action": "set_role",
 *   "role": "editor"
 * }
 */
function handleUpdateUser(PDO $pdo, int $adminId): void
{
    $body = api_get_json_body();

    $id = filter_var($body['id'] ?? null, FILTER_VALIDATE_INT);
    $action = $body['action'] ?? '';

    if (!$id) {
        api_json_response([
            'success' => false,
            'message' => 'Thiếu id user.',
        ], 422);
    }

    if ((int) $id === $adminId) {
        api_json_response([
            'success' => false,
            'message' => 'Không thể tự khóa hoặc tự đổi quyền chính mình.',
        ], 400);
    }

    $stmt = $pdo->prepare("
        SELECT
            users.id,
            users.status,
            users.role_id,
            roles.name AS role_name
        FROM users
        INNER JOIN roles ON roles.id = users.role_id
        WHERE users.id = :id
        LIMIT 1
    ");

    $stmt->execute([
        'id' => $id,
    ]);

    $targetUser = $stmt->fetch();

    if (!$targetUser) {
        api_json_response([
            'success' => false,
            'message' => 'Không tìm thấy user.',
        ], 404);
    }

    if ($action === 'toggle_status') {
        $newStatus = $targetUser['status'] === 'active'
            ? 'locked'
            : 'active';

        $updateStmt = $pdo->prepare("
            UPDATE users
            SET status = :status
            WHERE id = :id
        ");

        $updateStmt->execute([
            'status' => $newStatus,
            'id' => $id,
        ]);

        api_json_response([
            'success' => true,
            'message' => $newStatus === 'locked'
                ? 'Đã khóa tài khoản.'
                : 'Đã mở khóa tài khoản.',
            'data' => [
                'id' => $id,
                'status' => $newStatus,
            ],
        ]);
    }

    if ($action === 'set_role') {
        $newRole = $body['role'] ?? '';

        if (!in_array($newRole, ['admin', 'editor', 'user'], true)) {
            api_json_response([
                'success' => false,
                'message' => 'Role không hợp lệ.',
            ], 422);
        }

        $roleStmt = $pdo->prepare("
            SELECT id
            FROM roles
            WHERE name = :name
            LIMIT 1
        ");

        $roleStmt->execute([
            'name' => $newRole,
        ]);

        $role = $roleStmt->fetch();

        if (!$role) {
            api_json_response([
                'success' => false,
                'message' => 'Role chưa tồn tại trong bảng roles.',
            ], 404);
        }

        $updateStmt = $pdo->prepare("
            UPDATE users
            SET role_id = :role_id
            WHERE id = :id
        ");

        $updateStmt->execute([
            'role_id' => (int) $role['id'],
            'id' => $id,
        ]);

        api_json_response([
            'success' => true,
            'message' => 'Đã cập nhật phân quyền.',
            'data' => [
                'id' => $id,
                'role_name' => $newRole,
            ],
        ]);
    }

    api_json_response([
        'success' => false,
        'message' => 'Action không hợp lệ. Chỉ hỗ trợ toggle_status hoặc set_role.',
    ], 422);
}

/**
 * Xử lý DELETE:
 * DELETE users.php?id=2
 *
 * Lưu ý:
 * Không xóa cứng user khỏi database vì user có thể đang liên kết
 * với posts, articles, comments, reading_logs, page_views...
 *
 * Thay vào đó, ta khóa tài khoản bằng status = 'locked'.
 */
function handleDeleteUser(PDO $pdo, int $adminId): void
{
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

    if (!$id) {
        api_json_response([
            'success' => false,
            'message' => 'Thiếu id user.',
        ], 422);
    }

    if ((int) $id === $adminId) {
        api_json_response([
            'success' => false,
            'message' => 'Không thể tự xóa hoặc tự khóa chính mình.',
        ], 400);
    }

    $checkStmt = $pdo->prepare("
        SELECT 
            users.id,
            users.full_name,
            users.email,
            users.status,
            roles.name AS role_name
        FROM users
        INNER JOIN roles ON roles.id = users.role_id
        WHERE users.id = :id
        LIMIT 1
    ");

    $checkStmt->execute([
        'id' => $id,
    ]);

    $user = $checkStmt->fetch();

    if (!$user) {
        api_json_response([
            'success' => false,
            'message' => 'Không tìm thấy user.',
        ], 404);
    }

    if ($user['role_name'] === 'admin') {
        api_json_response([
            'success' => false,
            'message' => 'Không nên xóa tài khoản admin khác. Hãy đổi quyền trước nếu cần.',
        ], 400);
    }

    $updateStmt = $pdo->prepare("
        UPDATE users
        SET status = 'locked'
        WHERE id = :id
    ");

    $updateStmt->execute([
        'id' => $id,
    ]);

    api_json_response([
        'success' => true,
        'message' => 'Đã vô hiệu hóa tài khoản. Dữ liệu liên quan vẫn được giữ an toàn.',
        'data' => [
            'id' => $id,
            'status' => 'locked',
        ],
    ]);
}