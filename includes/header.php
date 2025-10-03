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
    <title><?php echo $lang['app_name']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php if ($current_lang === 'fa'): ?>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
    </style>
    <?php else: ?>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar-link.active {
            background-color: #3B82F6;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0 hidden md:block flex flex-col">
            <div class="p-6 flex-shrink-0">
                <h1 class="text-xl font-bold"><?php echo getSetting('clinic_name', $lang['app_name']); ?></h1>
            </div>
            
            <nav class="mt-6 flex-1 overflow-y-auto">
                <a href="<?php echo BASE_URL; ?>/dashboard.php" 
                   class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <?php echo $lang['menu_dashboard']; ?>
                </a>
                
                <a href="<?php echo BASE_URL; ?>/patients/index.php" 
                   class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo $current_page === 'patients' || strpos($_SERVER['PHP_SELF'], '/patients/') !== false ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <?php echo $lang['menu_patients']; ?>
                </a>
                
                <a href="<?php echo BASE_URL; ?>/services/index.php" 
                   class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo strpos($_SERVER['PHP_SELF'], '/services/') !== false ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <?php echo $lang['menu_services']; ?>
                </a>
                
                <a href="<?php echo BASE_URL; ?>/medicines/index.php" 
                   class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo strpos($_SERVER['PHP_SELF'], '/medicines/') !== false ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                    <?php echo $lang['menu_medicines']; ?>
                </a>
                
                <a href="<?php echo BASE_URL; ?>/suppliers/index.php" 
                   class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo strpos($_SERVER['PHP_SELF'], '/suppliers/') !== false ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <?php echo $lang['menu_suppliers']; ?>
                </a>
                
                <a href="<?php echo BASE_URL; ?>/partners/index.php" 
                   class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo strpos($_SERVER['PHP_SELF'], '/partners/') !== false ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <?php echo $lang['menu_partners']; ?>
                </a>
                
                <a href="<?php echo BASE_URL; ?>/reports/index.php" 
                   class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo strpos($_SERVER['PHP_SELF'], '/reports/') !== false ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <?php echo $lang['menu_reports']; ?>
                </a>
                
                <?php if (hasRole('admin')): ?>
                <a href="<?php echo BASE_URL; ?>/users/index.php" 
                   class="sidebar-link flex items-center px-6 py-3 hover:bg-gray-800 transition <?php echo strpos($_SERVER['PHP_SELF'], '/users/') !== false ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
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
                    <svg class="w-5 h-5 <?php echo $current_lang === 'fa' ? 'ml-3' : 'mr-3'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <button id="show-sidebar" class="hidden text-gray-600 hover:text-gray-800" title="نمایش منو">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <!-- Language Switcher -->
                        <div class="flex gap-2">
                            <a href="?lang=fa" class="px-3 py-1 rounded <?php echo $current_lang === 'fa' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">
                                FA
                            </a>
                            <a href="?lang=en" class="px-3 py-1 rounded <?php echo $current_lang === 'en' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">
                                EN
                            </a>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center gap-2 text-gray-700 hover:text-gray-900">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium">
                                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                </div>
                                <span class="hidden md:block"><?php echo $user['full_name']; ?></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <div id="user-menu" class="hidden absolute <?php echo $current_lang === 'fa' ? 'left-0' : 'right-0'; ?> mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                <div class="px-4 py-2 border-b">
                                    <p class="text-sm font-medium text-gray-900"><?php echo $user['full_name']; ?></p>
                                    <p class="text-xs text-gray-500"><?php echo $lang[$user['role']]; ?></p>
                                </div>
                                <?php if (hasRole('admin')): ?>
                                <a href="<?php echo BASE_URL; ?>/settings/index.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 inline-block <?php echo $current_lang === 'fa' ? 'ml-2' : 'mr-2'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <?php echo $lang['menu_settings']; ?>
                                </a>
                                <?php endif; ?>
                                <a href="<?php echo BASE_URL; ?>/api/auth/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <svg class="w-4 h-4 inline-block <?php echo $current_lang === 'fa' ? 'ml-2' : 'mr-2'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
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
