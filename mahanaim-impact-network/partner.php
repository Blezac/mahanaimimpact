<?php
// === partner.php ===
$pageTitle = "Partner With Us";
require_once 'config/db.php';
require_once 'includes/header.php';

$mentorSuccess = false;
$sponsorSuccess = false;
$errors = [];

// Handle Mentor Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mentor_submit'])) {
    $full_name = trim($_POST['mentor_name'] ?? '');
    $email = trim($_POST['mentor_email'] ?? '');
    $phone = trim($_POST['mentor_phone'] ?? '');
    $expertise = trim($_POST['mentor_expertise'] ?? '');
    $experience = trim($_POST['mentor_experience'] ?? '');
    
    // Validation
    if (empty($full_name)) $errors[] = "Full name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($expertise)) $errors[] = "Area of expertise is required";
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO mentors (full_name, email, phone, expertise, experience) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $phone, $expertise, $experience]);
            $mentorSuccess = true;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Handle Sponsor Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sponsor_submit'])) {
    $full_name = trim($_POST['sponsor_name'] ?? '');
    $email = trim($_POST['sponsor_email'] ?? '');
    $phone = trim($_POST['sponsor_phone'] ?? '');
    $amount = trim($_POST['sponsor_amount'] ?? '');
    $student_preference = trim($_POST['student_preference'] ?? '');
    
    // Validation
    if (empty($full_name)) $errors[] = "Full name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($amount) || !is_numeric($amount) || $amount <= 0) $errors[] = "Valid amount is required";
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO sponsors (full_name, email, phone, amount, student_preference) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $phone, $amount, $student_preference]);
            $sponsorSuccess = true;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%); color: white; padding: 5rem 0;">
    <div class="container text-center">
        <h1 class="display-3 fw-bold mb-4">Partner <span style="color: #D4AF37;">With Us</span></h1>
        <p class="lead" style="font-size: 1.4rem;">Join MIN in empowering the next generation through The Bridge mentorship program or student sponsorship</p>
    </div>
</section>

<!-- Partnership Options -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">How You Can Partner</h2>
            <p class="lead mt-4">Make a lasting impact through mentorship or sponsorship</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card card-custom h-100 p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-tie fa-5x" style="color: #D4AF37;"></i>
                    </div>
                    <h3 class="text-center mb-3" style="color: #4B0082;">Become a Mentor (The Bridge)</h3>
                    <p class="text-center mb-4">Share your knowledge, experience, and wisdom with young people through The Bridge mentorship program. Guide them in their personal and professional development journey.</p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Flexible commitment</li>
                        <li class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Virtual or in-person sessions</li>
                        <li class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Make lasting impact</li>
                        <li class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Join mentor community</li>
                    </ul>
                    <a href="#mentor-form" class="btn btn-blue w-100">Apply as Mentor</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-custom h-100 p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-hand-holding-heart fa-5x" style="color: #D4AF37;"></i>
                    </div>
                    <h3 class="text-center mb-3" style="color: #4B0082;">Sponsor a Student</h3>
                    <p class="text-center mb-4">Provide financial support to enable deserving young people access our programs, events, and academy without financial barriers.</p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Direct impact on lives</li>
                        <li class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Regular progress updates</li>
                        <li class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Tax-deductible donation</li>
                        <li class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Recognition & appreciation</li>
                    </ul>
                    <a href="#sponsor-form" class="btn btn-gold w-100">Sponsor a Student</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mentor Form -->
<section class="section-padding bg-light" id="mentor-form">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-tie fa-4x mb-3" style="color: #D4AF37;"></i>
                            <h2 style="color: #4B0082;">Become a Mentor</h2>
                            <p class="text-muted">Fill out the form below to join our mentor network</p>
                        </div>
                        
                        <?php if ($mentorSuccess): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Thank you!</strong> Your mentor application has been received. We'll contact you within 48 hours.
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errors) && isset($_POST['mentor_submit'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php foreach ($errors as $error): ?>
                                    <div><?php echo htmlspecialchars($error); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Name </label>
                                <input type="text" name="mentor_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email Address </label>
                                <input type="email" name="mentor_email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Phone Number </label>
                                <input type="tel" name="mentor_phone" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Area of Expertise </label>
                                <input type="text" name="mentor_expertise" class="form-control" placeholder="e.g., Business, Technology, Finance">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Professional Experience</label>
                                <textarea name="mentor_experience" class="form-control" rows="4" placeholder="Tell us about your background and what you can offer as a mentor"></textarea>
                            </div>
                            <button type="submit" name="mentor_submit" class="btn btn-blue btn-lg w-100">
                                <i class="fas fa-paper-plane me-2"></i>Submit Application
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sponsor Form -->
<section class="section-padding" id="sponsor-form">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-hand-holding-heart fa-4x mb-3" style="color: #D4AF37;"></i>
                            <h2 style="color: #4B0082;">Sponsor a Student</h2>
                            <p class="text-muted">Transform a life through education and opportunity</p>
                        </div>
                        
                        <?php if ($sponsorSuccess): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Thank you!</strong> Your sponsorship commitment has been received. We'll send you payment details shortly.
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errors) && isset($_POST['sponsor_submit'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php foreach ($errors as $error): ?>
                                    <div><?php echo htmlspecialchars($error); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-info mb-4">
                            <strong>Sponsorship Packages:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Full Program: ₦50,000 (Academy enrollment)</li>
                                <li>Event Access: ₦25,000 (Trade fair ticket)</li>
                                <li>Partial Support: Any amount</li>
                            </ul>
                        </div>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Name </label>
                                <input type="text" name="sponsor_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email Address </label>
                                <input type="email" name="sponsor_email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Phone Number </label>
                                <input type="tel" name="sponsor_phone" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Sponsorship Amount (₦) </label>
                                <input type="number" name="sponsor_amount" class="form-control" min="1000" step="1000" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Student Preference (Optional)</label>
                                <textarea name="student_preference" class="form-control" rows="3" placeholder="Any preferences for the student you'd like to sponsor? (e.g., field of study, background)"></textarea>
                            </div>
                            <button type="submit" name="sponsor_submit" class="btn btn-gold btn-lg w-100">
                                <i class="fas fa-heart me-2"></i>Commit to Sponsorship
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Impact Stats -->
<section class="section-padding" style="background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%); color: white;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Partnership Impact</h2>
            <p class="lead">Together, we're making a difference</p>
        </div>
        <div class="row text-center g-4">
            <div class="col-md-3">
                <div class="p-4">
                    <i class="fas fa-users fa-4x mb-3" style="color: #D4AF37;"></i>
                    <h3 class="display-4 fw-bold mb-2">100+</h3>
                    <p class="lead">Active Mentors</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="fas fa-graduation-cap fa-4x mb-3" style="color: #D4AF37;"></i>
                    <h3 class="display-4 fw-bold mb-2">50+</h3>
                    <p class="lead">Students Sponsored</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="fas fa-handshake fa-4x mb-3" style="color: #D4AF37;"></i>
                    <h3 class="display-4 fw-bold mb-2">100+</h3>
                    <p class="lead">Mentorship Hours</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="fas fa-star fa-4x mb-3" style="color: #D4AF37;"></i>
                    <h3 class="display-4 fw-bold mb-2">85%</h3>
                    <p class="lead">Success Rate</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
