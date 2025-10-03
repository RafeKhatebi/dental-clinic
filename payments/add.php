<?php
require_once '../config/config.php';

$patientId = intval($_GET['patient_id'] ?? 0);

if (empty($patientId)) {
    redirect('/patients/index.php');
}

$patient = fetchOne("SELECT * FROM patients WHERE id = ?", [$patientId]);

if (!$patient) {
    redirect('/patients/index.php');
}

// Get unpaid services
$unpaidServices = fetchAll("
    SELECT ps.*, s.service_name,
           COALESCE((SELECT SUM(amount) FROM payments WHERE patient_service_id = ps.id), 0) as paid
    FROM patient_services ps
    JOIN services s ON ps.service_id = s.id
    WHERE ps.patient_id = ?
    HAVING ps.final_price > paid
    ORDER BY ps.service_date DESC
", [$patientId]);

include '../includes/header.php';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['add_payment']; ?></h1>
        <a href="../patients/view.php?id=<?php echo $patientId; ?>" class="text-gray-600 hover:text-gray-800">
            ← <?php echo $lang['back']; ?>
        </a>
    </div>

    <!-- Patient Info -->
    <div class="bg-blue-50 rounded-lg p-4">
        <p class="text-sm text-blue-600 mb-1"><?php echo $lang['patient_code']; ?>: <strong><?php echo $patient['patient_code']; ?></strong></p>
        <p class="text-lg font-semibold text-blue-800"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></p>
    </div>

    <!-- Payment Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="payment-form" class="space-y-6">
            <input type="hidden" name="patient_id" value="<?php echo $patientId; ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Service (Optional) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['service_name']; ?> (<?php echo $lang['notes']; ?>)
                    </label>
                    <select name="patient_service_id" id="patient_service_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">-- <?php echo $lang['service_name']; ?> --</option>
                        <?php foreach ($unpaidServices as $service): ?>
                        <?php 
                            $remaining = $service['final_price'] - $service['paid'];
                        ?>
                        <option value="<?php echo $service['id']; ?>" data-remaining="<?php echo $remaining; ?>">
                            <?php echo htmlspecialchars($service['service_name']); ?> - 
                            <?php echo formatDate($service['service_date']); ?> - 
                            <?php echo $lang['remaining_amount']; ?>: <?php echo formatCurrency($remaining); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['payment_method']; ?> <span class="text-red-500">*</span>
                    </label>
                    <select name="payment_method" id="payment_method" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="cash"><?php echo $lang['cash']; ?></option>
                        <option value="installment"><?php echo $lang['installment']; ?></option>
                        <option value="loan"><?php echo $lang['loan']; ?></option>
                    </select>
                </div>

                <!-- Payment Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['payment_date']; ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['amount']; ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="amount" id="amount" min="0" step="1000" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
            </div>

            <!-- Installment Details (shown only for installment payment) -->
            <div id="installment-details" class="hidden space-y-4 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-800"><?php echo $lang['installments']; ?></h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <?php echo $lang['installment_number']; ?>
                        </label>
                        <input type="number" name="installment_count" id="installment_count" min="2" value="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <?php echo $lang['due_date']; ?> (<?php echo $lang['first_name']; ?>)
                        </label>
                        <input type="date" name="first_due_date" id="first_due_date" value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo $lang['notes']; ?>
                </label>
                <textarea name="notes" rows="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"></textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4">
                <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['save']; ?>
                </button>
                <a href="../patients/view.php?id=<?php echo $patientId; ?>" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['cancel']; ?>
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-fill amount when service is selected
document.getElementById('patient_service_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const remaining = selectedOption.getAttribute('data-remaining');
    if (remaining) {
        document.getElementById('amount').value = remaining;
    }
});

// Show/hide installment details
document.getElementById('payment_method').addEventListener('change', function() {
    const installmentDetails = document.getElementById('installment-details');
    if (this.value === 'installment') {
        installmentDetails.classList.remove('hidden');
    } else {
        installmentDetails.classList.add('hidden');
    }
});

// Form submission
document.getElementById('payment-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>/api/payments/create.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('<?php echo $lang['save_success']; ?>');
            window.location.href = '../patients/view.php?id=<?php echo $patientId; ?>';
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('<?php echo $lang['error_occurred']; ?>');
    }
});
</script>

<?php include '../includes/footer.php'; ?>
