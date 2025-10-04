<?php
require_once '../config/config.php';
include '../includes/header.php';

$fromDate = $_GET['from_date'] ?? date('Y-m-01');
$toDate = $_GET['to_date'] ?? date('Y-m-d');

// درآمدها
$serviceRevenue = fetchOne("SELECT COALESCE(SUM(final_price), 0) as total FROM services WHERE service_date BETWEEN ? AND ? AND status = 'completed'", [$fromDate, $toDate])['total'];
$medicineRevenue = fetchOne("SELECT COALESCE(SUM(sale_total_price), 0) as total FROM medicines WHERE sale_date BETWEEN ? AND ?", [$fromDate, $toDate])['total'];
$totalRevenue = $serviceRevenue + $medicineRevenue;

// پرداخت‌های دریافتی
$cashPayments = fetchOne("SELECT COALESCE(SUM(paid_amount), 0) as total FROM payments WHERE payment_date BETWEEN ? AND ? AND payment_method = 'cash' AND payment_type IN ('service', 'medicine')", [$fromDate, $toDate])['total'];
$installmentPayments = fetchOne("SELECT COALESCE(SUM(paid_amount), 0) as total FROM payments WHERE payment_date BETWEEN ? AND ? AND payment_method = 'installment' AND payment_type IN ('service', 'medicine')", [$fromDate, $toDate])['total'];
$totalReceived = $cashPayments + $installmentPayments;

// بدهی‌ها
$totalDebts = fetchOne("SELECT COALESCE(SUM(amount - paid_amount), 0) as total FROM payments WHERE status IN ('pending', 'partial') AND payment_method IN ('installment', 'loan') AND payment_type IN ('service', 'medicine')")['total'];

// هزینه‌ها
$salariesPaid = fetchOne("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE payment_date BETWEEN ? AND ? AND payment_type = 'salary'", [$fromDate, $toDate])['total'];
$expensesPaid = fetchOne("SELECT COALESCE(SUM(amount), 0) as total FROM documents WHERE document_type = 'expense' AND next_due_date BETWEEN ? AND ? AND status = 'active'", [$fromDate, $toDate])['total'];
$medicinePurchases = fetchOne("SELECT COALESCE(SUM(purchase_price * stock_quantity), 0) as total FROM medicines WHERE movement_type = 'purchase' AND movement_date BETWEEN ? AND ?", [$fromDate, $toDate])['total'];
$totalExpenses = $salariesPaid + $expensesPaid + $medicinePurchases;

// سود خالص
$netProfit = $totalReceived - $totalExpenses;

// سهم شرکا
$partners = fetchAll("SELECT partner_name, share_percentage FROM documents WHERE document_type = 'partner_share' AND status = 'active' GROUP BY partner_name, share_percentage");
$partnerShares = [];
foreach ($partners as $partner) {
    $partnerShares[] = [
        'name' => $partner['partner_name'],
        'percentage' => $partner['share_percentage'],
        'amount' => ($netProfit * $partner['share_percentage']) / 100
    ];
}

