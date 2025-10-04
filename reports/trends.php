<?php
require_once '../config/config.php';
include '../includes/header.php';

// Get date range
$months = $_GET['months'] ?? 6;

// Monthly revenue trend
$monthlyData = [];
for ($i = $months - 1; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $revenue = fetchOne("
        SELECT 
            SUM(CASE WHEN payment_method = 'cash' THEN amount ELSE 0 END) as cash,
            SUM(CASE WHEN payment_method = 'installment' THEN amount ELSE 0 END) as installment,
            SUM(amount) as total
        FROM payments 
        WHERE DATE_FORMAT(payment_date, '%Y-%m') = ?
    ", [$month]);
    
    $monthlyData[] = [
        'month' => $month,
        'cash' => $revenue['cash'] ?? 0,
        'installment' => $revenue['installment'] ?? 0,
        'total' => $revenue['total'] ?? 0
    ];
}

// Patient growth trend
$patientGrowth = [];
for ($i = $months - 1; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $count = fetchOne("
        SELECT COUNT(DISTINCT patient_id) as count
        FROM services
        WHERE DATE_FORMAT(service_date, '%Y-%m') = ? AND status != 'template'
    ", [$month])['count'] ?? 0;
    
    $patientGrowth[] = [
        'month' => $month,
        'count' => $count
    ];
}

// Service category breakdown (current month)
$categoryData = fetchAll("
    SELECT 
        category,
        COUNT(*) as count,
        SUM(final_price) as revenue
    FROM services
    WHERE DATE_FORMAT(service_date, '%Y-%m') = ? AND status != 'template' AND patient_id IS NOT NULL
    GROUP BY category
    ORDER BY revenue DESC
", [date('Y-m')]);
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">نمودارهای روند</h1>
        <form method="GET" class="flex gap-2">
            <select name="months" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="3" <?php echo $months == 3 ? 'selected' : ''; ?>>3 ماه</option>
                <option value="6" <?php echo $months == 6 ? 'selected' : ''; ?>>6 ماه</option>
                <option value="12" <?php echo $months == 12 ? 'selected' : ''; ?>>12 ماه</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">نمایش</button>
        </form>
    </div>

    <!-- Monthly Revenue Trend -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">روند درآمد ماهانه</h2>
        <canvas id="monthlyRevenueChart" height="80"></canvas>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Patient Growth -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">رشد تعداد بیماران</h2>
            <canvas id="patientGrowthChart"></canvas>
        </div>

        <!-- Service Categories -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">تفکیک خدمات (ماه جاری)</h2>
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

<script>
// Monthly Revenue Chart
new Chart(document.getElementById('monthlyRevenueChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($monthlyData, 'month')); ?>,
        datasets: [{
            label: 'نقدی',
            data: <?php echo json_encode(array_column($monthlyData, 'cash')); ?>,
            backgroundColor: 'rgba(34, 197, 94, 0.8)'
        }, {
            label: 'قسطی',
            data: <?php echo json_encode(array_column($monthlyData, 'installment')); ?>,
            backgroundColor: 'rgba(234, 179, 8, 0.8)'
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: { stacked: true },
            y: { stacked: true, beginAtZero: true }
        }
    }
});

// Patient Growth Chart
new Chart(document.getElementById('patientGrowthChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($patientGrowth, 'month')); ?>,
        datasets: [{
            label: 'تعداد بیماران',
            data: <?php echo json_encode(array_column($patientGrowth, 'count')); ?>,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});

// Category Chart
new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($categoryData, 'category')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($categoryData, 'revenue')); ?>,
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(34, 197, 94, 0.8)',
                'rgba(234, 179, 8, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(168, 85, 247, 0.8)',
                'rgba(236, 72, 153, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>

<script>
addBreadcrumb('گزارشات');
addBreadcrumb('نمودارهای روند');
</script>

<?php include '../includes/footer.php'; ?>
