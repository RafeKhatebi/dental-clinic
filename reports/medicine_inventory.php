<?php
require_once '../config/config.php';
include '../includes/header.php';

// Get filters
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? ''; // all, low_stock, expiring, expired

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
        <h1 class="text-3xl font-bold text-gray-800">گزارش موجودی دارو</h1>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
            🖨 چاپ
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">دسته‌بندی</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">همه</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وضعیت</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">همه</option>
                    <option value="low_stock" <?php echo $status === 'low_stock' ? 'selected' : ''; ?>>کم موجود</option>
                    <option value="expiring" <?php echo $status === 'expiring' ? 'selected' : ''; ?>>رو به انقضا</option>
                    <option value="expired" <?php echo $status === 'expired' ? 'selected' : ''; ?>>منقضی شده</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    نمایش گزارش
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-600 mb-1">تعداد اقلام</div>
            <div class="text-2xl font-bold text-gray-800"><?php echo count($medicines); ?></div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-600 mb-1">ارزش خرید</div>
            <div class="text-2xl font-bold text-blue-600"><?php echo formatCurrency($totalPurchaseValue); ?></div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-600 mb-1">ارزش فروش</div>
            <div class="text-2xl font-bold text-green-600"><?php echo formatCurrency($totalSaleValue); ?></div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-600 mb-1">سود احتمالی</div>
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
                    <div class="font-bold text-red-800">هشدار موجودی کم</div>
                    <div class="text-sm text-red-600"><?php echo $lowStockCount; ?> قلم دارو کم موجود است</div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($expiringCount > 0): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center gap-2">
                <span class="text-2xl">⏰</span>
                <div>
                    <div class="font-bold text-yellow-800">هشدار انقضا</div>
                    <div class="text-sm text-yellow-600"><?php echo $expiringCount; ?> قلم دارو رو به انقضا است</div>
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
                دادهای یافت نشد
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">کد</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نام دارو</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">دسته</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">موجودی</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">قیمت خرید</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">قیمت فروش</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">ارزش کل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">انقضا</th>
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
                                <br><span class="text-xs text-red-500">⚠ کم موجود</span>
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
                                    <br><span class="text-xs text-red-500">❌ منقضی</span>
                                    <?php elseif ($isExpiring): ?>
                                    <br><span class="text-xs text-yellow-500">⏰ نزدیک انقضا</span>
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
addBreadcrumb('گزارشات');
addBreadcrumb('موجودی دارو');
</script>

<?php include '../includes/footer.php'; ?>
