<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

requireAuth();

$pageTitle = 'Dashboard - SmartNews';

require_once __DIR__ . '/../app/Views/layout/header.php';

?>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card auth-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Thông tin tài khoản</h5>

                        <p class="mb-2">
                            <strong>Họ tên:</strong>
                            <?= e($_SESSION['user']['full_name']) ?>
                        </p>

                        <p class="mb-2">
                            <strong>Email:</strong>
                            <?= e($_SESSION['user']['email']) ?>
                        </p>

                        <p class="mb-0">
                            <strong>Vai trò:</strong>
                            <span class="badge bg-primary">
                                <?= e($_SESSION['user']['role_name']) ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card auth-card">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">
                            Chào mừng đến với SmartNews Dashboard
                        </h4>

                        <p class="text-muted mb-4">
                            Bạn đã đăng nhập thành công. Đây là khu vực quản trị/tài khoản.
                            Các module sau này có thể mở rộng tại đây như:
                        </p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4">
                                    Quản lý bài viết
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4">
                                    Quản lý danh mục
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4">
                                    Quản lý bình luận
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4">
                                    Quản lý người dùng
                                </div>
                            </div>
                        </div>

                        <a href="<?= url('/logout.php') ?>" class="btn btn-outline-danger mt-4">
                            Đăng xuất
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    
<?php

require_once __DIR__ . '/../app/Views/layout/footer.php';

?>