<?php
require_once '../config/config.php';

if (!hasRole('admin')) {
    redirect('/dashboard.php');
}

include '../includes/header.php';

// Get current settings
$settings = fetchAll("SELECT * FROM system WHERE record_type = 'setting'");
$settingsArray = [];
foreach ($settings as $setting) {
    $settingsArray[$setting['setting_key']] = $setting['setting_value'];
}
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['settings']; ?></h1>
    </div>

    <!-- Quick Links -->
    <div class="grid md:grid-cols-2 gap-4 mb-6">
        <a href="permissions.php" class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl p-6 text-white hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-1">🔐 مدیریت دسترسیها</h3>
                    <p class="text-sm opacity-90">تعیین سطح دسترسی نقشها</p>
                </div>
            </div>
        </a>
        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl p-6 text-white">
            <div class="flex items-center gap-4">
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-1">⚙️ تنظیمات عمومی</h3>
                    <p class="text-sm opacity-90">اطلاعات کلینیک و سیستم</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="settings-form" class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b pb-3"><?php echo $lang['general_settings']; ?></h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Clinic Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['clinic_name']; ?>
                    </label>
                    <input type="text" name="clinic_name" value="<?php echo htmlspecialchars($settingsArray['clinic_name'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Clinic Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['clinic_phone']; ?>
                    </label>
                    <input type="tel" name="clinic_phone" value="<?php echo htmlspecialchars($settingsArray['clinic_phone'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Clinic Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['clinic_email']; ?>
                    </label>
                    <input type="email" name="clinic_email" value="<?php echo htmlspecialchars($settingsArray['clinic_email'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Currency -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['currency']; ?>
                    </label>
                    <input type="text" name="currency" value="<?php echo htmlspecialchars($settingsArray['currency'] ?? 'IRT'); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Default Language -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['language']; ?>
                    </label>
                    <select name="language"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="fa" <?php echo ($settingsArray['language'] ?? 'fa') === 'fa' ? 'selected' : ''; ?>>فارسی</option>
                        <option value="en" <?php echo ($settingsArray['language'] ?? 'fa') === 'en' ? 'selected' : ''; ?>>English</option>
                    </select>
                </div>

                <!-- Low Stock Alert -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['low_stock_alert']; ?>
                    </label>
                    <input type="number" name="low_stock_alert" value="<?php echo htmlspecialchars($settingsArray['low_stock_alert'] ?? '10'); ?>" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Expiry Alert Days -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['expiry_alert_days']; ?>
                    </label>
                    <input type="number" name="expiry_alert_days" value="<?php echo htmlspecialchars($settingsArray['expiry_alert_days'] ?? '30'); ?>" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
            </div>

            <!-- Clinic Address -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo $lang['clinic_address']; ?>
                </label>
                <textarea name="clinic_address" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"><?php echo htmlspecialchars($settingsArray['clinic_address'] ?? ''); ?></textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4">
                <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['save']; ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('settings-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>/api/settings/update.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('<?php echo $lang['update_success']; ?>', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        showToast('<?php echo $lang['error_occurred']; ?>', 'error');
    }
});
</script>

<?php include '../includes/footer.php'; ?>
