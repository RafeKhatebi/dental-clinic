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
    </script>
</body>
</html>
