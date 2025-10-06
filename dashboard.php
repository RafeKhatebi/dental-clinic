<?php
require_once 'config/config.php';
include 'includes/header.php';

// Get today's date
$today = date('Y-m-d');

// Get dashboard statistics
$todayPatients = fetchOne("SELECT COUNT(DISTINCT patient_id) as count FROM services WHERE service_date = ? AND status != 'template'", [$today])['count'] ?? 0;

// Today's revenue
$todayRevenue = fetchOne("
    SELECT 
        SUM(CASE WHEN payment_method = 'cash' THEN amount ELSE 0 END) as cash,
        SUM(CASE WHEN payment_method = 'installment' THEN amount ELSE 0 END) as installment,
        SUM(amount) as total
    FROM payments 
    WHERE payment_date = ?
", [$today]);

$cashRevenue = $todayRevenue['cash'] ?? 0;
$installmentRevenue = $todayRevenue['installment'] ?? 0;
$totalRevenue = $todayRevenue['total'] ?? 0;

// Total debts (unpaid payments)
$totalDebts = fetchOne("
    SELECT SUM(amount - paid_amount) as total 
    FROM payments 
    WHERE status IN ('pending', 'partial') AND payment_method IN ('installment', 'loan')
")['total'] ?? 0;

// Overdue debts
$overdueDebts = fetchOne("
    SELECT SUM(amount - paid_amount) as total 
    FROM payments 
    WHERE status = 'overdue' AND due_date < ?
", [$today])['total'] ?? 0;

// Low stock medicines
$lowStockMedicines = fetchAll("
    SELECT * FROM medicines 
    WHERE stock_quantity <= min_stock_level AND is_active = 1
    ORDER BY stock_quantity ASC
    LIMIT 5
");

// Expiring medicines (within 30 days)
$expiryAlertDays = getSetting('expiry_alert_days', 30);
$expiringDate = date('Y-m-d', strtotime("+{$expiryAlertDays} days"));
$expiringMedicines = fetchAll("
    SELECT * FROM medicines 
    WHERE expiry_date <= ? AND expiry_date >= ? AND is_active = 1
    ORDER BY expiry_date ASC
    LIMIT 5
", [$expiringDate, $today]);

// Recent patients
$recentPatients = fetchAll("
    SELECT p.*, s.service_date, s.final_price
    FROM patients p
    JOIN services s ON p.id = s.patient_id
    WHERE s.service_date = ? AND s.status != 'template'
    ORDER BY s.created_at DESC
    LIMIT 5
", [$today]);

// بیماران جدید (7 روز اخیر)
$newPatientsData = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $count = fetchOne("SELECT COUNT(*) as count FROM patients WHERE DATE(created_at) = ?", [$date])['count'];
    $newPatientsData[] = ['date' => date('d/m', strtotime($date)), 'count' => $count];
}



// Revenue chart data (last 7 days)
$chartData = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $revenue = fetchOne("
        SELECT 
            SUM(CASE WHEN payment_method = 'cash' THEN amount ELSE 0 END) as cash,
            SUM(CASE WHEN payment_method = 'installment' THEN amount ELSE 0 END) as installment
        FROM payments 
        WHERE payment_date = ?
    ", [$date]);

    $chartData[] = [
        'date' => $date,
        'cash' => $revenue['cash'] ?? 0,
        'installment' => $revenue['installment'] ?? 0
    ];
}
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['dashboard']; ?></h1>
        <div class="text-sm text-gray-600">
            <?php echo date('Y-m-d H:i'); ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today's Patients -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600"><?php echo $lang['today_patients']; ?></p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $todayPatients; ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Cash Revenue -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600"><?php echo $lang['cash_revenue']; ?></p>
                    <p class="text-3xl font-bold text-green-600 mt-2"><?php echo formatCurrency($cashRevenue); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Installment Revenue -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600"><?php echo $lang['installment_revenue']; ?></p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">
                        <?php echo formatCurrency($installmentRevenue); ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Debts -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600"><?php echo $lang['total_debts']; ?></p>
                    <p class="text-3xl font-bold text-red-600 mt-2"><?php echo formatCurrency($totalDebts); ?></p>
                    <?php if ($overdueDebts > 0): ?>
                        <p class="text-xs text-red-500 mt-1"><?php echo $lang['overdue']; ?>:
                            <?php echo formatCurrency($overdueDebts); ?></p>
                    <?php endif; ?>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- بیماران جدید -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">بیماران جدید (7 روز)</h2>
            <canvas id="newPatientsChart"></canvas>
        </div>

        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4"><?php echo $lang['revenue_chart']; ?> (7 <?php echo $lang['date']; ?>)</h2>
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Alerts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Medicines -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800"><?php echo $lang['low_stock_medicines']; ?></h2>
                <a href="<?php echo BASE_URL; ?>/medicines/index.php" class="text-sm text-blue-600 hover:text-blue-800">
                    <?php echo $lang['view']; ?> →
                </a>
            </div>
            <?php if (empty($lowStockMedicines)): ?>
                <p class="text-gray-500 text-center py-8"><?php echo $lang['no_data']; ?></p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($lowStockMedicines as $medicine): ?>
                        <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($medicine['medicine_name']); ?>
                                </p>
                                <p class="text-sm text-gray-600"><?php echo $lang['medicine_code']; ?>:
                                    <?php echo $medicine['medicine_code']; ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-red-600 font-semibold"><?php echo $medicine['stock_quantity']; ?>
                                    <?php echo $medicine['unit']; ?></p>
                                <p class="text-xs text-gray-500"><?php echo $lang['min_stock_level']; ?>:
                                    <?php echo $medicine['min_stock_level']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Expiring Medicines -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800"><?php echo $lang['expiring_medicines']; ?></h2>
                <a href="<?php echo BASE_URL; ?>/medicines/index.php" class="text-sm text-blue-600 hover:text-blue-800">
                    <?php echo $lang['view']; ?> →
                </a>
            </div>
            <?php if (empty($expiringMedicines)): ?>
                <p class="text-gray-500 text-center py-8"><?php echo $lang['no_data']; ?></p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($expiringMedicines as $medicine): ?>
                        <div class="flex items-center justify-between p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($medicine['medicine_name']); ?>
                                </p>
                                <p class="text-sm text-gray-600"><?php echo $lang['medicine_code']; ?>:
                                    <?php echo $medicine['medicine_code']; ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-yellow-600 font-semibold">
                                    <?php echo formatDate($medicine['expiry_date']); ?></p>
                                <p class="text-xs text-gray-500"><?php echo $medicine['stock_quantity']; ?>
                                    <?php echo $medicine['unit']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Patients -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800"><?php echo $lang['today_patients']; ?></h2>
            <a href="<?php echo BASE_URL; ?>/patients/index.php" class="text-sm text-blue-600 hover:text-blue-800">
                <?php echo $lang['view']; ?> →
            </a>
        </div>
        <?php if (empty($recentPatients)): ?>
            <p class="text-gray-500 text-center py-8"><?php echo $lang['no_data']; ?></p>
        <?php else: ?>
            <!-- Desktop Table -->
            <div class="overflow-x-auto table-desktop">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase">
                                <?php echo $lang['patient_code']; ?></th>
                            <th
                                class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase">
                                <?php echo $lang['full_name']; ?></th>
                            <th
                                class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase">
                                <?php echo $lang['phone']; ?></th>
                            <th
                                class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase">
                                <?php echo $lang['service_date']; ?></th>
                            <th
                                class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase">
                                <?php echo $lang['amount']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($recentPatients as $patient): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo $patient['patient_code']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $patient['phone']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo formatDate($patient['service_date']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                    <?php echo formatCurrency($patient['final_price']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Cards -->
            <div class="cards-mobile space-y-3">
                <?php foreach ($recentPatients as $patient): ?>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-600">کد:</span>
                            <span class="text-xs font-semibold text-blue-600"><?php echo $patient['patient_code']; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-600">نام:</span>
                            <span class="text-xs font-semibold text-gray-900"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-600">تلفن:</span>
                            <span class="text-xs text-gray-900 dir-ltr"><?php echo $patient['phone']; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-600">مبلغ:</span>
                            <span class="text-xs font-semibold text-green-600"><?php echo formatCurrency($patient['final_price']); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // بیماران جدید
    const newPatientsData = <?php echo json_encode($newPatientsData); ?>;
    new Chart(document.getElementById('newPatientsChart'), {
        type: 'line',
        data: {
            labels: newPatientsData.map(d => d.date),
            datasets: [{
                label: 'بیماران جدید',
                data: newPatientsData.map(d => d.count),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Revenue Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chartData = <?php echo json_encode($chartData); ?>;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(d => d.date),
            datasets: [
                {
                    label: '<?php echo $lang['cash']; ?>',
                    data: chartData.map(d => d.cash),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4
                },
                {
                    label: '<?php echo $lang['installment']; ?>',
                    data: chartData.map(d => d.installment),
                    borderColor: 'rgb(234, 179, 8)',
                    backgroundColor: 'rgba(234, 179, 8, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>