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

// Get all active services
$services = fetchAll("SELECT * FROM services WHERE is_active = 1 ORDER BY service_name");

// Get all dentists
$dentists = fetchAll("SELECT id, full_name FROM users WHERE role = 'dentist' AND is_active = 1");

include '../includes/header.php';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['provide_service']; ?></h1>
        <a href="../patients/view.php?id=<?php echo $patientId; ?>" class="text-gray-600 hover:text-gray-800">
            ← <?php echo $lang['back']; ?>
        </a>
    </div>

    <!-- Patient Info -->
    <div class="bg-blue-50 rounded-lg p-4">
        <p class="text-sm text-blue-600 mb-1"><?php echo $lang['patient_code']; ?>: <strong><?php echo $patient['patient_code']; ?></strong></p>
        <p class="text-lg font-semibold text-blue-800"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></p>
    </div>

    <!-- Service Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="service-form" class="space-y-6">
            <input type="hidden" name="patient_id" value="<?php echo $patientId; ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Service -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['service_name']; ?> <span class="text-red-500">*</span>
                    </label>
                    <select name="service_id" id="service_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">-- <?php echo $lang['service_name']; ?> --</option>
                        <?php foreach ($services as $service): ?>
                        <option value="<?php echo $service['id']; ?>" data-price="<?php echo $service['base_price']; ?>">
                            <?php echo htmlspecialchars($service['service_name']); ?> - <?php echo formatCurrency($service['base_price']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Dentist -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['dentist']; ?> <span class="text-red-500">*</span>
                    </label>
                    <select name="dentist_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">-- <?php echo $lang['dentist']; ?> --</option>
                        <?php foreach ($dentists as $dentist): ?>
                        <option value="<?php echo $dentist['id']; ?>" <?php echo $dentist['id'] == $_SESSION['user_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dentist['full_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Service Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['service_date']; ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="service_date" value="<?php echo date('Y-m-d'); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Tooth Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['tooth_number']; ?>
                    </label>
                    <input type="text" name="tooth_number"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['quantity']; ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Unit Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['unit_price']; ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="unit_price" id="unit_price" min="0" step="1000" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Discount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['discount']; ?>
                    </label>
                    <input type="number" name="discount" id="discount" value="0" min="0" step="1000"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Final Price (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['final_price']; ?>
                    </label>
                    <input type="number" name="final_price" id="final_price" readonly
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 outline-none">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo $lang['notes']; ?>
                </label>
                <textarea name="notes" rows="3"
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
// Auto-fill unit price when service is selected
document.getElementById('service_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const price = selectedOption.getAttribute('data-price');
    document.getElementById('unit_price').value = price || 0;
    calculateFinalPrice();
});

// Calculate final price
function calculateFinalPrice() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    
    const totalPrice = quantity * unitPrice;
    const finalPrice = totalPrice - discount;
    
    document.getElementById('final_price').value = finalPrice;
}

// Add event listeners for calculation
document.getElementById('quantity').addEventListener('input', calculateFinalPrice);
document.getElementById('unit_price').addEventListener('input', calculateFinalPrice);
document.getElementById('discount').addEventListener('input', calculateFinalPrice);

// Form submission
document.getElementById('service-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>/api/services/provide.php', {
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
