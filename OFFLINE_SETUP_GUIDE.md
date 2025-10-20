# 📦 راهنمای نصب حالت آفلاین

## 🎯 هدف
تبدیل سیستم به حالت کاملاً آفلاین بدون نیاز به اینترنت

---

## 📋 لیست CDN های موجود

| # | نام | لینک فعلی | حجم | وضعیت |
|---|-----|-----------|------|-------|
| 1 | Tailwind CSS | `cdn.tailwindcss.com` | 3.5MB | ⚠️ نیاز به دانلود |
| 2 | Chart.js | `cdn.jsdelivr.net/npm/chart.js` | 200KB | ⚠️ نیاز به دانلود |
| 3 | Inter Font | `fonts.googleapis.com` | 500KB | ⚠️ نیاز به دانلود |
| 4 | Vazirmatn Font | `fonts.googleapis.com` | 400KB | ⚠️ نیاز به دانلود |

**جمع کل:** 4.6MB

---

## 🚀 روش نصب سریع

### گام 1: اجرای اسکریپت دانلود

```cmd
cd c:\xampp\htdocs\Teeth\teeth
download_offline_assets.bat
```

این اسکریپت به صورت خودکار موارد زیر را دانلود میکند:
- ✅ Tailwind CSS
- ✅ Chart.js

### گام 2: دانلود دستی فونتها

#### 2.1 دانلود Inter Font
1. برو به: https://fonts.google.com/specimen/Inter
2. کلیک روی "Download family"
3. فایل ZIP را استخراج کن
4. فایلهای زیر را کپی کن به `assets/fonts/inter/`:
   - `Inter-Light.woff2`
   - `Inter-Regular.woff2`
   - `Inter-Medium.woff2`
   - `Inter-SemiBold.woff2`
   - `Inter-Bold.woff2`

#### 2.2 دانلود Vazirmatn Font
1. برو به: https://github.com/rastikerdar/vazirmatn/releases
2. آخرین نسخه را دانلود کن
3. فایل ZIP را استخراج کن
4. فایلهای زیر را کپی کن به `assets/fonts/vazirmatn/`:
   - `Vazirmatn-Light.woff2`
   - `Vazirmatn-Regular.woff2`
   - `Vazirmatn-Medium.woff2`
   - `Vazirmatn-SemiBold.woff2`
   - `Vazirmatn-Bold.woff2`

### گام 3: تغییر Header

در همه فایلهای PHP، خط زیر را:
```php
include '../includes/header.php';
```

تبدیل کن به:
```php
include '../includes/header_offline.php';
```

یا اینکه محتوای `header.php` را با `header_offline.php` جایگزین کن.

---

## 📁 ساختار پوشهها

بعد از نصب، ساختار باید اینطوری باشه:

```
teeth/
├── assets/
│   ├── css/
│   │   ├── tailwind.min.css          ✅ (3.5MB)
│   │   ├── fonts.css                 ✅ (ایجاد شده)
│   │   └── mobile.css                ✅ (موجود)
│   ├── fonts/
│   │   ├── inter/
│   │   │   ├── Inter-Light.woff2     ⚠️ (دانلود دستی)
│   │   │   ├── Inter-Regular.woff2   ⚠️ (دانلود دستی)
│   │   │   ├── Inter-Medium.woff2    ⚠️ (دانلود دستی)
│   │   │   ├── Inter-SemiBold.woff2  ⚠️ (دانلود دستی)
│   │   │   └── Inter-Bold.woff2      ⚠️ (دانلود دستی)
│   │   └── vazirmatn/
│   │       ├── Vazirmatn-Light.woff2     ⚠️ (دانلود دستی)
│   │       ├── Vazirmatn-Regular.woff2   ⚠️ (دانلود دستی)
│   │       ├── Vazirmatn-Medium.woff2    ⚠️ (دانلود دستی)
│   │       ├── Vazirmatn-SemiBold.woff2  ⚠️ (دانلود دستی)
│   │       └── Vazirmatn-Bold.woff2      ⚠️ (دانلود دستی)
│   └── libs/
│       └── chartjs/
│           └── chart.min.js          ✅ (200KB)
└── includes/
    ├── header.php                    (نسخه آنلاین)
    └── header_offline.php            ✅ (نسخه آفلاین)
```

---

## ✅ تست نصب

### 1. قطع اینترنت
اینترنت کامپیوتر را قطع کن

### 2. باز کردن سیستم
```
http://localhost/Teeth/teeth
```

### 3. چک کردن
- ✅ صفحه باید کامل لود بشه
- ✅ فونتها باید درست نمایش داده بشن
- ✅ نمودارها باید کار کنن
- ✅ استایلها باید اعمال بشن

---

## 🔧 عیبیابی

### مشکل: فونتها نمایش داده نمیشن
**راه حل:**
1. مطمئن شو فایلهای woff2 در مسیر درست هستن
2. فایل `fonts.css` رو چک کن
3. Console مرورگر رو بررسی کن (F12)

### مشکل: Tailwind کار نمیکنه
**راه حل:**
1. مطمئن شو `tailwind.min.css` دانلود شده
2. حجم فایل باید حدود 3.5MB باشه
3. مسیر فایل رو در header چک کن

### مشکل: نمودارها نمایش داده نمیشن
**راه حل:**
1. مطمئن شو `chart.min.js` دانلود شده
2. Console مرورگر رو چک کن
3. مسیر فایل رو بررسی کن

---

## 📊 مقایسه قبل و بعد

| ویژگی | قبل (آنلاین) | بعد (آفلاین) |
|-------|--------------|---------------|
| نیاز به اینترنت | ✅ بله | ❌ خیر |
| سرعت لود | متوسط | ⚡ سریع |
| حجم پروژه | 10MB | 14.6MB |
| وابستگی خارجی | 4 CDN | 0 CDN |
| حریم خصوصی | متوسط | 🔒 عالی |

---

## 🎯 مزایای حالت آفلاین

✅ **استقلال کامل** - بدون نیاز به اینترنت  
✅ **سرعت بالا** - فایلها از سرور محلی لود میشن  
✅ **حریم خصوصی** - هیچ درخواستی به سرورهای خارجی نمیره  
✅ **پایداری** - اگر CDN ها down بشن، سیستم کار میکنه  
✅ **امنیت** - کنترل کامل روی فایلها  

---

## 📝 نکات مهم

⚠️ **توجه:** بعد از تبدیل به آفلاین:
- حجم پروژه 4.6MB بیشتر میشه
- فایلهای فونت باید حتماً woff2 باشن (کوچکترین حجم)
- Tailwind باید نسخه کامل باشه نه CDN
- Chart.js باید نسخه UMD باشه

---

## 🔄 بازگشت به حالت آنلاین

اگر خواستی دوباره به حالت آنلاین برگردی:

```php
// تغییر header به نسخه آنلاین
include '../includes/header.php';
```

---

## 📞 پشتیبانی

اگر مشکلی داشتی:
1. فایل `OFFLINE_CONVERSION.md` رو بخون
2. Console مرورگر رو چک کن (F12)
3. مسیرهای فایلها رو بررسی کن

---

**تاریخ:** 2025-10-09  
**نسخه:** 1.0  
**وضعیت:** ✅ آماده استفاده
