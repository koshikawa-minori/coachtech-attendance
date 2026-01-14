CREATE DATABASE IF NOT EXISTS test_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- 念のため権限を明示したい場合
GRANT ALL PRIVILEGES ON test_db.* TO 'root'@'%';
FLUSH PRIVILEGES;
