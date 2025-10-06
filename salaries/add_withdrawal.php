<?php
require_once '../config/config.php';

$staffId = $_GET['staff_id'] ?? 0;
$month = $_GET['month'] ?? date('Y-m');

$staff = fetchOne("SELECT * FROM users WHERE id = ? AND is_staff = 1", [$staffId]);
if (!$staff) {
    redirect('/staff/index.php');
}

include '../includes/header.php';
?>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['add_withdrawal']; ?></h1>
        <a href="index.php?month=<?php echo $month; ?>" class="text-gray-600 hover:text-gray-800"><?php echo $lang['back']; ?></a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="withdrawalForm" class="space-y-4">
            <input type="hidden" name="staff_id" value="<?php echo $staffId; ?>">
            <input type="hidden" name="month_year" value="<?php echo $month; ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['full_name']; ?></label>
                <input type="text" value="<?php echo htmlspecialchars($staff['full_name']); ?>" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['withdrawal_amount']; ?> *</label>
                <input type="number" name="amount" required min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['withdrawal_date']; ?> *</label>
                <input type="date" name="payment_date" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['withdrawal_reason']; ?></label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"><?php echo $lang['save']; ?></button>
                <a href="index.php?month=<?php echo $month; ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg"><?php echo $lang['cancel']; ?></a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('withdrawalForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);

    try {
        const response = await fetch('<?php echo BASE_URL; ?>/api/salaries/add_withdrawal.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
            showToast('<?php echo $lang['save_success']; ?>', 'success');
            setTimeout(() => window.location.href = 'index.php?month=<?php echo $month; ?>', 1000);
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        showToast('<?php echo $lang['error_occurred']; ?>', 'error');
    }
});
</script>

<?php include '../includes/footer.php'; ?>
