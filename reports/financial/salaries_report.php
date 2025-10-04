<?php
require_once '../../config/config.php';

$month = $_GET['month'] ?? date('Y-m');
$startDate = $month . '-01';
$endDate = date('Y-m-t', strtotime($startDate));

$salaries = fetchAll("
    SELECT u.full_name, u.job_title, u.monthly_salary,
           p.amount as paid_amount, p.payment_date,
           (SELECT COALESCE(SUM(amount), 0) FROM payments 
            WHERE staff_id = u.id AND payment_type = 'withdrawal' AND month_year = ?) as withdrawals
    FROM users u
    LEFT JOIN payments p ON u.id = p.staff_id AND p.payment_type = 'salary' AND p.month_year = ?
    WHERE u.is_staff = 1 AND u.is_active = 1
    ORDER BY u.full_name
", [$month, $month]);

$totalSalaries = array_sum(array_column($salaries, 'monthly_salary'));
$totalPaid = array_sum(array_column($salaries, 'paid_amount'));
$totalWithdrawals = array_sum(array_column($salaries, 'withdrawals'));
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش معاشات - <?php echo $month; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
        @media print { .no-print { display: none; } aside, header { display: none; } }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto">
        <div class="no-print flex gap-4 mb-4">
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg">چاپ</button>
            <a href="../index.php" class="bg-gray-600 text-white px-6 py-2 rounded-lg">بازگشت</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-8">
            <?php echo getInvoiceHeader(); ?>
            
            <h2 class="text-2xl font-bold text-center mb-6">گزارش معاشات</h2>
            <p class="text-center mb-6"><strong>دوره:</strong> <?php echo $month; ?></p>
            
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600">کل معاشات</p>
                    <p class="text-2xl font-bold text-blue-600"><?php echo formatCurrency($totalSalaries); ?></p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600">کل برداشتها</p>
                    <p class="text-2xl font-bold text-red-600"><?php echo formatCurrency($totalWithdrawals); ?></p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600">پرداخت شده</p>
                    <p class="text-2xl font-bold text-green-600"><?php echo formatCurrency($totalPaid); ?></p>
                </div>
            </div>
            
            <table class="w-full border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-3 text-right">نام کارمند</th>
                        <th class="border p-3 text-right">عنوان شغلی</th>
                        <th class="border p-3 text-right">معاش ماهانه</th>
                        <th class="border p-3 text-right">برداشتها</th>
                        <th class="border p-3 text-right">خالص</th>
                        <th class="border p-3 text-center">وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salaries as $s): 
                        $netSalary = $s['monthly_salary'] - $s['withdrawals'];
                        $isPaid = !empty($s['paid_amount']);
                    ?>
                    <tr>
                        <td class="border p-3"><?php echo htmlspecialchars($s['full_name']); ?></td>
                        <td class="border p-3"><?php echo htmlspecialchars($s['job_title']); ?></td>
                        <td class="border p-3 font-semibold"><?php echo formatCurrency($s['monthly_salary']); ?></td>
                        <td class="border p-3 text-red-600"><?php echo formatCurrency($s['withdrawals']); ?></td>
                        <td class="border p-3 font-bold text-green-600"><?php echo formatCurrency($netSalary); ?></td>
                        <td class="border p-3 text-center">
                            <span class="px-2 py-1 rounded text-xs <?php echo $isPaid ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                <?php echo $isPaid ? 'پرداخت شده' : 'در انتظار'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td colspan="2" class="border p-3 text-left">جمع کل:</td>
                        <td class="border p-3"><?php echo formatCurrency($totalSalaries); ?></td>
                        <td class="border p-3 text-red-600"><?php echo formatCurrency($totalWithdrawals); ?></td>
                        <td class="border p-3 text-green-600"><?php echo formatCurrency($totalSalaries - $totalWithdrawals); ?></td>
                        <td class="border p-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html>
