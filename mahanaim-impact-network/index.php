<?php
// === index.php ===
$pageTitle = "Home";
require_once 'config/db.php';
require_once 'includes/header.php';

// Fetch upcoming events
$stmt = $pdo->prepare("SELECT * FROM events WHERE status = 'active' AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3");
$stmt->execute();
$upcomingEvents = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%); color: white; padding: 6rem 0; position: relative; overflow: hidden;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0" style="animation: fadeInLeft 1s ease;">
                <div class="mb-3">
                    <span class="badge" style="background: #D4AF37; color: #4B0082; padding: 0.5rem 1.5rem; font-size: 1rem; font-weight: 700;">Mahanaim Impact Network</span>
                </div>
                <h1 class="display-3 fw-bold mb-4" style="font-size: 3.5rem;">Driving SDG 1, 5 & 8 <span style="color: #D4AF37;">Through Impact</span></h1>
                <p class="lead mb-4" style="font-size: 1.3rem; line-height: 1.8;">
                    Empowering youth and women through our <strong>Five Pillars</strong>: Trade Fairs, Financial Literacy, Mentorship, Humanitarian Support, and Community Building.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="register.php" class="btn btn-gold btn-lg"><i class="fas fa-ticket-alt me-2"></i>Attend Our Trade Fair</a>
                    <a href="about.php" class="btn btn-outline-light btn-lg">Discover the Five Pillars</a>
                </div>
                <div class="mt-4">
                    <p class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>No Poverty (SDG 1)</p>
                    <p class="mb-2"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Gender Equality (SDG 5)</p>
                    <p class="mb-0"><i class="fas fa-check-circle me-2" style="color: #D4AF37;"></i>Decent Work & Economic Growth (SDG 8)</p>
                </div>
            </div>
            <div class="col-lg-6" style="animation: fadeInRight 1s ease;">
                <div class="position-relative">
                    <i class="fas fa-handshake" style="font-size: 25rem; opacity: 0.1; position: absolute; top: -50%; right: -20%;"></i>
                    <div class="card border-0 shadow-lg" style="background: rgba(255,255,255,0.95); border-radius: 20px; padding: 2rem;">
                        <div class="card-body text-center">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-store fa-3x mb-2" style="color: #D4AF37;"></i>
                                        <h4 class="fw-bold" style="color: #4B0082;">50+</h4>
                                        <p class="mb-0" style="color: #666;">Vendors</p>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-users fa-3x mb-2" style="color: #D4AF37;"></i>
                                        <h4 class="fw-bold" style="color: #4B0082;">100+</h4>
                                        <p class="mb-0" style="color: #666;">Youth Reached</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-graduation-cap fa-3x mb-2" style="color: #D4AF37;"></i>
                                        <h4 class="fw-bold" style="color: #4B0082;">80+</h4>
                                        <p class="mb-0" style="color: #666;">Trained</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-hands-helping fa-3x mb-2" style="color: #D4AF37;"></i>
                                        <h4 class="fw-bold" style="color: #4B0082;">200+</h4>
                                        <p class="mb-0" style="color: #666;">Mentors</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission Overview -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Our Mission</h2>
            <p class="lead mt-4">Building a generation of economically empowered and purpose-driven youth</p>
        </div>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card card-custom h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-bullseye fa-4x" style="color: #D4AF37;"></i>
                    </div>
                    <h4 class="mb-3" style="color: #4B0082;">Vision</h4>
                    <p>To be Africa's leading platform for youth economic empowerment and legacy building.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-heart fa-4x" style="color: #D4AF37;"></i>
                    </div>
                    <h4 class="mb-3" style="color: #4B0082;">Values</h4>
                    <p>Faith, Excellence, Integrity, Innovation, and Community Impact.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-rocket fa-4x" style="color: #D4AF37;"></i>
                    </div>
                    <h4 class="mb-3" style="color: #4B0082;">Goal</h4>
                    <p>Empower 10,000 youth with skills, networks, and opportunities by 2026.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-trophy fa-4x" style="color: #D4AF37;"></i>
                    </div>
                    <h4 class="mb-3" style="color: #4B0082;">Impact</h4>
                    <p>Creating sustainable economic opportunities and lasting community transformation.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trade Fair Highlight -->
<section class="section-padding" style="background: linear-gradient(to right, #f8f9fa 0%, #ffffff 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="position-relative">
                    <i class="fas fa-store fa-20x" style="color: #4B0082; opacity: 0.05;"></i>
                    <img src="./assets/images/16.jpeg" class="img-fluid rounded shadow-lg" alt="Trade Fair" style="position: relative; z-index: 1;">
                </div>
            </div>
            <div class="col-lg-6">
                <h2 class="section-title mb-4">Mahanaim Trade Fair</h2>
                <p class="lead mb-4">Nigeria's premier youth-focused marketplace bringing together entrepreneurs, vendors, and opportunities.</p>
                <div class="mb-3 d-flex align-items-start">
                    <i class="fas fa-check-circle fa-2x me-3" style="color: #D4AF37;"></i>
                    <div>
                        <h5 style="color: #4B0082;">Network & Collaborate</h5>
                        <p>Connect with industry leaders, fellow entrepreneurs, and potential customers.</p>
                    </div>
                </div>
                <div class="mb-3 d-flex align-items-start">
                    <i class="fas fa-check-circle fa-2x me-3" style="color: #D4AF37;"></i>
                    <div>
                        <h5 style="color: #4B0082;">Showcase Your Business</h5>
                        <p>Display products, gain visibility, and close deals on the spot.</p>
                    </div>
                </div>
                <div class="mb-4 d-flex align-items-start">
                    <i class="fas fa-check-circle fa-2x me-3" style="color: #D4AF37;"></i>
                    <div>
                        <h5 style="color: #4B0082;">Learn & Grow</h5>
                        <p>Access workshops, masterclasses, and mentorship sessions.</p>
                    </div>
                </div>
                <a href="register.php" class="btn btn-blue btn-lg"><i class="fas fa-ticket-alt me-2"></i>Get Your Ticket</a>
            </div>
        </div>
    </div>
