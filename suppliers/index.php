<?php
require_once '../config/config.php';
include '../includes/header.php';

// Get all suppliers with pagination
$search = $_GET['search'] ?? '';
$where = "supplier_name IS NOT NULL AND supplier_name != ''";
$params = [];

if (!empty($search)) {
    $where .= " AND (supplier_name LIKE ? OR supplier_phone LIKE ?)";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam];
}

$totalRecords = fetchOne("SELECT COUNT(DISTINCT supplier_name) as count FROM medicines WHERE $where", $params)['count'];
$pagination = getPagination($totalRecords, 20);
$suppliers = fetchAll("
    SELECT DISTINCT 
        supplier_name, 
        supplier_phone as phone, 
        supplier_email as email,
        supplier_address as address,
        MIN(id) as id
    FROM medicines 
    WHERE $where
    GROUP BY supplier_name, supplier_phone, supplier_email, supplier_address
    ORDER BY supplier_name
    LIMIT {$pagination['perPage']} OFFSET {$pagination['offset']}
", $params);
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['supplier_list']; ?></h1>
        <div class="flex gap-2">
            <button onclick="exportToExcel('suppliersTable', 'suppliers')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                📊 Excel
            </button>
            <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                + <?php echo $lang['add_supplier']; ?>
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="جستجو در نام یا تلفن..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">جستجو</button>
            <?php if (!empty($search)): ?>
            <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">لغو</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Suppliers Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <?php if (empty($suppliers)): ?>
            <div class="p-8 text-center text-gray-500">
                <?php echo $lang['no_data']; ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table id="suppliersTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" class="w-4 h-4 cursor-pointer">
                            </th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['supplier_code']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['supplier_name']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['contact_person']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['phone']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['product_type']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($suppliers as $supplier): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" class="row-checkbox" value="<?php echo $supplier['id']; ?>" onchange="updateBulkButtons()" class="w-4 h-4 cursor-pointer">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                SUP-<?php echo str_pad($supplier['id'], 4, '0', STR_PAD_LEFT); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($supplier['supplier_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                -
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $supplier['phone'] ?: '-'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                داروها
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <a href="view.php?id=<?php echo $supplier['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <?php echo $lang['view']; ?>
                                    </a>
                                    <a href="edit.php?id=<?php echo $supplier['id']; ?>" 
                                       class="text-green-600 hover:text-green-900">
                                        <?php echo $lang['edit']; ?>
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
