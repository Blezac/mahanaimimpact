<?php
// === donate.php ===
$pageTitle = "Donate";
require_once 'config/db.php';
require_once 'includes/header.php';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    if (empty($full_name)) $errors[] = "Full name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($amount) || !is_numeric($amount) || $amount < 500) $errors[] = "Minimum donation is ₦500";
    
    if (empty($errors)) {
        // Generate transaction reference
        $transaction_ref = 'MIN-DONATION-' . time() . '-' . rand(1000, 9999);
        
        // Store donation record (you can create a donations table or use registrations table)
        try {
            // For now, we'll redirect to Paystack
            // You can store donation info in database if needed
            
            $amountInKobo = $amount * 100;
            header("Location: payments/paystack.php?reference=$transaction_ref&email=$email&amount=$amountInKobo&type=donation");
            exit();
        } catch (Exception $e) {
            $errors[] = "Error processing donation: " . $e->getMessage();
        }
    }
}
?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%); color: white; padding: 5rem 0;">
    <div class="container text-center">
        <h1 class="display-3 fw-bold mb-4">Support <span style="color: #D4AF37;">Our Mission</span></h1>
        <p class="lead" style="font-size: 1.4rem; max-width: 800px; margin: 0 auto;">
            Your donation empowers youth and women through the Five Pillars of Impact
        </p>
    </div>
</section>

<!-- Donation Impact -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="section-title mb-4">Why Your Donation Matters</h2>
                <p class="lead mb-3">
                    Every contribution to Lase Impact Network directly supports our Five Pillars of Impact, driving SDG 1, 5, and 8 across Nigeria.
                </p>
                <p class="mb-3">
                    Your donation helps us provide trade fair opportunities, financial literacy education, mentorship programs, humanitarian support, and community building initiatives.
                </p>
                <p class="mb-4">
                    Together, we're creating lasting transformation in the lives of young people and building sustainable economic opportunities.
                </p>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?w=800" class="img-fluid rounded shadow-lg" alt="Impact">
            </div>
        </div>
    </div>
</section>

<!-- Impact Breakdown -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">How Your Donation Helps</h2>
            <p class="lead mt-4">See the direct impact of your generosity</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card card-custom h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-store fa-4x" style="color: #D4AF37;"></i>
                    </div>
                    <h3 style="color: #4B0082;">₦5,000</h3>
                    <p class="mb-0">Sponsors one vendor booth at a University Trade Fair</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card card-custom h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-graduation-cap fa-4x" style="color: #D4AF37;"></i>
                    </div>
                    <h3 style="color: #4B0082;">₦15,000</h3>
                    <p class="mb-0">Provides one student access to Mahanaim Investor Circle</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card card-custom h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-heart fa-4x" style="color: #D4AF37;"></i>
                    </div>
                    <h3 style="color: #4B0082;">₦25,000</h3>
                    <p class="mb-0">Funds one orphanage/elderly home outreach with supplies</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card card-custom h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-users fa-4x" style="color: #D4AF37;"></i>
                    </div>
                    <h3 style="color: #4B0082;">₦50,000</h3>
                    <p class="mb-0">Supports a full cohort of mentees through The Bridge</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Donation Form -->
<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-heart fa-4x mb-3" style="color: #D4AF37;"></i>
                            <h2 style="color: #4B0082;">Make a Donation</h2>
                            <p class="text-muted">Your generosity creates lasting impact</p>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php foreach ($errors as $error): ?>
                                    <div><?php echo htmlspecialchars($error); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Full Name </label>
                                    <input type="text" name="full_name" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Email Address </label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Phone Number </label>
                                <input type="tel" name="phone" class="form-control" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Donation Amount (₦) </label>
                                <div class="btn-group w-100 mb-3" role="group">
                                    <input type="radio" class="btn-check" name="amount_preset" id="amount1" value="5000" onclick="document.getElementById('customAmount').value='5000'">
                                    <label class="btn btn-outline-primary" for="amount1">₦5,000</label>
                                    
                                    <input type="radio" class="btn-check" name="amount_preset" id="amount2" value="15000" onclick="document.getElementById('customAmount').value='15000'">
                                    <label class="btn btn-outline-primary" for="amount2">₦15,000</label>
                                    
                                    <input type="radio" class="btn-check" name="amount_preset" id="amount3" value="25000" onclick="document.getElementById('customAmount').value='25000'">
                                    <label class="btn btn-outline-primary" for="amount3">₦25,000</label>
                                    
                                    <input type="radio" class="btn-check" name="amount_preset" id="amount4" value="50000" onclick="document.getElementById('customAmount').value='50000'">
                                    <label class="btn btn-outline-primary" for="amount4">₦50,000</label>
                                </div>
                                <input type="number" id="customAmount" name="amount" class="form-control" min="500" step="100" placeholder="Or enter custom amount (min ₦500)" required>
                                <small class="text-muted">Minimum donation: ₦500</small>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Message (Optional)</label>
                                <textarea name="message" class="form-control" rows="3" placeholder="Leave a message or dedication"></textarea>
                            </div>
                            
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Secure Payment:</strong> You'll be redirected to Paystack for secure payment processing.
                            </div>
                            
                            <button type="submit" class="btn btn-gold btn-lg w-100">
                                <i class="fas fa-donate me-2"></i>Proceed to Secure Payment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Donor Testimonials</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x" style="color: #D4AF37;"></i>
                        </div>
                        <p class="mb-3">"Supporting MIN has been one of the most rewarding decisions. Seeing young people transform their lives through the Five Pillars is truly inspiring."</p>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background: #4B0082 !important;">
                                <strong>AO</strong>
                            </div>
                            <div>
                                <strong>Adebayo Okonkwo</strong>
                                <p class="mb-0 small text-muted">Monthly Donor</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x" style="color: #D4AF37;"></i>
                        </div>
                        <p class="mb-3">"I love how transparent MIN is with donations. I can see exactly how my contribution helps empower youth and women in our community."</p>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background: #4B0082 !important;">
                                <strong>CE</strong>
                            </div>
                            <div>
                                <strong>Chioma Eze</strong>
                                <p class="mb-0 small text-muted">Corporate Sponsor</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x" style="color: #D4AF37;"></i>
                        </div>
                        <p class="mb-3">"The impact is real and measurable. I've sponsored three students through the academy and seen them start successful businesses."</p>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background: #4B0082 !important;">
                                <strong>TM</strong>
                            </div>
                            <div>
                                <strong>Tunde Makinde</strong>
                                <p class="mb-0 small text-muted">Angel Donor</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Other Ways to Give -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Other Ways to Support</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center p-4">
                    <i class="fas fa-handshake fa-4x mb-3" style="color: #D4AF37;"></i>
                    <h4 style="color: #4B0082;">Become a Mentor</h4>
                    <p class="mb-3">Share your expertise through The Bridge mentorship program</p>
                    <a href="partner.php" class="btn btn-outline-primary">Join as Mentor</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <i class="fas fa-building fa-4x mb-3" style="color: #D4AF37;"></i>
                    <h4 style="color: #4B0082;">Corporate Partnership</h4>
                    <p class="mb-3">Partner with us for CSR initiatives and employee engagement</p>
                    <a href="partner.php" class="btn btn-outline-primary">Learn More</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <i class="fas fa-volunteer fa-4x mb-3" style="color: #D4AF37;"></i>
                    <h4 style="color: #4B0082;">Volunteer</h4>
                    <p class="mb-3">Give your time at trade fairs and humanitarian outreaches</p>
                    <a href="partner.php" class="btn btn-outline-primary">Get Involved</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
