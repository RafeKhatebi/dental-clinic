            </main>
        </div>
    </div>

    <script>
        // Desktop sidebar toggle
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const showSidebar = document.getElementById('show-sidebar');
        const sidebar = document.getElementById('sidebar');
        const sidebarTitle = document.getElementById('sidebar-title');
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        
        if (sidebarToggle && showSidebar && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.add('!w-16');
                sidebarTitle.classList.add('hidden');
                sidebarLinks.forEach(link => {
                    const text = link.childNodes[link.childNodes.length - 1];
                    if (text.nodeType === 3) text.textContent = '';
                });
                sidebarToggle.classList.add('hidden');
                showSidebar.classList.remove('hidden');
            });
            
            showSidebar.addEventListener('click', () => {
                sidebar.classList.remove('!w-16');
                sidebarTitle.classList.remove('hidden');
                location.reload();
            });
        }

        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        
        if (mobileMenuToggle && sidebar) {
            mobileMenuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('fixed');
                sidebar.classList.toggle('inset-0');
                sidebar.classList.toggle('z-50');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', (e) => {
                if (window.innerWidth < 768 && !sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                    sidebar.classList.add('hidden');
                    sidebar.classList.remove('fixed', 'inset-0', 'z-50');
                }
            });
        }

        // User menu toggle
        document.getElementById('user-menu-button').addEventListener('click', () => {
            document.getElementById('user-menu').classList.toggle('hidden');
        });

        // Close user menu when clicking outside
        document.addEventListener('click', (e) => {
            const userMenu = document.getElementById('user-menu');
            const userMenuButton = document.getElementById('user-menu-button');
            
            if (!userMenuButton.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.add('hidden');
            }
        });

        // Handle language change
        const langLinks = document.querySelectorAll('a[href*="?lang="]');
        langLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const url = new URL(window.location.href);
                const lang = new URL(link.href).searchParams.get('lang');
                url.searchParams.set('lang', lang);
                window.location.href = url.toString();
            });
        });

        // Submenu toggle
        window.toggleSubmenu = function(id) {
            const submenu = document.getElementById(id + '-submenu');
            const icon = document.getElementById(id + '-icon');
            if (submenu.classList.contains('hidden')) {
                submenu.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                submenu.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Loading State
        window.showLoading = function() {
            document.getElementById('loadingOverlay').classList.add('active');
        }
        window.hideLoading = function() {
            document.getElementById('loadingOverlay').classList.remove('active');
        }

        // Confirm Dialog
        window.confirm2 = function(message, title = 'تایید') {
            return new Promise((resolve) => {
                document.getElementById('confirmTitle').textContent = title;
                document.getElementById('confirmMessage').textContent = message;
                document.getElementById('confirmDialog').classList.add('active');
                window.confirmCallback = function(result) {
                    document.getElementById('confirmDialog').classList.remove('active');
                    resolve(result);
                };
            });
        }

        // Auto-hide loading on page load
        window.addEventListener('load', () => {
            hideLoading();
            // Auto-open active submenus
            document.querySelectorAll('.sidebar-link.active').forEach(link => {
                const submenu = link.closest('[id$="-submenu"]');
                if (submenu) {
                    submenu.classList.remove('hidden');
                    const menuId = submenu.id.replace('-submenu', '');
                    const icon = document.getElementById(menuId + '-icon');
                    if (icon) icon.style.transform = 'rotate(180deg)';
                }
            });
        });

        // Breadcrumb
        window.addBreadcrumb = function(title, url = null) {
            const breadcrumb = document.getElementById('breadcrumb').querySelector('ol');
            const li = document.createElement('li');
            li.className = 'flex items-center gap-2';
            li.innerHTML = '<span>»</span>' + (url ? `<a href="${url}" class="hover:text-blue-600">${title}</a>` : `<span class="text-gray-900 font-semibold">${title}</span>`);
            breadcrumb.appendChild(li);
        }

        // Autocomplete
        window.initAutocomplete = function(inputId, apiUrl, onSelect) {
            const input = document.getElementById(inputId);
            if (!input) return;
            
            const wrapper = document.createElement('div');
            wrapper.className = 'relative';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);
            
            const dropdown = document.createElement('div');
            dropdown.className = 'absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden';
            wrapper.appendChild(dropdown);
            
            let timeout;
            input.addEventListener('input', function() {
                clearTimeout(timeout);
                const query = this.value.trim();
                
                if (query.length < 2) {
                    dropdown.classList.add('hidden');
                    return;
                }
                
                timeout = setTimeout(async () => {
                    try {
                        const response = await fetch(`${apiUrl}?q=${encodeURIComponent(query)}`);
                        const results = await response.json();
                        
                        if (results.length === 0) {
                            dropdown.innerHTML = '<div class="p-3 text-gray-500 text-sm">نتیجهای یافت نشد</div>';
                        } else {
                            dropdown.innerHTML = results.map(item => 
                                `<div class="p-3 hover:bg-gray-100 cursor-pointer border-b last:border-b-0" data-item='${JSON.stringify(item)}'>
                                    <div class="font-semibold">${item.first_name} ${item.last_name}</div>
                                    <div class="text-sm text-gray-600">${item.patient_code} - ${item.phone}</div>
                                </div>`
                            ).join('');
                            
                            dropdown.querySelectorAll('[data-item]').forEach(el => {
                                el.addEventListener('click', function() {
                                    const item = JSON.parse(this.getAttribute('data-item'));
                                    onSelect(item);
                                    dropdown.classList.add('hidden');
                                });
                            });
                        }
                        dropdown.classList.remove('hidden');
                    } catch (error) {
                        console.error('Autocomplete error:', error);
                    }
                }, 300);
            });
            
            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>
