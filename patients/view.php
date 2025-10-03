<?php
require_once '../config/config.php';

$patientId = intval($_GET['id'] ?? 0);

if (empty($patientId)) {
    redirect('/patients/index.php');
}

$patient = fetchOne("SELECT * FROM patients WHERE id = ?", [$patientId]);

if (!$patient) {
    redirect('/patients/index.php');
}

// Get patient services
$services = fetchAll("
    SELECT s.*, u.full_name as dentist_name
    FROM services s
    LEFT JOIN users u ON s.dentist_id = u.id
    WHERE s.patient_id = ? AND s.status != 'template'
    ORDER BY s.service_date DESC
", [$patientId]);

// Get patient payments
$payments = fetchAll("
    SELECT p.*, s.service_date
    FROM payments p
    LEFT JOIN services s ON p.service_id = s.id
    WHERE p.patient_id = ? AND p.payment_type = 'service' AND p.payment_method != 'installment'
    ORDER BY p.payment_date DESC
", [$patientId]);

// Calculate totals
$totalServices = fetchOne("SELECT SUM(final_price) as total FROM services WHERE patient_id = ? AND status != 'template'", [$patientId])['total'] ?? 0;
$totalPaid = fetchOne("SELECT SUM(amount) as total FROM payments WHERE patient_id = ?", [$patientId])['total'] ?? 0;
$totalDebt = $totalServices - $totalPaid;

// Get installments
$installments = fetchAll("
    SELECT *
    FROM payments
    WHERE patient_id = ? AND payment_method = 'installment'
    ORDER BY due_date ASC
", [$patientId]);

include '../includes/header.php';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['patient_details']; ?></h1>
        <div class="flex gap-2">
            <a href="edit.php?id=<?php echo $patientId; ?>" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
                <?php echo $lang['edit']; ?>
            </a>
            <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                <?php echo $lang['back']; ?>
            </a>
        </div>
    </div>

    <!-- Patient Info Card -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['patient_code']; ?></p>
                <p class="text-lg font-semibold text-gray-800"><?php echo $patient['patient_code']; ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['full_name']; ?></p>
                <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['phone']; ?></p>
                <p class="text-lg font-semibold text-gray-800"><?php echo $patient['phone']; ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['age']; ?></p>
                <p class="text-lg font-semibold text-gray-800"><?php echo $patient['age'] ?? '-'; ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['gender']; ?></p>
                <p class="text-lg font-semibold text-gray-800"><?php echo $patient['gender'] ? $lang[$patient['gender']] : '-'; ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600"><?php echo $lang['email']; ?></p>
                <p class="text-lg font-semibold text-gray-800"><?php echo $patient['email'] ?: '-'; ?></p>
            </div>
        </div>

        <?php if (!empty($patient['address'])): ?>
        <div class="mt-4 pt-4 border-t">
            <p class="text-sm text-gray-600"><?php echo $lang['address']; ?></p>
            <p class="text-gray-800"><?php echo htmlspecialchars($patient['address']); ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($patient['medical_history'])): ?>
        <div class="mt-4 pt-4 border-t">
            <p class="text-sm text-gray-600"><?php echo $lang['medical_history']; ?></p>
            <p class="text-gray-800"><?php echo htmlspecialchars($patient['medical_history']); ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($patient['allergies'])): ?>
        <div class="mt-4 pt-4 border-t">
            <p class="text-sm text-gray-600"><?php echo $lang['allergies']; ?></p>
            <p class="text-red-600 font-medium"><?php echo htmlspecialchars($patient['allergies']); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Financial Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-50 rounded-lg p-6">
            <p class="text-sm text-blue-600 mb-2"><?php echo $lang['total']; ?> <?php echo $lang['services']; ?></p>
            <p class="text-2xl font-bold text-blue-700"><?php echo formatCurrency($totalServices); ?></p>
        </div>
        <div class="bg-green-50 rounded-lg p-6">
            <p class="text-sm text-green-600 mb-2"><?php echo $lang['paid_amount']; ?></p>
            <p class="text-2xl font-bold text-green-700"><?php echo formatCurrency($totalPaid); ?></p>
        </div>
        <div class="bg-red-50 rounded-lg p-6">
            <p class="text-sm text-red-600 mb-2"><?php echo $lang['remaining_amount']; ?></p>
            <p class="text-2xl font-bold text-red-700"><?php echo formatCurrency($totalDebt); ?></p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="switchTab('services')" id="tab-services" class="tab-button active px-6 py-3 border-b-2 border-blue-500 text-blue-600 font-medium">
                    <?php echo $lang['patient_services']; ?>
                </button>
                <button onclick="switchTab('payments')" id="tab-payments" class="tab-button px-6 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium">
                    <?php echo $lang['payments']; ?>
                </button>
                <button onclick="switchTab('installments')" id="tab-installments" class="tab-button px-6 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium">
                    <?php echo $lang['installments']; ?>
                </button>
            </nav>
        </div>

        <!-- Services Tab -->
        <div id="content-services" class="tab-content p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold"><?php echo $lang['patient_services']; ?></h3>
                <a href="../services/provide.php?patient_id=<?php echo $patientId; ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition text-sm">
                    + <?php echo $lang['provide_service']; ?>
                </a>
            </div>
            <?php if (empty($services)): ?>
                <p class="text-gray-500 text-center py-8"><?php echo $lang['no_data']; ?></p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['service_date']; ?></th>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['service_name']; ?></th>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['dentist']; ?></th>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['tooth_number']; ?></th>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['final_price']; ?></th>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase">عملیات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($services as $service): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo formatDate($service['service_date']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($service['service_name']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($service['dentist_name']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo $service['tooth_number'] ?: '-'; ?></td>
                                <td class="px-4 py-3 text-sm font-semibold text-green-600"><?php echo formatCurrency($service['final_price']); ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="../reports/invoices/service_invoice.php?id=<?php echo $service['id']; ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        🖨️ فاکتور
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Payments Tab -->
        <div id="content-payments" class="tab-content hidden p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold"><?php echo $lang['payments']; ?></h3>
                <a href="../payments/add.php?patient_id=<?php echo $patientId; ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition text-sm">
                    + <?php echo $lang['add_payment']; ?>
                </a>
            </div>
            <?php if (empty($payments)): ?>
                <p class="text-gray-500 text-center py-8"><?php echo $lang['no_data']; ?></p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['payment_date']; ?></th>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['payment_method']; ?></th>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['amount']; ?></th>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['notes']; ?></th>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase">عملیات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($payments as $payment): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo formatDate($payment['payment_date']); ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full <?php 
                                        echo $payment['payment_method'] === 'cash' ? 'bg-green-100 text-green-800' : 
                                            ($payment['payment_method'] === 'installment' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); 
                                    ?>">
                                        <?php echo $lang[$payment['payment_method']]; ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-green-600"><?php echo formatCurrency($payment['amount']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($payment['notes'] ?: '-'); ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="../reports/invoices/payment_receipt.php?id=<?php echo $payment['id']; ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        🖨️ رسید
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Installments Tab -->
        <div id="content-installments" class="tab-content hidden p-6">
            <h3 class="text-lg font-semibold mb-4"><?php echo $lang['installments']; ?></h3>
            <?php if (empty($installments)): ?>
                <p class="text-gray-500 text-center py-8"><?php echo $lang['no_data']; ?></p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['installment_number']; ?></th>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['due_date']; ?></th>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['amount']; ?></th>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['paid_amount']; ?></th>
                                <th class="px-4 py-3 text-<?php echo $current_lang === 'fa' ? 'right' : 'left'; ?> text-xs font-medium text-gray-500 uppercase"><?php echo $lang['status']; ?></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($installments as $installment): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo $installment['installment_number']; ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo formatDate($installment['due_date']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo formatCurrency($installment['amount']); ?></td>
                                <td class="px-4 py-3 text-sm font-semibold text-green-600"><?php echo formatCurrency($installment['paid_amount']); ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full <?php 
                                        echo $installment['status'] === 'paid' ? 'bg-green-100 text-green-800' : 
                                            ($installment['status'] === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); 
                                    ?>">
                                        <?php echo $lang[$installment['status']]; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active class to selected tab button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500');
}
</script>

<?php include '../includes/footer.php'; ?>
