<?php
require_once 'config/config.php';

// If user is logged in, redirect to dashboard
if (isLoggedIn()) {
    redirect('/dashboard.php');
}

// Handle language change
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fa', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
    $current_lang = $_GET['lang'];
}

$lang = loadLanguage();
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $current_lang === 'fa' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['app_name']; ?> - <?php echo $lang['login']; ?></title>
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
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <!-- Language Switcher -->
            <div class="flex justify-end mb-4">
                <div class="bg-white rounded-lg shadow-sm p-1 flex gap-1">
                    <a href="?lang=fa" class="px-3 py-1 rounded <?php echo $current_lang === 'fa' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">
                        فارسی
                    </a>
                    <a href="?lang=en" class="px-3 py-1 rounded <?php echo $current_lang === 'en' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">
                        English
                    </a>
                </div>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <!-- Logo/Header -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-500 rounded-full mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2"><?php echo $lang['app_name']; ?></h1>
                    <p class="text-gray-600"><?php echo $lang['welcome']; ?></p>
                </div>

                <!-- Error Message -->
                <div id="error-message" class="hidden mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg"></div>

                <!-- Login Form -->
                <form id="login-form" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <?php echo $lang['username']; ?>
                        </label>
                        <input type="text" id="username" name="username" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <?php echo $lang['password']; ?>
                        </label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="<?php echo $current_lang === 'fa' ? 'mr-2' : 'ml-2'; ?> text-sm text-gray-600">
                                <?php echo $lang['remember_me']; ?>
                            </span>
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition duration-200 transform hover:scale-105">
                        <?php echo $lang['login']; ?>
                    </button>
                </form>

                <!-- Default Credentials Info -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-600 text-center">
                        <strong>Default Login:</strong><br>
                        Username: admin | Password: admin123
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const errorDiv = document.getElementById('error-message');
            
            try {
                const response = await fetch('<?php echo BASE_URL; ?>/api/auth/login.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = '<?php echo BASE_URL; ?>/dashboard.php';
                } else {
                    errorDiv.textContent = data.message;
                    errorDiv.classList.remove('hidden');
                }
            } catch (error) {
                errorDiv.textContent = '<?php echo $lang['error_occurred']; ?>';
                errorDiv.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
