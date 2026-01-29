/**
 * bucv_admin.js
 * JavaScript para el panel de administración
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar funcionalidades
    initAlertDismiss();
    initSearchForm();
    initDeleteConfirmation();
    initSidebarToggle();
});

/**
 * Auto-cerrar alertas después de 5 segundos
 */
function initAlertDismiss() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.3s';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
}

/**
 * Búsqueda con Enter
 */
function initSearchForm() {
    const searchInput = document.querySelector('.admin-search input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    }
}

/**
 * Confirmación de eliminación
 */
function initDeleteConfirmation() {
    const deleteForms = document.querySelectorAll('form[onsubmit*="confirm"]');
    deleteForms.forEach(form => {
        form.removeAttribute('onsubmit');
        form.addEventListener('submit', function(e) {
            if (!confirm('¿Está seguro de eliminar este elemento?')) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Toggle del sidebar en móvil
 */
function initSidebarToggle() {
    const sidebar = document.querySelector('.admin-sidebar');
    const toggleBtn = document.querySelector('.sidebar-toggle');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
    }
}

/**
 * Función auxiliar para mostrar notificaciones
 */
function showNotification(message, type = 'success') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;

    const content = document.querySelector('.admin-content');
    if (content) {
        content.insertBefore(alert, content.firstChild);

        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.3s';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    }
}

/**
 * Función para confirmar acciones
 */
function confirmAction(message) {
    return confirm(message);
}
