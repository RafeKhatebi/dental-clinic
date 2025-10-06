<?php
require_once '../config/config.php';
include '../includes/header.php';

$currentMonth = date('Y-m');
$selectedMonth = $_GET['month'] ?? $currentMonth;

// Get all staff
$staff = fetchAll("SELECT * FROM users WHERE is_staff = 1 AND is_active = 1 ORDER BY full_name");

// Get salary payments for selected month
$salaryPayments = [];
foreach ($staff as $member) {
    $payment = fetchOne("
        SELECT p.*, 
               (SELECT COALESCE(SUM(amount), 0) FROM payments 
                WHERE staff_id = ? AND payment_type = 'withdrawal' 
                AND month_year = ?) as total_withdrawals
        FROM payments p
        WHERE p.staff_id = ? AND p.payment_type = 'salary' AND p.month_year = ?
    ", [$member['id'], $selectedMonth, $member['id'], $selectedMonth]);
    
    $withdrawals = fetchOne("
        SELECT COALESCE(SUM(amount), 0) as total 
        FROM payments 
        WHERE staff_id = ? AND payment_type = 'withdrawal' AND month_year = ?
    ", [$member['id'], $selectedMonth])['total'] ?? 0;
    
    $salaryPayments[$member['id']] = [
        'payment' => $payment,
        'withdrawals' => $withdrawals,
        'net_salary' => $member['monthly_salary'] - $withdrawals
    ];
}
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['salary_list']; ?></h1>
        <div class="flex gap-2">
            <button onclick="exportToExcel('salariesTable', 'salaries')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                📊 Excel
            </button>
            <input type="month" id="month-selector" value="<?php echo $selectedMonth; ?>" 
                class="px-4 py-2 border border-gray-300 rounded-lg" 
                onchange="window.location.href='?month='+this.value">
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <?php if (empty($staff)): ?>
            <div class="p-8 text-center text-gray-500"><?php echo $lang['no_data']; ?></div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table id="salariesTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['full_name']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['job_title']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['monthly_salary']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['total_withdrawals']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['net_salary']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['status']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($staff as $member): 
                            $data = $salaryPayments[$member['id']];
                            $isPaid = !empty($data['payment']);
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($member['full_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($member['job_title'] ?: '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600"><?php echo formatCurrency($member['monthly_salary']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                <?php if ($data['withdrawals'] > 0): ?>
                                    <a href="withdrawals.php?staff_id=<?php echo $member['id']; ?>&month=<?php echo $selectedMonth; ?>" class="hover:underline">
                                        <?php echo formatCurrency($data['withdrawals']); ?>
                                    </a>
                                <?php else: ?>
                                    0
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600"><?php echo formatCurrency($data['net_salary']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if ($isPaid): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800"><?php echo $lang['salary_paid']; ?></span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800"><?php echo $lang['salary_pending']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <?php if (!$isPaid): ?>
                                        <button onclick="paySalary(<?php echo $member['id']; ?>, '<?php echo $selectedMonth; ?>', <?php echo $data['net_salary']; ?>)" 
                                            class="text-green-600 hover:text-green-900"><?php echo $lang['pay_salary']; ?></button>
                                    <?php endif; ?>
                                    <a href="add_withdrawal.php?staff_id=<?php echo $member['id']; ?>&month=<?php echo $selectedMonth; ?>" 
                                       class="text-blue-600 hover:text-blue-900"><?php echo $lang['add_withdrawal']; ?></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function paySalary(staffId, monthYear, netSalary) {
    if (!confirm('<?php echo $current_lang === 'fa' ? 'آیا از پرداخت معاش اطمینان دارید؟' : 'Confirm salary payment?'; ?>')) {
        return;
    }

    try {
        const response = await fetch('<?php echo BASE_URL; ?>/api/salaries/pay.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({staff_id: staffId, month_year: monthYear, amount: netSalary})
        });

        const data = await response.json();
        if (data.success) {
            showToast('<?php echo $lang['salary_paid']; ?>', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        showToast('<?php echo $lang['error_occurred']; ?>', 'error');
    }
}
</script>

<?php include '../includes/footer.php'; ?>
