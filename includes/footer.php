        </div>
    </main>

    <!-- Mobile Drawer toggle script -->
    <script>
        const sidebarContainer = document.getElementById('sidebar-container');
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileBackdrop = document.getElementById('mobile-backdrop');

        if (mobileMenuToggle && sidebarContainer) {
            mobileMenuToggle.addEventListener('click', () => {
                sidebarContainer.classList.remove('hidden');
            });
        }

        if (mobileBackdrop && sidebarContainer) {
            mobileBackdrop.addEventListener('click', () => {
                sidebarContainer.classList.add('hidden');
            });
        }
    </script>
</body>
</html>
