/**
 * JavaScript principal del sistema
 */

// Utilidades AJAX
const API = {
    post: async (url, data) => {
        const formData = new FormData();
        for (const key in data) {
            formData.append(key, data[key]);
        }
        
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        return response.json();
    },
    
    get: async (url) => {
        const response = await fetch(url);
        return response.json();
    }
};

// Validaciones client-side
const Validator = {
    email: (email) => {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },
    
    required: (value) => {
        return value !== null && value !== undefined && value.toString().trim() !== '';
    },
    
    minLength: (value, min) => {
        return value.toString().length >= min;
    }
};

// Notificaciones
const Notification = {
    show: (message, type = 'info') => {
        // Implementar sistema de notificaciones con Bootstrap toast
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        const container = document.querySelector('.toast-container') || createToastContainer();
        container.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    },
    
    success: (message) => Notification.show(message, 'success'),
    error: (message) => Notification.show(message, 'danger'),
    info: (message) => Notification.show(message, 'info'),
    warning: (message) => Notification.show(message, 'warning')
};

function createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    document.body.appendChild(container);
    return container;
}

// Formateo de moneda
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: 'PEN'
    }).format(amount);
}

// Formateo de fecha
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-PE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts después de 5 segundos
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

