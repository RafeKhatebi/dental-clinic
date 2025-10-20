# 📦 تبدیل به حالت آفلاین

## 🔍 CDN های شناسایی شده:

### 1. Tailwind CSS
- **لینک فعلی:** `https://cdn.tailwindcss.com`
- **راه حل:** استفاده از Tailwind CSS کامپایل شده
- **فایل:** `assets/css/tailwind.min.css`

### 2. Google Fonts - Inter
- **لینک فعلی:** `https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700`
- **راه حل:** دانلود و استفاده محلی
- **پوشه:** `assets/fonts/inter/`

### 3. Google Fonts - Vazirmatn (فارسی)
- **لینک فعلی:** `https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700`
- **راه حل:** دانلود و استفاده محلی
- **پوشه:** `assets/fonts/vazirmatn/`

### 4. Chart.js
- **لینک فعلی:** `https://cdn.jsdelivr.net/npm/chart.js`
- **راه حل:** دانلود نسخه محلی
- **فایل:** `assets/libs/chartjs/chart.min.js`

---

## 📥 دستورالعمل دانلود:

### 1. Tailwind CSS (حجم: ~3.5MB)
```bash
# دانلود از:
https://cdn.tailwindcss.com/3.4.1/tailwind.min.css
# ذخیره در:
assets/css/tailwind.min.css
```

### 2. Font Inter (حجم: ~500KB)
```bash
# دانلود از:
https://fonts.google.com/download?family=Inter
# استخراج و کپی فایلهای woff2 به:
assets/fonts/inter/
```

### 3. Font Vazirmatn (حجم: ~400KB)
```bash
# دانلود از:
https://github.com/rastikerdar/vazirmatn/releases
# استخراج و کپی فایلهای woff2 به:
assets/fonts/vazirmatn/
```

### 4. Chart.js (حجم: ~200KB)
```bash
# دانلود از:
https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js
# ذخیره در:
assets/libs/chartjs/chart.min.js
```

---

## ✅ فایلهای ایجاد شده:

1. `assets/css/fonts.css` - تعریف فونتهای محلی
2. `includes/header_offline.php` - نسخه آفلاین header

---

## 🚀 نحوه استفاده:

### گام 1: دانلود فایلها
طبق دستورالعمل بالا فایلها را دانلود کنید.

### گام 2: جایگزینی Header
```php
// در هر فایل PHP به جای:
include '../includes/header.php';

// استفاده کنید از:
include '../includes/header_offline.php';
```

### گام 3: تست
سیستم را بدون اینترنت تست کنید.

---

## 📊 مقایسه حجم:

| مورد | آنلاین | آفلاین | تفاوت |
|------|--------|---------|-------|
| Tailwind CSS | CDN | 3.5MB | +3.5MB |
| Inter Font | CDN | 500KB | +500KB |
| Vazirmatn Font | CDN | 400KB | +400KB |
| Chart.js | CDN | 200KB | +200KB |
| **جمع کل** | 0MB | **4.6MB** | **+4.6MB** |

---

## ⚡ مزایا:

✅ کار بدون اینترنت  
✅ سرعت بارگذاری بیشتر  
✅ عدم وابستگی به سرویسهای خارجی  
✅ حریم خصوصی بهتر  

## ⚠️ نکات:

- فایلهای فونت باید در فرمت woff2 باشند (کوچکترین حجم)
- Tailwind CSS باید نسخه کامل باشد
- Chart.js باید نسخه UMD باشد

---

**تاریخ:** 2025-10-09  
**وضعیت:** آماده برای پیادهسازی
