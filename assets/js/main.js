// main.js – final working version for all actions
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('confirmModal');
    const confirmBtn = document.getElementById('modalConfirmBtn');
    const cancelBtn = document.getElementById('modalCancelBtn');
    let currentUrl = null;

    function showModal() {
        if (modal) modal.style.display = 'flex';
    }

    function hideModal() {
        if (modal) modal.style.display = 'none';
        currentUrl = null;
        // Reset modal text and button to defaults
        const modalBody = document.querySelector('#confirmModal .modal-body p');
        if (modalBody) modalBody.innerText = 'Are you sure? This action cannot be undone.';
        confirmBtn.innerText = 'Delete';
        confirmBtn.style.background = '';
    }

    function setupAction(selector, actionText, confirmText, buttonColor) {
        document.querySelectorAll(selector).forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                currentUrl = this.href;
                const modalBody = document.querySelector('#confirmModal .modal-body p');
                if (modalBody) modalBody.innerText = actionText;
                confirmBtn.innerText = confirmText;
                confirmBtn.style.background = buttonColor;
                showModal();
            });
        });
    }

    // Soft delete (expenses page)
    setupAction('.delete-link', 
        'Move this expense to trash? You can restore it later from the Trash page.',
        'Move to Trash',
        'linear-gradient(135deg, #f59e0b, #d97706)'
    );

    // Restore (trash page)
    setupAction('.restore-link',
        'Restore this expense? It will reappear in your main list.',
        'Restore',
        'linear-gradient(135deg, #10b981, #059669)'
    );

    // Hard delete / permanent delete (trash page)
    setupAction('.permanent-delete-link',
        '⚠️ Permanently delete this expense? This action cannot be undone.',
        'Permanently Delete',
        'linear-gradient(135deg, #ef4444, #dc2626)'
    );

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (currentUrl) window.location.href = currentUrl;
            hideModal();
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', hideModal);
    }

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) hideModal();
        });
    }
});