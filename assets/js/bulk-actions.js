// Bulk Actions
function toggleSelectAll(checkbox) {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = checkbox.checked);
    updateBulkButtons();
}

function updateBulkButtons() {
    const selected = document.querySelectorAll('.row-checkbox:checked').length;
    const buttons = ['bulkActivate', 'bulkDeactivate', 'bulkDelete'];
    buttons.forEach(id => {
        const btn = document.getElementById(id);
        if (btn) btn.classList.toggle('hidden', selected === 0);
    });
}

async function bulkAction(action) {
    const selected = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) return;

    const messages = {
        activate: 'آیا از فعال کردن موارد انتخابی اطمینان دارید؟',
        deactivate: 'آیا از غیرفعال کردن موارد انتخابی اطمینان دارید؟',
        delete: 'آیا از حذف موارد انتخابی اطمینان دارید؟'
    };

    if (!await confirm2(messages[action], 'تایید عملیات')) return;

    showLoading();
    try {
        const response = await fetch(window.location.pathname.replace('index.php', 'bulk.php'), {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action, ids: selected})
        });

        const data = await response.json();
        hideLoading();
        
        if (data.success) {
            showToast(data.message || 'عملیات با موفقیت انجام شد', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'خطایی رخ داد', 'error');
        }
    } catch (error) {
        hideLoading();
        showToast('خطایی رخ داد', 'error');
    }
}
