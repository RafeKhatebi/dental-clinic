<?php
require_once '../config/config.php';

$id = $_GET['id'] ?? 0;
$expense = fetchOne("SELECT * FROM documents WHERE id = ? AND document_type = 'expense'", [$id]);

if (!$expense) {
    redirect('/expenses/index.php');
}

$categories = explode(',', getSetting('expense_categories', 'کرایه,برق,آب,گاز,اینترنت,تلفن,نظافت,تعمیرات,سایر'));
include '../includes/header.php';
?>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['edit_expense']; ?></h1>
        <a href="index.php" class="text-gray-600 hover:text-gray-800"><?php echo $lang['back']; ?></a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="expenseForm" class="space-y-4">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div>
                <label
                    class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['description'] ?? 'عنوان'; ?>
                    *</label>
                <input type="text" name="title" required value="<?php echo htmlspecialchars($expense['title']); ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['expense_category']; ?>
                        *</label>
                    <select name="expense_category" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo trim($cat); ?>" <?php echo $expense['expense_category'] === trim($cat) ? 'selected' : ''; ?>><?php echo trim($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['expense_type']; ?>
                        *</label>
                    <select name="expense_type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="fixed" <?php echo $expense['expense_type'] === 'fixed' ? 'selected' : ''; ?>>
                            <?php echo $lang['fixed']; ?></option>
                        <option value="variable" <?php echo $expense['expense_type'] === 'variable' ? 'selected' : ''; ?>>
                            <?php echo $lang['variable']; ?></option>
                        <option value="one_time" <?php echo $expense['expense_type'] === 'one_time' ? 'selected' : ''; ?>>
                            <?php echo $lang['one_time']; ?></option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['amount']; ?> *</label>
                    <input type="number" name="amount" required min="0" step="0.01"
                        value="<?php echo $expense['amount']; ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['recurrence']; ?>
                        *</label>
                    <select name="recurrence" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="monthly" <?php echo $expense['recurrence'] === 'monthly' ? 'selected' : ''; ?>>
                            <?php echo $lang['monthly']; ?></option>
                        <option value="quarterly" <?php echo $expense['recurrence'] === 'quarterly' ? 'selected' : ''; ?>>
                            <?php echo $lang['quarterly']; ?></option>
                        <option value="yearly" <?php echo $expense['recurrence'] === 'yearly' ? 'selected' : ''; ?>>
                            <?php echo $lang['yearly']; ?></option>
                        <option value="one_time" <?php echo $expense['recurrence'] === 'one_time' ? 'selected' : ''; ?>>
                            <?php echo $lang['one_time']; ?></option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['next_due_date']; ?>
                    *</label>
                <input type="date" name="next_due_date" required value="<?php echo $expense['next_due_date']; ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['status']; ?></label>
                <select name="status"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="active" <?php echo $expense['status'] === 'active' ? 'selected' : ''; ?>>
                        <?php echo $lang['active']; ?></option>
                    <option value="inactive" <?php echo $expense['status'] === 'inactive' ? 'selected' : ''; ?>>
                        <?php echo $lang['inactive']; ?></option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['description']; ?></label>
                <textarea name="content" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($expense['content']); ?></textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"><?php echo $lang['save']; ?></button>
                <a href="index.php"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg"><?php echo $lang['cancel']; ?></a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('expenseForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        try {
            const response = await fetch('<?php echo BASE_URL; ?>/api/expenses/update.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            if (result.success) {
                showToast('<?php echo $lang['update_success']; ?>', 'success');
                setTimeout(() => window.location.href = 'index.php', 1000);
            } else {
                showToast(result.message, 'error');
            }
        } catch (error) {
            showToast('<?php echo $lang['error_occurred']; ?>', 'error');
        }
    });
</script>

<?php include '../includes/footer.php'; ?>