CREATE DATABASE IF NOT EXISTS spiderman_db;
USE spiderman_db;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `token` VARCHAR(64) DEFAULT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

SET @add_token_column = IF(
  (SELECT COUNT(*)
   FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'users'
     AND COLUMN_NAME = 'token') = 0,
  'ALTER TABLE `users` ADD COLUMN `token` VARCHAR(64) DEFAULT NULL UNIQUE',
  'SELECT 1'
);
PREPARE token_column_statement FROM @add_token_column;
EXECUTE token_column_statement;
DEALLOCATE PREPARE token_column_statement;

INSERT INTO `users` (`username`, `email`, `password`) VALUES
  ('Kovi', 'kovi@example.com', '$2y$10$krazRmqJQ8ZYwg.9xaKm3ereQQfXx1cO5fbqm6AiwY8bw3hm2jdqq'),
  ('lopeaonm', 'erika@example.com', '$2y$10$10wGTVCJCD3nX4qoSD8yZ.v3tzhdLQvhmH3wpfR0qJoUTNsLhwzwW'),
  ('petya', 'peter@example.com', '$2y$10$2Rx2Qekg5n2KH.Z.BgmNauJ/w/DKhlPbovZJec.DstpamJ746UXBK');

CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_name` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;