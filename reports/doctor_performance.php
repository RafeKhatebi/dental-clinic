<?php
require_once '../config/config.php';
include '../includes/header.php';

// Get date range
$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo = $_GET['date_to'] ?? date('Y-m-d');
$doctorId = $_GET['doctor_id'] ?? '';

// Get all dentists
$dentists = fetchAll("SELECT id, full_name FROM users WHERE role = 'dentist' AND is_active = 1 ORDER BY full_name");

// Build query
$whereClause = "WHERE s.service_date BETWEEN ? AND ?";
$params = [$dateFrom, $dateTo];

if (!empty($doctorId)) {
    $whereClause .= " AND s.dentist_id = ?";
    $params[] = $doctorId;
}

// Get doctor performance data
$performance = fetchAll("
    SELECT 
        u.id,
        u.full_name,
        COUNT(DISTINCT s.id) as total_services,
        COUNT(DISTINCT s.patient_id) as total_patients,
        SUM(s.final_price) as total_revenue,
        AVG(s.final_price) as avg_service_price
    FROM services s
    JOIN users u ON s.dentist_id = u.id
    $whereClause AND s.status != 'template' AND s.patient_id IS NOT NULL
    GROUP BY u.id, u.full_name
    ORDER BY total_revenue DESC
", $params);

// Get service breakdown per doctor
$serviceBreakdown = [];
if (!empty($doctorId)) {
    $serviceBreakdown = fetchAll("
        SELECT 
            service_name,
            COUNT(id) as count,
            SUM(final_price) as revenue
        FROM services
        WHERE dentist_id = ? AND service_date BETWEEN ? AND ? AND status != 'template' AND patient_id IS NOT NULL
        GROUP BY service_name
        ORDER BY revenue DESC
        LIMIT 10
    ", [$doctorId, $dateFrom, $dateTo]);
}
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['doctor_performance']; ?></h1>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
            🖨 <?php echo $lang['print']; ?>
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo $lang['from_date']; ?></label>
                <input type="date" name="date_from" value="<?php echo $dateFrom; ?>" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo $lang['to_date']; ?></label>
                <input type="date" name="date_to" value="<?php echo $dateTo; ?>" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo $lang['dentist']; ?></label>
                <select name="doctor_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value=""><?php echo $current_lang === 'fa' ? 'همه دکترها' : 'All Doctors'; ?></option>
                    <?php foreach ($dentists as $dentist): ?>
                    <option value="<?php echo $dentist['id']; ?>" <?php echo $doctorId == $dentist['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($dentist['full_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['generate_report']; ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Performance Summary -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-bold"><?php echo $lang['doctor_performance']; ?></h2>
            <p class="text-sm text-gray-600"><?php echo $lang['from_date']; ?>: <?php echo formatDate($dateFrom); ?> - <?php echo $lang['to_date']; ?>: <?php echo formatDate($dateTo); ?></p>
        </div>
        
        <?php if (empty($performance)): ?>
            <div class="p-8 text-center text-gray-500">
                <?php echo $lang['no_data']; ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['dentist']; ?></th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['total_services']; ?></th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['total_patients']; ?></th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['total_revenue']; ?></th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['avg_service_price']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($performance as $perf): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                <a href="?date_from=<?php echo $dateFrom; ?>&date_to=<?php echo $dateTo; ?>&doctor_id=<?php echo $perf['id']; ?>" 
                                   class="text-blue-600 hover:underline">
                                    <?php echo htmlspecialchars($perf['full_name']); ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo $perf['total_services']; ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo $perf['total_patients']; ?></td>
                            <td class="px-6 py-4 text-sm font-semibold text-green-600"><?php echo formatCurrency($perf['total_revenue']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo formatCurrency($perf['avg_service_price']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Service Breakdown -->
    <?php if (!empty($serviceBreakdown)): ?>
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-bold"><?php echo $lang['service_breakdown']; ?></h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['service_name']; ?></th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['count']; ?></th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['revenue']; ?></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($serviceBreakdown as $service): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($service['service_name']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo $service['count']; ?></td>
                        <td class="px-6 py-4 text-sm font-semibold text-green-600"><?php echo formatCurrency($service['revenue']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
addBreadcrumb('<?php echo $lang['reports']; ?>');
addBreadcrumb('<?php echo $lang['doctor_performance']; ?>');
</script>

<?php include '../includes/footer.php'; ?>
