<?php
require_once '../config/config.php';
include '../includes/header.php';

// Get all services with pagination and filters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';

$whereClauses = [];
$params = [];

if (!empty($search)) {
    $whereClauses[] = "(service_name LIKE ? OR service_name_en LIKE ?)";
    $searchParam = "%$search%";
    $params = array_merge($params, [$searchParam, $searchParam]);
}

if (!empty($category)) {
    $whereClauses[] = "category = ?";
    $params[] = $category;
}

if ($status !== '') {
    $whereClauses[] = "is_active = ?";
    $params[] = $status;
}

$whereClause = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Get categories for filter
$categories = fetchAll("SELECT DISTINCT category FROM services WHERE category IS NOT NULL AND category != '' ORDER BY category");

$totalRecords = fetchOne("SELECT COUNT(*) as count FROM services $whereClause", $params)['count'];
$pagination = getPagination($totalRecords, 20);
$services = fetchAll("SELECT * FROM services $whereClause ORDER BY category, service_name LIMIT {$pagination['perPage']} OFFSET {$pagination['offset']}", $params);
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $lang['service_list']; ?></h1>
        <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
            <button onclick="bulkAction('activate')" id="bulkActivate" class="hidden bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                ✔ فعال
            </button>
            <button onclick="bulkAction('deactivate')" id="bulkDeactivate" class="hidden bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition">
                ✖ غیرفعال
            </button>
            <button onclick="bulkAction('delete')" id="bulkDelete" class="hidden bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                🗑 حذف
            </button>
            <button onclick="exportToExcel('servicesTable', 'services')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                📊 Excel
            </button>
            <?php if (hasRole(['admin', 'dentist'])): ?>
            <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                + <?php echo $lang['add_service']; ?>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="space-y-4">
            <div class="flex flex-col md:flex-row gap-4">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="جستجو..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                <button type="button" onclick="document.getElementById('advFilters').classList.toggle('hidden')" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition" data-tooltip="فیلترها">
                    ⚙ فیلتر
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    اعمال فیلتر
                </button>
                <?php if (!empty($category) || $status !== ''): ?>
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['cancel']; ?>
                </a>
                <?php endif; ?>
            </div>
            
            <div id="advFilters" class="<?php echo (!empty($category) || $status !== '') ? '' : 'hidden'; ?> grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">دستهبندی</label>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">همه</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo isset($lang[$cat['category']]) ? $lang[$cat['category']] : htmlspecialchars($cat['category']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">وضعیت</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">همه</option>
                        <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>فعال</option>
                        <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>غیرفعال</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Services Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <?php if (empty($services)): ?>
            <div class="p-8 text-center text-gray-500">
                <?php echo $lang['no_data']; ?>
            </div>
        <?php else: ?>
            <!-- Desktop Table -->
            <div class="overflow-x-auto table-desktop">
                <table id="servicesTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" class="w-4 h-4 cursor-pointer">
                            </th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['service_name']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['category']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['base_price']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['status']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($services as $service): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" class="row-checkbox" value="<?php echo $service['id']; ?>" onchange="updateBulkButtons()" class="w-4 h-4 cursor-pointer">
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($service['service_name']); ?>
                                <?php if (!empty($service['service_name_en'])): ?>
                                <br><span class="text-xs text-gray-500"><?php echo htmlspecialchars($service['service_name_en']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo ($service['category'] && isset($lang[$service['category']])) ? $lang[$service['category']] : ($service['category'] ?: '-'); ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-green-600">
                                <?php echo formatCurrency($service['base_price']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $service['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $service['is_active'] ? $lang['active'] : $lang['inactive']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <div class="flex gap-2">
                                    <?php if (hasRole(['admin', 'dentist'])): ?>
                                    <a href="edit.php?id=<?php echo $service['id']; ?>" 
                                       class="text-green-600 hover:text-green-900">
                                        <?php echo $lang['edit']; ?>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Cards -->
            <div class="cards-mobile space-y-4 p-4">
                <?php foreach ($services as $service): ?>
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3 pb-3 border-b">
                        <input type="checkbox" class="row-checkbox w-5 h-5" value="<?php echo $service['id']; ?>" onchange="updateBulkButtons()">
                        <?php if (hasRole(['admin', 'dentist'])): ?>
                        <a href="edit.php?id=<?php echo $service['id']; ?>" class="text-green-600 hover:text-green-900 text-sm font-medium">ویرایش</a>
                        <?php endif; ?>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">نام خدمت:</span>
                            <span class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($service['service_name']); ?></span>
                        </div>
                        <?php if (!empty($service['service_name_en'])): ?>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">نام انگلیسی:</span>
                            <span class="text-sm text-gray-500"><?php echo htmlspecialchars($service['service_name_en']); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">دسته:</span>
                            <span class="text-sm font-semibold text-gray-900"><?php echo ($service['category'] && isset($lang[$service['category']])) ? $lang[$service['category']] : ($service['category'] ?: '-'); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">قیمت:</span>
                            <span class="text-sm font-semibold text-green-600"><?php echo formatCurrency($service['base_price']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">وضعیت:</span>
                            <span class="px-2 py-1 text-xs rounded-full <?php echo $service['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $service['is_active'] ? $lang['active'] : $lang['inactive']; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Desktop Pagination -->
            <div class="pagination-desktop">
                <?php echo renderPagination($pagination); ?>
            </div>
            
            <!-- Mobile Pagination -->
            <?php if ($pagination['totalPages'] > 1): ?>
            <div class="pagination-mobile flex items-center justify-between p-4 bg-white border-t">
                <a href="?page=<?php echo max(1, $pagination['currentPage'] - 1); ?>" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg <?php echo $pagination['currentPage'] === 1 ? 'opacity-50 pointer-events-none' : ''; ?>">
                    قبلی
                </a>
                <span class="text-sm text-gray-600">صفحه <?php echo $pagination['currentPage']; ?> از <?php echo $pagination['totalPages']; ?></span>
                <a href="?page=<?php echo min($pagination['totalPages'], $pagination['currentPage'] + 1); ?>" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg <?php echo $pagination['currentPage'] === $pagination['totalPages'] ? 'opacity-50 pointer-events-none' : ''; ?>">
                    بعدی
                </a>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
