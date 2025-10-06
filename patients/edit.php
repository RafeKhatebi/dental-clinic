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

include '../includes/header.php';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $lang['edit_patient']; ?></h1>
        <a href="view.php?id=<?php echo $patientId; ?>" class="text-gray-600 hover:text-gray-800">
            ← <?php echo $lang['back']; ?>
        </a>
    </div>

    <!-- Patient Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="patient-form" class="space-y-6">
            <input type="hidden" name="id" value="<?php echo $patientId; ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- First Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['first_name']; ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($patient['first_name']); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Last Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['last_name']; ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($patient['last_name']); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Age -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['age']; ?>
                    </label>
                    <input type="number" name="age" value="<?php echo $patient['age']; ?>" min="0" max="150"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['gender']; ?>
                    </label>
                    <select name="gender"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">-- <?php echo $lang['gender']; ?> --</option>
                        <option value="male" <?php echo $patient['gender'] === 'male' ? 'selected' : ''; ?>><?php echo $lang['male']; ?></option>
                        <option value="female" <?php echo $patient['gender'] === 'female' ? 'selected' : ''; ?>><?php echo $lang['female']; ?></option>
                    </select>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['phone']; ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($patient['phone']); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo $lang['email']; ?>
                    </label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($patient['email']); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo $lang['address']; ?>
                </label>
                <textarea name="address" rows="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"><?php echo htmlspecialchars($patient['address']); ?></textarea>
            </div>

            <!-- Medical History -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo $lang['medical_history']; ?>
                </label>
                <textarea name="medical_history" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"><?php echo htmlspecialchars($patient['medical_history']); ?></textarea>
            </div>

            <!-- Allergies -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo $lang['allergies']; ?>
                </label>
                <textarea name="allergies" rows="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"><?php echo htmlspecialchars($patient['allergies']); ?></textarea>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo $lang['notes']; ?>
                </label>
                <textarea name="notes" rows="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"><?php echo htmlspecialchars($patient['notes']); ?></textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4">
                <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['save']; ?>
                </button>
                <a href="view.php?id=<?php echo $patientId; ?>" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                    <?php echo $lang['cancel']; ?>
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('patient-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>/api/patients/update.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('<?php echo $lang['update_success']; ?>', 'success');
            setTimeout(() => window.location.href = 'view.php?id=<?php echo $patientId; ?>', 1000);
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        showToast('<?php echo $lang['error_occurred']; ?>', 'error');
    }
});
</script>

<?php include '../includes/footer.php'; ?>
