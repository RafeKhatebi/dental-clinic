<?php
require_once '../config/config.php';

$id = $_GET['id'] ?? 0;
$staff = fetchOne("SELECT * FROM users WHERE id = ? AND is_staff = 1", [$id]);

if (!$staff) {
    redirect('/staff/index.php');
}

include '../includes/header.php';
?>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['edit_staff']; ?></h1>
        <a href="view.php?id=<?php echo $id; ?>" class="text-gray-600 hover:text-gray-800"><?php echo $lang['back']; ?></a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="staffForm" class="space-y-4">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['full_name']; ?> *</label>
                    <input type="text" name="full_name" required value="<?php echo htmlspecialchars($staff['full_name']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['job_title']; ?> *</label>
                    <input type="text" name="job_title" required value="<?php echo htmlspecialchars($staff['job_title']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['phone']; ?> *</label>
                    <input type="text" name="phone" required value="<?php echo $staff['phone']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['email']; ?></label>
                    <input type="email" name="email" value="<?php echo $staff['email']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['monthly_salary']; ?> *</label>
                    <input type="number" name="monthly_salary" required min="0" step="0.01" value="<?php echo $staff['monthly_salary']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['hire_date']; ?> *</label>
                    <input type="date" name="hire_date" required value="<?php echo $staff['hire_date']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['status']; ?></label>
                <select name="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="1" <?php echo $staff['is_active'] ? 'selected' : ''; ?>><?php echo $lang['active']; ?></option>
                    <option value="0" <?php echo !$staff['is_active'] ? 'selected' : ''; ?>><?php echo $lang['inactive']; ?></option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"><?php echo $lang['save']; ?></button>
                <a href="view.php?id=<?php echo $id; ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg"><?php echo $lang['cancel']; ?></a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('staffForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);

    try {
        const response = await fetch('<?php echo BASE_URL; ?>/api/staff/update.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
            showToast('<?php echo $lang['update_success']; ?>', 'success');
            window.location.href = 'view.php?id=<?php echo $id; ?>';
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        showToast('<?php echo $lang['error_occurred']; ?>', 'error');
    }
});
</script>

<?php include '../includes/footer.php'; ?>
