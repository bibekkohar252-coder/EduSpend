</main>
<footer class="footer minimal-footer">
    <div class="footer-inner">
        <div class="footer-branding">
            <img src="/EduSpend/assets/images/logo.jpeg" alt="EduSpend" class="footer-logo-mini">
            <span>EduSpend</span>
        </div>
        <div class="footer-links">
            <a href="/EduSpend/index.php">Home</a>
            <a href="/EduSpend/modules/expense/view.php">Expenses</a>
            <a href="/EduSpend/modules/student/profile.php">Profile</a>
            <a href="#">Support</a>
        </div>
        <div class="footer-copy">
            © <?= date('Y') ?> BlueOrbit – EduSpend
        </div>
    </div>
    <button id="backToTop" class="back-to-top" style="display:none;">↑</button>
</footer>

<!-- Centered confirmation modal (hidden by default) -->
<div id="confirmModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 10000;">
    <div style="background: white; max-width: 400px; width: 90%; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #eef2f6;">
            <h3 style="margin: 0;">Confirm Deletion</h3>
        </div>
        <div style="padding: 1.5rem;">
            <p style="margin: 0;">Are you sure? This action cannot be undone.</p>
        </div>
        <div style="padding: 1rem 1.5rem; border-top: 1px solid #eef2f6; display: flex; justify-content: flex-end; gap: 0.75rem;">
            <button id="modalCancelBtn" class="btn btn-secondary">Cancel</button>
            <button id="modalConfirmBtn" class="btn btn-danger">Delete</button>
        </div>
    </div>
</div>

<script>
    // Back to top button
    const backBtn = document.getElementById('backToTop');
    if (backBtn) {
        window.addEventListener('scroll', () => {
            backBtn.style.display = window.scrollY > 300 ? 'flex' : 'none';
        });
        backBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
</script>
<script src="/EduSpend/assets/js/main.js"></script>
</body>
</html>