<?php
require_once '../config/config.php';

if (!hasRole('admin')) {
    redirect('/dashboard.php');
}

$partnerId = intval($_GET['id'] ?? 0);
if (empty($partnerId)) {
    redirect('/partners/index.php');
}

$partner = fetchOne("SELECT * FROM documents WHERE id = ? AND document_type = 'partner_share'", [$partnerId]);
if (!$partner) {
    redirect('/partners/index.php');
}

// Calculate total revenue
$totalRevenue = fetchOne("SELECT SUM(final_price) as total FROM services WHERE status = 'completed' AND service_date BETWEEN ? AND ?", 
    [$partner['period_start'], $partner['period_end'] ?? date('Y-m-d')])['total'] ?? 0;

$partnerShare = ($totalRevenue * $partner['share_percentage']) / 100;

include '../includes/header.php';
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['edit_partner']; ?></h1>
        <div class="flex gap-2">
            <a href="edit.php?id=<?php echo $partnerId; ?>" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
                <?php echo $lang['edit']; ?>
            </a>
            <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                <?php echo $lang['back']; ?>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['partner_name']; ?></p>
                <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($partner['partner_name']); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['phone']; ?></p>
                <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($partner['partner_phone']); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['email']; ?></p>
                <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($partner['partner_email'] ?? '-'); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['share_percentage']; ?></p>
                <p class="text-lg font-semibold text-gray-800"><?php echo $partner['share_percentage']; ?>%</p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['period_start']; ?></p>
                <p class="text-lg font-semibold text-gray-800"><?php echo formatDate($partner['period_start']); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['period_end']; ?></p>
                <p class="text-lg font-semibold text-gray-800"><?php echo $partner['period_end'] ? formatDate($partner['period_end']) : $lang['active']; ?></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-blue-50 rounded-lg p-6">
            <p class="text-sm text-blue-600 mb-2"><?php echo $lang['total_revenue']; ?></p>
            <p class="text-2xl font-bold text-blue-700"><?php echo formatCurrency($totalRevenue); ?></p>
        </div>
        <div class="bg-green-50 rounded-lg p-6">
            <p class="text-sm text-green-600 mb-2"><?php echo $lang['share_amount']; ?></p>
            <p class="text-2xl font-bold text-green-700"><?php echo formatCurrency($partnerShare); ?></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
