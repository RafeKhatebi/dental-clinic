<?php
require_once '../config/config.php';

if (!hasRole('admin')) {
    redirect('/dashboard.php');
}

$userId = intval($_GET['id'] ?? 0);
if (empty($userId)) {
    redirect('/users/index.php');
}

$user = fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);
if (!$user) {
    redirect('/users/index.php');
}

include '../includes/header.php';
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['edit_user']; ?></h1>
        <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
            <?php echo $lang['back']; ?>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="user-form" class="space-y-6">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['username']; ?> *
                    </label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['full_name']; ?> *
                    </label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['password']; ?> (<?php echo $current_lang === 'fa' ? 'خالی بگذارید تا تغییر نکند' : 'Leave empty to keep current'; ?>)
                    </label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['role']; ?> *
                    </label>
                    <select name="role" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>><?php echo $lang['admin']; ?></option>
                        <option value="dentist" <?php echo $user['role'] === 'dentist' ? 'selected' : ''; ?>><?php echo $lang['dentist']; ?></option>
                        <option value="secretary" <?php echo $user['role'] === 'secretary' ? 'selected' : ''; ?>><?php echo $lang['secretary']; ?></option>
                        <option value="accountant" <?php echo $user['role'] === 'accountant' ? 'selected' : ''; ?>><?php echo $lang['accountant']; ?></option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['phone']; ?>
                    </label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['email']; ?>
                    </label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['save']; ?>
                </button>
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['cancel']; ?>
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('user-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>/api/users/update.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('<?php echo $lang['update_success']; ?>');
            window.location.href = 'index.php';
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('<?php echo $lang['error_occurred']; ?>');
    }
});
</script>

<?php include '../includes/footer.php'; ?>
