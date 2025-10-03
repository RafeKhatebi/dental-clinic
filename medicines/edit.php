<?php
require_once '../config/config.php';

$medicineId = intval($_GET['id'] ?? 0);
if (empty($medicineId)) {
    redirect('/medicines/index.php');
}

$medicine = fetchOne("SELECT * FROM medicines WHERE id = ?", [$medicineId]);
if (!$medicine) {
    redirect('/medicines/index.php');
}

include '../includes/header.php';
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['edit_medicine']; ?></h1>
        <a href="index.php" class="text-gray-600 hover:text-gray-800">
            ← <?php echo $lang['back']; ?>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="medicine-form" class="space-y-6">
            <input type="hidden" name="id" value="<?php echo $medicine['id']; ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['medicine_name']; ?> (فارسی) *
                    </label>
                    <input type="text" name="medicine_name" value="<?php echo htmlspecialchars($medicine['medicine_name']); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['medicine_name']; ?> (English)
                    </label>
                    <input type="text" name="medicine_name_en" value="<?php echo htmlspecialchars($medicine['medicine_name_en'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['category']; ?>
                    </label>
                    <input type="text" name="category" value="<?php echo htmlspecialchars($medicine['category'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['manufacturer']; ?>
                    </label>
                    <input type="text" name="manufacturer" value="<?php echo htmlspecialchars($medicine['manufacturer'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['unit']; ?> *
                    </label>
                    <select name="unit" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="Box" <?php echo $medicine['unit'] === 'Box' ? 'selected' : ''; ?>>Box</option>
                        <option value="Bottle" <?php echo $medicine['unit'] === 'Bottle' ? 'selected' : ''; ?>>Bottle</option>
                        <option value="Tablet" <?php echo $medicine['unit'] === 'Tablet' ? 'selected' : ''; ?>>Tablet</option>
                        <option value="Capsule" <?php echo $medicine['unit'] === 'Capsule' ? 'selected' : ''; ?>>Capsule</option>
                        <option value="Vial" <?php echo $medicine['unit'] === 'Vial' ? 'selected' : ''; ?>>Vial</option>
                        <option value="Tube" <?php echo $medicine['unit'] === 'Tube' ? 'selected' : ''; ?>>Tube</option>
                        <option value="Piece" <?php echo $medicine['unit'] === 'Piece' ? 'selected' : ''; ?>>Piece</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['purchase_price']; ?> *
                    </label>
                    <input type="number" name="purchase_price" value="<?php echo $medicine['purchase_price']; ?>" min="0" step="1000" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['sale_price']; ?> *
                    </label>
                    <input type="number" name="sale_price" value="<?php echo $medicine['sale_price']; ?>" min="0" step="1000" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['stock_quantity']; ?>
                    </label>
                    <input type="number" name="stock_quantity" value="<?php echo $medicine['stock_quantity']; ?>" min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['min_stock_level']; ?>
                    </label>
                    <input type="number" name="min_stock_level" value="<?php echo $medicine['min_stock_level']; ?>" min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['expiry_date']; ?>
                    </label>
                    <input type="date" name="expiry_date" value="<?php echo $medicine['expiry_date'] ?? ''; ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo $lang['description']; ?>
                </label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"><?php echo htmlspecialchars($medicine['description'] ?? ''); ?></textarea>
            </div>

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
        const response = await fetch('<?php echo BASE_URL; ?>/api/medicines/update.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('<?php echo $lang['update_success']; ?>');
            window.location.href = 'index.php';
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('<?php echo $lang['error_occurred']; ?>');
    }
});
</script>

<?php include '../includes/footer.php'; ?>
