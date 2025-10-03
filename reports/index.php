<?php
require_once '../config/config.php';
include '../includes/header.php';

// Get date range from request
$fromDate = $_GET['from_date'] ?? date('Y-m-01');
$toDate = $_GET['to_date'] ?? date('Y-m-d');

// Financial Summary
$financialData = fetchOne("
    SELECT 
        SUM(CASE WHEN payment_method = 'cash' THEN amount ELSE 0 END) as cash_revenue,
        SUM(CASE WHEN payment_method = 'installment' THEN amount ELSE 0 END) as installment_revenue,
        SUM(CASE WHEN payment_method = 'loan' THEN amount ELSE 0 END) as loan_revenue,
        SUM(amount) as total_revenue
    FROM payments 
    WHERE payment_date BETWEEN ? AND ?
", [$fromDate, $toDate]);

// Service Revenue
$serviceRevenue = fetchOne("
    SELECT SUM(final_price) as total
    FROM services
    WHERE service_date BETWEEN ? AND ? AND status != 'template' AND patient_id IS NOT NULL
", [$fromDate, $toDate])['total'] ?? 0;

// Medicine Revenue
$medicineRevenue = fetchOne("
    SELECT SUM(sale_total_price) as total
    FROM medicines
    WHERE sale_date BETWEEN ? AND ? AND sale_date IS NOT NULL
", [$fromDate, $toDate])['total'] ?? 0;

// Patient Statistics
$patientStats = fetchOne("
    SELECT 
        COUNT(DISTINCT patient_id) as total_patients,
        COUNT(*) as total_services
    FROM services
    WHERE service_date BETWEEN ? AND ? AND status != 'template' AND patient_id IS NOT NULL
", [$fromDate, $toDate]);

// Top Services
$topServices = fetchAll("
    SELECT service_name, COUNT(*) as count, SUM(final_price) as revenue
    FROM services
    WHERE service_date BETWEEN ? AND ? AND status != 'template' AND patient_id IS NOT NULL
    GROUP BY service_name
    ORDER BY revenue DESC
    LIMIT 5
", [$fromDate, $toDate]);

// Debt Summary
$debtSummary = fetchOne("
    SELECT 
        SUM(amount - paid_amount) as total_debt,
        SUM(CASE WHEN status = 'overdue' THEN amount - paid_amount ELSE 0 END) as overdue_debt,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count
    FROM payments
    WHERE payment_method IN ('installment', 'loan')
");
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['reports']; ?></h1>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo $lang['from_date']; ?>
                </label>
                <input type="date" name="from_date" value="<?php echo $fromDate; ?>" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo $lang['to_date']; ?>
                </label>
                <input type="date" name="to_date" value="<?php echo $toDate; ?>" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                <?php echo $lang['generate_report']; ?>
            </button>
        </form>
    </div>

    <!-- Financial Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-sm p-6 text-white">
            <p class="text-sm opacity-90 mb-2"><?php echo $lang['total_revenue']; ?></p>
            <p class="text-3xl font-bold"><?php echo formatCurrency($financialData['total_revenue'] ?? 0); ?></p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-sm p-6 text-white">
            <p class="text-sm opacity-90 mb-2"><?php echo $lang['cash_revenue']; ?></p>
            <p class="text-3xl font-bold"><?php echo formatCurrency($financialData['cash_revenue'] ?? 0); ?></p>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-sm p-6 text-white">
            <p class="text-sm opacity-90 mb-2"><?php echo $lang['installment_revenue']; ?></p>
            <p class="text-3xl font-bold"><?php echo formatCurrency($financialData['installment_revenue'] ?? 0); ?></p>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-sm p-6 text-white">
            <p class="text-sm opacity-90 mb-2"><?php echo $lang['total_debts']; ?></p>
            <p class="text-3xl font-bold"><?php echo formatCurrency($debtSummary['total_debt'] ?? 0); ?></p>
        </div>
    </div>

    <!-- Revenue Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4"><?php echo $lang['service_revenue']; ?></h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-gray-700"><?php echo $lang['services']; ?></span>
                    <span class="text-xl font-bold text-blue-600"><?php echo formatCurrency($serviceRevenue); ?></span>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-gray-700"><?php echo $lang['medicines']; ?></span>
                    <span class="text-xl font-bold text-green-600"><?php echo formatCurrency($medicineRevenue); ?></span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-700 font-semibold"><?php echo $lang['total']; ?></span>
                    <span class="text-xl font-bold text-gray-800"><?php echo formatCurrency($serviceRevenue + $medicineRevenue); ?></span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4"><?php echo $lang['patient_report']; ?></h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                    <span class="text-gray-700"><?php echo $lang['patients']; ?></span>
                    <span class="text-xl font-bold text-purple-600"><?php echo $patientStats['total_patients'] ?? 0; ?></span>
                </div>
                <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg">
                    <span class="text-gray-700"><?php echo $lang['services']; ?></span>
                    <span class="text-xl font-bold text-indigo-600"><?php echo $patientStats['total_services'] ?? 0; ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Services -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4"><?php echo $lang['services']; ?> (Top 5)</h2>
        <?php if (empty($topServices)): ?>
            <p class="text-gray-500 text-center py-8"><?php echo $lang['no_data']; ?></p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['service_name']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['quantity']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['total_revenue']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($topServices as $service): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($service['service_name']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo $service['count']; ?></td>
                            <td class="px-6 py-4 text-sm font-semibold text-green-600"><?php echo formatCurrency($service['revenue']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Debt Summary -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4"><?php echo $lang['debt_report']; ?></h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600 mb-1"><?php echo $lang['total_debts']; ?></p>
                <p class="text-2xl font-bold text-red-700"><?php echo formatCurrency($debtSummary['total_debt'] ?? 0); ?></p>
            </div>
            <div class="p-4 bg-orange-50 border border-orange-200 rounded-lg">
                <p class="text-sm text-orange-600 mb-1"><?php echo $lang['overdue']; ?></p>
                <p class="text-2xl font-bold text-orange-700"><?php echo formatCurrency($debtSummary['overdue_debt'] ?? 0); ?></p>
            </div>
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-600 mb-1"><?php echo $lang['pending']; ?> <?php echo $lang['installments']; ?></p>
                <p class="text-2xl font-bold text-yellow-700"><?php echo $debtSummary['pending_count'] ?? 0; ?></p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
