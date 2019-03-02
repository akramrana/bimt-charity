-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.25-log - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             10.1.0.5464
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for bimt_charity
CREATE DATABASE IF NOT EXISTS `bimt_charity` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `bimt_charity`;

-- Dumping structure for table bimt_charity.expenses
CREATE TABLE IF NOT EXISTS `expenses` (
  `expense_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `purpose` text,
  `is_deleted` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`expense_id`),
  KEY `FK_expenses_users` (`user_id`),
  CONSTRAINT `FK_expenses_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table bimt_charity.expenses: ~0 rows (approximately)
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;

-- Dumping structure for table bimt_charity.fund_requests
CREATE TABLE IF NOT EXISTS `fund_requests` (
  `fund_request_id` int(11) NOT NULL AUTO_INCREMENT,
  `fund_request_number` varchar(50) NOT NULL,
  `request_user_id` int(11) NOT NULL,
  `request_description` text NOT NULL,
  `request_amount` float NOT NULL,
  `file` varchar(250) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`fund_request_id`),
  UNIQUE KEY `fund_request_number` (`fund_request_number`),
  KEY `FK_fund_requests_users` (`request_user_id`),
  CONSTRAINT `FK_fund_requests_users` FOREIGN KEY (`request_user_id`) REFERENCES `users` (`user_id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table bimt_charity.fund_requests: ~0 rows (approximately)
/*!40000 ALTER TABLE `fund_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `fund_requests` ENABLE KEYS */;

-- Dumping structure for table bimt_charity.fund_request_status
CREATE TABLE IF NOT EXISTS `fund_request_status` (
  `fund_request_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `fund_request_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comments` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`fund_request_status_id`),
  KEY `FK_fund_request_status_fund_requests` (`fund_request_id`),
  KEY `FK_fund_request_status_status` (`status_id`),
  KEY `FK_fund_request_status_users` (`user_id`),
  CONSTRAINT `FK_fund_request_status_fund_requests` FOREIGN KEY (`fund_request_id`) REFERENCES `fund_requests` (`fund_request_id`) ON UPDATE NO ACTION,
  CONSTRAINT `FK_fund_request_status_status` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON UPDATE NO ACTION,
  CONSTRAINT `FK_fund_request_status_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table bimt_charity.fund_request_status: ~0 rows (approximately)
/*!40000 ALTER TABLE `fund_request_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `fund_request_status` ENABLE KEYS */;

-- Dumping structure for table bimt_charity.login_history
CREATE TABLE IF NOT EXISTS `login_history` (
  `login_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`login_history_id`),
  KEY `FK_login_history_users` (`user_id`),
  CONSTRAINT `FK_login_history_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table bimt_charity.login_history: ~0 rows (approximately)
/*!40000 ALTER TABLE `login_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_history` ENABLE KEYS */;

-- Dumping structure for table bimt_charity.monthly_invoice
CREATE TABLE IF NOT EXISTS `monthly_invoice` (
  `monthly_invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `monthly_invoice_number` varchar(50) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `instalment_month` varchar(50) NOT NULL,
  `instalment_year` varchar(50) NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`monthly_invoice_id`),
  UNIQUE KEY `monthly_invoice_number` (`monthly_invoice_number`),
  KEY `FK_monthly_invoice_users` (`receiver_id`),
  CONSTRAINT `FK_monthly_invoice_users` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table bimt_charity.monthly_invoice: ~0 rows (approximately)
/*!40000 ALTER TABLE `monthly_invoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `monthly_invoice` ENABLE KEYS */;

-- Dumping structure for table bimt_charity.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('FR','MI','PREC','PREL','GE') DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `comments` text NOT NULL,
  `created_at` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table bimt_charity.notifications: ~0 rows (approximately)
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;

-- Dumping structure for table bimt_charity.payment_received
CREATE TABLE IF NOT EXISTS `payment_received` (
  `payment_received_id` int(11) NOT NULL AUTO_INCREMENT,
  `received_invoice_number` varchar(50) NOT NULL,
  `donated_by` int(11) NOT NULL,
  `received_by` int(11) NOT NULL,
  `comments` text,
  `amount` float NOT NULL,
  `instalment_month` varchar(50) DEFAULT NULL,
  `instalment_year` varchar(50) DEFAULT NULL,
  `has_invoice` tinyint(1) DEFAULT '0',
  `monthly_invoice_id` int(11) DEFAULT NULL,
  `created_at` float NOT NULL,
  `updated_at` float NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`payment_received_id`),
  UNIQUE KEY `invoice_number` (`received_invoice_number`),
  KEY `FK_payment_received_users` (`donated_by`),
  KEY `FK_payment_received_users_2` (`received_by`),
  KEY `FK_payment_received_monthly_invoice` (`monthly_invoice_id`),
  CONSTRAINT `FK_payment_received_monthly_invoice` FOREIGN KEY (`monthly_invoice_id`) REFERENCES `monthly_invoice` (`monthly_invoice_id`) ON UPDATE NO ACTION,
  CONSTRAINT `FK_payment_received_users` FOREIGN KEY (`donated_by`) REFERENCES `users` (`user_id`) ON UPDATE NO ACTION,
  CONSTRAINT `FK_payment_received_users_2` FOREIGN KEY (`received_by`) REFERENCES `users` (`user_id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table bimt_charity.payment_received: ~0 rows (approximately)
/*!40000 ALTER TABLE `payment_received` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_received` ENABLE KEYS */;

-- Dumping structure for table bimt_charity.payment_release
CREATE TABLE IF NOT EXISTS `payment_release` (
  `payment_release_id` int(11) NOT NULL AUTO_INCREMENT,
  `release_invoice_number` varchar(50) NOT NULL,
  `fund_request_id` int(11) NOT NULL,
  `release_by` int(11) NOT NULL,
  `amount` float NOT NULL,
  `note` text,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`payment_release_id`),
  UNIQUE KEY `invoice_number` (`release_invoice_number`),
  KEY `FK_payment_release_fund_requests` (`fund_request_id`),
  KEY `FK_payment_release_users` (`release_by`),
  CONSTRAINT `FK_payment_release_fund_requests` FOREIGN KEY (`fund_request_id`) REFERENCES `fund_requests` (`fund_request_id`) ON UPDATE NO ACTION,
  CONSTRAINT `FK_payment_release_users` FOREIGN KEY (`release_by`) REFERENCES `users` (`user_id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table bimt_charity.payment_release: ~0 rows (approximately)
/*!40000 ALTER TABLE `payment_release` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_release` ENABLE KEYS */;

-- Dumping structure for table bimt_charity.status
CREATE TABLE IF NOT EXISTS `status` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table bimt_charity.status: ~0 rows (approximately)
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
/*!40000 ALTER TABLE `status` ENABLE KEYS */;

-- Dumping structure for table bimt_charity.users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(50) NOT NULL,
  `image` varchar(250) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `alt_phone` varchar(50) DEFAULT NULL,
  `address` text,
  `batch` varchar(50) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `enable_login` int(11) NOT NULL DEFAULT '0',
  `password` int(11) NOT NULL,
  `user_type` enum('S','A','M','G') NOT NULL,
  `recurring_amount` float NOT NULL DEFAULT '500',
  `is_active` tinyint(4) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table bimt_charity.users: ~0 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
