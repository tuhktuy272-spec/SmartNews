<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../app/Helpers/functions.php';

requireAdmin();

$pdo = require __DIR__ . '/../../config/db_connect.php';

$adminId = (int) ($_SESSION['user']['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleUserAction($pdo, $adminId);
}

$search = trim($_GET['search'] ?? '');
$role = $_GET['role'] ?? '';
$status = $_GET['status'] ?? '';

$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(users.full_name LIKE :search_name OR users.email LIKE :search_email)";
    $params['search_name'] = '%' . $search . '%';
    $params['search_email'] = '%' . $search . '%';
}

if (in_array($role, ['admin', 'editor', 'user'], true)) {
    $where[] = "roles.name = :role";
    $params['role'] = $role;
}

if (in_array($status, ['active', 'locked'], true)) {
    $where[] = "users.status = :status";
    $params['status'] = $status;
}

$whereSql = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);

$stmt = $pdo->prepare("
    SELECT
        users.id,
        users.full_name,
        users.email,
        users.status,
        users.created_at,
        roles.name AS role_name
    FROM users
    INNER JOIN roles ON roles.id = users.role_id
    $whereSql
    ORDER BY users.id ASC
");

foreach ($params as $key => $value) {
    $stmt->bindValue(':' . $key, $value);
}

$stmt->execute();

$users = $stmt->fetchAll();

$pageTitle = 'Quản lý người dùng - SmartNews';

require_once __DIR__ . '/../../app/Views/layout/header.php';

?>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-1">Quản lý người dùng</h1>

                <p class="text-muted mb-0">
                    Admin có thể đổi quyền user thành editor, admin hoặc khóa tài khoản.
                </p>
            </div>

            <a href="<?= url('/admin/dashboard.php') ?>" class="btn btn-outline-secondary">
                Về Admin Dashboard
            </a>
        </div>

        <?php renderFlashMessages(); ?>

        <div class="card auth-card mb-4">
            <div class="card-body p-4">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Tìm theo tên hoặc email"
                            value="<?= e($search) ?>"
                        >
                    </div>

                    <div class="col-md-3">
                        <select name="role" class="form-select">
                            <option value="">Tất cả quyền</option>
                            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="editor" <?= $role === 'editor' ? 'selected' : '' ?>>Editor</option>
                            <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>User</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                            <option value="locked" <?= $status === 'locked' ? 'selected' : '' ?>>Đã khóa</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">
                            Lọc
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card auth-card">
            <div class="card-body p-4">
                <?php if (empty($users)): ?>
                    <p class="text-muted mb-0">
                        Không có người dùng phù hợp.
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Người dùng</th>
                                    <th>Email</th>
                                    <th>Quyền hiện tại</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Đổi quyền</th>
                                    <th>Khóa/Mở</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= (int) $user['id'] ?></td>

                                        <td>
                                            <strong><?= e($user['full_name']) ?></strong>

                                            <?php if ((int) $user['id'] === $adminId): ?>
                                                <div class="small text-muted">
                                                    Tài khoản hiện tại
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <td><?= e($user['email']) ?></td>

                                        <td>
                                            <?php if ($user['role_name'] === 'admin'): ?>
                                                <span class="badge bg-danger">Admin</span>
                                            <?php elseif ($user['role_name'] === 'editor'): ?>
                                                <span class="badge bg-primary">Editor</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">User</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if ($user['status'] === 'active'): ?>
                                                <span class="badge bg-success">Đang hoạt động</span>
                                            <?php else: ?>
                                                <span class="badge bg-dark">Đã khóa</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                                        </td>

                                        <td style="min-width: 220px;">
                                            <form method="POST" class="d-flex gap-2">
                                                <input type="hidden" name="action" value="set_role">

                                                <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">

                                                <select name="role" class="form-select form-select-sm">
                                                    <option value="user" <?= $user['role_name'] === 'user' ? 'selected' : '' ?>>
                                                        User
                                                    </option>

                                                    <option value="editor" <?= $user['role_name'] === 'editor' ? 'selected' : '' ?>>
                                                        Editor
                                                    </option>

                                                    <option value="admin" <?= $user['role_name'] === 'admin' ? 'selected' : '' ?>>
                                                        Admin
                                                    </option>
                                                </select>

                                                <button class="btn btn-sm btn-outline-primary">
                                                    Lưu
                                                </button>
                                            </form>
                                        </td>

                                        <td>
                                            <form method="POST">
                                                <input type="hidden" name="action" value="toggle_status">

                                                <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">

                                                <button
                                                    class="btn btn-sm <?= $user['status'] === 'active' ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                                    <?= (int) $user['id'] === $adminId ? 'disabled' : '' ?>
                                                >
                                                    <?= $user['status'] === 'active' ? 'Khóa' : 'Mở khóa' ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php

require_once __DIR__ . '/../../app/Views/layout/footer.php';

function handleUserAction(PDO $pdo, int $adminId): void
{
    $action = $_POST['action'] ?? '';
    $userId = (int) ($_POST['user_id'] ?? 0);

    if ($userId <= 0) {
        setFlash('flash_error', 'ID người dùng không hợp lệ.');
        redirect('/admin/users.php');
    }

    if ($action === 'set_role') {
        $newRole = $_POST['role'] ?? '';

        if (!in_array($newRole, ['admin', 'editor', 'user'], true)) {
            setFlash('flash_error', 'Quyền không hợp lệ.');
            redirect('/admin/users.php');
        }

        if ($userId === $adminId && $newRole !== 'admin') {
            setFlash('flash_error', 'Không thể tự hạ quyền admin của chính mình.');
            redirect('/admin/users.php');
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

        $roleId = $roleStmt->fetchColumn();

        if (!$roleId) {
            setFlash('flash_error', 'Không tìm thấy quyền trong database.');
            redirect('/admin/users.php');
        }

        $updateStmt = $pdo->prepare("
            UPDATE users
            SET role_id = :role_id
            WHERE id = :id
        ");

        $updateStmt->execute([
            'role_id' => (int) $roleId,
            'id' => $userId,
        ]);

        setFlash('flash_success', 'Đã cập nhật quyền người dùng thành ' . $newRole . '.');
        redirect('/admin/users.php');
    }

    if ($action === 'toggle_status') {
        if ($userId === $adminId) {
            setFlash('flash_error', 'Không thể tự khóa tài khoản của chính mình.');
            redirect('/admin/users.php');
        }

        $statusStmt = $pdo->prepare("
            SELECT status
            FROM users
            WHERE id = :id
            LIMIT 1
        ");

        $statusStmt->execute([
            'id' => $userId,
        ]);

        $currentStatus = $statusStmt->fetchColumn();

        if (!$currentStatus) {
            setFlash('flash_error', 'Không tìm thấy người dùng.');
            redirect('/admin/users.php');
        }

        $newStatus = $currentStatus === 'active' ? 'locked' : 'active';

        $updateStmt = $pdo->prepare("
            UPDATE users
            SET status = :status
            WHERE id = :id
        ");

        $updateStmt->execute([
            'status' => $newStatus,
            'id' => $userId,
        ]);

        setFlash('flash_success', 'Đã cập nhật trạng thái tài khoản.');
        redirect('/admin/users.php');
    }

    setFlash('flash_error', 'Hành động không hợp lệ.');
    redirect('/admin/users.php');
}