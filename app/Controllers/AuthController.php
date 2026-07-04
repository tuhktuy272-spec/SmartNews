<?php

declare(strict_types=1);

/**
 * AuthController xử lý logic đăng ký, đăng nhập.
 */
class AuthController
{
    private User $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * Xử lý đăng ký tài khoản.
     */
    public function register(array $postData): array
    {
        $errors = [];

        $fullName = trim($postData['full_name'] ?? '');
        $email = strtolower(trim($postData['email'] ?? ''));
        $password = $postData['password'] ?? '';
        $confirmPassword = $postData['confirm_password'] ?? '';

        if ($fullName === '') {
            $errors['full_name'] = 'Vui lòng nhập họ tên.';
        }

        if ($email === '') {
            $errors['email'] = 'Vui lòng nhập email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ.';
        }

        if ($password === '') {
            $errors['password'] = 'Vui lòng nhập mật khẩu.';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 8 ký tự.';
        }

        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Vui lòng nhập lại mật khẩu.';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Mật khẩu nhập lại không khớp.';
        }

        /**
         * Bước quan trọng:
         * Kiểm tra trùng email trước khi insert vào database.
         */
        if (!isset($errors['email']) && $this->userModel->emailExists($email)) {
            $errors['email'] = 'Email này đã được đăng ký.';
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'old' => [
                    'full_name' => $fullName,
                    'email' => $email,
                ],
            ];
        }

        $this->userModel->create($fullName, $email, $password);

        return [
            'success' => true,
            'errors' => [],
            'old' => [],
        ];
    }

    /**
     * Xử lý đăng nhập.
     */
    public function login(array $postData): array
    {
        $errors = [];

        $email = strtolower(trim($postData['email'] ?? ''));
        $password = $postData['password'] ?? '';

        if ($email === '') {
            $errors['email'] = 'Vui lòng nhập email.';
        }

        if ($password === '') {
            $errors['password'] = 'Vui lòng nhập mật khẩu.';
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'old' => [
                    'email' => $email,
                ],
            ];
        }

        $user = $this->userModel->verifyLogin($email, $password);

        if (!$user) {
            return [
                'success' => false,
                'errors' => [
                    'login' => 'Email hoặc mật khẩu không đúng.',
                ],
                'old' => [
                    'email' => $email,
                ],
            ];
        }

        /**
         * Tạo lại session ID sau khi đăng nhập để hạn chế Session Fixation.
         */
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'role_id' => $user['role_id'],
            'role_name' => $user['role_name'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
        ];

        return [
            'success' => true,
            'errors' => [],
            'old' => [],
        ];
    }
}