<?php
require_once '../../config/config.php';

$date = $_GET['date'] ?? date('Y-m-d');

$cashIncome = fetchOne("SELECT SUM(amount) as total FROM payments WHERE payment_date = ? AND payment_method = 'cash'", [$date])['total'] ?? 0;
$installmentIncome = fetchOne("SELECT SUM(paid_amount) as total FROM payments WHERE paid_date = ? AND payment_method = 'installment'", [$date])['total'] ?? 0;
$patientsCount = fetchOne("SELECT COUNT(DISTINCT patient_id) as total FROM services WHERE service_date = ?", [$date])['total'] ?? 0;

$services = fetchAll("
    SELECT s.service_name, COUNT(*) as count, SUM(s.final_price) as total
    FROM services s
    WHERE s.service_date = ? AND s.status = 'completed'
    GROUP BY s.service_name
", [$date]);

$totalIncome = $cashIncome + $installmentIncome;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گزارش روزانه</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto">
        <div class="no-print flex gap-4 mb-4">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                چاپ
            </button>
            <a href="../index.php" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                بازگشت
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-center mb-6">گزارش روزانه صندوق</h1>
            <p class="text-center mb-6"><strong>تاریخ:</strong> <?php echo $date; ?></p>
            
            <div class="border-b pb-4 mb-4">
                <h2 class="text-xl font-bold mb-3">خلاصه مالی</h2>
                <p>دریافتی نقدی: <?php echo number_format($cashIncome); ?> افغانی</p>
                <p>دریافتی اقساط: <?php echo number_format($installmentIncome); ?> افغانی</p>
                <p class="text-xl font-bold mt-2">جمع کل: <?php echo number_format($totalIncome); ?> افغانی</p>
            </div>
            
            <div class="border-b pb-4 mb-4">
                <h2 class="text-xl font-bold mb-3">آمار</h2>
                <p>تعداد بیماران: <?php echo $patientsCount; ?></p>
                <p>تعداد خدمات: <?php echo count($services); ?></p>
            </div>
            
            <?php if ($services): ?>
            <div>
                <h2 class="text-xl font-bold mb-3">خدمات ارائه شده</h2>
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-right">خدمت</th>
                            <th class="p-2 text-right">تعداد</th>
                            <th class="p-2 text-right">مبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $s): ?>
                        <tr class="border-b">
                            <td class="p-2"><?php echo $s['service_name']; ?></td>
                            <td class="p-2"><?php echo $s['count']; ?></td>
                            <td class="p-2"><?php echo number_format($s['total']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
