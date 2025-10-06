<?php
require_once '../config/config.php';

if (!hasRole('admin')) {
    redirect('/dashboard.php');
}

include '../includes/header.php';

// Get all users with pagination
$totalRecords = fetchOne("SELECT COUNT(*) as count FROM users")['count'];
$pagination = getPagination($totalRecords, 20);
$users = fetchAll("SELECT * FROM users ORDER BY is_active DESC, full_name LIMIT {$pagination['perPage']} OFFSET {$pagination['offset']}");
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $lang['user_list']; ?></h1>
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
            <button onclick="exportToExcel('usersTable', 'users')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                📊 Excel
            </button>
            <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                + <?php echo $lang['add_user']; ?>
            </a>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <?php if (empty($users)): ?>
            <div class="p-8 text-center text-gray-500">
                <?php echo $lang['no_data']; ?>
            </div>
        <?php else: ?>
            <!-- Desktop Table -->
            <div class="overflow-x-auto table-desktop">
                <table id="usersTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" class="w-4 h-4 cursor-pointer">
                            </th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['username']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['full_name']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['email']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['role']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['status']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" class="row-checkbox" value="<?php echo $user['id']; ?>" onchange="updateBulkButtons()" class="w-4 h-4 cursor-pointer" <?php echo $user['id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($user['full_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($user['email'] ?: '-'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    <?php echo $lang[$user['role']]; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $user['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $user['is_active'] ? $lang['active'] : $lang['inactive']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="edit.php?id=<?php echo $user['id']; ?>" 
                                       class="text-green-600 hover:text-green-900">
                                        <?php echo $lang['edit']; ?>
                                    </a>
                                    <button onclick="deleteUser(<?php echo $user['id']; ?>)" 
                                            class="text-red-600 hover:text-red-900">
                                        <?php echo $lang['delete']; ?>
                                    </button>
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
                <?php foreach ($users as $user): ?>
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3 pb-3 border-b">
                        <input type="checkbox" class="row-checkbox w-5 h-5" value="<?php echo $user['id']; ?>" onchange="updateBulkButtons()" <?php echo $user['id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <div class="flex gap-3">
                            <a href="edit.php?id=<?php echo $user['id']; ?>" class="text-green-600 hover:text-green-900 text-sm font-medium">ویرایش</a>
                            <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="text-red-600 hover:text-red-900 text-sm font-medium">حذف</button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">نام کاربری:</span>
                            <span class="text-sm font-semibold text-blue-600"><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">نام کامل:</span>
                            <span class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($user['full_name']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">ایمیل:</span>
                            <span class="text-sm text-gray-900"><?php echo htmlspecialchars($user['email'] ?: '-'); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">نقش:</span>
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                <?php echo $lang[$user['role']]; ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">وضعیت:</span>
                            <span class="px-2 py-1 text-xs rounded-full <?php echo $user['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $user['is_active'] ? $lang['active'] : $lang['inactive']; ?>
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

<script>
function deleteUser(id) {
    if (!confirm('<?php echo $lang['delete_confirm']; ?>')) {
        return;
    }
    
    fetch(`<?php echo BASE_URL; ?>/api/users/delete.php?id=${id}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('<?php echo $lang['delete_success']; ?>', 'success');
            location.reload();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        showToast('<?php echo $lang['error_occurred']; ?>', 'error');
    });
}
</script>

<?php include '../includes/footer.php'; ?>
