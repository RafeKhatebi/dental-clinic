// Mobile Helper Functions
(function() {
    'use strict';
    
    // بستن Sidebar در موبایل با کلیک بیرون
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.getElementById('mobile-menu-toggle');
    
    if (mobileToggle && sidebar) {
        // باز کردن
        mobileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.add('mobile-open');
        });
        
        // بستن با کلیک بیرون
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 768 && 
                sidebar.classList.contains('mobile-open') && 
                !sidebar.contains(e.target) && 
                !mobileToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
        
        // بستن با کلیک روی لینکها
        sidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('mobile-open');
                }
            });
        });
    }
    
    // Export to Excel - بهبود برای موبایل
    window.exportToExcel = function(tableId, filename) {
        const table = document.getElementById(tableId);
        if (!table) return;
        
        // حذف ستون checkbox
        const rows = Array.from(table.querySelectorAll('tr')).map(row => {
            const cells = Array.from(row.querySelectorAll('th, td'));
            return cells.filter((_, i) => i !== 0); // حذف اولین ستون
        });
        
        let csv = rows.map(row => 
            row.map(cell => `"${cell.textContent.trim()}"`).join(',')
        ).join('\n');
        
        // BOM برای فارسی
        csv = '\uFEFF' + csv;
        
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `${filename}_${new Date().getTime()}.csv`;
        link.click();
        
        showToast('فایل Excel دانلود شد', 'success');
    };
    
    // تشخیص موبایل
    window.isMobile = function() {
        return window.innerWidth < 768;
    };
    
    // Pagination Mobile
    window.renderMobilePagination = function(current, total) {
        if (total <= 1) return '';
        
        const prev = current > 1 ? current - 1 : 1;
        const next = current < total ? current + 1 : total;
        
        return `
            <div class="pagination-mobile flex items-center justify-between p-4 bg-white border-t">
                <a href="?page=${prev}" class="px-4 py-2 bg-gray-200 rounded ${current === 1 ? 'opacity-50 pointer-events-none' : ''}">
                    قبلی
                </a>
                <span class="text-sm text-gray-600">
                    صفحه ${current} از ${total}
                </span>
                <a href="?page=${next}" class="px-4 py-2 bg-gray-200 rounded ${current === total ? 'opacity-50 pointer-events-none' : ''}">
                    بعدی
                </a>
            </div>
        `;
    };
    
})();
