<?php
require_once '../config/config.php';

if (!hasRole('admin')) {
    redirect('/dashboard.php');
}

include '../includes/header.php';

// Get all backups
$backups = fetchAll("SELECT d.*, u.full_name as created_by_name FROM documents d LEFT JOIN users u ON d.created_by = u.id WHERE d.document_type = 'backup' ORDER BY d.created_at DESC");
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['backup']; ?></h1>
        <button onclick="createBackup()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
            + <?php echo $lang['create_backup']; ?>
        </button>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm text-blue-800">
            <strong><?php echo $lang['notes']; ?>:</strong> 
            <?php echo $current_lang === 'fa' ? 'پشتیبان‌گیری از دیتابیس SQLite انجام می‌شود. فایل پشتیبان در پوشه backups ذخیره می‌گردد.' : 'Database backup will be created in the backups folder.'; ?>
        </p>
    </div>

    <!-- Backups Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <?php if (empty($backups)): ?>
            <div class="p-8 text-center text-gray-500">
                <?php echo $lang['no_data']; ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['backup_name']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['backup_size']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['date']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($backups as $backup): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($backup['title']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $backup['file_size'] ? number_format($backup['file_size'] / 1024, 2) . ' KB' : '-'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo date('Y-m-d H:i:s', strtotime($backup['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <a href="<?php echo BASE_URL; ?>/api/backup/download.php?id=<?php echo $backup['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <?php echo $lang['download']; ?>
                                    </a>
                                    <button onclick="deleteBackup(<?php echo $backup['id']; ?>)" 
                                            class="text-red-600 hover:text-red-900">
                                        <?php echo $lang['delete']; ?>
                                    </button>
                                </div>
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
async function createBackup() {
    if (!confirm('<?php echo $current_lang === 'fa' ? 'آیا از ایجاد پشتیبان اطمینان دارید؟' : 'Are you sure you want to create a backup?'; ?>')) {
        return;
    }
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>/api/backup/create.php', {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('<?php echo $lang['backup_success']; ?>');
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('<?php echo $lang['error_occurred']; ?>');
    }
}

async function deleteBackup(id) {
    if (!confirm('<?php echo $lang['delete_confirm']; ?>')) {
        return;
    }
    
    try {
        const response = await fetch(`<?php echo BASE_URL; ?>/api/backup/delete.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('<?php echo $lang['delete_success']; ?>');
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('<?php echo $lang['error_occurred']; ?>');
    }
}
</script>

<?php include '../includes/footer.php'; ?>
