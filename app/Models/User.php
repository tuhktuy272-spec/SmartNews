<?php

declare(strict_types=1);

/**
 * Model User chịu trách nhiệm làm việc với bảng users.
 */
class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Tìm user theo email.
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "
            SELECT 
                users.id,
                users.role_id,
                users.full_name,
                users.email,
                users.password_hash,
                users.status,
                roles.name AS role_name
            FROM users
            INNER JOIN roles ON users.role_id = roles.id
            WHERE users.email = :email
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $email,
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Kiểm tra email đã tồn tại chưa.
     * Bắt buộc dùng trước khi insert user mới.
     */
    public function emailExists(string $email): bool
    {
        $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $email,
        ]);

        return (bool) $stmt->fetch();
    }

    /**
     * Tạo user mới.
     * Mật khẩu được mã hóa bằng password_hash().
     */
    public function create(
        string $fullName,
        string $email,
        string $password,
        int $roleId = 2
    ): int {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "
            INSERT INTO users 
                (role_id, full_name, email, password_hash)
            VALUES 
                (:role_id, :full_name, :email, :password_hash)
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'role_id' => $roleId,
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Xác thực đăng nhập.
     */
    public function verifyLogin(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return null;
        }

        if ($user['status'] !== 'active') {
            return null;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }

        return $user;
    }
}