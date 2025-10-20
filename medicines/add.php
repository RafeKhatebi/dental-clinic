<?php
require_once '../config/config.php';
include '../includes/header.php';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['add_medicine']; ?></h1>
        <a href="index.php" class="text-gray-600 hover:text-gray-800">
            ← <?php echo $lang['back']; ?>
        </a>
    </div>

    <!-- Medicine Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="medicine-form" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Medicine Name (Persian) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['medicine_name']; ?> (فارسی) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="medicine_name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Medicine Name (English) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['medicine_name']; ?> (English)
                    </label>
                    <input type="text" name="medicine_name_en"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['category']; ?>
                    </label>
                    <input type="text" name="category"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Manufacturer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['manufacturer']; ?>
                    </label>
                    <input type="text" name="manufacturer"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Unit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['unit']; ?> <span class="text-red-500">*</span>
                    </label>
                    <select name="unit" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="Box">Box</option>
                        <option value="Bottle">Bottle</option>
                        <option value="Tablet">Tablet</option>
                        <option value="Capsule">Capsule</option>
                        <option value="Vial">Vial</option>
                        <option value="Tube">Tube</option>
                        <option value="Piece">Piece</option>
                    </select>
                </div>

                <!-- Purchase Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['purchase_price']; ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="purchase_price" min="0" step="1000" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Sale Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['sale_price']; ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="sale_price" min="0" step="1000" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Initial Stock -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['stock_quantity']; ?>
                    </label>
                    <input type="number" name="stock_quantity" value="0" min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Min Stock Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['min_stock_level']; ?>
                    </label>
                    <input type="number" name="min_stock_level" value="10" min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Expiry Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['expiry_date']; ?>
                    </label>
                    <input type="date" name="expiry_date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo $lang['description']; ?>
                </label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"></textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['save']; ?>
                </button>
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['cancel']; ?>
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('medicine-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);

        try {
            const response = await fetch('<?php echo BASE_URL; ?>/api/medicines/create.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToast('<?php echo $lang['save_success']; ?>', 'success');
                setTimeout(() => window.location.href = 'index.php', 1000);
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            showToast('<?php echo $lang['error_occurred']; ?>', 'error');
        }
    });
</script>

<?php include '../includes/footer.php'; ?>