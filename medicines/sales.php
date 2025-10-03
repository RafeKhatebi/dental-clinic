<?php
require_once '../config/config.php';
include '../includes/header.php';

// Get all medicine sales
$sales = fetchAll("
    SELECT m.id, m.sale_code, m.sale_date, m.sale_total_price as final_amount, 
           pay.payment_method,
           CASE WHEN m.sale_patient_id IS NOT NULL THEN CONCAT(p.first_name, ' ', p.last_name) ELSE 'مشتری' END as customer
    FROM medicines m
    LEFT JOIN patients p ON m.sale_patient_id = p.id
    LEFT JOIN payments pay ON pay.medicine_id = m.id AND pay.payment_type = 'medicine'
    WHERE m.sale_date IS NOT NULL
    GROUP BY m.id
    ORDER BY m.sale_date DESC, m.created_at DESC
");
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['medicine_sales']; ?></h1>
        <a href="add_sale.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
            + <?php echo $lang['add_sale']; ?>
        </a>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <?php if (empty($sales)): ?>
            <div class="p-8 text-center text-gray-500">
                <?php echo $lang['no_data']; ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['sale_code']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['customer_name']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['sale_date']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['final_price']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['payment_method']; ?></th>
                            <th class="px-6 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($sales as $sale): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                <?php echo $sale['sale_code']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($sale['customer']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo formatDate($sale['sale_date']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                <?php echo formatCurrency($sale['final_amount']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 text-xs rounded-full <?php 
                                    echo $sale['payment_method'] === 'cash' ? 'bg-green-100 text-green-800' : 
                                        ($sale['payment_method'] === 'installment' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); 
                                ?>">
                                    <?php echo $lang[$sale['payment_method']]; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="view_sale.php?id=<?php echo $sale['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <?php echo $lang['view']; ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
