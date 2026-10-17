-- Voyage ticket system schema
-- Import into an empty `ticket_system` database.
-- Includes the fictional demo account records from the supplied dump.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `accounts` (`id`, `username`, `email`, `phone`, `password`, `created_at`, `is_admin`) VALUES
(1, 'Mouse', 'mouse@mouse.com', '+0', 'MOUSE', '2026-05-19 06:48:05', 1),
(2, 'Admin', 'admin@mouse.com', '+1', 'admin', '2026-09-27 04:28:25', 1),
(13, 'Bob', 'test@email.com', '+00000000000000', 'test', '2026-09-27 07:46:01', 0);

CREATE TABLE IF NOT EXISTS `available_trips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `origin` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `ticket_date` date NOT NULL,
  `ticket_time` time NOT NULL DEFAULT '00:00:00',
  `created_at` datetime DEFAULT current_timestamp(),
  `estimated_arrival_time` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `accounts` AUTO_INCREMENT = 14;

CREATE TABLE IF NOT EXISTS `ticket_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trip_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone_number` varchar(30) NOT NULL,
  `origin` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `card_number` varchar(50) NOT NULL DEFAULT '',
  `qr_token` char(64) DEFAULT NULL,
  `ticket_date` date NOT NULL,
  `ticket_time` time NOT NULL DEFAULT '00:00:00',
  `created_at` datetime DEFAULT current_timestamp(),
  `estimated_arrival_time` time NOT NULL DEFAULT '00:00:00',
  `email` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
