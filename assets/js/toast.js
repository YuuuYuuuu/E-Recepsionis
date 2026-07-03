// Toast Notification System
// Reusable toast notification function

function showToast(type, title, message, duration = 5000) {
    // Create container if not exists
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    
    // Icon mapping
    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-x-circle-fill',
        info: 'bi-info-circle-fill',
        warning: 'bi-exclamation-triangle-fill'
    };
    
    // Toast HTML
    toast.innerHTML = `
        <div class="toast-icon ${type}">
            <i class="bi ${icons[type] || icons.info}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()" aria-label="Close">
            <i class="bi bi-x"></i>
        </button>
    `;
    
    // Add to container
    container.appendChild(toast);
    
    // Trigger animation
    setTimeout(() => {
        toast.style.opacity = '1';
    }, 10);
    
    // Auto remove after duration
    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    }, duration);
    
    return toast;
}

// Helper functions for common toast types
function showSuccess(title, message, duration) {
    return showToast('success', title, message, duration);
}

function showError(title, message, duration) {
    return showToast('error', title, message, duration);
}

function showInfo(title, message, duration) {
    return showToast('info', title, message, duration);
}

function showWarning(title, message, duration) {
    return showToast('warning', title, message, duration);
}
