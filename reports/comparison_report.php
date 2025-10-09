<?php
require_once '../config/config.php';
include '../includes/header.php';

// Comparison periods
$period1From = $_GET['period1_from'] ?? date('Y-m-01', strtotime('-1 month'));
$period1To = $_GET['period1_to'] ?? date('Y-m-t', strtotime('-1 month'));
$period2From = $_GET['period2_from'] ?? date('Y-m-01');
$period2To = $_GET['period2_to'] ?? date('Y-m-d');

// Period 1 stats
$period1Stats = [
    'revenue' => fetchOne("SELECT SUM(amount) as total FROM payments WHERE payment_date BETWEEN ? AND ?", [$period1From, $period1To])['total'] ?? 0,
    'patients' => fetchOne("SELECT COUNT(DISTINCT patient_id) as count FROM services WHERE service_date BETWEEN ? AND ?", [$period1From, $period1To])['count'] ?? 0,
    'services' => fetchOne("SELECT COUNT(*) as count FROM services WHERE service_date BETWEEN ? AND ? AND status != 'template'", [$period1From, $period1To])['count'] ?? 0,
    'new_patients' => fetchOne("SELECT COUNT(*) as count FROM patients WHERE DATE(created_at) BETWEEN ? AND ?", [$period1From, $period1To])['count'] ?? 0
];

// Period 2 stats
$period2Stats = [
    'revenue' => fetchOne("SELECT SUM(amount) as total FROM payments WHERE payment_date BETWEEN ? AND ?", [$period2From, $period2To])['total'] ?? 0,
    'patients' => fetchOne("SELECT COUNT(DISTINCT patient_id) as count FROM services WHERE service_date BETWEEN ? AND ?", [$period2From, $period2To])['count'] ?? 0,
    'services' => fetchOne("SELECT COUNT(*) as count FROM services WHERE service_date BETWEEN ? AND ? AND status != 'template'", [$period2From, $period2To])['count'] ?? 0,
    'new_patients' => fetchOne("SELECT COUNT(*) as count FROM patients WHERE DATE(created_at) BETWEEN ? AND ?", [$period2From, $period2To])['count'] ?? 0
];

// Calculate changes
function calculateChange($old, $new) {
    if ($old == 0) return $new > 0 ? 100 : 0;
    return round((($new - $old) / $old) * 100, 1);
}
?>

<div class="space-y-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">📊 گزارش مقایسهای</h1>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-700">دوره اول</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">از</label>
                        <input type="date" name="period1_from" value="<?php echo $period1From; ?>" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">تا</label>
                        <input type="date" name="period1_to" value="<?php echo $period1To; ?>" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-700">دوره دوم</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">از</label>
                        <input type="date" name="period2_from" value="<?php echo $period2From; ?>" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">تا</label>
                        <input type="date" name="period2_to" value="<?php echo $period2To; ?>" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                    مقایسه دورهها
                </button>
            </div>
        </form>
    </div>

    <!-- Comparison Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
        $metrics = [
            ['key' => 'revenue', 'label' => 'درآمد', 'icon' => '💰', 'format' => 'currency'],
            ['key' => 'patients', 'label' => 'بیماران', 'icon' => '👥', 'format' => 'number'],
            ['key' => 'services', 'label' => 'خدمات', 'icon' => '🦷', 'format' => 'number'],
            ['key' => 'new_patients', 'label' => 'بیماران جدید', 'icon' => '✨', 'format' => 'number']
        ];
        
        foreach ($metrics as $metric):
            $change = calculateChange($period1Stats[$metric['key']], $period2Stats[$metric['key']]);
            $isPositive = $change >= 0;
            $value2 = $metric['format'] === 'currency' ? formatCurrency($period2Stats[$metric['key']]) : number_format($period2Stats[$metric['key']]);
        ?>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <span class="text-3xl"><?php echo $metric['icon']; ?></span>
                <span class="text-2xl font-bold <?php echo $isPositive ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo $isPositive ? '↑' : '↓'; ?> <?php echo abs($change); ?>%
                </span>
            </div>
            <h3 class="text-sm text-gray-600 mb-2"><?php echo $metric['label']; ?></h3>
            <p class="text-2xl font-bold text-gray-900"><?php echo $value2; ?></p>
            <p class="text-xs text-gray-500 mt-2">
                دوره قبل: <?php echo $metric['format'] === 'currency' ? formatCurrency($period1Stats[$metric['key']]) : number_format($period1Stats[$metric['key']]); ?>
            </p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Comparison Chart -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold mb-4">مقایسه بصری</h2>
        <canvas id="comparisonChart"></canvas>
    </div>
</div>

<script>
new Chart(document.getElementById('comparisonChart'), {
    type: 'bar',
    data: {
        labels: ['درآمد', 'بیماران', 'خدمات', 'بیماران جدید'],
        datasets: [
            {
                label: 'دوره اول',
                data: [
                    <?php echo $period1Stats['revenue']; ?>,
                    <?php echo $period1Stats['patients']; ?>,
                    <?php echo $period1Stats['services']; ?>,
                    <?php echo $period1Stats['new_patients']; ?>
                ],
                backgroundColor: '#94a3b8'
            },
            {
                label: 'دوره دوم',
                data: [
                    <?php echo $period2Stats['revenue']; ?>,
                    <?php echo $period2Stats['patients']; ?>,
                    <?php echo $period2Stats['services']; ?>,
                    <?php echo $period2Stats['new_patients']; ?>
                ],
                backgroundColor: '#3b82f6'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: { y: { beginAtZero: true } }
    }
});
</script>

<?php include '../includes/footer.php'; ?>
