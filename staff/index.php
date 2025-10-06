<?php
require_once '../config/config.php';
include '../includes/header.php';

$search = $_GET['search'] ?? '';
$where = "is_staff = 1";
$params = [];

if (!empty($search)) {
    $where .= " AND (full_name LIKE ? OR phone LIKE ? OR email LIKE ?)";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam, $searchParam];
}

$totalRecords = fetchOne("SELECT COUNT(*) as count FROM users WHERE $where", $params)['count'];
$pagination = getPagination($totalRecords, 20);
$staff = fetchAll("SELECT * FROM users WHERE $where ORDER BY is_active DESC, full_name LIMIT {$pagination['perPage']} OFFSET {$pagination['offset']}", $params);
?>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $lang['staff_list']; ?></h1>
        <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
            <button onclick="bulkAction('activate')" id="bulkActivate" class="hidden bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                ✔ فعال
            </button>
            <button onclick="bulkAction('deactivate')" id="bulkDeactivate" class="hidden bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition">
                ✖ غیرفعال
            </button>
            <button onclick="exportToExcel('staffTable', 'staff')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                📊 Excel
            </button>
            <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                + <?php echo $lang['add_staff']; ?>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="جستجو در نام، تلفن، ایمیل..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">جستجو</button>
            <?php if (!empty($search)): ?>
            <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">لغو</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <?php if (empty($staff)): ?>
            <div class="p-8 text-center text-gray-500"><?php echo $lang['no_data']; ?></div>
        <?php else: ?>
            <!-- Desktop Table -->
            <div class="overflow-x-auto table-desktop">
                <table id="staffTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" class="w-4 h-4 cursor-pointer">
                            </th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['full_name']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['job_title']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['phone']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['monthly_salary']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['hire_date']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['status']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($staff as $member): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" class="row-checkbox" value="<?php echo $member['id']; ?>" onchange="updateBulkButtons()" class="w-4 h-4 cursor-pointer">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($member['full_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($member['job_title'] ?: '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $member['phone'] ?: '-'; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600"><?php echo formatCurrency($member['monthly_salary']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo formatDate($member['hire_date']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $member['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $member['is_active'] ? $lang['active'] : $lang['inactive']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <a href="view.php?id=<?php echo $member['id']; ?>" class="text-blue-600 hover:text-blue-900"><?php echo $lang['view']; ?></a>
                                    <a href="edit.php?id=<?php echo $member['id']; ?>" class="text-green-600 hover:text-green-900"><?php echo $lang['edit']; ?></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Cards -->
            <div class="cards-mobile space-y-4 p-4">
                <?php foreach ($staff as $member): ?>
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3 pb-3 border-b">
                        <input type="checkbox" class="row-checkbox w-5 h-5" value="<?php echo $member['id']; ?>" onchange="updateBulkButtons()">
                        <div class="flex gap-3">
                            <a href="view.php?id=<?php echo $member['id']; ?>" class="text-blue-600 hover:text-blue-900 text-sm font-medium">مشاهده</a>
                            <a href="edit.php?id=<?php echo $member['id']; ?>" class="text-green-600 hover:text-green-900 text-sm font-medium">ویرایش</a>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">نام:</span>
                            <span class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($member['full_name']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">سمت:</span>
                            <span class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($member['job_title'] ?: '-'); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">تلفن:</span>
                            <span class="text-sm text-gray-900 dir-ltr"><?php echo $member['phone'] ?: '-'; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">حقوق:</span>
                            <span class="text-sm font-semibold text-green-600"><?php echo formatCurrency($member['monthly_salary']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">تاریخ استخدام:</span>
                            <span class="text-sm text-gray-900"><?php echo formatDate($member['hire_date']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">وضعیت:</span>
                            <span class="px-2 py-1 text-xs rounded-full <?php echo $member['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $member['is_active'] ? $lang['active'] : $lang['inactive']; ?>
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
