// main.js
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('confirmModal');
    const confirmBtn = document.getElementById('modalConfirmBtn');
    const cancelBtn = document.getElementById('modalCancelBtn');
    let currentDeleteUrl = null;

    // Function to show modal (centered)
    function showModal() {
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function hideModal() {
        if (modal) {
            modal.style.display = 'none';
            currentDeleteUrl = null;
        }
    }

    // Attach click handlers to all delete links
    function initDeleteLinks() {
        const deleteLinks = document.querySelectorAll('.delete-link');
        deleteLinks.forEach(link => {
            // Remove any existing inline onclick or old events
            link.removeAttribute('onclick');
            // Replace with new click handler
            link.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                currentDeleteUrl = this.href;
                showModal();
            });
        });
    }

    // Confirm button clicks
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (currentDeleteUrl) {
                window.location.href = currentDeleteUrl;
            }
            hideModal();
        });
    }

    // Cancel button
    if (cancelBtn) {
        cancelBtn.addEventListener('click', hideModal);
    }

    // Clicking outside the modal (on the overlay) closes it
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideModal();
            }
        });
    }

    initDeleteLinks();
});