document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebarTexts = document.querySelectorAll('.sidebar-text');
    const sectionTitles = document.querySelectorAll('.sidebar-section-title');
    const iconOnlyLabels = document.querySelectorAll('.sidebar-icon-only');

    if (!sidebar || !toggleBtn) return;

    // Restore sidebar state from localStorage
    const collapsed = localStorage.getItem('sidebar_collapsed') === 'true';
    if (collapsed) {
        sidebar.classList.add('collapsed');
        toggleBtn.querySelector('i').classList.remove('fa-bars');
        toggleBtn.querySelector('i').classList.add('fa-chevron-right');
    }

    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        const isCollapsed = sidebar.classList.contains('collapsed');

        // Toggle icon
        const icon = toggleBtn.querySelector('i');
        if (isCollapsed) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-chevron-right');
        } else {
            icon.classList.remove('fa-chevron-right');
            icon.classList.add('fa-bars');
        }

        // Save state
        localStorage.setItem('sidebar_collapsed', isCollapsed);
    });
});