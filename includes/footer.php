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
<script>
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
</body>
</html>