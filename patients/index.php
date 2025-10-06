<?php
require_once '../config/config.php';
include '../includes/header.php';

// Get all patients with pagination and filters
$search = $_GET['search'] ?? '';
$gender = $_GET['gender'] ?? '';
$ageFrom = $_GET['age_from'] ?? '';
$ageTo = $_GET['age_to'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';

$whereClauses = [];
$params = [];

if (!empty($search)) {
    $whereClauses[] = "(patient_code LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR phone LIKE ?)";
    $searchParam = "%$search%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
}

if (!empty($gender)) {
    $whereClauses[] = "gender = ?";
    $params[] = $gender;
}

if (!empty($ageFrom)) {
    $whereClauses[] = "age >= ?";
    $params[] = $ageFrom;
}

if (!empty($ageTo)) {
    $whereClauses[] = "age <= ?";
    $params[] = $ageTo;
}

if (!empty($dateFrom)) {
    $whereClauses[] = "DATE(created_at) >= ?";
    $params[] = $dateFrom;
}

if (!empty($dateTo)) {
    $whereClauses[] = "DATE(created_at) <= ?";
    $params[] = $dateTo;
}

$whereClause = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$totalRecords = fetchOne("SELECT COUNT(*) as count FROM patients $whereClause", $params)['count'];
$pagination = getPagination($totalRecords, 20);
$patients = fetchAll("SELECT * FROM patients $whereClause ORDER BY created_at DESC LIMIT {$pagination['perPage']} OFFSET {$pagination['offset']}", $params);
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['patient_list']; ?></h1>
        <div class="flex gap-2">
            <button onclick="bulkAction('delete')" id="bulkDelete" class="hidden bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                🗑 حذف
            </button>
            <button onclick="exportToExcel('patientsTable', 'patients')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                📊 Excel
            </button>
            <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                + <?php echo $lang['add_patient']; ?>
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
                <?php if (!empty($search) || !empty($gender) || !empty($ageFrom) || !empty($ageTo) || !empty($dateFrom) || !empty($dateTo)): ?>
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['cancel']; ?>
                </a>
                <?php endif; ?>
            </div>
            
            <div id="advFilters" class="<?php echo (!empty($gender) || !empty($ageFrom) || !empty($ageTo) || !empty($dateFrom) || !empty($dateTo)) ? '' : 'hidden'; ?> grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">جنسیت</label>
                    <select name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">همه</option>
                        <option value="male" <?php echo $gender === 'male' ? 'selected' : ''; ?>>مرد</option>
                        <option value="female" <?php echo $gender === 'female' ? 'selected' : ''; ?>>زن</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">سن از</label>
                    <input type="number" name="age_from" value="<?php echo htmlspecialchars($ageFrom); ?>" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" min="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">سن تا</label>
                    <input type="number" name="age_to" value="<?php echo htmlspecialchars($ageTo); ?>" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" min="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">تاریخ ثبت از</label>
                    <input type="date" name="date_from" value="<?php echo htmlspecialchars($dateFrom); ?>" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">تاریخ ثبت تا</label>
                    <input type="date" name="date_to" value="<?php echo htmlspecialchars($dateTo); ?>" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>
        </form>
    </div>

    <!-- Patients Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <?php if (empty($patients)): ?>
            <div class="p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">بیماری یافت نشد</h3>
                <p class="text-gray-500 mb-4"><?php echo empty($search) ? 'هنوز بیماری ثبت نشده است' : 'بیماری با این مشخصات یافت نشد'; ?></p>
                <?php if (empty($search)): ?>
                <a href="add.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    + افزودن اولین بیمار
                </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table id="patientsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" class="w-4 h-4 cursor-pointer">
                            </th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['patient_code']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['full_name']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['age']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['gender']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['phone']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($patients as $patient): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" class="row-checkbox" value="<?php echo $patient['id']; ?>" onchange="updateBulkButtons()" class="w-4 h-4 cursor-pointer">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                <?php echo $patient['patient_code']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $patient['age'] ?? '-'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $patient['gender'] ? $lang[$patient['gender']] : '-'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $patient['phone']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <a href="view.php?id=<?php echo $patient['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <?php echo $lang['view']; ?>
                                    </a>
                                    <a href="edit.php?id=<?php echo $patient['id']; ?>" 
                                       class="text-green-600 hover:text-green-900">
                                        <?php echo $lang['edit']; ?>
                                    </a>
                                    <button onclick="deletePatient(<?php echo $patient['id']; ?>)" 
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
            <?php echo renderPagination($pagination); ?>
        <?php endif; ?>
    </div>
</div>

<script>
addBreadcrumb('بیماران');

async function deletePatient(id) {
    const confirmed = await confirm2('آیا از حذف این بیمار اطمینان دارید؟', 'حذف بیمار');
    if (!confirmed) return;
    
    showLoading();
    try {
        const response = await fetch(`<?php echo BASE_URL; ?>/api/patients/delete.php?id=${id}`, {
            method: 'DELETE'
        });
        const data = await response.json();
        
        if (data.success) {
            showToast('بیمار با موفقیت حذف شد', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            hideLoading();
            showToast(data.message, 'error');
        }
    } catch (error) {
        hideLoading();
        showToast('خطایی رخ داده است', 'error');
    }
}
</script>

<?php include '../includes/footer.php'; ?>
