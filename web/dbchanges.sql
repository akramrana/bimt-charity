/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  akram
 * Created: Apr 25, 2019
 */
ALTER TABLE `payment_received`
	ADD COLUMN `received_date` DATE NULL DEFAULT NULL AFTER `monthly_invoice_id`;

CREATE TABLE `documents` (
	`document_id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NULL DEFAULT NULL,
	`description` TEXT NULL,
	`file` VARCHAR(255) NOT NULL,
	`user_id` INT(11) NOT NULL,
	`created_at` DATETIME NOT NULL,
	`is_deleted` TINYINT(4) NOT NULL DEFAULT '0',
	PRIMARY KEY (`document_id`),
	INDEX `FK_documents_users` (`user_id`),
	CONSTRAINT `FK_documents_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;


ALTER TABLE `notifications`
	CHANGE COLUMN `type` `type` ENUM('FR','MI','PREC','PREL','EX','US','D') NULL DEFAULT NULL AFTER `notification_id`;

ALTER TABLE `fund_requests`
	ADD COLUMN `title` VARCHAR(255) NULL AFTER `request_user_id`,
	CHANGE COLUMN `request_description` `request_description` TEXT NOT NULL AFTER `title`,
	ADD COLUMN `reason` TEXT NULL AFTER `request_description`,
	ADD COLUMN `receiver_contact_details` TEXT NULL AFTER `reason`,
	ADD COLUMN `investigation_information` TEXT NULL AFTER `receiver_contact_details`,
	ADD COLUMN `fund_receiver_account_details` TEXT NULL AFTER `investigation_information`,
	ADD COLUMN `additional_information` TEXT NULL AFTER `fund_receiver_account_details`;

ALTER TABLE `status`
	ADD COLUMN `sort_order` INT NULL AFTER `name`;
