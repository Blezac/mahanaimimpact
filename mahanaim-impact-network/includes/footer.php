<!-- === includes/footer.php === -->
<footer class="bg-dark text-white py-5" style="background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%) !important;">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h4 class="mb-3" style="color: #D4AF37;">Mahanaim Impact Network (MIN)</h4>
                <p class="mb-3">Driving SDG 1, 5, and 8 through the Five Pillars of Impact</p>
                <p>Empowering youth and women for sustainable economic growth and community transformation.</p>
                <div class="mt-3">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-linkedin fa-lg"></i></a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="mb-3" style="color: #D4AF37;">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" class="text-white text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Home</a></li>
                    <li class="mb-2"><a href="about.php" class="text-white text-decoration-none"><i class="fas fa-chevron-right me-2"></i>About MIN</a></li>
                    <li class="mb-2"><a href="academy.php" class="text-white text-decoration-none"><i class="fas fa-chevron-right me-2"></i>The Academy</a></li>
                    <li class="mb-2"><a href="gallery.php" class="text-white text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Impact Gallery</a></li>
                    <li class="mb-2"><a href="partner.php" class="text-white text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Partner With Us</a></li>
                    <li class="mb-2"><a href="register.php" class="text-white text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Register</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="mb-3" style="color: #D4AF37;">Contact Us</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>Abuja, Nigeria</li>
                    <li class="mb-2"><i class="fas fa-phone me-2"></i>+234-708-891-6615</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i>info@mahanaimimpact.com</li>
                </ul>
                <div class="mt-3">
                    <p class="small mb-2"><strong>Put our URL in your:</strong></p>
                    <p class="small mb-0"><i class="fab fa-whatsapp me-2"></i>WhatsApp Bio</p>
                    <p class="small mb-0"><i class="fab fa-instagram me-2"></i>Instagram Profile</p>
                    <p class="small mb-0"><i class="fab fa-linkedin me-2"></i>LinkedIn Summary</p>
                </div>
                <div class="mt-3">
                    <a href="admin/login.php" class="text-white text-decoration-none"><i class="fas fa-lock me-1"></i>Admin Login</a>
                </div>
            </div>
        </div>
        <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
        <div class="text-center">
            <p class="mb-1">&copy; <?php echo date('Y'); ?> Lase Impact Network. All Rights Reserved.</p>
            <p class="mb-0 small">The Mahanaim Impact Network now has a digital home! <span style="color: #D4AF37;"></span></p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Navbar background on scroll
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.style.boxShadow = '0 5px 20px rgba(0,0,0,0.2)';
        } else {
            navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        }
    });
</script>
</body>
</html>
