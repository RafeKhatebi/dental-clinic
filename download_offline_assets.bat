@echo off
echo ========================================
echo دانلود فایلهای آفلاین
echo ========================================
echo.

REM ایجاد پوشهها
echo [1/4] ایجاد پوشهها...
mkdir assets\fonts\inter 2>nul
mkdir assets\fonts\vazirmatn 2>nul
mkdir assets\libs\chartjs 2>nul
echo ✓ پوشهها ایجاد شدند
echo.

REM دانلود Tailwind CSS
echo [2/4] دانلود Tailwind CSS...
curl -L "https://cdn.tailwindcss.com" -o assets\css\tailwind.min.css
if %errorlevel% equ 0 (
    echo ✓ Tailwind CSS دانلود شد
) else (
    echo ✗ خطا در دانلود Tailwind CSS
)
echo.

REM دانلود Chart.js
echo [3/4] دانلود Chart.js...
curl -L "https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" -o assets\libs\chartjs\chart.min.js
if %errorlevel% equ 0 (
    echo ✓ Chart.js دانلود شد
) else (
    echo ✗ خطا در دانلود Chart.js
)
echo.

REM راهنمای دانلود فونتها
echo [4/4] دانلود فونتها...
echo.
echo ⚠️ فونتها باید به صورت دستی دانلود شوند:
echo.
echo 1. Inter Font:
echo    - برو به: https://fonts.google.com/specimen/Inter
echo    - دکمه "Download family" را بزن
echo    - فایلهای woff2 را در assets\fonts\inter\ کپی کن
echo.
echo 2. Vazirmatn Font:
echo    - برو به: https://github.com/rastikerdar/vazirmatn/releases
echo    - آخرین نسخه را دانلود کن
echo    - فایلهای woff2 را در assets\fonts\vazirmatn\ کپی کن
echo.

echo ========================================
echo دانلود خودکار تکمیل شد!
echo لطفا فونتها را به صورت دستی دانلود کنید.
echo ========================================
pause
