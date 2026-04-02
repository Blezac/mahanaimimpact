<?php
session_start();
$pageTitle = "Enroll in Academy";
require_once 'config/db.php';

$errors = [];

// Handle form submission first
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $education = trim($_POST['education'] ?? '');
    $occupation = trim($_POST['occupation'] ?? '');
    $why_join = trim($_POST['why_join'] ?? '');
    $financial_goals = trim($_POST['financial_goals'] ?? '');
    
    // Validation
    if (empty($full_name)) $errors[] = "Full name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($education)) $errors[] = "Education level is required";
    if (empty($why_join)) $errors[] = "Please tell us why you want to join";
    
    if (empty($errors)) {
        $enrollment_fee = 50000;
        $transaction_ref = 'LIN-ACADEMY-' . time() . '-' . rand(1000, 9999);
        $amountInKobo = $enrollment_fee * 100;

        // Store session data
        $_SESSION['academy_enrollment'] = [
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'education' => $education,
            'occupation' => $occupation,
            'why_join' => $why_join,
            'financial_goals' => $financial_goals
        ];

        header("Location: payments/paystack.php?reference=$transaction_ref&email=$email&amount=$amountInKobo&type=academy");
        exit();
    }
}


require_once 'includes/header.php';
?>


<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%); color: white; padding: 5rem 0;">
    <div class="container text-center">
        <h1 class="display-3 fw-bold mb-4">Enroll in <span style="color: #D4AF37;">The Academy</span></h1>
        <h3 class="mb-4">Mahanaim Investor Circle</h3>
        <p class="lead" style="font-size: 1.3rem; max-width: 800px; margin: 0 auto;">
            Join the next cohort and master financial literacy to secure your economic future
        </p>
    </div>
</section>

<!-- Enrollment Form -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <h2 class="mb-4" style="color: #4B0082;">Enrollment Application</h2>
                        <p class="mb-4">Complete this form to join the next cohort of the Mahanaim Investor Circle</p>
                        
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
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Phone Number </label>
                                    <input type="tel" name="phone" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Education Level </label>
                                    <select name="education" class="form-select" required>
                                        <option value="">Select...</option>
                                        <option value="secondary">Secondary School</option>
                                        <option value="undergraduate">Undergraduate</option>
                                        <option value="graduate">Graduate</option>
                                        <option value="postgraduate">Postgraduate</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Current Occupation</label>
                                <input type="text" name="occupation" class="form-control" placeholder="Student, Entrepreneur, Professional, etc.">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Why do you want to join the Mahanaim Investor Circle? </label>
                                <textarea name="why_join" class="form-control" rows="3" required placeholder="Tell us about your motivation..."></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">What are your financial goals?</label>
                                <textarea name="financial_goals" class="form-control" rows="3" placeholder="Share your short-term and long-term financial goals..."></textarea>
                            </div>
                            
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Program Fee:</strong> ₦50,000 (includes weeks of training, materials, certification, and lifetime access to alumni network)
                            </div>
                            
                            <button type="submit" class="btn btn-gold btn-lg w-100">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Payment (₦50,000)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Program Details -->
                <div class="card border-0 shadow-lg sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h4 class="mb-3" style="color: #4B0082;">Program Details</h4>
                        
                        <div class="mb-3">
                            <h6 class="mb-2"><i class="fas fa-calendar me-2" style="color: #D4AF37;"></i>Duration</h6>
                            <p class="mb-0">Weeks</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="mb-2"><i class="fas fa-clock me-2" style="color: #D4AF37;"></i>Schedule</h6>
                            <p class="mb-0">2 sessions per week<br>Evenings & Weekends</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="mb-2"><i class="fas fa-map-marker-alt me-2" style="color: #D4AF37;"></i>Format</h6>
                            <p class="mb-0">Hybrid (Online + In-person)</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="mb-2"><i class="fas fa-certificate me-2" style="color: #D4AF37;"></i>Certification</h6>
                            <p class="mb-0">Yes, upon completion</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="mb-2"><i class="fas fa-users me-2" style="color: #D4AF37;"></i>Cohort Size</h6>
                            <p class="mb-0">Maximum 50 participants</p>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3" style="color: #4B0082;">What's Included</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>weeks curriculum</li>
                            <li class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Course materials</li>
                            <li class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Expert instructors</li>
                            <li class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Mentorship access</li>
                            <li class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Certificate</li>
                            <li class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Alumni network</li>
                        </ul>
                        
                        <hr>
                        
                        <div class="text-center">
                            <h5 class="mb-2" style="color: #4B0082;">Enrollment Fee</h5>
                            <h2 class="mb-0" style="color: #D4AF37;">₦50,000</h2>
                            <small class="text-muted">One-time payment</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Join -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Why Join the Mahanaim Investor Circle?</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-chart-line fa-4x mb-3" style="color: #D4AF37;"></i>
                        <h5 style="color: #4B0082;">Build Wealth</h5>
                        <p class="mb-0">Learn proven strategies for creating and growing wealth sustainably</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-lightbulb fa-4x mb-3" style="color: #D4AF37;"></i>
                        <h5 style="color: #4B0082;">Financial Freedom</h5>
                        <p class="mb-0">Gain the knowledge to make informed financial decisions independently</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-users fa-4x mb-3" style="color: #D4AF37;"></i>
                        <h5 style="color: #4B0082;">Network</h5>
                        <p class="mb-0">Connect with like-minded individuals and successful investors</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
