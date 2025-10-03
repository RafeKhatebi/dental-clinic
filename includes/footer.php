            </main>
        </div>
    </div>

    <script>
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
    </script>
</body>
</html>
