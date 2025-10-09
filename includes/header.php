<?php
if (!isLoggedIn()) {
    redirect('/index.php');
}

$lang = loadLanguage();
$user = getCurrentUser();
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $current_lang === 'fa' ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo csrfMeta(); ?>
    <title><?php echo $lang['app_name']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php if ($current_lang === 'fa'): ?>
        <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap"
            rel="stylesheet">
        <style>
            body {
                font-family: 'Vazirmatn', sans-serif;
            }
        </style>
    <?php else: ?>
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
        </style>
    <?php endif; ?>
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
    <!-- offline link -->
    <script src="../assets/libs/chartjs/chart.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/mobile.css">
    <script src="<?php echo BASE_URL; ?>/assets/js/csrf.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/shortcuts.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/bulk-actions.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/validation.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/mobile.js" defer></script>
    <style>
        .sidebar-link.active {
            background-color: #3B82F6;
            color: white;
        }

        /* Loading Overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3B82F6;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Tooltip */
        [data-tooltip] {
            position: relative;
            cursor: help;
        }

        [data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 8px 12px;
            background: #1f2937;
            color: white;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
            margin-bottom: 5px;
        }

        [data-tooltip]:hover::before {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-top-color: #1f2937;
            z-index: 1000;
        }

        /* Confirm Dialog */
        .confirm-dialog {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9998;
            justify-content: center;
            align-items: center;
        }

        .confirm-dialog.active {
            display: flex;
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 16px;
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .toast.success {
            border-left: 4px solid #10b981;
        }

        .toast.error {
            border-left: 4px solid #ef4444;
        }

        .toast.warning {
            border-left: 4px solid #f59e0b;
        }

        .toast.info {
            border-left: 4px solid #3b82f6;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .toast.hiding {
            animation: slideOut 0.3s ease-in;
        }

        /* Print Styles */
        @media print {

            #sidebar,
            #mobile-menu-toggle,
            #show-sidebar,
            #user-menu-button,
            #user-menu,
            #breadcrumb,
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            main {
                padding: 0 !important;
            }

            .bg-white {
                box-shadow: none !important;
            }

            button,
            a[href]:not([href^="#"]) {
                display: none !important;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="w-64 bg-gray-900 text-white flex-shrink-0 hidden md:flex flex-col transition-all duration-300">
            <div class="p-6 flex-shrink-0 flex items-center justify-between">
                <h1 id="sidebar-title" class="text-xl font-bold">
                    <?php echo getSetting('clinic_name', $lang['app_name']); ?>
                </h1>
                <button id="sidebar-toggle" class="text-white hover:text-gray-300 hidden md:block" title="بستن منو">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto pb-6" style="scrollbar-width: thin; scrollbar-color: #4B5563 #1F2937;">
                <a href="<?php echo BASE_URL; ?>/dashboard.php"
                    class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <?php echo $lang['menu_dashboard']; ?>
                </a>

                <a href="<?php echo BASE_URL; ?>/patients/index.php"
                    class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo $current_page === 'patients' || strpos($_SERVER['PHP_SELF'], '/patients/') !== false ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <?php echo $lang['menu_patients']; ?>
                </a>

                <a href="<?php echo BASE_URL; ?>/services/index.php"
                    class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo strpos($_SERVER['PHP_SELF'], '/services/') !== false ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    <?php echo $lang['menu_services']; ?>
                </a>

                <a href="<?php echo BASE_URL; ?>/medicines/index.php"
                    class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo strpos($_SERVER['PHP_SELF'], '/medicines/') !== false ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                        </path>
                    </svg>
                    <?php echo $lang['menu_medicines']; ?>
                </a>

                <a href="<?php echo BASE_URL; ?>/suppliers/index.php"
                    class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo strpos($_SERVER['PHP_SELF'], '/suppliers/') !== false ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <?php echo $lang['menu_suppliers']; ?>
                </a>

                <!-- Financial Management -->
                <div class="mt-2">
                    <button onclick="toggleSubmenu('financial')"
                        class="sidebar-link flex items-center justify-between w-full px-6 py-3 hover:bg-gray-800 transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <span><?php echo $current_lang === 'fa' ? 'مدیریت مالی' : 'Financial'; ?></span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" id="financial-icon" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div id="financial-submenu" class="hidden bg-gray-800">
                        <a href="<?php echo BASE_URL; ?>/partners/index.php"
                            class="sidebar-link flex items-center px-12 py-2 hover:bg-gray-700 transition text-sm <?php echo strpos($_SERVER['PHP_SELF'], '/partners/') !== false ? 'active' : ''; ?>">
                            <?php echo $lang['menu_partners']; ?>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/staff/index.php"
                            class="sidebar-link flex items-center px-12 py-2 hover:bg-gray-700 transition text-sm <?php echo strpos($_SERVER['PHP_SELF'], '/staff/') !== false ? 'active' : ''; ?>">
                            <?php echo $lang['menu_staff']; ?>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/salaries/index.php"
                            class="sidebar-link flex items-center px-12 py-2 hover:bg-gray-700 transition text-sm <?php echo strpos($_SERVER['PHP_SELF'], '/salaries/') !== false ? 'active' : ''; ?>">
                            <?php echo $lang['menu_salaries']; ?>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/expenses/index.php"
                            class="sidebar-link flex items-center px-12 py-2 hover:bg-gray-700 transition text-sm <?php echo strpos($_SERVER['PHP_SELF'], '/expenses/') !== false ? 'active' : ''; ?>">
                            <?php echo $lang['menu_expenses']; ?>
                        </a>
                    </div>
                </div>

                <!-- Reports -->
                <div class="mt-2">
                    <button onclick="toggleSubmenu('reports')"
                        class="sidebar-link flex items-center justify-between w-full px-6 py-3 hover:bg-gray-800 transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span><?php echo $lang['menu_reports']; ?></span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" id="reports-icon" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div id="reports-submenu" class="hidden bg-gray-800">
                        <a href="<?php echo BASE_URL; ?>/reports/financial_summary.php"
                            class="sidebar-link flex items-center px-12 py-2 hover:bg-gray-700 transition text-sm <?php echo basename($_SERVER['PHP_SELF']) === 'financial_summary.php' ? 'active' : ''; ?>">
                            خلاصه مالی
                        </a>
                        <a href="<?php echo BASE_URL; ?>/reports/doctor_performance.php"
                            class="sidebar-link flex items-center px-12 py-2 hover:bg-gray-700 transition text-sm <?php echo basename($_SERVER['PHP_SELF']) === 'doctor_performance.php' ? 'active' : ''; ?>">
                            عملکرد دکتر
                        </a>
                        <a href="<?php echo BASE_URL; ?>/reports/medicine_inventory.php"
                            class="sidebar-link flex items-center px-12 py-2 hover:bg-gray-700 transition text-sm <?php echo basename($_SERVER['PHP_SELF']) === 'medicine_inventory.php' ? 'active' : ''; ?>">
                            موجودی دارو
                        </a>
                        <a href="<?php echo BASE_URL; ?>/reports/index.php"
                            class="sidebar-link flex items-center px-12 py-2 hover:bg-gray-700 transition text-sm <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' && strpos($_SERVER['PHP_SELF'], '/reports/') !== false ? 'active' : ''; ?>">
                            گزارشات و فعالیتها
                        </a>
                    </div>
                </div>

                <?php if (hasRole('admin')): ?>
                    <a href="<?php echo BASE_URL; ?>/users/index.php"
                        class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo strpos($_SERVER['PHP_SELF'], '/users/') !== false ? 'active' : ''; ?>">
                        <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <?php echo $lang['menu_users']; ?>
                    </a>

                    <?php /* Settings moved to user menu in header
             <a href="<?php echo BASE_URL; ?>/settings/index.php" 
                class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo strpos($_SERVER['PHP_SELF'], '/settings/') !== false ? 'active' : ''; ?>">
                 <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                 </svg>
                 <?php echo $lang['menu_settings']; ?>
             </a>
             */ ?>

                    <a href="<?php echo BASE_URL; ?>/backup/index.php"
                        class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo strpos($_SERVER['PHP_SELF'], '/backup/') !== false ? 'active' : ''; ?>">
                        <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        <?php echo $lang['menu_backup']; ?>
                    </a>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-3">
                        <button id="mobile-menu-toggle" class="md:hidden text-gray-600 hover:text-gray-800">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <button id="show-sidebar" class="hidden text-gray-600 hover:text-gray-800" title="نمایش منو">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- Notifications -->
                        <div class="relative">
                            <button id="notif-button" class="relative text-gray-600 hover:text-gray-800">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                    </path>
                                </svg>
                                <span id="notif-badge"
                                    class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                            </button>
                            <div id="notif-menu"
                                class="hidden absolute left-0 mt-2 w-80 bg-white rounded-lg shadow-lg py-2 z-50 max-h-96 overflow-y-auto">
                            </div>
                        </div>

                        <!-- Language Switcher -->
                        <div class="flex gap-2">
                            <a href="?lang=fa"
                                class="px-3 py-1 rounded <?php echo $current_lang === 'fa' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">
                                FA
                            </a>
                            <a href="?lang=en"
                                class="px-3 py-1 rounded <?php echo $current_lang === 'en' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">
                                EN
                            </a>
                        </div>

                        <!-- User Menu -->
                        <div class="relative">
                            <button id="user-menu-button"
                                class="flex items-center gap-2 text-gray-700 hover:text-gray-900">
                                <div
                                    class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium">
                                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                </div>
                                <span class="hidden md:block"><?php echo $user['full_name']; ?></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div id="user-menu"
                                class="hidden absolute <?php echo $current_lang === 'fa' ? 'left-0' : 'right-0'; ?> mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                <div class="px-4 py-2 border-b">
                                    <p class="text-sm font-medium text-gray-900"><?php echo $user['full_name']; ?></p>
                                    <p class="text-xs text-gray-500"><?php echo $lang[$user['role']]; ?></p>
                                </div>
                                <a href="<?php echo BASE_URL; ?>/help/index.php"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 inline-block <?php echo $current_lang === 'fa' ? 'ml-2' : 'mr-2'; ?>"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    راهنما
                                </a>
                                <?php if (hasRole('admin')): ?>
                                    <a href="<?php echo BASE_URL; ?>/settings/index.php"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 inline-block <?php echo $current_lang === 'fa' ? 'ml-2' : 'mr-2'; ?>"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <?php echo $lang['menu_settings']; ?>
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo BASE_URL; ?>/api/auth/logout.php"
                                    class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <svg class="w-4 h-4 inline-block <?php echo $current_lang === 'fa' ? 'ml-2' : 'mr-2'; ?>"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    <?php echo $lang['logout']; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Breadcrumb -->
                <nav class="mb-4 text-sm" id="breadcrumb">
                    <ol class="flex items-center gap-2 text-gray-600">
                        <li><a href="<?php echo BASE_URL; ?>/dashboard.php" class="hover:text-blue-600">🏠 داشبورد</a>
                        </li>
                    </ol>
                </nav>

                <!-- Loading Overlay -->
                <div id="loadingOverlay" class="loading-overlay">
                    <div class="spinner"></div>
                </div>

                <!-- Confirm Dialog -->
                <div id="confirmDialog" class="confirm-dialog">
                    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md mx-4">
                        <h3 class="text-lg font-bold mb-4" id="confirmTitle">تایید</h3>
                        <p class="text-gray-600 mb-6" id="confirmMessage"></p>
                        <div class="flex gap-3 justify-end">
                            <button onclick="window.confirmCallback(false)"
                                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">انصراف</button>
                            <button onclick="window.confirmCallback(true)"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">تایید</button>
                        </div>
                    </div>
                </div>

                <!-- Toast Container -->
                <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 10000;"></div>