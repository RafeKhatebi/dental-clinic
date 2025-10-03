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

    <!-- Settings Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="settings-form" class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b pb-3"><?php echo $lang['general_settings']; ?></h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Clinic Name (Persian) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['clinic_name']; ?> (فارسی)
                    </label>
                    <input type="text" name="clinic_name_fa" value="<?php echo htmlspecialchars($settingsArray['clinic_name_fa'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Clinic Name (English) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['clinic_name']; ?> (English)
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
            alert('<?php echo $lang['update_success']; ?>');
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('<?php echo $lang['error_occurred']; ?>');
    }
});
</script>

<?php include '../includes/footer.php'; ?>
