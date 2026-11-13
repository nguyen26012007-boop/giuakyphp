CREATE DATABASE IF NOT EXISTS QLNhanSu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE QLNhanSu;
CREATE TABLE IF NOT EXISTS Users (
    admin_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    admin_email VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    admin_password VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    images VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (admin_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