// آمار تفصیلی
$patientCount = fetchOne("SELECT COUNT(DISTINCT patient_id) as count FROM services WHERE service_date BETWEEN ? AND ? AND status = 'completed'", [$fromDate, $toDate])['count'];
$serviceCount = fetchOne("SELECT COUNT(*) as count FROM services WHERE service_date BETWEEN ? AND ? AND status = 'completed'", [$fromDate, $toDate])['count'];
$medicineSales = fetchOne("SELECT COUNT(*) as count FROM medicines WHERE sale_date BETWEEN ? AND ?", [$fromDate, $toDate])['count'];
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">گزارش مالی جامع</h1>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
            <?php echo $lang['print']; ?>
        </button>
    </div>

    <!-- فیلتر تاریخ -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['from_date']; ?></label>
                <input type="date" name="from_date" value="<?php echo $fromDate; ?>" class="px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['to_date']; ?></label>
                <input type="date" name="to_date" value="<?php echo $toDate; ?>" class="px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"><?php echo $lang['generate_report']; ?></button>
        </form>
    </div>

    <!-- خلاصه مالی -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <p class="text-sm opacity-90">کل درآمد</p>
            <p class="text-3xl font-bold mt-2"><?php echo formatCurrency($totalRevenue); ?></p>
            <p class="text-xs mt-1 opacity-75">افغانی</p>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <p class="text-sm opacity-90">دریافتی نقدی</p>
            <p class="text-3xl font-bold mt-2"><?php echo formatCurrency($totalReceived); ?></p>
            <p class="text-xs mt-1 opacity-75">افغانی</p>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
            <p class="text-sm opacity-90">کل هزینه‌ها</p>
            <p class="text-3xl font-bold mt-2"><?php echo formatCurrency($totalExpenses); ?></p>
            <p class="text-xs mt-1 opacity-75">افغانی</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <p class="text-sm opacity-90">سود خالص</p>
            <p class="text-3xl font-bold mt-2"><?php echo formatCurrency($netProfit); ?></p>
            <p class="text-xs mt-1 opacity-75">افغانی</p>
        </div>
    </div>

    <!-- جزئیات درآمد -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">تفکیک درآمدها</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="border-r-4 border-green-500 bg-green-50 p-4 rounded">
                <p class="text-sm text-gray-600">درآمد خدمات درمانی</p>
                <p class="text-2xl font-bold text-green-600 mt-2"><?php echo formatCurrency($serviceRevenue); ?></p>
                <p class="text-xs text-gray-500 mt-1"><?php echo number_format(($serviceRevenue/$totalRevenue)*100, 1); ?>% از کل</p>
            </div>
            <div class="border-r-4 border-blue-500 bg-blue-50 p-4 rounded">
                <p class="text-sm text-gray-600">درآمد فروش دارو</p>
                <p class="text-2xl font-bold text-blue-600 mt-2"><?php echo formatCurrency($medicineRevenue); ?></p>
                <p class="text-xs text-gray-500 mt-1"><?php echo number_format(($medicineRevenue/$totalRevenue)*100, 1); ?>% از کل</p>
            </div>
            <div class="border-r-4 border-yellow-500 bg-yellow-50 p-4 rounded">
                <p class="text-sm text-gray-600">بدهی‌های باقیمانده</p>
                <p class="text-2xl font-bold text-yellow-600 mt-2"><?php echo formatCurrency($totalDebts); ?></p>
                <p class="text-xs text-gray-500 mt-1">قابل وصول</p>
            </div>
        </div>
    </div>

    <!-- جزئیات هزینه -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">تفکیک هزینه‌ها</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="border-r-4 border-red-500 bg-red-50 p-4 rounded">
                <p class="text-sm text-gray-600">معاشات پرداختی</p>
                <p class="text-2xl font-bold text-red-600 mt-2"><?php echo formatCurrency($salariesPaid); ?></p>
                <p class="text-xs text-gray-500 mt-1"><?php echo number_format(($salariesPaid/$totalExpenses)*100, 1); ?>% از کل</p>
            </div>
            <div class="border-r-4 border-orange-500 bg-orange-50 p-4 rounded">
                <p class="text-sm text-gray-600">مصارف ثابت</p>
                <p class="text-2xl font-bold text-orange-600 mt-2"><?php echo formatCurrency($expensesPaid); ?></p>
                <p class="text-xs text-gray-500 mt-1"><?php echo number_format(($expensesPaid/$totalExpenses)*100, 1); ?>% از کل</p>
            </div>
            <div class="border-r-4 border-pink-500 bg-pink-50 p-4 rounded">
                <p class="text-sm text-gray-600">خرید دارو</p>
                <p class="text-2xl font-bold text-pink-600 mt-2"><?php echo formatCurrency($medicinePurchases); ?></p>
                <p class="text-xs text-gray-500 mt-1"><?php echo number_format(($medicinePurchases/$totalExpenses)*100, 1); ?>% از کل</p>
            </div>
        </div>
    </div>

    <!-- سهم شرکا -->
    <?php if (!empty($partnerShares)): ?>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">تقسیم سود بین شرکا</h2>
        <div class="space-y-3">
            <?php foreach ($partnerShares as $share): ?>
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-200">
                <div>
                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($share['name']); ?></p>
                    <p class="text-sm text-gray-600">سهم: <?php echo $share['percentage']; ?>%</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-indigo-600"><?php echo formatCurrency($share['amount']); ?></p>
                    <p class="text-xs text-gray-500">افغانی</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- آمار عملیاتی -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">آمار عملیاتی</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-4xl font-bold text-blue-600"><?php echo $patientCount; ?></p>
                <p class="text-sm text-gray-600 mt-2">تعداد بیماران</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-4xl font-bold text-green-600"><?php echo $serviceCount; ?></p>
                <p class="text-sm text-gray-600 mt-2">خدمات ارائه شده</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-4xl font-bold text-purple-600"><?php echo $medicineSales; ?></p>
                <p class="text-sm text-gray-600 mt-2">فروش دارو</p>
            </div>
        </div>
    </div>

    <!-- نتیجه نهایی -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-lg shadow-xl p-8 text-white">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">خلاصه دوره مالی</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span>از تاریخ:</span><span class="font-semibold"><?php echo $fromDate; ?></span></div>
                    <div class="flex justify-between"><span>تا تاریخ:</span><span class="font-semibold"><?php echo $toDate; ?></span></div>
                    <div class="flex justify-between"><span>مدت:</span><span class="font-semibold"><?php echo ceil((strtotime($toDate) - strtotime($fromDate)) / 86400); ?> روز</span></div>
                </div>
            </div>
            <div class="border-r border-gray-700 pr-8">
                <h3 class="text-lg font-semibold mb-4">نتیجه مالی</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-lg"><span>کل دریافتی:</span><span class="font-bold text-green-400"><?php echo formatCurrency($totalReceived); ?></span></div>
                    <div class="flex justify-between text-lg"><span>کل هزینه:</span><span class="font-bold text-red-400"><?php echo formatCurrency($totalExpenses); ?></span></div>
                    <div class="border-t border-gray-700 pt-3 flex justify-between text-2xl"><span>سود خالص:</span><span class="font-bold <?php echo $netProfit >= 0 ? 'text-green-400' : 'text-red-400'; ?>"><?php echo formatCurrency($netProfit); ?></span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    aside, header, .no-print { display: none !important; }
    body { background: white; }
    main { padding: 0 !important; }
    .flex.h-screen { display: block !important; }
    .flex-1.flex.flex-col { width: 100% !important; }
}
</style>

<?php include '../includes/footer.php'; ?>
