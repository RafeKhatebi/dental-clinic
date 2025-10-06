<?php
require_once '../config/config.php';
include '../includes/header.php';

// Get all medicines with pagination and filters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$lowStock = $_GET['low_stock'] ?? '';
$expiring = $_GET['expiring'] ?? '';

$whereClauses = [];
$params = [];

if (!empty($search)) {
    $whereClauses[] = "(medicine_code LIKE ? OR medicine_name LIKE ? OR medicine_name_en LIKE ?)";
    $searchParam = "%$search%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
}

if (!empty($category)) {
    $whereClauses[] = "category = ?";
    $params[] = $category;
}

if ($lowStock === '1') {
    $whereClauses[] = "stock_quantity <= min_stock_level";
}

if ($expiring === '1') {
    $whereClauses[] = "expiry_date IS NOT NULL AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND expiry_date >= CURDATE()";
}

$whereClause = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Get categories for filter
$categories = fetchAll("SELECT DISTINCT category FROM medicines WHERE category IS NOT NULL AND category != '' ORDER BY category");

$totalRecords = fetchOne("SELECT COUNT(*) as count FROM medicines $whereClause", $params)['count'];
$pagination = getPagination($totalRecords, 20);
$medicines = fetchAll("SELECT * FROM medicines $whereClause ORDER BY medicine_name LIMIT {$pagination['perPage']} OFFSET {$pagination['offset']}", $params);
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['medicine_list']; ?></h1>
        <div class="flex gap-2">
            <button onclick="bulkAction('activate')" id="bulkActivate" class="hidden bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                ✔ فعال
            </button>
            <button onclick="bulkAction('deactivate')" id="bulkDeactivate" class="hidden bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition">
                ✖ غیرفعال
            </button>
            <button onclick="bulkAction('delete')" id="bulkDelete" class="hidden bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                🗑 حذف
            </button>
            <button onclick="exportToExcel('medicinesTable', 'medicines')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                📊 Excel
            </button>
            <a href="sales.php" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
                <?php echo $lang['medicine_sales']; ?>
            </a>
            <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                + <?php echo $lang['add_medicine']; ?>
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="space-y-4">
            <div class="flex gap-4">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                    placeholder="<?php echo $lang['search']; ?>..." 
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                <button type="button" onclick="document.getElementById('advFilters').classList.toggle('hidden')" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition" data-tooltip="فیلترهای پیشرفته">
                    ⚙ فیلتر
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['search']; ?>
                </button>
                <?php if (!empty($search) || !empty($category) || $lowStock === '1' || $expiring === '1'): ?>
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['cancel']; ?>
                </a>
                <?php endif; ?>
            </div>
            
            <div id="advFilters" class="<?php echo (!empty($category) || $lowStock === '1' || $expiring === '1') ? '' : 'hidden'; ?> grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">موجودی کم</label>
                    <label class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg cursor-pointer">
                        <input type="checkbox" name="low_stock" value="1" <?php echo $lowStock === '1' ? 'checked' : ''; ?> class="w-4 h-4">
                        <span>فقط داروهای کم موجود</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">انقضا نزدیک</label>
                    <label class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg cursor-pointer">
                        <input type="checkbox" name="expiring" value="1" <?php echo $expiring === '1' ? 'checked' : ''; ?> class="w-4 h-4">
                        <span>فقط داروهای رو به انقضا</span>
                    </label>
                </div>
            </div>
        </form>
    </div>

    <!-- Medicines Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <?php if (empty($medicines)): ?>
            <div class="p-8 text-center text-gray-500">
                <?php echo $lang['no_data']; ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table id="medicinesTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" class="w-4 h-4 cursor-pointer">
                            </th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['medicine_code']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['medicine_name']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['category']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['stock_quantity']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['sale_price']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['expiry_date']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($medicines as $medicine): ?>
                        <?php 
                            $isLowStock = $medicine['stock_quantity'] <= $medicine['min_stock_level'];
                            $isExpiringSoon = !empty($medicine['expiry_date']) && strtotime($medicine['expiry_date']) <= strtotime('+30 days');
                        ?>
                        <tr class="hover:bg-gray-50 <?php echo $isLowStock || $isExpiringSoon ? 'bg-yellow-50' : ''; ?>">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" class="row-checkbox" value="<?php echo $medicine['id']; ?>" onchange="updateBulkButtons()" class="w-4 h-4 cursor-pointer">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                <?php echo $medicine['medicine_code']; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo htmlspecialchars($medicine['medicine_name']); ?>
                                <?php if (!empty($medicine['medicine_name_en'])): ?>
                                <br><span class="text-xs text-gray-500"><?php echo htmlspecialchars($medicine['medicine_name_en']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($medicine['category'] ?: '-'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="<?php echo $isLowStock ? 'text-red-600 font-semibold' : 'text-gray-900'; ?>">
                                    <?php echo $medicine['stock_quantity']; ?> <?php echo $medicine['unit']; ?>
                                </span>
                                <?php if ($isLowStock): ?>
                                <br><span class="text-xs text-red-500">⚠ <?php echo $lang['low_stock_medicines']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                <?php echo formatCurrency($medicine['sale_price']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if (!empty($medicine['expiry_date'])): ?>
                                    <span class="<?php echo $isExpiringSoon ? 'text-red-600 font-semibold' : 'text-gray-900'; ?>">
                                        <?php echo formatDate($medicine['expiry_date']); ?>
                                    </span>
                                    <?php if ($isExpiringSoon): ?>
                                    <br><span class="text-xs text-red-500">⚠ <?php echo $lang['expiring_medicines']; ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <a href="edit.php?id=<?php echo $medicine['id']; ?>" 
                                       class="text-green-600 hover:text-green-900">
                                        <?php echo $lang['edit']; ?>
                                    </a>
                                    <a href="stock.php?id=<?php echo $medicine['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <?php echo $lang['stock_quantity']; ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php echo renderPagination($pagination); ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
