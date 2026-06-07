/**
 * SISMAKA — Main JavaScript
 * Handles: toast auto-close, modal helpers, form utilities
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Auto-close toasts from server-side flash messages ──
    document.querySelectorAll('[data-auto-close]').forEach(function (toast) {
        setTimeout(function () {
            toast.style.animation = 'toastOut 0.25s ease forwards';
            setTimeout(function () { toast.remove(); }, 250);
        }, 3500);
    });

    // ── Show toast programmatically ──
    window.showToast = function (type, msg) {
        const icons = {
            success: 'ti-circle-check',
            error:   'ti-alert-circle',
            info:    'ti-info-circle'
        };
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <i class="ti ${icons[type] || 'ti-info-circle'}"></i>
            <span class="toast-msg">${msg}</span>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="ti ti-x"></i>
            </button>`;
        container.appendChild(toast);

        setTimeout(function () {
            toast.style.animation = 'toastOut 0.25s ease forwards';
            setTimeout(function () { toast.remove(); }, 250);
        }, 3500);
    };

    // ── CSRF token helper for fetch ──
    window.csrfToken = function () {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    };

    // ── Confirm delete helper (used in mahasiswa/index) ──
    window.confirmDelete = function (id, nama) {
        const nameEl  = document.getElementById('delete-name');
        const formEl  = document.getElementById('delete-form');
        const overlay = document.getElementById('modal-delete');
        if (!overlay) return;
        if (nameEl)  nameEl.textContent  = nama;
        if (formEl)  formEl.action = `/mahasiswa/${id}`;
        overlay.style.display = 'flex';
    };

    window.closeDeleteModal = function () {
        const overlay = document.getElementById('modal-delete');
        if (overlay) overlay.style.display = 'none';
    };

    // Close modal on overlay click
    const deleteOverlay = document.getElementById('modal-delete');
    if (deleteOverlay) {
        deleteOverlay.addEventListener('click', function (e) {
            if (e.target === this) window.closeDeleteModal();
        });
    }

    // ── Live search debounce (mahasiswa index) ──
    const mainSearch = document.getElementById('main-search');
    if (mainSearch) {
        let debounceTimer;
        mainSearch.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                document.getElementById('filter-form').submit();
            }, 450);
        });
    }

    // ── Search page: submit on Enter ──
    const searchKeyword = document.getElementById('search-keyword');
    if (searchKeyword) {
        searchKeyword.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                document.getElementById('search-form').submit();
            }
        });
    }

});