</section>

<!-- Academy Highlight -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800" class="img-fluid rounded shadow-lg" alt="Academy">
                </div>
            </div>
            <div class="col-lg-6 order-lg-1">
                <h2 class="section-title mb-4">Wealth & Investment Academy</h2>
                <p class="lead mb-4">Transform your financial future with our comprehensive financial literacy program.</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-3"><i class="fas fa-graduation-cap me-3" style="color: #D4AF37;"></i>Professional Financial Education</li>
                    <li class="mb-3"><i class="fas fa-chart-line me-3" style="color: #D4AF37;"></i>Investment Strategies</li>
                    <li class="mb-3"><i class="fas fa-piggy-bank me-3" style="color: #D4AF37;"></i>Wealth Building Principles</li>
                    <li class="mb-3"><i class="fas fa-user-tie me-3" style="color: #D4AF37;"></i>Expert Mentorship</li>
                </ul>
                <a href="academy.php" class="btn btn-gold btn-lg"><i class="fas fa-arrow-right me-2"></i>Learn More</a>
            </div>
        </div>
    </div>
</section>

<!-- Upcoming Events -->
<section class="section-padding" id="events">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Upcoming Events</h2>
            <p class="lead mt-4">Join us at our transformative events</p>
        </div>
        <div class="row g-4">
            <?php if (!empty($upcomingEvents)): ?>
                <?php foreach ($upcomingEvents as $event): ?>
                    <?php
                    $isEarlyBird = (strtotime($event['early_bird_deadline']) >= time());
                    $displayPrice = $isEarlyBird ? $event['early_bird_price'] : $event['regular_price'];
                    $eventDate = new DateTime($event['event_date']);
                    ?>
                    <div class="col-md-4">
                        <div class="card card-custom h-100">
                            <div class="card-body p-4">
                                <?php if ($isEarlyBird): ?>
                                    <span class="badge bg-success mb-3">Early Bird Active</span>
                                <?php endif; ?>
                                <h3 class="h4 mb-3" style="color: #4B0082;"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p class="mb-3"><?php echo htmlspecialchars(substr($event['description'], 0, 120)) . '...'; ?></p>
                                <div class="mb-2">
                                    <i class="fas fa-map-marker-alt me-2" style="color: #D4AF37;"></i>
                                    <span><?php echo htmlspecialchars($event['location']); ?></span>
                                </div>
                                <div class="mb-3">
                                    <i class="fas fa-calendar me-2" style="color: #D4AF37;"></i>
                                    <span><?php echo $eventDate->format('F j, Y'); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted">From</span>
                                        <h4 class="mb-0" style="color: #4B0082;">₦<?php echo number_format($displayPrice, 0); ?></h4>
                                    </div>
                                    <a href="register.php?event_id=<?php echo $event['id']; ?>" class="btn btn-gold">Register Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>No upcoming events at the moment. Check back soon!
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section-padding" style="background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%); color: white;">
    <div class="container text-center">
        <h2 class="display-4 fw-bold mb-4">Ready to Transform Your Future?</h2>
        <p class="lead mb-5">Join thousands of young people building their legacy with Mahanaim Impact Network</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="register.php" class="btn btn-gold btn-lg"><i class="fas fa-ticket-alt me-2"></i>Register for Event</a>
            <a href="partner.php" class="btn btn-outline-light btn-lg"><i class="fas fa-handshake me-2"></i>Partner With Us</a>
        </div>
    </div>
</section>

<!-- Donation CTA -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="section-title mb-4">Support Our Mission</h2>
                <p class="lead mb-3">Your donation helps empower youth with skills, resources, and opportunities for a better future.</p>
                <p class="mb-4">Every contribution makes a lasting impact in transforming lives and building legacies.</p>
            </div>
            <div class="col-lg-6 text-center">
                <div class="card border-0 shadow-lg p-5" style="border-radius: 20px;">
                    <i class="fas fa-heart fa-5x mb-4" style="color: #D4AF37;"></i>
                    <h3 class="mb-4" style="color: #4B0082;">Make a Donation</h3>
                    <p class="mb-4">Help us reach more youth and create greater impact</p>
                    <a href="donation.php" class="btn btn-gold btn-lg w-100"><i class="fas fa-donate me-2"></i>Donate Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?>
