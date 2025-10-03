<?php
require_once '../config/config.php';

$medicines = fetchAll("
    SELECT * FROM medicines 
    WHERE is_active = 1 
    ORDER BY stock_quantity ASC
");

$lowStock = fetchAll("
    SELECT * FROM medicines 
    WHERE is_active = 1 AND stock_quantity <= min_stock_level
    ORDER BY stock_quantity ASC
");

$expiringSoon = fetchAll("
    SELECT * FROM medicines 
    WHERE is_active = 1 AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    ORDER BY expiry_date ASC
");

include '../includes/header.php';
?>

<div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-800">گزارش موجودی دارو</h1>

    <!-- آمار کلی -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-50 rounded-lg p-6">
            <p class="text-sm text-blue-600 mb-2">کل داروها</p>
            <p class="text-2xl font-bold text-blue-700"><?php echo count($medicines); ?></p>
        </div>
        <div class="bg-red-50 rounded-lg p-6">
            <p class="text-sm text-red-600 mb-2">موجودی کم</p>
            <p class="text-2xl font-bold text-red-700"><?php echo count($lowStock); ?></p>
        </div>
        <div class="bg-yellow-50 rounded-lg p-6">
            <p class="text-sm text-yellow-600 mb-2">رو به انقضا</p>
            <p class="text-2xl font-bold text-yellow-700"><?php echo count($expiringSoon); ?></p>
        </div>
    </div>

    <!-- موجودی کم -->
    <?php if ($lowStock): ?>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-bold mb-4 text-red-600">⚠️ داروهای با موجودی کم</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">نام دارو</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">موجودی فعلی</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">حداقل موجودی</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">قیمت خرید</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($lowStock as $m): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($m['medicine_name']); ?></td>
                        <td class="px-4 py-3 text-sm text-red-600 font-bold"><?php echo $m['stock_quantity']; ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo $m['min_stock_level']; ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo formatCurrency($m['purchase_price']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- رو به انقضا -->
    <?php if ($expiringSoon): ?>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-bold mb-4 text-yellow-600">⏰ داروهای رو به انقضا</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">نام دارو</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">موجودی</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاریخ انقضا</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">روز باقیمانده</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($expiringSoon as $m): 
                        $daysLeft = floor((strtotime($m['expiry_date']) - time()) / 86400);
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($m['medicine_name']); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo $m['stock_quantity']; ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo $m['expiry_date']; ?></td>
                        <td class="px-4 py-3 text-sm <?php echo $daysLeft < 7 ? 'text-red-600 font-bold' : 'text-yellow-600'; ?>">
                            <?php echo $daysLeft; ?> روز
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- تمام داروها -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-bold mb-4">لیست کامل موجودی</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">کد</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">نام دارو</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">موجودی</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">قیمت خرید</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">قیمت فروش</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاریخ انقضا</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($medicines as $m): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo $m['medicine_code']; ?></td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($m['medicine_name']); ?></td>
                        <td class="px-4 py-3 text-sm <?php echo $m['stock_quantity'] <= $m['min_stock_level'] ? 'text-red-600 font-bold' : 'text-gray-900'; ?>">
                            <?php echo $m['stock_quantity']; ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo formatCurrency($m['purchase_price']); ?></td>
                        <td class="px-4 py-3 text-sm text-green-600"><?php echo formatCurrency($m['sale_price']); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo $m['expiry_date'] ?: '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
