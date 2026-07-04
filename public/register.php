<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

requireGuest();

$pdo = require __DIR__ . '/../config/db_connect.php';

require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';

$userModel = new User($pdo);
$authController = new AuthController($userModel);

$errors = [];
$old = [
    'full_name' => '',
    'email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $authController->register($_POST);

    if ($result['success']) {
        $_SESSION['flash_success'] = 'Đăng ký thành công. Vui lòng đăng nhập.';
        redirect('/login.php');
    }

    $errors = $result['errors'];
    $old = $result['old'];
}

$pageTitle = 'Đăng ký - SmartNews';

require_once __DIR__ . '/../app/Views/layout/header.php';

?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8">
                <div class="card auth-card">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="fw-bold mb-2 text-center">Tạo tài khoản</h2>

                        <p class="text-muted text-center mb-4">
                            Đăng ký để sử dụng hệ thống SmartNews.
                        </p>

                        <form method="POST" action="<?= url('/register.php') ?>" novalidate>
                            <div class="mb-3">
                                <label for="full_name" class="form-label">
                                    Họ và tên
                                </label>

                                <input 
                                    type="text"
                                    name="full_name"
                                    id="full_name"
                                    class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
                                    value="<?= e($old['full_name']) ?>"
                                    placeholder="Nguyễn Văn A"
                                >

                                <?php if (isset($errors['full_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= e($errors['full_name']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    Email
                                </label>

                                <input 
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                    value="<?= e($old['email']) ?>"
                                    placeholder="example@gmail.com"
                                >

                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?= e($errors['email']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    Mật khẩu
                                </label>

                                <input 
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                    placeholder="Ít nhất 8 ký tự"
                                >

                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?= e($errors['password']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">
                                    Nhập lại mật khẩu
                                </label>

                                <input 
                                    type="password"
                                    name="confirm_password"
                                    id="confirm_password"
                                    class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                                    placeholder="Nhập lại mật khẩu"
                                >

                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback">
                                        <?= e($errors['confirm_password']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                Đăng ký
                            </button>
                        </form>

                        <p class="text-center mt-4 mb-0">
                            Đã có tài khoản?
                            <a href="<?= url('/login.php') ?>" class="text-decoration-none fw-semibold">
                                Đăng nhập
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    
<?php

require_once __DIR__ . '/../app/Views/layout/footer.php';

?>