<?php
require_once '../config/config.php';

if (!hasRole(['admin', 'dentist'])) {
    redirect('/dashboard.php');
}

include '../includes/header.php';
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['add_service']; ?></h1>
        <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
            <?php echo $lang['back']; ?>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="service-form" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['service_name']; ?> (فارسی) *
                    </label>
                    <input type="text" name="service_name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['service_name']; ?> (English)
                    </label>
                    <input type="text" name="service_name_en"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['category']; ?>
                    </label>
                    <input type="text" name="category"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['base_price']; ?> *
                    </label>
                    <input type="number" name="base_price" step="0.01" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo $lang['description']; ?>
                </label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"></textarea>
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
document.getElementById('service-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>/api/services/create.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('<?php echo $lang['save_success']; ?>');
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
