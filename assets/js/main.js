document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('loaded');

    const navToggle = document.getElementById('navToggle');
    const navLinks = document.querySelector('.nav-links');
    if (navToggle && navLinks) {
        navToggle.setAttribute('aria-expanded', 'false');
        navToggle.addEventListener('click', () => {
            navLinks.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', navLinks.classList.contains('open') ? 'true' : 'false');
        });
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('open');
                navToggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // File input visual feedback
    const fileInputs = document.querySelectorAll('.file-upload-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const selectedFile = e.target.files && e.target.files[0] ? e.target.files[0] : null;
            const fileName = selectedFile ? selectedFile.name : '';
            if (fileName) {
                const textContainer = this.nextElementSibling;
                if(textContainer) {
                     textContainer.innerHTML = `<i class="fas fa-file-alt"></i> ${fileName}`;
                }
            }
        });
    });

    // Handle alert messages logic to disappear after sometime
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Tab Navigation Logic
    const sidebarLinks = document.querySelectorAll('.sidebar-nav a[href^="#"]');
    if (sidebarLinks.length > 0) {
        // Find all sections that are targeted by the sidebar links
        const sections = Array.from(sidebarLinks).map(link => document.querySelector(link.getAttribute('href')));
        
        // Function to switch tabs
        function switchTab(hash) {
            if (!hash) return;
            
            // If the hash matches a link, switch to it
            const targetLink = document.querySelector(`.sidebar-nav a[href="${hash}"]`);
            const targetSection = document.querySelector(hash);
            
            if (targetLink && targetSection) {
                // Update active link
                document.querySelectorAll('.sidebar-nav a').forEach(link => link.classList.remove('active'));
                targetLink.classList.add('active');
                
                // Show target section, hide others
                sections.forEach(sec => {
                    if (sec) sec.style.display = 'none';
                });
                targetSection.style.display = 'block';
            }
        }

        // Add click event listeners to links
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const hash = this.getAttribute('href');
                history.pushState(null, null, hash);
                switchTab(hash);
            });
        });

        // Initialize based on current hash or default to first tab
        if (window.location.hash) {
            switchTab(window.location.hash);
        } else {
            // Default: show first section, hide rest
            const firstHash = sidebarLinks[0].getAttribute('href');
            switchTab(firstHash);
        }
    }

    // Sidebar collapse/expand
    if (document.querySelector('.dashboard .sidebar')) {
        const sidebar = document.querySelector('.dashboard .sidebar');
        const sidebarBtn = document.createElement('button');
        sidebarBtn.className = 'sidebar-toggle';
        sidebarBtn.type = 'button';
        sidebarBtn.setAttribute('aria-label', 'Toggle sidebar');
        sidebarBtn.innerHTML = '<i class="fas fa-angle-left"></i>';
        sidebar.prepend(sidebarBtn);
        sidebarBtn.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-collapsed');
            sidebarBtn.innerHTML = document.body.classList.contains('sidebar-collapsed')
                ? '<i class="fas fa-angle-right"></i>'
                : '<i class="fas fa-angle-left"></i>';
        });
    }

    // Vanilla modal support for existing data-bs attributes
    const modalTriggers = document.querySelectorAll('[data-bs-toggle="modal"]');
    const dismissButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    function openModal(modal) {
        if (!modal) return;
        modal.style.display = 'block';
        requestAnimationFrame(() => modal.classList.add('show'));
        document.body.classList.add('modal-open');
    }

    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const target = trigger.getAttribute('data-bs-target');
            if (!target) return;
            const modal = document.querySelector(target) || document.getElementById(target.replace('#', ''));
            openModal(modal);
        });
    });

    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-bs-toggle="modal"]');
        if (!trigger) return;
        e.preventDefault();
        const target = trigger.getAttribute('data-bs-target');
        if (!target) return;
        const modal = document.querySelector(target) || document.getElementById(target.replace('#', ''));
        openModal(modal);
    });

    dismissButtons.forEach(button => {
        button.addEventListener('click', () => {
            closeModal(button.closest('.modal'));
        });
    });

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(modal);
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const openedModal = document.querySelector('.modal.show');
            if (openedModal) closeModal(openedModal);
        }
    });
});

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function openModalById(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.style.display = 'block';
    requestAnimationFrame(() => modal.classList.add('show'));
    document.body.classList.add('modal-open');
}

function closeModalElement(modal) {
    if (!modal) return;
    modal.classList.remove('show');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
}

window.openModalById = openModalById;
window.closeModalElement = closeModalElement;

/**
 * Category Filtering for Internship Cards
 */
function filterByCardCategory(category, button) {
    const cards = document.querySelectorAll('.internship-card');
    const buttons = document.querySelectorAll('.category-chip');
    
    // Update active button state
    buttons.forEach(btn => btn.classList.remove('active'));
    if (button) button.classList.add('active');
    
    cards.forEach(card => {
        // Show all if 'all' is selected, otherwise match category
        if (category === 'all' || card.getAttribute('data-category') === category) {
            card.style.display = 'flex';
            // Simple fade-in effect
            card.style.opacity = '0';
            requestAnimationFrame(() => {
                card.style.transition = 'opacity 0.3s ease';
                card.style.opacity = '1';
            });
        } else {
            card.style.display = 'none';
        }
    });
}

window.filterByCardCategory = filterByCardCategory;

/* ============================================================
   THEME TOGGLE — Default ↔ Violet
   ============================================================ */
(function () {
    const STORAGE_KEY = 'internhub_theme';
    const VIOLET_CLASS = 'theme-violet';

    // Apply persisted theme on load (body available at DOMContentLoaded)
    document.addEventListener('DOMContentLoaded', function () {
        try {
            if (localStorage.getItem(STORAGE_KEY) === 'violet') {
                document.body.classList.add(VIOLET_CLASS);
            }
            // Clean up the pre-paint html class
            document.documentElement.classList.remove('theme-violet-pre');
        } catch (e) {}

        const btn = document.getElementById('themeToggleBtn');
        if (!btn) return;

        btn.addEventListener('click', function () {
            const isViolet = document.body.classList.toggle(VIOLET_CLASS);
            try {
                localStorage.setItem(STORAGE_KEY, isViolet ? 'violet' : 'default');
            } catch (e) {}

            // Spin + pulse animation on click
            btn.style.transform = 'scale(1.25) rotate(180deg)';
            btn.style.transition = 'transform 0.35s cubic-bezier(0.4,0,0.2,1), box-shadow 0.25s ease, background 0.35s ease';
            setTimeout(function () {
                btn.style.transform = '';
                btn.style.transition = '';
            }, 380);
        });
    });
})();
