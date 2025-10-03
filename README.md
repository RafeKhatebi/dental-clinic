# Dental Clinic Management System
# سیستم مدیریت مرکز دندانپزشکی

A comprehensive offline web-based dental clinic management system built with HTML, Tailwind CSS, JavaScript, PHP, and SQLite.

## Features / ویژگی‌ها

### 1. Dashboard / داشبورد
- Today's patients and revenue statistics
- Cash and installment revenue tracking
- Debt and loan monitoring
- Low stock medicine alerts
- Expiring medicine warnings
- Partner share calculations
- Revenue charts (last 7 days)

### 2. Patient Management / مدیریت بیماران
- Complete patient registration
- Medical history and allergies tracking
- Service history
- Payment history
- Debt management
- Patient search and filtering

### 3. Dental Services / خدمات درمانی
- Service catalog management
- Service provision to patients
- Pricing and discounts
- Tooth number tracking
- Service history per patient

### 4. Pharmacy / داروخانه
- Medicine inventory management
- Stock tracking with alerts
- Expiry date monitoring
- Medicine sales (cash/installment/loan)
- Purchase price and sale price management
- Stock movement history

### 5. Suppliers / تامین‌کنندگان
- Supplier registration
- Purchase management
- Stock replenishment
- Supplier contact information

### 6. Partners / شرکا
- Partner registration with share percentages
- Automatic share calculation
- Revenue distribution
- Partner activity periods

### 7. Reports / گزارش‌گیری
- Daily, monthly, and annual reports
- Financial reports (cash, installment, loan)
- Patient statistics
- Top services report
- Debt and overdue reports
- Medicine sales reports

### 8. Settings / تنظیمات
- Clinic information
- Bilingual support (Persian/English)
- Currency settings
- Stock alert levels
- Expiry alert configuration

### 9. Backup / پشتیبان‌گیری
- Database backup creation
- Backup download
- Backup management

## Installation / نصب

### Requirements / پیش‌نیازها
- PHP 7.4 or higher
- SQLite extension enabled
- Web server (Apache/Nginx) or XAMPP

### Steps / مراحل نصب

1. **Clone or download the project**
   ```bash
   git clone [repository-url]
   cd teeth
   ```

2. **Place in web server directory**
   - For XAMPP: `C:\xampp\htdocs\Teeth\teeth`
   - For other servers: Place in your web root

3. **Set permissions**
   - Ensure `database/` folder is writable
   - Ensure `backups/` folder is writable

4. **Access the system**
   - Open browser and navigate to: `http://localhost/Teeth/teeth`
   - Default login:
     - Username: `admin`
     - Password: `admin123`

5. **First-time setup**
   - Change admin password
   - Configure clinic settings
   - Add users (dentists, secretary, accountant)
   - Add services
   - Add medicines

## Database Structure / ساختار دیتابیس

The system uses SQLite database with the following main tables:

- `users` - System users with roles
- `patients` - Patient information
- `services` - Dental services catalog
- `patient_services` - Services provided to patients
- `payments` - Payment records
- `installments` - Installment payment tracking
- `medicines` - Medicine inventory
- `medicine_sales` - Medicine sales records
- `medicine_stock` - Stock movement history
- `suppliers` - Supplier information
- `purchases` - Purchase records
- `partners` - Business partners
- `partner_shares` - Partner share calculations
- `prescriptions` - Medical prescriptions
- `settings` - System settings
- `activity_logs` - User activity tracking
- `backups` - Backup records

## User Roles / نقش‌های کاربری

1. **Admin / مدیر**
   - Full system access
   - User management
   - Settings configuration
   - Backup management

2. **Dentist / دندانپزشک**
   - Patient management
   - Service provision
   - Prescription creation
   - View reports

3. **Secretary / منشی**
   - Patient registration
   - Appointment management
   - Payment recording
   - Basic reports

4. **Accountant / حسابدار**
   - Financial reports
   - Payment management
   - Debt tracking
   - Partner share reports

## Technology Stack / فناوری‌های استفاده شده

- **Frontend:**
  - HTML5
  - Tailwind CSS (via CDN)
  - JavaScript (Vanilla)
  - Chart.js for data visualization

- **Backend:**
  - PHP 7.4+
  - SQLite database
  - PDO for database operations

- **Features:**
  - Responsive design
  - RTL support for Persian
  - Bilingual interface (Persian/English)
  - AJAX for dynamic operations
  - Session-based authentication

## Security Features / امنیت

- Password hashing (bcrypt)
- SQL injection prevention (PDO prepared statements)
- XSS protection (input sanitization)
- Session-based authentication
- Role-based access control
- Activity logging

## Backup & Restore / پشتیبان‌گیری و بازیابی

- Automatic database backup creation
- Manual backup download
- Backup file management
- Easy restoration process

## Support / پشتیبانی

For issues or questions:
- Check the documentation
- Review the code comments
- Contact system administrator

## License / مجوز

This system is developed for dental clinic management purposes.

## Version / نسخه

Version 1.0.0 - Initial Release

---

**Note:** This is an offline system designed to run on a local computer or internal network. No internet connection is required for operation.

**توجه:** این سیستم آفلاین طراحی شده و روی کامپیوتر محلی یا شبکه داخلی اجرا می‌شود. نیازی به اتصال اینترنت ندارد.
