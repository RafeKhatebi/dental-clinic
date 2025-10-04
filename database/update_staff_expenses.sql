-- Update Database for Staff, Expenses, Withdrawals, and Salaries
-- Using existing optimized structure

-- 1. Add salary fields to users table (for staff management)
ALTER TABLE users 
ADD COLUMN monthly_salary DECIMAL(10, 2) DEFAULT 0 AFTER role,
ADD COLUMN salary_currency VARCHAR(20) DEFAULT 'افغانی' AFTER monthly_salary,
ADD COLUMN hire_date DATE AFTER salary_currency,
ADD COLUMN job_title VARCHAR(100) AFTER hire_date,
ADD COLUMN is_staff TINYINT(1) DEFAULT 0 AFTER is_active,
ADD INDEX idx_staff_active (is_staff, is_active);

-- 2. Extend payments table for withdrawals and salary payments
ALTER TABLE payments 
MODIFY COLUMN payment_type ENUM('service', 'medicine', 'salary', 'withdrawal', 'expense') NOT NULL,
ADD COLUMN staff_id INT AFTER medicine_id,
ADD COLUMN expense_category VARCHAR(50) AFTER payment_type,
ADD COLUMN month_year VARCHAR(7) AFTER due_date,
ADD INDEX idx_staff_month (staff_id, month_year),
ADD INDEX idx_expense_category (expense_category, payment_date),
ADD FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE;

-- 3. Extend documents table for fixed expenses
ALTER TABLE documents 
MODIFY COLUMN document_type ENUM('prescription', 'partner_share', 'backup', 'invoice', 'expense') NOT NULL,
ADD COLUMN expense_type ENUM('fixed', 'variable', 'one_time') AFTER document_type,
ADD COLUMN expense_category VARCHAR(50) AFTER expense_type,
ADD COLUMN amount DECIMAL(10, 2) AFTER expense_category,
ADD COLUMN recurrence ENUM('monthly', 'quarterly', 'yearly', 'one_time') AFTER amount,
ADD COLUMN next_due_date DATE AFTER recurrence,
ADD INDEX idx_expense_type (expense_type, status),
ADD INDEX idx_expense_category (expense_category),
ADD INDEX idx_next_due (next_due_date, status);

-- Insert default expense categories in system settings
INSERT INTO system (record_type, setting_key, setting_value, setting_type, description) VALUES
('setting', 'expense_categories', 'کرایه,برق,آب,گاز,اینترنت,تلفن,نظافت,تعمیرات,سایر', 'text', 'دسته‌بندی مصارف'),
('setting', 'salary_payment_day', '1', 'number', 'روز پرداخت معاش (روز ماه)');
