-- MySQL Backup
-- Date: 2025-10-04 14:19:16

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_type` enum('prescription','partner_share','backup','invoice') NOT NULL,
  `document_code` varchar(20) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `content` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `partner_name` varchar(100) DEFAULT NULL,
  `partner_phone` varchar(20) DEFAULT NULL,
  `partner_email` varchar(100) DEFAULT NULL,
  `share_percentage` decimal(5,2) DEFAULT NULL,
  `period_start` date DEFAULT NULL,
  `period_end` date DEFAULT NULL,
  `share_amount` decimal(10,2) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `diagnosis` text DEFAULT NULL,
  `medicine_instructions` text DEFAULT NULL,
  `status` enum('active','inactive','completed','cancelled') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_code` (`document_code`),
  KEY `idx_document_type_code` (`document_type`,`document_code`),
  KEY `idx_patient_type` (`patient_id`,`document_type`),
  KEY `idx_service_type` (`service_id`,`document_type`),
  KEY `idx_partner_period` (`partner_name`,`period_start`,`period_end`),
  KEY `idx_status_type` (`status`,`document_type`),
  KEY `idx_created_date_type` (`created_at`,`document_type`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `documents` VALUES ('1','partner_share','PT000001',NULL,NULL,'Dr. Mehdi Karimi',NULL,NULL,NULL,'Dr. Mehdi Karimi','09121112233','mehdi@email.com','30.00','2024-01-01',NULL,NULL,'0.00',NULL,NULL,'active','1','2025-10-03 07:33:55','2025-10-03 07:33:55');
INSERT INTO `documents` VALUES ('2','partner_share','PT000002',NULL,NULL,'Dr. Zahra Hosseini',NULL,NULL,NULL,'Dr. Zahra Hosseini','09121112234','zahra.h@email.com','20.00','2024-01-01',NULL,NULL,'0.00',NULL,NULL,'active','1','2025-10-03 07:33:55','2025-10-03 07:33:55');
INSERT INTO `documents` VALUES ('3','backup','BK109338',NULL,NULL,'backup_2025-10-03_19-57-38.sql',NULL,'C:\\xampp\\htdocs\\Teeth\\teeth/backups/backup_2025-10-03_19-57-38.sql','31842',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'0.00',NULL,NULL,'active','1','2025-10-03 09:27:38','2025-10-03 09:27:38');
INSERT INTO `documents` VALUES ('4','partner_share','PT860467',NULL,NULL,'Mir Naiem',NULL,NULL,NULL,'Mir Naiem','0799900990','rkhatibi2003@gmail.com','15.00','2025-10-04','2028-01-04',NULL,'0.00',NULL,NULL,'active','1','2025-10-04 03:47:23','2025-10-04 03:47:23');

DROP TABLE IF EXISTS `medicines`;
CREATE TABLE `medicines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_code` varchar(20) NOT NULL,
  `medicine_name` varchar(100) NOT NULL,
  `medicine_name_en` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `unit` varchar(20) NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 10,
  `expiry_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `supplier_name` varchar(100) DEFAULT NULL,
  `supplier_phone` varchar(20) DEFAULT NULL,
  `supplier_email` varchar(100) DEFAULT NULL,
  `supplier_address` text DEFAULT NULL,
  `sale_patient_id` int(11) DEFAULT NULL,
  `sale_code` varchar(20) DEFAULT NULL,
  `sale_date` date DEFAULT NULL,
  `sale_quantity` int(11) DEFAULT NULL,
  `sale_unit_price` decimal(10,2) DEFAULT NULL,
  `sale_total_price` decimal(10,2) DEFAULT NULL,
  `movement_type` enum('purchase','sale','adjustment') DEFAULT NULL,
  `movement_date` date DEFAULT NULL,
  `movement_quantity` int(11) DEFAULT NULL,
  `movement_notes` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `medicine_code` (`medicine_code`),
  KEY `idx_medicine_code` (`medicine_code`),
  KEY `idx_medicine_name_active` (`medicine_name`,`is_active`),
  KEY `idx_category_active` (`category`,`is_active`),
  KEY `idx_stock_alert` (`stock_quantity`,`min_stock_level`),
  KEY `idx_expiry_alert` (`expiry_date`,`is_active`),
  KEY `idx_sale_date` (`sale_date`,`sale_patient_id`),
  KEY `idx_movement_date` (`movement_date`,`movement_type`),
  KEY `idx_supplier_name` (`supplier_name`),
  KEY `sale_patient_id` (`sale_patient_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `medicines_ibfk_1` FOREIGN KEY (`sale_patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `medicines_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `medicines` VALUES ('1','M849115','ییی','yy','دیدی','ww','Tube','2000.00','3000.00','10','10','2025-10-31','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,'2025-10-03 17:20:24','2025-10-03 17:20:24');
INSERT INTO `medicines` VALUES ('2','M000001','آموکسی سیلین 500','Amoxicillin 500mg','آنتی بیوتیک','داروسازی ابوریحان','Box','150000.00','200000.00','50','10','2025-12-31',NULL,'شرکت پخش دارو','02188776655','supplier1@email.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1','2025-10-03 07:33:34','2025-10-03 07:33:34');
INSERT INTO `medicines` VALUES ('3','M000002','ایبوپروفن 400','Ibuprofen 400mg','مسکن','داروسازی سبحان','Box','80000.00','120000.00','100','20','2025-10-31',NULL,'شرکت پخش دارو','02188776655','supplier1@email.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1','2025-10-03 07:33:34','2025-10-03 07:33:34');
INSERT INTO `medicines` VALUES ('4','M000003','متروندازول 250','Metronidazole 250mg','آنتی بیوتیک','داروسازی جابر','Box','100000.00','150000.00','30','10','2025-11-30',NULL,'شرکت پخش دارو','02188776655','supplier1@email.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1','2025-10-03 07:33:34','2025-10-03 07:33:34');
INSERT INTO `medicines` VALUES ('5','M000004','دهانشویه کلرهگزیدین','Chlorhexidine Mouthwash','بهداشتی','شرکت بهداشتی پارس','Bottle','50000.00','80000.00','80','15','2026-06-30',NULL,'توزیع کالای بهداشتی','02188776656','supplier2@email.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1','2025-10-03 07:33:34','2025-10-03 07:33:34');
INSERT INTO `medicines` VALUES ('6','M000005','ژل فلوراید','Fluoride Gel','بهداشتی','شرکت دندانپزشکی ایران','Tube','120000.00','180000.00','40','10','2026-03-31',NULL,'توزیع کالای بهداشتی','02188776656','supplier2@email.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1','2025-10-03 07:33:34','2025-10-03 07:33:34');
INSERT INTO `medicines` VALUES ('7','M000006','سفیکسیم 400','Cefixime 400mg','آنتی بیوتیک','داروسازی فارابی','Box','200000.00','280000.00','25','10','2025-09-30',NULL,'شرکت پخش دارو','02188776655','supplier1@email.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1','2025-10-03 07:33:34','2025-10-03 07:33:34');
INSERT INTO `medicines` VALUES ('8','M000007','ژل بی حسی موضعی','Topical Anesthetic Gel','بی حس کننده','شرکت دندانپزشکی ایران','Tube','150000.00','220000.00','60','15','2026-01-31',NULL,'توزیع کالای بهداشتی','02188776656','supplier2@email.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1','2025-10-03 07:33:34','2025-10-03 07:33:34');
INSERT INTO `medicines` VALUES ('9','M000008','آسپرین 80','Aspirin 80mg','مسکن','داروسازی رازی','Box','30000.00','50000.00','5','10','2025-08-31',NULL,'شرکت پخش دارو','02188776655','supplier1@email.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1','2025-10-03 07:33:34','2025-10-03 07:33:34');
INSERT INTO `medicines` VALUES ('10','M468746','متادول','Metadol','ندارد','www','Box','1000.00','3000.00','20','10',NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,'2025-10-04 03:46:18','2025-10-04 03:46:18');

DROP TABLE IF EXISTS `patients`;
CREATE TABLE `patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_code` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `patient_code` (`patient_code`),
  KEY `idx_patient_code` (`patient_code`),
  KEY `idx_phone` (`phone`),
  KEY `idx_name_phone` (`first_name`,`last_name`,`phone`),
  KEY `idx_created_date` (`created_at`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `patients` VALUES ('2','P000002','فاطمه','رضایی','28','female','09131234568','fatemeh@email.com','تهران، خیابان انقلاب',NULL,NULL,NULL,'1','2025-10-03 07:32:31','2025-10-03 07:32:31');
INSERT INTO `patients` VALUES ('3','P000003','علی','محمدی','42','male','09131234569',NULL,'تهران، خیابان آزادی','فشار خون بالا',NULL,NULL,'1','2025-10-03 07:32:31','2025-10-03 07:32:31');
INSERT INTO `patients` VALUES ('4','P000004','زهرا','کریمی','25','female','09131234570','zahra@email.com','تهران، خیابان شریعتی',NULL,'آسپرین',NULL,'1','2025-10-03 07:32:31','2025-10-03 07:32:31');
INSERT INTO `patients` VALUES ('5','P000005','حسین','حسینی','50','male','09131234571',NULL,'تهران، خیابان کریمخان','بیماری قلبی',NULL,NULL,'1','2025-10-03 07:32:31','2025-10-03 07:32:31');
INSERT INTO `patients` VALUES ('6','P000006','مریم','نوری','33','female','09131234572','maryam@email.com','تهران، خیابان سعادت آباد',NULL,NULL,NULL,'1','2025-10-03 07:32:31','2025-10-03 07:32:31');
INSERT INTO `patients` VALUES ('7','P000007','رضا','صادقی','45','male','09131234573',NULL,'تهران، خیابان نیاوران',NULL,NULL,NULL,'1','2025-10-03 07:32:31','2025-10-03 07:32:31');
INSERT INTO `patients` VALUES ('8','P000008','سارا','موسوی','30','female','09131234574','sara@email.com','تهران، خیابان فرمانیه',NULL,NULL,NULL,'1','2025-10-03 07:32:31','2025-10-03 07:32:31');
INSERT INTO `patients` VALUES ('9','P215401','کریم شاه','کریمی','20','male','0728958423','rkhatibi2003@gmail.com','هرات','ندارد','شب نمیدانم','دد','1','2025-10-03 07:35:31','2025-10-03 07:35:31');
INSERT INTO `patients` VALUES ('10','P999041','شریف احمد','شریفی','23','male','0728958423','rkhatibi2003@gmail.com','هرات شهر کهنه','درد دندان','آلرژی به آلو','بخشش لازم نیست اعدامش کنید','1','2025-10-04 03:23:03','2025-10-04 03:23:03');

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `payment_type` enum('service','medicine') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','installment','loan') NOT NULL,
  `payment_date` date NOT NULL,
  `installment_number` int(11) DEFAULT 1,
  `total_installments` int(11) DEFAULT 1,
  `due_date` date DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `paid_date` date DEFAULT NULL,
  `status` enum('pending','paid','overdue','partial') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_patient_date` (`patient_id`,`payment_date`),
  KEY `idx_payment_method_date` (`payment_method`,`payment_date`),
  KEY `idx_payment_type_date` (`payment_type`,`payment_date`),
  KEY `idx_status_due` (`status`,`due_date`),
  KEY `idx_installments` (`payment_method`,`installment_number`,`total_installments`),
  KEY `idx_overdue_payments` (`status`,`due_date`),
  KEY `service_id` (`service_id`),
  KEY `medicine_id` (`medicine_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `payments` VALUES ('3','2','11',NULL,'service','1000000.00','cash','2024-01-18','1','1',NULL,'1000000.00','2024-01-18','paid',NULL,'1','2025-10-03 07:33:34','2025-10-03 07:33:34');
INSERT INTO `payments` VALUES ('4','3','12',NULL,'service','1500000.00','cash','2024-01-22','1','1',NULL,'1500000.00','2024-01-22','paid',NULL,'1','2025-10-03 07:33:34','2025-10-03 07:33:34');
INSERT INTO `payments` VALUES ('5','4','13',NULL,'service','2800000.00','installment','2024-01-25','1','1',NULL,'0.00',NULL,'pending',NULL,'1','2025-10-03 07:33:34','2025-10-03 07:33:34');
INSERT INTO `payments` VALUES ('6','5','14',NULL,'service','800000.00','cash','2024-01-28','1','1',NULL,'800000.00','2024-01-28','paid',NULL,'1','2025-10-03 07:33:34','2025-10-03 07:33:34');
INSERT INTO `payments` VALUES ('7','6','15',NULL,'service','3000000.00','loan','2024-02-01','1','1',NULL,'0.00',NULL,'pending',NULL,'1','2025-10-03 07:33:34','2025-10-03 07:33:34');
INSERT INTO `payments` VALUES ('8','7','16',NULL,'service','900000.00','cash','2024-02-05','1','1',NULL,'900000.00','2024-02-05','paid',NULL,'1','2025-10-03 07:33:34','2025-10-03 07:33:34');
INSERT INTO `payments` VALUES ('9','10','21',NULL,'service','300000.00','cash','2025-10-04','1','1',NULL,'300000.00','2025-10-04','paid','','1','2025-10-04 03:33:05','2025-10-04 03:33:05');

DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `service_name` varchar(100) NOT NULL,
  `service_name_en` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `tooth_number` varchar(10) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `final_price` decimal(10,2) DEFAULT NULL,
  `dentist_id` int(11) DEFAULT NULL,
  `status` enum('template','pending','completed','cancelled') DEFAULT 'template',
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_service_name_active` (`service_name`,`is_active`),
  KEY `idx_category_active` (`category`,`is_active`),
  KEY `idx_patient_date` (`patient_id`,`service_date`),
  KEY `idx_dentist_date` (`dentist_id`,`service_date`),
  KEY `idx_status_date` (`status`,`service_date`),
  KEY `idx_template_services` (`status`,`is_active`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `services_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `services_ibfk_2` FOREIGN KEY (`dentist_id`) REFERENCES `users` (`id`),
  CONSTRAINT `services_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `services` VALUES ('1',NULL,'ترمیم دندان','Tooth Filling','ترمیم و پر کردن دندان','500000.00','restorative',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1',NULL,'2025-10-03 16:39:06','2025-10-03 16:39:06');
INSERT INTO `services` VALUES ('2',NULL,'کشیدن دندان','Tooth Extraction','کشیدن دندان ساده','300000.00','surgery',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1',NULL,'2025-10-03 16:39:06','2025-10-03 16:39:06');
INSERT INTO `services` VALUES ('3',NULL,'عصب کشی','Root Canal','درمان ریشه دندان','1500000.00','endodontics',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1',NULL,'2025-10-03 16:39:06','2025-10-03 16:39:06');
INSERT INTO `services` VALUES ('4',NULL,'جرمگیری','Scaling','پاکسازی جرم دندان','400000.00','preventive',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1',NULL,'2025-10-03 16:39:06','2025-10-03 16:39:06');
INSERT INTO `services` VALUES ('5',NULL,'ارتودنسی','Orthodontics','درمان ارتودنسی','20000000.00','orthodontics',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1',NULL,'2025-10-03 16:39:06','2025-10-03 16:39:06');
INSERT INTO `services` VALUES ('6',NULL,'ایمپلنت','Dental Implant','کاشت ایمپلنت دندان','15000000.00','surgery',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1',NULL,'2025-10-03 16:39:06','2025-10-03 16:39:06');
INSERT INTO `services` VALUES ('7',NULL,'بلیچینگ','Teeth Whitening','سفید کردن دندان','2000000.00','cosmetic',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1',NULL,'2025-10-03 16:39:06','2025-10-03 16:39:06');
INSERT INTO `services` VALUES ('8',NULL,'روکش دندان','Dental Crown','روکش کردن دندان','3000000.00','restorative',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1',NULL,'2025-10-03 16:39:06','2025-10-03 16:39:06');
INSERT INTO `services` VALUES ('9',NULL,'ایمپلیمنت درجه دو','2nd implement','ثثثث','1233.00','دیدی',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1','1','2025-10-03 17:18:32','2025-10-03 17:18:32');
INSERT INTO `services` VALUES ('10',NULL,'عصب کشی','Root Canal','درمان ریشه دندان','5000000.00','endodontics',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1','1','2025-10-03 07:33:05','2025-10-03 07:33:05');
INSERT INTO `services` VALUES ('11',NULL,'ترمیم دندان','Tooth Filling','پر کردن دندان','1500000.00','restorative',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1','1','2025-10-03 07:33:05','2025-10-03 07:33:05');
INSERT INTO `services` VALUES ('12',NULL,'کشیدن دندان','Tooth Extraction','خارج کردن دندان','800000.00','surgery',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1','1','2025-10-03 07:33:05','2025-10-03 07:33:05');
INSERT INTO `services` VALUES ('13',NULL,'جرم گیری','Scaling','پاکسازی جرم دندان','1000000.00','preventive',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1','1','2025-10-03 07:33:05','2025-10-03 07:33:05');
INSERT INTO `services` VALUES ('14',NULL,'بلیچینگ','Teeth Whitening','سفید کردن دندان','3000000.00','cosmetic',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1','1','2025-10-03 07:33:05','2025-10-03 07:33:05');
INSERT INTO `services` VALUES ('15',NULL,'ایمپلنت','Dental Implant','کاشت دندان','15000000.00','surgery',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1','1','2025-10-03 07:33:05','2025-10-03 07:33:05');
INSERT INTO `services` VALUES ('16',NULL,'روکش دندان','Crown','روکش سرامیکی','8000000.00','restorative',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1','1','2025-10-03 07:33:05','2025-10-03 07:33:05');
INSERT INTO `services` VALUES ('17',NULL,'ارتودنسی','Orthodontics','تنظیم دندان','25000000.00','orthodontics',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1','1','2025-10-03 07:33:05','2025-10-03 07:33:05');
INSERT INTO `services` VALUES ('21','10','جرمگیری','Scaling',NULL,'400000.00','preventive','2025-10-04','','1','400000.00','400000.00','0.00','400000.00','4','completed','','1','1','2025-10-04 03:30:19','2025-10-04 03:30:19');
INSERT INTO `services` VALUES ('22',NULL,'پاک کاری','cleaning','ندارد','550.00','ندارد',NULL,NULL,'1',NULL,NULL,'0.00',NULL,NULL,'template',NULL,'1','1','2025-10-04 03:42:52','2025-10-04 03:42:52');

DROP TABLE IF EXISTS `system`;
CREATE TABLE `system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `record_type` enum('setting','activity_log') NOT NULL,
  `setting_key` varchar(50) DEFAULT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` varchar(20) DEFAULT 'text',
  `description` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_setting` (`setting_key`,`record_type`),
  KEY `idx_record_type` (`record_type`),
  KEY `idx_setting_key` (`setting_key`),
  KEY `idx_user_action` (`user_id`,`action`),
  KEY `idx_table_record` (`table_name`,`record_id`),
  KEY `idx_activity_date` (`created_at`),
  CONSTRAINT `system_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `system` VALUES ('1','setting','clinic_name','هری','text','نام مرکز',NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-03 16:39:06','2025-10-03 19:52:23');
INSERT INTO `system` VALUES ('2','setting','clinic_address','','text','آدرس مرکز',NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-03 16:39:06','2025-10-03 19:52:23');
INSERT INTO `system` VALUES ('3','setting','clinic_phone','','text','تلفن مرکز',NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-03 16:39:06','2025-10-03 19:52:23');
INSERT INTO `system` VALUES ('4','setting','clinic_email','','email','ایمیل مرکز',NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-03 16:39:06','2025-10-03 19:52:23');
INSERT INTO `system` VALUES ('5','setting','currency','افغانی','text','واحد پول',NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-03 16:39:06','2025-10-03 19:52:23');
INSERT INTO `system` VALUES ('6','setting','language','fa','text','زبان پیشفرض',NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-03 16:39:06','2025-10-03 19:52:23');
INSERT INTO `system` VALUES ('7','setting','low_stock_alert','10','number','حد هشدار موجودی کم',NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-03 16:39:06','2025-10-03 19:52:23');
INSERT INTO `system` VALUES ('8','setting','expiry_alert_days','30','number','روزهای هشدار انقضا',NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-03 16:39:06','2025-10-03 19:52:23');
INSERT INTO `system` VALUES ('9','activity_log',NULL,NULL,'text','User logged out','1','logout','users','1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 16:55:29','2025-10-03 16:55:29');
INSERT INTO `system` VALUES ('10','activity_log',NULL,NULL,'text','User logged in','1','login','users','1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 16:55:42','2025-10-03 16:55:42');
INSERT INTO `system` VALUES ('11','activity_log',NULL,NULL,'text','Created service template: ایمپلیمنت درجه دو','1','create','services','9','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 17:18:32','2025-10-03 17:18:32');
INSERT INTO `system` VALUES ('12','activity_log',NULL,NULL,'text','Created medicine: ییی','1','create','medicines','1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 17:20:24','2025-10-03 17:20:24');
INSERT INTO `system` VALUES ('13','setting','clinic_name_fa','قادری','text',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-03 17:24:52','2025-10-03 19:30:56');
INSERT INTO `system` VALUES ('14','activity_log',NULL,NULL,'text','Updated system settings','1','update','settings',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 17:24:52','2025-10-03 17:24:52');
INSERT INTO `system` VALUES ('15','activity_log',NULL,NULL,'text','User logged out','1','logout','users','1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 17:26:21','2025-10-03 17:26:21');
INSERT INTO `system` VALUES ('16','activity_log',NULL,NULL,'text','User logged in','1','login','users','1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 17:26:30','2025-10-03 17:26:30');
INSERT INTO `system` VALUES ('17','activity_log',NULL,NULL,'text','Created user: student','1','create','users','2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 17:29:50','2025-10-03 17:29:50');
INSERT INTO `system` VALUES ('18','activity_log',NULL,NULL,'text','User logged out','1','logout','users','1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 17:29:55','2025-10-03 17:29:55');
INSERT INTO `system` VALUES ('19','activity_log',NULL,NULL,'text','User logged in','2','login','users','2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 17:29:59','2025-10-03 17:29:59');
INSERT INTO `system` VALUES ('20','activity_log',NULL,NULL,'text','User logged out','2','logout','users','2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 17:30:12','2025-10-03 17:30:12');
INSERT INTO `system` VALUES ('21','activity_log',NULL,NULL,'text','User logged in','1','login','users','1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 07:24:21','2025-10-03 07:24:21');
INSERT INTO `system` VALUES ('31','activity_log',NULL,NULL,'text','Created patient: کریم شاه کریمی','1','create','patients','9','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 07:35:31','2025-10-03 07:35:31');
INSERT INTO `system` VALUES ('32','activity_log',NULL,NULL,'text','Deleted patient: محمد احمدی','1','delete','patients','1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 07:36:11','2025-10-03 07:36:11');
INSERT INTO `system` VALUES ('33','activity_log',NULL,NULL,'text','Updated system settings','1','update','settings',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 07:39:01','2025-10-03 07:39:01');
INSERT INTO `system` VALUES ('34','activity_log',NULL,NULL,'text','Updated system settings','1','update','settings',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 07:39:18','2025-10-03 07:39:18');
INSERT INTO `system` VALUES ('35','activity_log',NULL,NULL,'text','Updated system settings','1','update','settings',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 09:00:56','2025-10-03 09:00:56');
INSERT INTO `system` VALUES ('36','activity_log',NULL,NULL,'text','Created user: manger','1','create','users','3','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 09:12:15','2025-10-03 09:12:15');
INSERT INTO `system` VALUES ('37','activity_log',NULL,NULL,'text','User logged out','1','logout','users','1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 09:12:36','2025-10-03 09:12:36');
INSERT INTO `system` VALUES ('38','activity_log',NULL,NULL,'text','User logged in','3','login','users','3','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 09:12:42','2025-10-03 09:12:42');
INSERT INTO `system` VALUES ('39','activity_log',NULL,NULL,'text','User logged out','3','logout','users','3','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 09:13:04','2025-10-03 09:13:04');
INSERT INTO `system` VALUES ('40','activity_log',NULL,NULL,'text','User logged in','2','login','users','2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 09:13:11','2025-10-03 09:13:11');
INSERT INTO `system` VALUES ('41','activity_log',NULL,NULL,'text','User logged out','2','logout','users','2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 09:13:15','2025-10-03 09:13:15');
INSERT INTO `system` VALUES ('42','activity_log',NULL,NULL,'text','User logged in','1','login','users','1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 09:13:20','2025-10-03 09:13:20');
INSERT INTO `system` VALUES ('43','activity_log',NULL,NULL,'text','User logged in','1','login','users','1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0','2025-10-03 09:20:52','2025-10-03 09:20:52');
INSERT INTO `system` VALUES ('44','activity_log',NULL,NULL,'text','Updated system settings','1','update','settings',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0','2025-10-03 09:22:23','2025-10-03 09:22:23');
INSERT INTO `system` VALUES ('45','activity_log',NULL,NULL,'text','Created database backup: backup_2025-10-03_19-57-38.sql','1','create','documents','3','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-03 09:27:38','2025-10-03 09:27:38');
INSERT INTO `system` VALUES ('46','activity_log',NULL,NULL,'text','User logged in','1','login','users','1','10.10.10.252','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','2025-10-03 09:46:02','2025-10-03 09:46:02');
INSERT INTO `system` VALUES ('47','activity_log',NULL,NULL,'text','User logged in','1','login','users','1','10.10.10.252','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','2025-10-03 09:53:57','2025-10-03 09:53:57');
INSERT INTO `system` VALUES ('48','activity_log',NULL,NULL,'text','User logged in','1','login','users','1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-04 03:20:13','2025-10-04 03:20:13');
INSERT INTO `system` VALUES ('49','activity_log',NULL,NULL,'text','Created patient: شریف احمد شریفی','1','create','patients','10','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-04 03:23:03','2025-10-04 03:23:03');
INSERT INTO `system` VALUES ('50','activity_log',NULL,NULL,'text','Created user: رامین شاه','1','create','users','4','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-04 03:29:32','2025-10-04 03:29:32');
INSERT INTO `system` VALUES ('51','activity_log',NULL,NULL,'text','Provided service to patient ID: 10','1','create','services','21','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-04 03:30:19','2025-10-04 03:30:19');
INSERT INTO `system` VALUES ('52','activity_log',NULL,NULL,'text','Created payment for patient ID: 10','1','create','payments',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-04 03:33:05','2025-10-04 03:33:05');
INSERT INTO `system` VALUES ('53','activity_log',NULL,NULL,'text','Created service template: پاک کاری','1','create','services','22','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-04 03:42:52','2025-10-04 03:42:52');
INSERT INTO `system` VALUES ('54','activity_log',NULL,NULL,'text','Created medicine: متادول','1','create','medicines','10','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-04 03:46:18','2025-10-04 03:46:18');
INSERT INTO `system` VALUES ('55','activity_log',NULL,NULL,'text','Created partner: Mir Naiem','1','create','documents','4','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-10-04 03:47:23','2025-10-04 03:47:23');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','dentist','secretary','accountant') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `failed_login_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `password_changed_at` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_username_active` (`username`,`is_active`),
  KEY `idx_role_active` (`role`,`is_active`),
  KEY `idx_login_attempts` (`failed_login_attempts`,`locked_until`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` VALUES ('1','admin','$2y$10$ZKkdGMDYNrAZ5DQcIkwT4eN0Iyg3p3RPbilwonDNQxgItTWpFDV6q','مدیر سیستم',NULL,NULL,'admin','1',NULL,'0',NULL,'2025-10-03 16:39:06','2025-10-03 16:39:06','2025-10-03 16:56:17');
INSERT INTO `users` VALUES ('2','student','$2y$10$n5MkYZVTP9EhVjvz/FdF1uerTWazBf1nhOPkKa2qnrXkedq7b.CU2','Rafe Ahmad Khatebi','rkhatibi2003@gmail.com','0728958423','secretary','1',NULL,'0',NULL,'2025-10-03 17:29:50','2025-10-03 17:29:50','2025-10-03 17:29:50');
INSERT INTO `users` VALUES ('3','manger','$2y$10$lAJkPffthu5hQ/10Q0Ybw.csggX2ntjVek3ISwFJvXsP0PjyEx9cm','Rafe Ahmad Khatebi','rkhatibi2003@gmail.com','0728958423','accountant','1',NULL,'0',NULL,'2025-10-03 09:12:15','2025-10-03 09:12:15','2025-10-03 09:12:15');
INSERT INTO `users` VALUES ('4','رامین شاه','$2y$10$7NNlUmhW.n4VuPqEcIOxZuXm7rV3D6iQoWtd9o4JGkKGj.4Jnw/ym','سروشی','rkhatibi2003@gmail.com','0728958423','dentist','1',NULL,'0',NULL,'2025-10-04 03:29:32','2025-10-04 03:29:32','2025-10-04 03:29:32');

SET FOREIGN_KEY_CHECKS=1;
