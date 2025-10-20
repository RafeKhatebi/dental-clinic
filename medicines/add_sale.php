<?php
require_once '../config/config.php';
include '../includes/header.php';

// Get all active medicines with stock
$medicines = fetchAll("SELECT * FROM medicines WHERE is_active = 1 AND stock_quantity > 0 ORDER BY medicine_name");

// Get all patients
$patients = fetchAll("SELECT id, patient_code, first_name, last_name FROM patients ORDER BY first_name, last_name");
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['add_sale']; ?></h1>
        <a href="sales.php" class="text-gray-600 hover:text-gray-800">
            ← <?php echo $lang['back']; ?>
        </a>
    </div>

    <!-- Sale Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="sale-form" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Patient (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['patient_code']; ?> (<?php echo $lang['notes']; ?>)
                    </label>
                    <select name="patient_id" id="patient_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">-- <?php echo $lang['patient_code']; ?> --</option>
                        <?php foreach ($patients as $patient): ?>
                            <option value="<?php echo $patient['id']; ?>">
                                <?php echo $patient['patient_code']; ?> -
                                <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Customer Name (if not patient) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['customer_name']; ?>
                    </label>
                    <input type="text" name="customer_name" id="customer_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Sale Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['sale_date']; ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="sale_date" value="<?php echo date('Y-m-d'); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['payment_method']; ?> <span class="text-red-500">*</span>
                    </label>
                    <select name="payment_method" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="cash"><?php echo $lang['cash']; ?></option>
                        <option value="installment"><?php echo $lang['installment']; ?></option>
                        <option value="loan"><?php echo $lang['loan']; ?></option>
                    </select>
                </div>
            </div>

            <!-- Sale Items -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800"><?php echo $lang['sale_items']; ?></h3>
                    <button type="button" onclick="addSaleItem()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition text-sm">
                        + <?php echo $lang['add']; ?>
                    </button>
                </div>

                <div id="sale-items" class="space-y-3">
                    <!-- Sale items will be added here -->
                </div>
            </div>

            <!-- Totals -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['total']; ?>
                    </label>
                    <input type="number" id="total_amount" readonly
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['discount']; ?>
                    </label>
                    <input type="number" name="discount" id="discount" value="0" min="0" step="1000"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['final_price']; ?>
                    </label>
                    <input type="number" id="final_amount" readonly
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white font-semibold text-green-600 outline-none">
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
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['save']; ?>
                </button>
                <a href="sales.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['cancel']; ?>
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const medicines = <?php echo json_encode($medicines); ?>;
    let itemCounter = 0;

    // Add initial item
    addSaleItem();

    function addSaleItem() {
        const container = document.getElementById('sale-items');
        const itemId = itemCounter++;

        const itemHtml = `
        <div class="sale-item grid grid-cols-12 gap-2 p-3 bg-white border border-gray-200 rounded-lg" data-item-id="${itemId}">
            <div class="col-span-5">
                <select name="items[${itemId}][medicine_id]" class="medicine-select w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required onchange="updateItemPrice(${itemId})">
                    <option value="">-- <?php echo $lang['medicine_name']; ?> --</option>
                    ${medicines.map(m => `<option value="${m.id}" data-price="${m.sale_price}" data-stock="${m.stock_quantity}">${m.medicine_name} (${m.stock_quantity} ${m.unit})</option>`).join('')}
                </select>
            </div>
            <div class="col-span-2">
                <input type="number" name="items[${itemId}][quantity]" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" min="1" value="1" required onchange="updateItemPrice(${itemId})">
            </div>
            <div class="col-span-2">
                <input type="number" name="items[${itemId}][unit_price]" class="price-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" min="0" step="1000" required onchange="updateItemPrice(${itemId})">
            </div>
            <div class="col-span-2">
                <input type="number" class="total-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-sm font-semibold" readonly>
            </div>
            <div class="col-span-1 flex items-center">
                <button type="button" onclick="removeSaleItem(${itemId})" class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;

        container.insertAdjacentHTML('beforeend', itemHtml);
    }

    function removeSaleItem(itemId) {
        const item = document.querySelector(`[data-item-id="${itemId}"]`);
        if (item) {
            item.remove();
            calculateTotals();
        }
    }

    function updateItemPrice(itemId) {
        const item = document.querySelector(`[data-item-id="${itemId}"]`);
        const select = item.querySelector('.medicine-select');
        const quantityInput = item.querySelector('.quantity-input');
        const priceInput = item.querySelector('.price-input');
        const totalInput = item.querySelector('.total-input');

        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        const stock = parseInt(selectedOption.getAttribute('data-stock') || 0);
        const quantity = parseInt(quantityInput.value) || 0;

        if (price && !priceInput.value) {
            priceInput.value = price;
        }

        if (quantity > stock) {
            showToast('<?php echo $lang['stock_quantity']; ?>: ' + stock, 'warning');
            quantityInput.value = stock;
        }

        const unitPrice = parseFloat(priceInput.value) || 0;
        const total = quantity * unitPrice;
        totalInput.value = total;

        calculateTotals();
    }

    function calculateTotals() {
        let total = 0;
        document.querySelectorAll('.total-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });

        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const finalAmount = total - discount;

        document.getElementById('total_amount').value = total;
        document.getElementById('final_amount').value = finalAmount;
    }

    document.getElementById('discount').addEventListener('input', calculateTotals);

    // Form submission
    document.getElementById('sale-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);

        // Validate at least one item
        if (document.querySelectorAll('.sale-item').length === 0) {
            showToast('<?php echo $lang['required_fields']; ?>', 'warning');
            return;
        }

        try {
            const response = await fetch('<?php echo BASE_URL; ?>/api/medicines/create_sale.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToast('<?php echo $lang['save_success']; ?>', 'success');
                setTimeout(() => window.location.href = 'sales.php', 1000);
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            showToast('<?php echo $lang['error_occurred']; ?>', 'error');
        }
    });
</script>

<?php include '../includes/footer.php'; ?>