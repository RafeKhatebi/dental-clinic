<?php
require_once '../config/config.php';

$id = $_GET['id'] ?? 0;
$staff = fetchOne("SELECT * FROM users WHERE id = ? AND is_staff = 1", [$id]);

if (!$staff) {
    redirect('/staff/index.php');
}

// Get salary history
$salaries = fetchAll("SELECT * FROM payments WHERE staff_id = ? AND payment_type = 'salary' ORDER BY payment_date DESC LIMIT 12", [$id]);

// Get withdrawal history
$withdrawals = fetchAll("SELECT * FROM payments WHERE staff_id = ? AND payment_type = 'withdrawal' ORDER BY payment_date DESC LIMIT 20", [$id]);

// Calculate totals
$totalSalariesPaid = fetchOne("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE staff_id = ? AND payment_type = 'salary'", [$id])['total'];
$totalWithdrawals = fetchOne("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE staff_id = ? AND payment_type = 'withdrawal'", [$id])['total'];

include '../includes/header.php';
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['staff']; ?></h1>
        <div class="flex gap-2">
            <a href="edit.php?id=<?php echo $id; ?>" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg"><?php echo $lang['edit']; ?></a>
            <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg"><?php echo $lang['back']; ?></a>
        </div>
    </div>

    <!-- Staff Info -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4"><?php echo $lang['staff']; ?></h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['full_name']; ?></p>
                <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($staff['full_name']); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['job_title']; ?></p>
                <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($staff['job_title']); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['phone']; ?></p>
                <p class="font-semibold text-gray-900"><?php echo $staff['phone']; ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['email']; ?></p>
                <p class="font-semibold text-gray-900"><?php echo $staff['email'] ?: '-'; ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['monthly_salary']; ?></p>
                <p class="font-semibold text-green-600"><?php echo formatCurrency($staff['monthly_salary']); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['hire_date']; ?></p>
                <p class="font-semibold text-gray-900"><?php echo formatDate($staff['hire_date']); ?></p>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-sm text-gray-600"><?php echo $lang['monthly_salary']; ?></p>
            <p class="text-3xl font-bold text-blue-600 mt-2"><?php echo formatCurrency($staff['monthly_salary']); ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-sm text-gray-600"><?php echo $lang['total_withdrawals']; ?></p>
            <p class="text-3xl font-bold text-red-600 mt-2"><?php echo formatCurrency($totalWithdrawals); ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-sm text-gray-600"><?php echo $lang['salary_paid']; ?></p>
            <p class="text-3xl font-bold text-green-600 mt-2"><?php echo formatCurrency($totalSalariesPaid); ?></p>
        </div>
    </div>

    <!-- Salary History -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800"><?php echo $lang['salary_list']; ?></h2>
        </div>
        <?php if (empty($salaries)): ?>
            <div class="p-8 text-center text-gray-500"><?php echo $lang['no_data']; ?></div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['month_year']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['amount']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['payment_date']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['status']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($salaries as $salary): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $salary['month_year']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600"><?php echo formatCurrency($salary['amount']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo formatDate($salary['payment_date']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800"><?php echo $lang['paid']; ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Withdrawal History -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800"><?php echo $lang['withdrawal_list']; ?></h2>
        </div>
        <?php if (empty($withdrawals)): ?>
            <div class="p-8 text-center text-gray-500"><?php echo $lang['no_data']; ?></div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['withdrawal_date']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['amount']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['month_year']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['notes']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($withdrawals as $withdrawal): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo formatDate($withdrawal['payment_date']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600"><?php echo formatCurrency($withdrawal['amount']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $withdrawal['month_year']; ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($withdrawal['notes'] ?: '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
