<?php
require_once '../config/config.php';
if (!hasRole('admin')) redirect('/dashboard.php');
include '../includes/header.php';

$permissions = [
    'dashboard' => ['label' => 'داشبورد', 'icon' => '🏠'],
    'patients' => ['label' => 'بیماران', 'icon' => '👥'],
    'services' => ['label' => 'خدمات', 'icon' => '🦷'],
    'medicines' => ['label' => 'داروخانه', 'icon' => '💊'],
    'suppliers' => ['label' => 'تامین کنندگان', 'icon' => '📦'],
    'financial' => ['label' => 'مدیریت مالی', 'icon' => '💰'],
    'reports' => ['label' => 'گزارشات', 'icon' => '📊'],
    'users' => ['label' => 'کاربران', 'icon' => '👤'],
    'settings' => ['label' => 'تنظیمات', 'icon' => '⚙️'],
    'backup' => ['label' => 'پشتیبان', 'icon' => '💾']
];

$roles = ['admin', 'dentist', 'secretary', 'accountant'];

// Load current permissions
$currentPerms = [];
$permsData = fetchAll("SELECT * FROM system WHERE record_type = 'permission'");
foreach ($permsData as $perm) {
    $data = json_decode($perm['data'], true);
    $currentPerms[$perm['setting_key']] = $data;
}

// Default permissions
$defaultPerms = [
    'admin' => array_keys($permissions),
    'dentist' => ['dashboard', 'patients', 'services', 'medicines', 'reports'],
    'secretary' => ['dashboard', 'patients', 'medicines', 'reports'],
    'accountant' => ['dashboard', 'financial', 'reports']
];
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">🔐 مدیریت دسترسیها</h1>
        <a href="index.php" class="text-blue-600 hover:text-blue-800">← بازگشت به تنظیمات</a>
    </div>

    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
        <div class="flex items-start gap-3">
            <div class="bg-blue-500 text-white p-2 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-800 mb-1">راهنما</h3>
                <p class="text-sm text-gray-700">با تیک زدن هر گزینه، دسترسی آن نقش به بخش مربوطه فعال میشود. نقش Admin همیشه دسترسی کامل دارد.</p>
            </div>
        </div>
    </div>

    <form id="permissions-form" class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-700 sticky right-0 bg-gray-50">بخش</th>
                        <?php foreach ($roles as $role): ?>
                        <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">
                            <div class="flex flex-col items-center gap-1">
                                <span><?php echo $lang[$role]; ?></span>
                                <?php if ($role === 'admin'): ?>
                                <span class="text-xs text-gray-500">(کامل)</span>
                                <?php endif; ?>
                            </div>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($permissions as $key => $perm): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-white">
                            <div class="flex items-center gap-2">
                                <span class="text-xl"><?php echo $perm['icon']; ?></span>
                                <span><?php echo $perm['label']; ?></span>
                            </div>
                        </td>
                        <?php foreach ($roles as $role): ?>
                        <td class="px-6 py-4 text-center">
                            <?php 
                            $isChecked = in_array($key, $currentPerms[$role] ?? $defaultPerms[$role] ?? []);
                            $isDisabled = $role === 'admin';
                            ?>
                            <input type="checkbox" 
                                name="permissions[<?php echo $role; ?>][]" 
                                value="<?php echo $key; ?>"
                                <?php echo $isChecked ? 'checked' : ''; ?>
                                <?php echo $isDisabled ? 'disabled checked' : ''; ?>
                                class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer">
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50 flex gap-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                💾 ذخیره تغییرات
            </button>
            <button type="button" onclick="resetToDefault()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                🔄 بازگشت به پیشفرض
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('permissions-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>/api/settings/update_permissions.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('✅ دسترسیها با موفقیت بهروزرسانی شد', 'success');
        } else {
            showToast(data.message || 'خطا در بهروزرسانی', 'error');
        }
    } catch (error) {
        showToast('خطا در ارتباط با سرور', 'error');
    }
});

function resetToDefault() {
    if (confirm('آیا مطمئن هستید که میخواهید دسترسیها به حالت پیشفرض برگردند؟')) {
        location.href = '?reset=1';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
