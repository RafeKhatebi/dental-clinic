<?php
require_once '../config/config.php';
include '../includes/header.php';

// Get filters
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';

// Get categories
$categories = fetchAll("SELECT DISTINCT category FROM medicines WHERE category IS NOT NULL AND category != '' ORDER BY category");

// Build query
$whereClauses = [];
$params = [];

if (!empty($category)) {
    $whereClauses[] = "category = ?";
    $params[] = $category;
}

if ($status === 'low_stock') {
    $whereClauses[] = "stock_quantity <= min_stock_level";
} elseif ($status === 'expiring') {
    $whereClauses[] = "expiry_date IS NOT NULL AND DATE(expiry_date) <= DATE('now', '+30 days') AND DATE(expiry_date) >= DATE('now')";
} elseif ($status === 'expired') {
    $whereClauses[] = "expiry_date IS NOT NULL AND DATE(expiry_date) < DATE('now')";
}

$whereClause = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Get medicines
$medicines = fetchAll("
    SELECT 
        medicine_code,
        medicine_name,
        medicine_name_en,
        category,
        stock_quantity,
        unit,
        min_stock_level,
        purchase_price,
        sale_price,
        expiry_date,
        (stock_quantity * purchase_price) as total_purchase_value,
        (stock_quantity * sale_price) as total_sale_value
    FROM medicines
    $whereClause
    ORDER BY category, medicine_name
", $params);

// Calculate totals
$totalPurchaseValue = 0;
$totalSaleValue = 0;
$lowStockCount = 0;
$expiringCount = 0;

foreach ($medicines as $med) {
    $totalPurchaseValue += $med['total_purchase_value'];
    $totalSaleValue += $med['total_sale_value'];
    if ($med['stock_quantity'] <= $med['min_stock_level']) $lowStockCount++;
    if (!empty($med['expiry_date']) && strtotime($med['expiry_date']) <= strtotime('+30 days')) $expiringCount++;
}
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['medicine_inventory']; ?></h1>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
            🖨 <?php echo $lang['print']; ?>
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo $lang['category']; ?></label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value=""><?php echo $lang['all']; ?></option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo $lang['status']; ?></label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value=""><?php echo $lang['all']; ?></option>
                    <option value="low_stock" <?php echo $status === 'low_stock' ? 'selected' : ''; ?>><?php echo $lang['low_stock_medicines']; ?></option>
                    <option value="expiring" <?php echo $status === 'expiring' ? 'selected' : ''; ?>><?php echo $lang['expiring_medicines']; ?></option>
                    <option value="expired" <?php echo $status === 'expired' ? 'selected' : ''; ?>><?php echo $current_lang === 'fa' ? 'منقضی شده' : 'Expired'; ?></option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['generate_report']; ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-600 mb-1"><?php echo $lang['items']; ?></div>
            <div class="text-2xl font-bold text-gray-800"><?php echo count($medicines); ?></div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-600 mb-1"><?php echo $lang['purchase_value']; ?></div>
            <div class="text-2xl font-bold text-blue-600"><?php echo formatCurrency($totalPurchaseValue); ?></div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-600 mb-1"><?php echo $lang['sale_value']; ?></div>
            <div class="text-2xl font-bold text-green-600"><?php echo formatCurrency($totalSaleValue); ?></div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-600 mb-1"><?php echo $lang['potential_profit']; ?></div>
            <div class="text-2xl font-bold text-purple-600"><?php echo formatCurrency($totalSaleValue - $totalPurchaseValue); ?></div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($lowStockCount > 0 || $expiringCount > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php if ($lowStockCount > 0): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center gap-2">
                <span class="text-2xl">⚠</span>
                <div>
                    <div class="font-bold text-red-800"><?php echo $lang['low_stock_alert']; ?></div>
                    <div class="text-sm text-red-600"><?php echo $lowStockCount; ?> <?php echo $current_lang === 'fa' ? 'قلم دارو کم موجود است' : 'items low in stock'; ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($expiringCount > 0): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center gap-2">
                <span class="text-2xl">⏰</span>
                <div>
                    <div class="font-bold text-yellow-800"><?php echo $lang['expiry_alert']; ?></div>
                    <div class="text-sm text-yellow-600"><?php echo $expiringCount; ?> <?php echo $current_lang === 'fa' ? 'قلم دارو رو به انقضا است' : 'items expiring soon'; ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Inventory Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <?php if (empty($medicines)): ?>
            <div class="p-8 text-center text-gray-500">
                <?php echo $lang['no_data']; ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['medicine_code']; ?></th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['medicine_name']; ?></th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['category']; ?></th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['stock_quantity']; ?></th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['purchase_price']; ?></th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['sale_price']; ?></th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['total']; ?></th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"><?php echo $lang['expiry_date']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($medicines as $med): 
                            $isLowStock = $med['stock_quantity'] <= $med['min_stock_level'];
                            $isExpiring = !empty($med['expiry_date']) && strtotime($med['expiry_date']) <= strtotime('+30 days');
                            $isExpired = !empty($med['expiry_date']) && strtotime($med['expiry_date']) < time();
                        ?>
                        <tr class="hover:bg-gray-50 <?php echo ($isLowStock || $isExpiring) ? 'bg-yellow-50' : ''; ?>">
                            <td class="px-6 py-4 text-sm font-medium text-blue-600"><?php echo $med['medicine_code']; ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo htmlspecialchars($med['medicine_name']); ?>
                                <?php if (!empty($med['medicine_name_en'])): ?>
                                <br><span class="text-xs text-gray-500"><?php echo htmlspecialchars($med['medicine_name_en']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($med['category'] ?: '-'); ?></td>
                            <td class="px-6 py-4 text-sm">
                                <span class="<?php echo $isLowStock ? 'text-red-600 font-bold' : 'text-gray-900'; ?>">
                                    <?php echo $med['stock_quantity']; ?> <?php echo $med['unit']; ?>
                                </span>
                                <?php if ($isLowStock): ?>
                                <br><span class="text-xs text-red-500">⚠ <?php echo $current_lang === 'fa' ? 'کم موجود' : 'Low Stock'; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo formatCurrency($med['purchase_price']); ?></td>
                            <td class="px-6 py-4 text-sm font-semibold text-green-600"><?php echo formatCurrency($med['sale_price']); ?></td>
                            <td class="px-6 py-4 text-sm font-semibold text-blue-600"><?php echo formatCurrency($med['total_sale_value']); ?></td>
                            <td class="px-6 py-4 text-sm">
                                <?php if (!empty($med['expiry_date'])): ?>
                                    <span class="<?php echo $isExpired ? 'text-red-600 font-bold' : ($isExpiring ? 'text-yellow-600 font-bold' : 'text-gray-900'); ?>">
                                        <?php echo formatDate($med['expiry_date']); ?>
                                    </span>
                                    <?php if ($isExpired): ?>
                                    <br><span class="text-xs text-red-500">❌ <?php echo $current_lang === 'fa' ? 'منقضی' : 'Expired'; ?></span>
                                    <?php elseif ($isExpiring): ?>
                                    <br><span class="text-xs text-yellow-500">⏰ <?php echo $current_lang === 'fa' ? 'نزدیک انقضا' : 'Expiring Soon'; ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
addBreadcrumb('<?php echo $lang['reports']; ?>');
addBreadcrumb('<?php echo $lang['medicine_inventory']; ?>');
</script>

<?php include '../includes/footer.php'; ?>
