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
    'email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $authController->login($_POST);

    if ($result['success']) {
        redirect('/dashboard.php');
    }

    $errors = $result['errors'];
    $old = $result['old'];
}

$successMessage = getFlash('flash_success');

$pageTitle = 'Đăng nhập - SmartNews';

require_once __DIR__ . '/../app/Views/layout/header.php';

?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8">
                <div class="card auth-card">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="fw-bold mb-2 text-center">Đăng nhập</h2>

                        <p class="text-muted text-center mb-4">
                            Truy cập tài khoản SmartNews của bạn.
                        </p>

                        <?php if ($successMessage): ?>
                            <div class="alert alert-success">
                                <?= e($successMessage) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($errors['login'])): ?>
                            <div class="alert alert-danger">
                                <?= e($errors['login']) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?= url('/login.php') ?>" novalidate>
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

                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    Mật khẩu
                                </label>

                                <input 
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                    placeholder="Nhập mật khẩu"
                                >

                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?= e($errors['password']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                Đăng nhập
                            </button>
                        </form>

                        <p class="text-center mt-4 mb-0">
                            Chưa có tài khoản?
                            <a href="<?= url('/register.php') ?>" class="text-decoration-none fw-semibold">
                                Đăng ký
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