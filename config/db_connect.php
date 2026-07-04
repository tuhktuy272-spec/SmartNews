<?php

declare(strict_types=1);

/**
 * File kết nối database bằng PDO.
 * PDO giúp truy vấn an toàn hơn, hỗ trợ prepared statement để chống SQL Injection.
 */

$host = '127.0.0.1';
$dbName = 'smartnews';
$dbUser = 'root';
$dbPass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbName};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

    // Trả dữ liệu dạng mảng kết hợp: ['email' => 'abc@gmail.com']
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

    // Tắt giả lập prepared statement để tăng độ an toàn
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    return new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    die('Không thể kết nối database. Vui lòng kiểm tra lại cấu hình.');
}