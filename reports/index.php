<?php
require_once '../config/config.php';
include '../includes/header.php';

$fromDate = $_GET['from_date'] ?? date('Y-m-01');
$toDate = $_GET['to_date'] ?? date('Y-m-d');

$financialData = fetchOne("
    SELECT 
        SUM(CASE WHEN payment_method = 'cash' THEN amount ELSE 0 END) as cash_revenue,
        SUM(CASE WHEN payment_method = 'installment' THEN amount ELSE 0 END) as installment_revenue,
        SUM(amount) as total_revenue
    FROM payments 
    WHERE payment_date BETWEEN ? AND ?
", [$fromDate, $toDate]);

$serviceRevenue = fetchOne("SELECT SUM(final_price) as total FROM services WHERE service_date BETWEEN ? AND ? AND status != 'template' AND patient_id IS NOT NULL", [$fromDate, $toDate])['total'] ?? 0;

$patientStats = fetchOne("SELECT COUNT(DISTINCT patient_id) as total_patients, COUNT(*) as total_services FROM services WHERE service_date BETWEEN ? AND ? AND status != 'template' AND patient_id IS NOT NULL", [$fromDate, $toDate]);

$topServices = fetchAll("SELECT service_name, COUNT(*) as count, SUM(final_price) as revenue FROM services WHERE service_date BETWEEN ? AND ? AND status != 'template' AND patient_id IS NOT NULL GROUP BY service_name ORDER BY revenue DESC LIMIT 5", [$fromDate, $toDate]);

$debtSummary = fetchOne("SELECT SUM(amount - paid_amount) as total_debt, SUM(CASE WHEN status = 'overdue' THEN amount - paid_amount ELSE 0 END) as overdue_debt FROM payments WHERE payment_method IN ('installment', 'loan')");

$lowStock = fetchAll("SELECT * FROM medicines WHERE is_active = 1 AND stock_quantity <= min_stock_level ORDER BY stock_quantity ASC LIMIT 5");

$expiringSoon = fetchAll("SELECT * FROM medicines WHERE is_active = 1 AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) ORDER BY expiry_date ASC LIMIT 5");

// Activity Log
$filter = $_GET['filter'] ?? 'all';
$where = "record_type = 'activity_log'";
if ($filter !== 'all') $where .= " AND action = '$filter'";
$totalRecords = fetchOne("SELECT COUNT(*) as count FROM system WHERE $where")['count'];
$pagination = getPagination($totalRecords, 50);
$logs = fetchAll("SELECT s.*, u.full_name, u.username FROM system s LEFT JOIN users u ON s.user_id = u.id WHERE $where ORDER BY s.created_at DESC LIMIT {$pagination['perPage']} OFFSET {$pagination['offset']}");
?>

<div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['reports']; ?></h1>

    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['from_date']; ?></label>
                <input type="date" name="from_date" value="<?php echo $fromDate; ?>" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['to_date']; ?></label>
                <input type="date" name="to_date" value="<?php echo $toDate; ?>" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"><?php echo $lang['generate_report']; ?></button>
        </form>
    </div>

    <!-- Summary Cards -->
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

    <!-- Revenue & Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4"><?php echo $lang['service_revenue']; ?></h2>
            <div class="space-y-3">
                <div class="flex justify-between p-3 bg-blue-50 rounded-lg">
                    <span><?php echo $lang['services']; ?></span>
                    <span class="text-xl font-bold text-blue-600"><?php echo formatCurrency($serviceRevenue); ?></span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4"><?php echo $lang['patient_report']; ?></h2>
            <div class="space-y-3">
                <div class="flex justify-between p-3 bg-purple-50 rounded-lg">
                    <span><?php echo $lang['patients']; ?></span>
                    <span class="text-xl font-bold text-purple-600"><?php echo $patientStats['total_patients'] ?? 0; ?></span>
                </div>
                <div class="flex justify-between p-3 bg-indigo-50 rounded-lg">
                    <span><?php echo $lang['services']; ?></span>
                    <span class="text-xl font-bold text-indigo-600"><?php echo $patientStats['total_services'] ?? 0; ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Services -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold mb-4"><?php echo $lang['services']; ?> (Top 5)</h2>
        <?php if ($topServices): ?>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['service_name']; ?></th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['quantity']; ?></th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['total_revenue']; ?></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($topServices as $s): ?>
                <tr>
                    <td class="px-6 py-4 text-sm"><?php echo htmlspecialchars($s['service_name']); ?></td>
                    <td class="px-6 py-4 text-sm"><?php echo $s['count']; ?></td>
                    <td class="px-6 py-4 text-sm font-semibold text-green-600"><?php echo formatCurrency($s['revenue']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Quick Reports -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold mb-4">گزارشات سریع</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- روزانه -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">📊</span>
                    <h3 class="font-bold">روزانه</h3>
                </div>
                <form action="financial/daily_report.php" method="GET" target="_blank" class="space-y-2">
                    <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" class="w-full px-3 py-2 border rounded text-sm">
                    <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-sm">مشاهده PDF</button>
                    <a href="financial/export_daily_excel.php?date=<?php echo date('Y-m-d'); ?>" class="block w-full text-center bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 text-sm">دانلود Excel</a>
                </form>
            </div>

            <!-- ماهانه -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">📈</span>
                    <h3 class="font-bold">ماهانه</h3>
                </div>
                <form action="financial/monthly_report.php" method="GET" target="_blank" class="space-y-2">
                    <input type="month" name="month" value="<?php echo date('Y-m'); ?>" class="w-full px-3 py-2 border rounded text-sm">
                    <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-sm">مشاهده PDF</button>
                </form>
            </div>

            <!-- سالانه -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">📉</span>
                    <h3 class="font-bold">سالانه</h3>
                </div>
                <form action="financial/yearly_report.php" method="GET" target="_blank" class="space-y-2">
                    <input type="number" name="year" value="<?php echo date('Y'); ?>" min="2020" max="2030" class="w-full px-3 py-2 border rounded text-sm">
                    <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-sm">مشاهده PDF</button>
                </form>
            </div>

            <!-- بدهیها -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">💰</span>
                    <h3 class="font-bold">بدهیها</h3>
                </div>
                <a href="financial/debts_report.php" target="_blank" class="block w-full text-center bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 text-sm">مشاهده PDF</a>
                <a href="financial/export_debts_excel.php" class="block w-full text-center bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 text-sm mt-2">دانلود Excel</a>
            </div>

            <!-- اقساط -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">📅</span>
                    <h3 class="font-bold">اقساط</h3>
                </div>
                <div class="space-y-2">
                    <a href="financial/installments_report.php?filter=today" target="_blank" class="block w-full text-center bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-sm">امروز</a>
                    <a href="financial/installments_report.php?filter=week" target="_blank" class="block w-full text-center bg-yellow-600 text-white px-3 py-2 rounded hover:bg-yellow-700 text-sm">هفته</a>
                    <a href="financial/installments_report.php?filter=overdue" target="_blank" class="block w-full text-center bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 text-sm">معوق</a>
                </div>
            </div>

            <!-- شرکا -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">🤝</span>
                    <h3 class="font-bold">شرکا</h3>
                </div>
                <form action="financial/partners_report.php" method="GET" target="_blank" class="space-y-2">
                    <input type="date" name="start_date" value="<?php echo date('Y-m-01'); ?>" class="w-full px-3 py-2 border rounded text-sm" placeholder="از">
                    <input type="date" name="end_date" value="<?php echo date('Y-m-d'); ?>" class="w-full px-3 py-2 border rounded text-sm" placeholder="تا">
                    <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-sm">مشاهده PDF</button>
                </form>
            </div>

            <!-- معاشات -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">💵</span>
                    <h3 class="font-bold">معاشات</h3>
                </div>
                <form action="financial/salaries_report.php" method="GET" target="_blank" class="space-y-2">
                    <input type="month" name="month" value="<?php echo date('Y-m'); ?>" class="w-full px-3 py-2 border rounded text-sm">
                    <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-sm">مشاهده PDF</button>
                </form>
            </div>

            <!-- مصارف -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">💸</span>
                    <h3 class="font-bold">مصارف</h3>
                </div>
                <form action="financial/expenses_report.php" method="GET" target="_blank" class="space-y-2">
                    <input type="date" name="start_date" value="<?php echo date('Y-m-01'); ?>" class="w-full px-3 py-2 border rounded text-sm">
                    <input type="date" name="end_date" value="<?php echo date('Y-m-d'); ?>" class="w-full px-3 py-2 border rounded text-sm">
                    <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-sm">مشاهده PDF</button>
                </form>
            </div>

            <!-- سود/زیان -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">📊</span>
                    <h3 class="font-bold">سود/زیان</h3>
                </div>
                <form action="financial/profit_loss.php" method="GET" target="_blank" class="space-y-2">
                    <input type="date" name="start_date" value="<?php echo date('Y-m-01'); ?>" class="w-full px-3 py-2 border rounded text-sm">
                    <input type="date" name="end_date" value="<?php echo date('Y-m-d'); ?>" class="w-full px-3 py-2 border rounded text-sm">
                    <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-sm">مشاهده PDF</button>
                </form>
            </div>

            <!-- موجودی دارو -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">💊</span>
                    <h3 class="font-bold">موجودی</h3>
                </div>
                <a href="../medicines/stock.php" class="block w-full text-center bg-purple-600 text-white px-3 py-2 rounded hover:bg-purple-700 text-sm">مشاهده گزارش</a>
            </div>

            <!-- تحلیل پیشرفته -->
            <div class="border rounded-lg p-4 bg-gradient-to-br from-purple-50 to-blue-50">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">📊</span>
                    <h3 class="font-bold text-purple-700">تحلیل پیشرفته</h3>
                </div>
                <a href="advanced_analytics.php" class="block w-full text-center bg-gradient-to-r from-purple-600 to-blue-600 text-white px-3 py-2 rounded hover:from-purple-700 hover:to-blue-700 text-sm font-semibold">مشاهده تحلیلها</a>
            </div>

            <!-- گزارش مقایسهای -->
            <div class="border rounded-lg p-4 bg-gradient-to-br from-green-50 to-teal-50">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">📈</span>
                    <h3 class="font-bold text-green-700">مقایسه دورهها</h3>
                </div>
                <a href="comparison_report.php" class="block w-full text-center bg-gradient-to-r from-green-600 to-teal-600 text-white px-3 py-2 rounded hover:from-green-700 hover:to-teal-700 text-sm font-semibold">مقایسه کن</a>
            </div>

        </div>
    </div>

    <!-- هشدارهای داروخانه -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- داروهای با موجودی کم -->
        <?php if ($lowStock): ?>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-red-600">⚠️ داروهای با موجودی کم</h2>
                <div class="flex gap-2">
                    <a href="financial/stock_report.php?type=low" target="_blank" class="text-red-600 hover:text-red-800" title="چاپ PDF">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </a>
                    <a href="../medicines/stock.php" class="text-sm text-blue-600 hover:underline">مشاهده همه »</a>
                </div>
            </div>
            <div class="space-y-2">
                <?php foreach ($lowStock as $m): ?>
                <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                    <div>
                        <div class="font-medium text-gray-900"><?php echo htmlspecialchars($m['medicine_name']); ?></div>
                        <div class="text-sm text-gray-600">موجودی: <span class="text-red-600 font-bold"><?php echo $m['stock_quantity']; ?></span> / حداقل: <?php echo $m['min_stock_level']; ?></div>
                    </div>
                    <div class="text-sm text-gray-600"><?php echo formatCurrency($m['purchase_price']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- داروهای رو به انقضا -->
        <?php if ($expiringSoon): ?>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-yellow-600">⏰ داروهای رو به انقضا</h2>
                <div class="flex gap-2">
                    <a href="financial/stock_report.php?type=expiring" target="_blank" class="text-yellow-600 hover:text-yellow-800" title="چاپ PDF">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </a>
                    <a href="../medicines/stock.php" class="text-sm text-blue-600 hover:underline">مشاهده همه »</a>
                </div>
            </div>
            <div class="space-y-2">
                <?php foreach ($expiringSoon as $m): 
                    $daysLeft = floor((strtotime($m['expiry_date']) - time()) / 86400);
                ?>
                <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                    <div>
                        <div class="font-medium text-gray-900"><?php echo htmlspecialchars($m['medicine_name']); ?></div>
                        <div class="text-sm text-gray-600">موجودی: <?php echo $m['stock_quantity']; ?> | انقضا: <?php echo $m['expiry_date']; ?></div>
                    </div>
                    <div class="text-sm <?php echo $daysLeft < 7 ? 'text-red-600 font-bold' : 'text-yellow-600'; ?>"><?php echo $daysLeft; ?> روز</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Activity Log Section -->
    <?php if (hasRole('admin')): ?>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold"><?php echo $current_lang === 'fa' ? 'گزارش فعالیتها' : 'Activity Log'; ?></h2>
            <select onchange="location.href='?filter='+this.value" class="px-4 py-2 border rounded-lg text-sm">
                <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>><?php echo $current_lang === 'fa' ? 'همه' : 'All'; ?></option>
                <option value="login" <?php echo $filter === 'login' ? 'selected' : ''; ?>><?php echo $current_lang === 'fa' ? 'ورود' : 'Login'; ?></option>
                <option value="create" <?php echo $filter === 'create' ? 'selected' : ''; ?>><?php echo $current_lang === 'fa' ? 'ایجاد' : 'Create'; ?></option>
                <option value="update" <?php echo $filter === 'update' ? 'selected' : ''; ?>><?php echo $current_lang === 'fa' ? 'ویرایش' : 'Update'; ?></option>
                <option value="delete" <?php echo $filter === 'delete' ? 'selected' : ''; ?>><?php echo $current_lang === 'fa' ? 'حذف' : 'Delete'; ?></option>
            </select>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500"><?php echo $current_lang === 'fa' ? 'کاربر' : 'User'; ?></th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500"><?php echo $current_lang === 'fa' ? 'عملیات' : 'Action'; ?></th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500"><?php echo $current_lang === 'fa' ? 'جدول' : 'Table'; ?></th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500"><?php echo $current_lang === 'fa' ? 'توضیحات' : 'Description'; ?></th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500"><?php echo $current_lang === 'fa' ? 'IP' : 'IP'; ?></th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500"><?php echo $current_lang === 'fa' ? 'زمان' : 'Time'; ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($logs as $log): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm"><?php echo htmlspecialchars($log['full_name'] ?? 'N/A'); ?></td>
                        <td class="px-6 py-4 text-sm"><span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800"><?php echo $log['action']; ?></span></td>
                        <td class="px-6 py-4 text-sm"><?php echo $log['table_name'] ?? '-'; ?></td>
                        <td class="px-6 py-4 text-sm"><?php echo htmlspecialchars($log['description'] ?? '-'); ?></td>
                        <td class="px-6 py-4 text-sm"><?php echo $log['ip_address']; ?></td>
                        <td class="px-6 py-4 text-sm"><?php echo formatDate($log['created_at'], 'Y-m-d H:i'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php echo renderPagination($pagination); ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
