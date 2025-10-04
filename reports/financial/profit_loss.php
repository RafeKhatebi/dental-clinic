<?php
require_once '../../config/config.php';

$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-d');

// درآمدها
$serviceIncome = fetchOne("SELECT COALESCE(SUM(final_price), 0) as total FROM services WHERE service_date BETWEEN ? AND ? AND status = 'completed'", [$startDate, $endDate])['total'];
$medicineIncome = fetchOne("SELECT COALESCE(SUM(sale_total_price), 0) as total FROM medicines WHERE sale_date BETWEEN ? AND ?", [$startDate, $endDate])['total'];
$totalIncome = $serviceIncome + $medicineIncome;

// دریافتی واقعی
$cashReceived = fetchOne("SELECT COALESCE(SUM(paid_amount), 0) as total FROM payments WHERE payment_date BETWEEN ? AND ? AND payment_type IN ('service', 'medicine')", [$startDate, $endDate])['total'];

// هزینهها
$salaries = fetchOne("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE payment_date BETWEEN ? AND ? AND payment_type = 'salary'", [$startDate, $endDate])['total'];
$expenses = fetchOne("SELECT COALESCE(SUM(amount), 0) as total FROM documents WHERE document_type = 'expense' AND next_due_date BETWEEN ? AND ?", [$startDate, $endDate])['total'];
$medicinePurchases = fetchOne("SELECT COALESCE(SUM(purchase_price * movement_quantity), 0) as total FROM medicines WHERE movement_type = 'purchase' AND movement_date BETWEEN ? AND ?", [$startDate, $endDate])['total'];
$totalExpenses = $salaries + $expenses + $medicinePurchases;

// سود/زیان
$grossProfit = $totalIncome - $totalExpenses;
$netProfit = $cashReceived - $totalExpenses;
$profitMargin = $totalIncome > 0 ? ($grossProfit / $totalIncome) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش سود و زیان</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
        @media print { .no-print { display: none; } aside, header { display: none; } }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto">
        <div class="no-print flex gap-4 mb-4">
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg">چاپ</button>
            <a href="../index.php" class="bg-gray-600 text-white px-6 py-2 rounded-lg">بازگشت</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-8">
            <?php echo getInvoiceHeader(); ?>
            
            <h2 class="text-2xl font-bold text-center mb-6">گزارش سود و زیان</h2>
            <p class="text-center mb-6"><strong>از:</strong> <?php echo $startDate; ?> <strong>تا:</strong> <?php echo $endDate; ?></p>
            
            <!-- درآمدها -->
            <div class="mb-6">
                <h3 class="text-xl font-bold mb-3 text-green-700">درآمدها</h3>
                <table class="w-full">
                    <tr class="border-b">
                        <td class="py-2">درآمد خدمات درمانی</td>
                        <td class="py-2 text-left font-semibold"><?php echo formatCurrency($serviceIncome); ?></td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2">درآمد فروش دارو</td>
                        <td class="py-2 text-left font-semibold"><?php echo formatCurrency($medicineIncome); ?></td>
                    </tr>
                    <tr class="bg-green-50 font-bold">
                        <td class="py-3">کل درآمد</td>
                        <td class="py-3 text-left text-green-700 text-xl"><?php echo formatCurrency($totalIncome); ?></td>
                    </tr>
                </table>
            </div>
            
            <!-- هزینهها -->
            <div class="mb-6">
                <h3 class="text-xl font-bold mb-3 text-red-700">هزینهها</h3>
                <table class="w-full">
                    <tr class="border-b">
                        <td class="py-2">معاشات پرداختی</td>
                        <td class="py-2 text-left font-semibold"><?php echo formatCurrency($salaries); ?></td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2">مصارف ثابت</td>
                        <td class="py-2 text-left font-semibold"><?php echo formatCurrency($expenses); ?></td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2">خرید دارو</td>
                        <td class="py-2 text-left font-semibold"><?php echo formatCurrency($medicinePurchases); ?></td>
                    </tr>
                    <tr class="bg-red-50 font-bold">
                        <td class="py-3">کل هزینه</td>
                        <td class="py-3 text-left text-red-700 text-xl"><?php echo formatCurrency($totalExpenses); ?></td>
                    </tr>
                </table>
            </div>
            
            <!-- نتیجه -->
            <div class="border-t-4 border-gray-300 pt-6">
                <table class="w-full text-lg">
                    <tr class="border-b">
                        <td class="py-3 font-semibold">سود ناخالص (بر اساس فاکتور)</td>
                        <td class="py-3 text-left font-bold <?php echo $grossProfit >= 0 ? 'text-green-600' : 'text-red-600'; ?>"><?php echo formatCurrency($grossProfit); ?></td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">دریافتی واقعی</td>
                        <td class="py-3 text-left font-bold text-blue-600"><?php echo formatCurrency($cashReceived); ?></td>
                    </tr>
                    <tr class="bg-gray-100">
                        <td class="py-4 font-bold text-xl">سود خالص (واقعی)</td>
                        <td class="py-4 text-left font-bold text-2xl <?php echo $netProfit >= 0 ? 'text-green-600' : 'text-red-600'; ?>"><?php echo formatCurrency($netProfit); ?></td>
                    </tr>
                    <tr>
                        <td class="py-3 font-semibold">حاشیه سود</td>
                        <td class="py-3 text-left font-bold text-purple-600"><?php echo number_format($profitMargin, 2); ?>%</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
