<?php
require_once '../config/config.php';
include '../includes/header.php';
?>

<div class="max-w-6xl mx-auto space-y-6">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">📚 راهنمای سیستم</h1>
        <p class="text-gray-600">راهنمای سریع استفاده از سیستم مدیریت کلینیک دندانپزشکی</p>
    </div>

    <!-- Quick Start -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-lg p-8 text-white">
        <div class="flex items-center gap-4 mb-4">
            <div class="bg-white/20 p-3 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold">شروع سریع</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                <div class="text-3xl mb-2">👤</div>
                <h3 class="font-bold mb-1">1. ثبت بیمار</h3>
                <p class="text-sm opacity-90">بیماران → افزودن بیمار جدید</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                <div class="text-3xl mb-2">🦷</div>
                <h3 class="font-bold mb-1">2. ثبت خدمت</h3>
                <p class="text-sm opacity-90">خدمات → افزودن خدمت جدید</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                <div class="text-3xl mb-2">💰</div>
                <h3 class="font-bold mb-1">3. دریافت پرداخت</h3>
                <p class="text-sm opacity-90">پرداختها → ثبت پرداخت</p>
            </div>
        </div>
    </div>

    <!-- Main Features -->
    <div class="grid md:grid-cols-2 gap-6">

        <!-- Patients -->
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800">مدیریت بیماران</h3>
            </div>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start gap-2">
                    <span class="text-green-500 mt-1">✓</span>
                    <span>ثبت اطلاعات کامل بیمار با سابقه پزشکی</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-green-500 mt-1">✓</span>
                    <span>جستجوی سریع با نام، شماره تماس یا کد ملی</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-green-500 mt-1">✓</span>
                    <span>مشاهده تاریخچه خدمات و پرداختها</span>
                </li>
            </ul>
        </div>

        <!-- Services -->
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800">خدمات دندانپزشکی</h3>
            </div>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start gap-2">
                    <span class="text-green-500 mt-1">✓</span>
                    <span>ثبت خدمات با قیمت و تخفیف</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-green-500 mt-1">✓</span>
                    <span>مشخص کردن شماره دندان</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-green-500 mt-1">✓</span>
                    <span>قالبهای خدمات برای ثبت سریع</span>
                </li>
            </ul>
        </div>

        <!-- Medicines -->
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800">مدیریت داروخانه</h3>
            </div>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start gap-2">
                    <span class="text-green-500 mt-1">✓</span>
                    <span>کنترل موجودی با هشدار موجودی کم</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-green-500 mt-1">✓</span>
                    <span>پیگیری تاریخ انقضا</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-green-500 mt-1">✓</span>
                    <span>ثبت خرید و فروش دارو</span>
                </li>
            </ul>
        </div>

        <!-- Financial -->
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800">مدیریت مالی</h3>
            </div>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start gap-2">
                    <span class="text-green-500 mt-1">✓</span>
                    <span>پرداخت نقدی، اقساطی و وام</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-green-500 mt-1">✓</span>
                    <span>مدیریت شرکا و معاشات</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-green-500 mt-1">✓</span>
                    <span>ثبت مصارف و هزینهها</span>
                </li>
            </ul>
        </div>

    </div>

    <!-- Keyboard Shortcuts -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="bg-indigo-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                    </path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800">میانبرهای صفحه کلید</h3>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                <kbd class="px-3 py-1 bg-white border border-gray-300 rounded shadow-sm font-mono text-sm">Ctrl+K</kbd>
                <span class="text-sm text-gray-600">جستجوی سریع</span>
            </div>
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                <kbd class="px-3 py-1 bg-white border border-gray-300 rounded shadow-sm font-mono text-sm">Ctrl+N</kbd>
                <span class="text-sm text-gray-600">افزودن جدید</span>
            </div>
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                <kbd class="px-3 py-1 bg-white border border-gray-300 rounded shadow-sm font-mono text-sm">Ctrl+S</kbd>
                <span class="text-sm text-gray-600">ذخیره</span>
            </div>
        </div>
    </div>

    <!-- Tips -->
    <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-6 border border-green-200">
        <div class="flex items-start gap-3">
            <div class="bg-green-500 text-white p-2 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                    </path>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-800 mb-2">💡 نکات مهم</h3>
                <ul class="space-y-1 text-sm text-gray-700">
                    <li>• پشتیبان گیری منظم از دیتابیس را فراموش نکنید</li>
                    <li>• برای امنیت بیشتر، رمز عبور خود را به صورت دورهای تغییر دهید</li>
                    <li>• از فیلترهای جستجو برای یافتن سریع اطلاعات استفاده کنید</li>
                    <li>• گزارشات مالی را به صورت ماهانه بررسی کنید</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Support -->
    <div class="bg-white rounded-xl shadow-sm p-6 text-center">
        <div class="inline-block bg-red-100 p-4 rounded-full mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                </path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">نیاز به کمک دارید؟</h3>
        <p class="text-gray-600 mb-4">برای پشتیبانی با ما تماس بگیرید</p>
        <a href="mailto:rkhatibi2003@gmail.com"
            class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
            ارسال ایمیل پشتیبانی
        </a>
    </div>

</div>

<?php include '../includes/footer.php'; ?>