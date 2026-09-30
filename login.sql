CREATE DATABASE IF NOT EXISTS spiderman_db;
USE spiderman_db;

CREATE TABLE IF NOT EXISTS `felhasznalok` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

INSERT INTO `felhasznalok` (`username`, `email`, `password`) VALUES
  ('Kovi', 'kovi@example.com', '$2y$10$krazRmqJQ8ZYwg.9xaKm3ereQQfXx1cO5fbqm6AiwY8bw3hm2jdqq'),
  ('lopeaonm', 'erika@example.com', '$2y$10$10wGTVCJCD3nX4qoSD8yZ.v3tzhdLQvhmH3wpfR0qJoUTNsLhwzwW'),
  ('petya', 'peter@example.com', '$2y$10$2Rx2Qekg5n2KH.Z.BgmNauJ/w/DKhlPbovZJec.DstpamJ746UXBK');

