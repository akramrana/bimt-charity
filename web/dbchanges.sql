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
