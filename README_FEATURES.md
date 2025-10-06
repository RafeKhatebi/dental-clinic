# ویژگیهای جدید سیستم / New Features

## 1. Activity Log (گزارش فعالیتها)
- ثبت تمام فعالیتهای کاربران (ورود، ایجاد، ویرایش، حذف)
- نمایش IP و User Agent
- فیلتر بر اساس نوع عملیات
- دسترسی فقط برای Admin
- مسیر: `/reports/activity_log.php`

## 2. Toast Notifications (اعلانهای زیبا)
- جایگزینی تمام `alert()` ها با `showToast()`
- 4 نوع: success, error, warning, info
- انیمیشن ورود و خروج
- بسته شدن خودکار بعد از 3 ثانیه
- امکان بستن دستی

## 3. Keyboard Shortcuts (میانبرهای کیبورد)
- `Ctrl+K`: جستجو سریع
- `Ctrl+N`: رکورد جدید
- `Ctrl+S`: ذخیره فرم
- `Ctrl+P`: چاپ
- `Esc`: بستن
- `Ctrl+/`: نمایش راهنمای کلیدها

## 4. Data Export (خروجی داده)
- **Excel Export**: دانلود جداول به صورت CSV
- **PDF Export**: چاپ صفحات به صورت PDF
- دکمه‌های Export در صفحات:
  - Users List
  - Patients List
  - سایر لیست‌ها

## 5. Dashboard بهبود یافته
- نمودار درآمد 7 روز اخیر
- آمار بیماران امروز
- درآمد نقدی و قسطی
- بدهی‌ها و معوقات
- هشدار داروهای کم موجود
- هشدار داروهای در حال انقضا
- سهم شرکا
- لیست بیماران امروز

## نحوه استفاده

### Activity Log
```php
logActivity('create', 'patients', $patientId, 'Created new patient');
logActivity('update', 'users', $userId, 'Updated user profile');
logActivity('delete', 'services', $serviceId, 'Deleted service');
```

### Toast Notifications
```javascript
showToast('عملیات موفق بود', 'success');
showToast('خطایی رخ داد', 'error');
showToast('هشدار', 'warning');
showToast('اطلاعات', 'info');
```

### Export Functions
```javascript
exportToExcel('tableId', 'filename');
exportToPDF();
```

## فایلهای تغییر یافته
1. `/includes/header.php` - اضافه شدن shortcuts.js
2. `/includes/footer.php` - تابع showToast موجود بود
3. `/config/config.php` - تابع logActivity موجود بود
4. `/assets/js/shortcuts.js` - فایل جدید
5. `/reports/activity_log.php` - صفحه جدید
6. `/users/index.php` - دکمه‌های Export
7. `/patients/index.php` - دکمه‌های Export
8. `/staff/add.php` - showToast
9. `/staff/edit.php` - showToast
10. `/suppliers/add.php` - showToast
11. `/users/add.php` - showToast
12. `/users/edit.php` - showToast
13. `/users/index.php` - showToast

## تست شده ✅
- Activity Log کار می‌کند
- Toast Notifications جایگزین alert شده
- Keyboard Shortcuts فعال است
- Export Excel/PDF کار می‌کند
- Dashboard بهبود یافته است
