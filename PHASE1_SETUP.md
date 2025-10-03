# راهنمای نصب فاز 1
# Phase 1 Setup Guide

## گام 1: نصب TCPDF

### روش 1: دانلود دستی
1. به سایت بروید: https://github.com/tecnickcom/TCPDF/releases
2. آخرین نسخه را دانلود کنید
3. فایل zip را در `teeth/lib/tcpdf/` استخراج کنید

### روش 2: Composer (توصیه میشود)
```bash
cd c:\xampp\htdocs\Teeth\teeth
composer require tecnickcom/tcpdf
```

## گام 2: دانلود فونت فارسی

### فونت Vazir
1. دانلود: https://github.com/rastikerdar/vazir-font/releases
2. فایلهای TTF را در `teeth/lib/fonts/` قرار دهید

### فونت IRANSans
1. دانلود: https://github.com/rastikerdar/iran-sans/releases
2. فایلهای TTF را در `teeth/lib/fonts/` قرار دهید

## گام 3: نصب PHPSpreadsheet (برای Excel)

```bash
composer require phpoffice/phpspreadsheet
```

## گام 4: تنظیمات

1. لوگوی کلینیک را در `teeth/assets/images/logo.png` قرار دهید
2. مهر کلینیک را در `teeth/assets/images/stamp.png` قرار دهید
3. امضای دیجیتال را در `teeth/assets/images/signature.png` قرار دهید

## گام 5: تست

بعد از نصب، فایلهای زیر را اجرا کنید:
- `reports/test_pdf.php` - تست PDF
- `reports/test_excel.php` - تست Excel

---

## ساختار فایلها

```
teeth/
├── lib/
│   ├── tcpdf/          # کتابخانه TCPDF
│   ├── fonts/          # فونتهای فارسی
│   └── pdf_helper.php  # توابع کمکی PDF
├── exports/
│   ├── pdf/            # فایلهای PDF تولید شده
│   └── excel/          # فایلهای Excel تولید شده
├── reports/
│   ├── financial/      # گزارشات مالی
│   ├── invoices/       # فاکتورها
│   └── prescriptions/  # نسخهها
└── assets/
    └── images/
        ├── logo.png
        ├── stamp.png
        └── signature.png
```

## وضعیت نصب

- [ ] TCPDF نصب شد
- [ ] فونت فارسی اضافه شد
- [ ] PHPSpreadsheet نصب شد
- [ ] لوگو و مهر اضافه شد
- [ ] تست موفق بود

---

**آماده برای شروع کدنویسی!**
