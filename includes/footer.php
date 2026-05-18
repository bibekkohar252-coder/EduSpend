</main>
<footer class="modern-footer">
    <div class="footer-content">
        <div class="footer-column">
            <div class="footer-logo">
                <img src="/EduSpend/assets/images/logo.jpeg" alt="EduSpend" class="footer-logo-img">
                <span>EduSpend</span>
            </div>
            <p class="footer-tagline">Track • Plan • Save • Grow</p>
            <p class="footer-desc">Smart expense tracking for students.</p>
        </div>
        
        <div class="footer-column">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="/EduSpend/index.php">Dashboard</a></li>
                <li><a href="/EduSpend/modules/expense/view.php">Expenses</a></li>
                <li><a href="/EduSpend/modules/category/list.php">Categories</a></li>
                <li><a href="/EduSpend/modules/income/list.php">Income</a></li>
                <li><a href="/EduSpend/modules/savings/list.php">Savings</a></li>
            </ul>
        </div>
        
        <div class="footer-column">
            <h4>Account</h4>
            <ul>
                <li><a href="/EduSpend/modules/student/profile.php">Profile</a></li>
                <li><a href="/EduSpend/modules/student/edit.php">Settings</a></li>
                <li><a href="#" id="supportLink">Support</a></li>
                <li><a href="/EduSpend/logout.php">Logout</a></li>
            </ul>
        </div>
        
        <div class="footer-column">
            <h4>Connect</h4>
            <div class="social-icons">
                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="GitHub"><i class="fab fa-github"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
            <p class="footer-email">📧 support@eduspend.com</p>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> BlueOrbit – EduSpend. All rights reserved.</p>
        <div class="footer-bottom-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
        </div>
    </div>
    
    <button id="backToTop" class="back-to-top" style="display:none;">↑</button>
</footer>

<!-- Font Awesome for social icons (if not already loaded) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Centered confirmation modal (unchanged) -->
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