<?php
require_once '../config/config.php';
include '../includes/header.php';

// Date filters
$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo = $_GET['date_to'] ?? date('Y-m-d');
$reportType = $_GET['type'] ?? 'overview';

// Revenue by payment method
$revenueByMethod = fetchAll("
    SELECT 
        payment_method,
        COUNT(*) as count,
        SUM(amount) as total
    FROM payments
    WHERE payment_date BETWEEN ? AND ?
    GROUP BY payment_method
", [$dateFrom, $dateTo]);

// Top services
$topServices = fetchAll("
    SELECT 
        s.service_name,
        COUNT(*) as count,
        SUM(s.final_price) as total
    FROM services s
    WHERE s.service_date BETWEEN ? AND ? AND s.status != 'template'
    GROUP BY s.service_name
    ORDER BY total DESC
    LIMIT 10
", [$dateFrom, $dateTo]);

// Patient growth
$patientGrowth = fetchAll("
    SELECT 
        DATE(created_at) as date,
        COUNT(*) as count
    FROM patients
    WHERE DATE(created_at) BETWEEN ? AND ?
    GROUP BY DATE(created_at)
    ORDER BY date
", [$dateFrom, $dateTo]);

// Revenue trend
$revenueTrend = fetchAll("
    SELECT 
        DATE(payment_date) as date,
        SUM(amount) as total
    FROM payments
    WHERE payment_date BETWEEN ? AND ?
    GROUP BY DATE(payment_date)
    ORDER BY date
", [$dateFrom, $dateTo]);

// Medicine sales
$medicineSales = fetchAll("
    SELECT 
        medicine_name,
        SUM(quantity) as total_quantity,
        SUM(total_price) as total_revenue
    FROM medicine_sales
    WHERE sale_date BETWEEN ? AND ?
    GROUP BY medicine_name
    ORDER BY total_revenue DESC
    LIMIT 10
", [$dateFrom, $dateTo]);

// Doctor performance
$doctorPerformance = fetchAll("
    SELECT 
        u.full_name,
        COUNT(DISTINCT s.patient_id) as patients,
        COUNT(s.id) as services,
        SUM(s.final_price) as revenue
    FROM services s
    JOIN users u ON s.created_by = u.id
    WHERE s.service_date BETWEEN ? AND ? AND s.status != 'template'
    GROUP BY u.id, u.full_name
    ORDER BY revenue DESC
", [$dateFrom, $dateTo]);
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">📊 تحلیل پیشرفته</h1>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg no-print">
            🖨️ چاپ گزارش
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 no-print">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">از تاریخ</label>
                <input type="date" name="date_from" value="<?php echo $dateFrom; ?>" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">تا تاریخ</label>
                <input type="date" name="date_to" value="<?php echo $dateTo; ?>" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">نوع گزارش</label>
                <select name="type" class="w-full px-4 py-2 border rounded-lg">
                    <option value="overview" <?php echo $reportType === 'overview' ? 'selected' : ''; ?>>کلی</option>
                    <option value="financial" <?php echo $reportType === 'financial' ? 'selected' : ''; ?>>مالی</option>
                    <option value="patients" <?php echo $reportType === 'patients' ? 'selected' : ''; ?>>بیماران</option>
                    <option value="services" <?php echo $reportType === 'services' ? 'selected' : ''; ?>>خدمات</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    اعمال فیلتر
                </button>
            </div>
        </form>
    </div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue by Method -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">درآمد بر اساس روش پرداخت</h2>
            <canvas id="revenueMethodChart"></canvas>
        </div>

        <!-- Top Services -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">خدمات پرطرفدار</h2>
            <canvas id="topServicesChart"></canvas>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Patient Growth -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">رشد بیماران</h2>
            <canvas id="patientGrowthChart"></canvas>
        </div>

        <!-- Revenue Trend -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">روند درآمد</h2>
            <canvas id="revenueTrendChart"></canvas>
        </div>
    </div>

    <!-- Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Doctor Performance -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">عملکرد دکترها</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-right">نام</th>
                            <th class="px-4 py-2 text-right">بیماران</th>
                            <th class="px-4 py-2 text-right">خدمات</th>
                            <th class="px-4 py-2 text-right">درآمد</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doctorPerformance as $doc): ?>
                        <tr class="border-t">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($doc['full_name']); ?></td>
                            <td class="px-4 py-2"><?php echo $doc['patients']; ?></td>
                            <td class="px-4 py-2"><?php echo $doc['services']; ?></td>
                            <td class="px-4 py-2 font-semibold text-green-600"><?php echo formatCurrency($doc['revenue']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Medicine Sales -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">فروش داروها</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-right">دارو</th>
                            <th class="px-4 py-2 text-right">تعداد</th>
                            <th class="px-4 py-2 text-right">درآمد</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medicineSales as $med): ?>
                        <tr class="border-t">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($med['medicine_name']); ?></td>
                            <td class="px-4 py-2"><?php echo $med['total_quantity']; ?></td>
                            <td class="px-4 py-2 font-semibold text-green-600"><?php echo formatCurrency($med['total_revenue']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Revenue by Method
new Chart(document.getElementById('revenueMethodChart'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($revenueByMethod, 'payment_method')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($revenueByMethod, 'total')); ?>,
            backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});

// Top Services
new Chart(document.getElementById('topServicesChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($topServices, 'service_name')); ?>,
        datasets: [{
            label: 'درآمد',
            data: <?php echo json_encode(array_column($topServices, 'total')); ?>,
            backgroundColor: '#3b82f6'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: { y: { beginAtZero: true } }
    }
});

// Patient Growth
new Chart(document.getElementById('patientGrowthChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($patientGrowth, 'date')); ?>,
        datasets: [{
            label: 'بیماران جدید',
            data: <?php echo json_encode(array_column($patientGrowth, 'count')); ?>,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: { y: { beginAtZero: true } }
    }
});

// Revenue Trend
new Chart(document.getElementById('revenueTrendChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($revenueTrend, 'date')); ?>,
        datasets: [{
            label: 'درآمد',
            data: <?php echo json_encode(array_column($revenueTrend, 'total')); ?>,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: { y: { beginAtZero: true } }
    }
});
</script>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white; }
    .bg-white { box-shadow: none !important; }
}
</style>

<?php include '../includes/footer.php'; ?>
