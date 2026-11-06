// bagian bersama start

document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobile Sidebar Drawer Toggle
    const btnHamburger = document.getElementById('btnHamburger');
    const appSidebar = document.getElementById('appSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (btnHamburger && appSidebar && sidebarOverlay) {
        btnHamburger.addEventListener('click', function () {
            appSidebar.classList.toggle('open');
            sidebarOverlay.classList.toggle('active');
        });

        sidebarOverlay.addEventListener('click', function () {
            appSidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
        });
    }

    // 2. Auto-fade alert banners after 5 seconds
    const alerts = document.querySelectorAll('.alert-banner');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.4s ease';
            alert.style.opacity = '0';
            setTimeout(function () {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 400);
        }, 5000);
    });

    // 3. Toggle Tampilan Password (Ikon Mata)
    document.querySelectorAll('.btn-password-eye').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const wrap = this.closest('.password-field-wrap');
            if (!wrap) return;

            const input = wrap.querySelector('.password-toggle-input');
            const eyeOpen = this.querySelector('.eye-open');
            const eyeClosed = this.querySelector('.eye-closed');

            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    if (eyeOpen) eyeOpen.style.display = 'none';
                    if (eyeClosed) eyeClosed.style.display = 'block';
                    this.setAttribute('title', 'Sembunyikan Password');
                } else {
                    input.type = 'password';
                    if (eyeOpen) eyeOpen.style.display = 'block';
                    if (eyeClosed) eyeClosed.style.display = 'none';
                    this.setAttribute('title', 'Lihat Password');
                }
            }
        });
    });
});

/**
 * Membuka Modal Konfirmasi Hapus Universal
 * @param {string} actionUrl - URL target penghapusan (POST)
 * @param {string} itemName  - Nama item yang akan dihapus
 */
function confirmDelete(actionUrl, itemName) {
    const modal = document.getElementById('modalConfirmDelete');
    const form = document.getElementById('formConfirmDelete');
    const nameElem = document.getElementById('deleteItemName');

    if (modal && form && nameElem) {
        form.action = actionUrl;
        nameElem.textContent = itemName;
        modal.classList.add('show');
    }
}

/**
 * Menutup Modal Konfirmasi Hapus Universal
 */
function closeDeleteModal() {
    const modal = document.getElementById('modalConfirmDelete');
    if (modal) {
        modal.classList.remove('show');
    }
}

// Tutup modal jika user mengklik backdrop di luar dialog
window.addEventListener('click', function (event) {
    const modal = document.getElementById('modalConfirmDelete');
    if (event.target === modal) {
        closeDeleteModal();
    }
});

// bagian bersama end

