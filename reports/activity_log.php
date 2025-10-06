<?php
require_once '../config/config.php';
if (!hasRole('admin')) redirect('/dashboard.php');
include '../includes/header.php';

$filter = $_GET['filter'] ?? 'all';
$where = "record_type = 'activity_log'";
if ($filter !== 'all') $where .= " AND action = '$filter'";

$totalRecords = fetchOne("SELECT COUNT(*) as count FROM system WHERE $where")['count'];
$pagination = getPagination($totalRecords, 50);
$logs = fetchAll("SELECT s.*, u.full_name, u.username FROM system s LEFT JOIN users u ON s.user_id = u.id WHERE $where ORDER BY s.created_at DESC LIMIT {$pagination['perPage']} OFFSET {$pagination['offset']}");
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $current_lang === 'fa' ? 'گزارش فعالیتها' : 'Activity Log'; ?></h1>
        <select onchange="location.href='?filter='+this.value" class="px-4 py-2 border rounded-lg">
            <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>><?php echo $current_lang === 'fa' ? 'همه' : 'All'; ?></option>
            <option value="login" <?php echo $filter === 'login' ? 'selected' : ''; ?>><?php echo $current_lang === 'fa' ? 'ورود' : 'Login'; ?></option>
            <option value="create" <?php echo $filter === 'create' ? 'selected' : ''; ?>><?php echo $current_lang === 'fa' ? 'ایجاد' : 'Create'; ?></option>
            <option value="update" <?php echo $filter === 'update' ? 'selected' : ''; ?>><?php echo $current_lang === 'fa' ? 'ویرایش' : 'Update'; ?></option>
            <option value="delete" <?php echo $filter === 'delete' ? 'selected' : ''; ?>><?php echo $current_lang === 'fa' ? 'حذف' : 'Delete'; ?></option>
        </select>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500"><?php echo $current_lang === 'fa' ? 'کاربر' : 'User'; ?></th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500"><?php echo $current_lang === 'fa' ? 'عملیات' : 'Action'; ?></th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500"><?php echo $current_lang === 'fa' ? 'جدول' : 'Table'; ?></th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500"><?php echo $current_lang === 'fa' ? 'توضیحات' : 'Description'; ?></th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500"><?php echo $current_lang === 'fa' ? 'IP' : 'IP'; ?></th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500"><?php echo $current_lang === 'fa' ? 'زمان' : 'Time'; ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($logs as $log): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm"><?php echo htmlspecialchars($log['full_name'] ?? 'N/A'); ?></td>
                    <td class="px-6 py-4 text-sm"><span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800"><?php echo $log['action']; ?></span></td>
                    <td class="px-6 py-4 text-sm"><?php echo $log['table_name'] ?? '-'; ?></td>
                    <td class="px-6 py-4 text-sm"><?php echo htmlspecialchars($log['description'] ?? '-'); ?></td>
                    <td class="px-6 py-4 text-sm"><?php echo $log['ip_address']; ?></td>
                    <td class="px-6 py-4 text-sm"><?php echo formatDate($log['created_at'], 'Y-m-d H:i'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo renderPagination($pagination); ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
