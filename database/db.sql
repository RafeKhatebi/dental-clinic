-- Optimized Dental Clinic Management System Database Schema
-- Reduced from 17 tables to 7 tables with enhanced indexing

SET FOREIGN_KEY_CHECKS = 1;
SET sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('admin', 'dentist', 'secretary', 'accountant') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    failed_login_attempts INT DEFAULT 0,
    locked_until DATETIME,
    password_changed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username_active (username, is_active),
    INDEX idx_role_active (role, is_active),
    INDEX idx_login_attempts (failed_login_attempts, locked_until)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Patients Table
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_code VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    age INT,
    gender ENUM('male', 'female', 'other'),
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    address TEXT,
    medical_history TEXT,
    allergies TEXT,
    notes TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_patient_code (patient_code),
    INDEX idx_phone (phone),
    INDEX idx_name_phone (first_name, last_name, phone),
    INDEX idx_created_date (created_at),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Services Table (Combined services and patient services)
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    service_name VARCHAR(100) NOT NULL,
    service_name_en VARCHAR(100),
    description TEXT,
    base_price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50),
    service_date DATE,
    tooth_number VARCHAR(10),
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10, 2),
    total_price DECIMAL(10, 2),
    discount DECIMAL(10, 2) DEFAULT 0,
    final_price DECIMAL(10, 2),
    dentist_id INT,
    status ENUM('template', 'pending', 'completed', 'cancelled') DEFAULT 'template',
    notes TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_service_name_active (service_name, is_active),
    INDEX idx_category_active (category, is_active),
    INDEX idx_patient_date (patient_id, service_date),
    INDEX idx_dentist_date (dentist_id, service_date),
    INDEX idx_status_date (status, service_date),
    INDEX idx_template_services (status, is_active),
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (dentist_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Medicines Table (Combined medicines, stock, sales, and suppliers)
CREATE TABLE IF NOT EXISTS medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_code VARCHAR(20) UNIQUE NOT NULL,
    medicine_name VARCHAR(100) NOT NULL,
    medicine_name_en VARCHAR(100),
    category VARCHAR(50),
    manufacturer VARCHAR(100),
    unit VARCHAR(20) NOT NULL,
    purchase_price DECIMAL(10, 2) NOT NULL,
    sale_price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    min_stock_level INT DEFAULT 10,
    expiry_date DATE,
    description TEXT,
    supplier_name VARCHAR(100),
    supplier_phone VARCHAR(20),
    supplier_email VARCHAR(100),
    supplier_address TEXT,
    sale_patient_id INT,
    sale_code VARCHAR(20),
    sale_date DATE,
    sale_quantity INT,
    sale_unit_price DECIMAL(10, 2),
    sale_total_price DECIMAL(10, 2),
    movement_type ENUM('purchase', 'sale', 'adjustment'),
    movement_date DATE,
    movement_quantity INT,
    movement_notes TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_medicine_code (medicine_code),
    INDEX idx_medicine_name_active (medicine_name, is_active),
    INDEX idx_category_active (category, is_active),
    INDEX idx_stock_alert (stock_quantity, min_stock_level),
    INDEX idx_expiry_alert (expiry_date, is_active),
    INDEX idx_sale_date (sale_date, sale_patient_id),
    INDEX idx_movement_date (movement_date, movement_type),
    INDEX idx_supplier_name (supplier_name),
    FOREIGN KEY (sale_patient_id) REFERENCES patients(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Payments Table (Combined payments and installments)
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    service_id INT,
    medicine_id INT,
    payment_type ENUM('service', 'medicine') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'installment', 'loan') NOT NULL,
    payment_date DATE NOT NULL,
    installment_number INT DEFAULT 1,
    total_installments INT DEFAULT 1,
    due_date DATE,
    paid_amount DECIMAL(10, 2) DEFAULT 0,
    paid_date DATE,
    status ENUM('pending', 'paid', 'overdue', 'partial') DEFAULT 'pending',
    notes TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_patient_date (patient_id, payment_date),
    INDEX idx_payment_method_date (payment_method, payment_date),
    INDEX idx_payment_type_date (payment_type, payment_date),
    INDEX idx_status_due (status, due_date),
    INDEX idx_installments (payment_method, installment_number, total_installments),
    INDEX idx_overdue_payments (status, due_date),
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Documents Table (Combined prescriptions, partners, and backups)
CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_type ENUM('prescription', 'partner_share', 'backup', 'invoice') NOT NULL,
    document_code VARCHAR(20) UNIQUE NOT NULL,
    patient_id INT,
    service_id INT,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    file_path VARCHAR(255),
    file_size BIGINT,
    partner_name VARCHAR(100),
    partner_phone VARCHAR(20),
    partner_email VARCHAR(100),
    share_percentage DECIMAL(5, 2),
    period_start DATE,
    period_end DATE,
    share_amount DECIMAL(10, 2),
    paid_amount DECIMAL(10, 2) DEFAULT 0,
    diagnosis TEXT,
    medicine_instructions TEXT,
    status ENUM('active', 'inactive', 'completed', 'cancelled') DEFAULT 'active',
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_document_type_code (document_type, document_code),
    INDEX idx_patient_type (patient_id, document_type),
    INDEX idx_service_type (service_id, document_type),
    INDEX idx_partner_period (partner_name, period_start, period_end),
    INDEX idx_status_type (status, document_type),
    INDEX idx_created_date_type (created_at, document_type),
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. System Table (Combined settings and activity logs)
CREATE TABLE IF NOT EXISTS system (
    id INT AUTO_INCREMENT PRIMARY KEY,
    record_type ENUM('setting', 'activity_log') NOT NULL,
    setting_key VARCHAR(50),
    setting_value TEXT,
    setting_type VARCHAR(20) DEFAULT 'text',
    description TEXT,
    user_id INT,
    action VARCHAR(50),
    table_name VARCHAR(50),
    record_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_record_type (record_type),
    INDEX idx_setting_key (setting_key),
    INDEX idx_user_action (user_id, action),
    INDEX idx_table_record (table_name, record_id),
    INDEX idx_activity_date (created_at),
    UNIQUE KEY unique_setting (setting_key, record_type),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user
INSERT IGNORE INTO users (username, password, full_name, role, is_active) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدیر سیستم', 'admin', 1);

-- Insert default settings
INSERT IGNORE INTO system (record_type, setting_key, setting_value, setting_type, description) VALUES
('setting', 'clinic_name', 'مرکز دندانپزشکی', 'text', 'نام مرکز'),
('setting', 'clinic_address', '', 'text', 'آدرس مرکز'),
('setting', 'clinic_phone', '', 'text', 'تلفن مرکز'),
('setting', 'clinic_email', '', 'email', 'ایمیل مرکز'),
('setting', 'currency', 'افغانی', 'text', 'واحد پول'),
('setting', 'language', 'fa', 'text', 'زبان پیشفرض'),
('setting', 'low_stock_alert', '10', 'number', 'حد هشدار موجودی کم'),
('setting', 'expiry_alert_days', '30', 'number', 'روزهای هشدار انقضا');

-- Insert default service templates
INSERT IGNORE INTO services (service_name, service_name_en, description, base_price, category, status, is_active) VALUES
('ترمیم دندان', 'Tooth Filling', 'ترمیم و پر کردن دندان', 500000, 'restorative', 'template', 1),
('کشیدن دندان', 'Tooth Extraction', 'کشیدن دندان ساده', 300000, 'surgery', 'template', 1),
('عصب کشی', 'Root Canal', 'درمان ریشه دندان', 1500000, 'endodontics', 'template', 1),
('جرمگیری', 'Scaling', 'پاکسازی جرم دندان', 400000, 'preventive', 'template', 1),
('ارتودنسی', 'Orthodontics', 'درمان ارتودنسی', 20000000, 'orthodontics', 'template', 1),
('ایمپلنت', 'Dental Implant', 'کاشت ایمپلنت دندان', 15000000, 'surgery', 'template', 1),
('بلیچینگ', 'Teeth Whitening', 'سفید کردن دندان', 2000000, 'cosmetic', 'template', 1),
('روکش دندان', 'Dental Crown', 'روکش کردن دندان', 3000000, 'restorative', 'template', 1);