<?php
// register.php

session_start(); 
$pageTitle = "Event Registration";
require_once 'config/db.php';

$event = null;
$eventId = $_GET['event_id'] ?? null;
$errors = [];

// === FORM HANDLING STARTS HERE ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = trim($_POST['event_id'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $business_name = trim($_POST['business_name'] ?? '');
    $ticket_type = trim($_POST['ticket_type'] ?? '');
    
    if (empty($event_id)) $errors[] = "Please select an event";
    if (empty($full_name)) $errors[] = "Full name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($ticket_type)) $errors[] = "Please select a ticket type";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $selectedEvent = $stmt->fetch();

        if ($selectedEvent) {
            $amount_paid = $selectedEvent['regular_price'] ?? 0;
            $transaction_ref = 'MIN-' . time() . '-' . rand(1000, 9999);

            $stmt = $pdo->prepare("INSERT INTO registrations (event_id, full_name, email, phone, business_name, ticket_type, amount_paid, payment_status, transaction_ref) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
            $stmt->execute([$event_id, $full_name, $email, $phone, $business_name, $ticket_type, $amount_paid, $transaction_ref]);

            if ($amount_paid > 0) {
                header("Location: payments/paystack.php?reference=$transaction_ref&email=$email&amount=" . ($amount_paid*100));
            } else {
                $_SESSION['message'] = "Registration successful! Payment info will be sent later.";
                header("Location: register.php?event_id=$event_id");
            }
            exit();
        } else {
            $errors[] = "Invalid event selected";
        }
    }
}
require_once 'includes/header.php';

$event = null;
$eventId = $_GET['event_id'] ?? null;
$errors = [];

// Fetch event if ID provided
if ($eventId) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND status = 'active'");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();
}

// Fetch all active events for dropdown
$stmt = $pdo->prepare("SELECT * FROM events WHERE status = 'active' AND event_date >= CURDATE() ORDER BY event_date ASC");
$stmt->execute();
$allEvents = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = trim($_POST['event_id'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $business_name = trim($_POST['business_name'] ?? '');
    $ticket_type = trim($_POST['ticket_type'] ?? '');
    
    // Validation
    if (empty($event_id)) $errors[] = "Please select an event";
    if (empty($full_name)) $errors[] = "Full name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($ticket_type)) $errors[] = "Please select a ticket type";
    
    if (empty($errors)) {
        // Fetch event details
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $selectedEvent = $stmt->fetch();
        
        if ($selectedEvent) {
            // Determine pricing
            $isEarlyBird = (strtotime($selectedEvent['early_bird_deadline']) >= time());
            $amount_paid = $isEarlyBird ? $selectedEvent['early_bird_price'] : $selectedEvent['regular_price'];
            
            // Generate transaction reference
            $transaction_ref = 'MIN-' . time() . '-' . rand(1000, 9999);
            
            // Insert registration as pending
            try {
                $stmt = $pdo->prepare("INSERT INTO registrations (event_id, full_name, email, phone, business_name, ticket_type, amount_paid, payment_status, transaction_ref) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
                $stmt->execute([$event_id, $full_name, $email, $phone, $business_name, $ticket_type, $amount_paid, $transaction_ref]);
                
                // Redirect to payment
                $amountInKobo = $amount_paid * 100;
                header("Location: payments/paystack.php?reference=$transaction_ref&email=$email&amount=$amountInKobo");
                exit();
            } catch (PDOException $e) {
                $errors[] = "Registration error: " . $e->getMessage();
            }
        } else {
            $errors[] = "Invalid event selected";
        }
    }
}

// Calculate pricing if event selected
$isEarlyBird = false;
$displayPrice = 0;
if ($event) {
    $isEarlyBird = (strtotime($event['early_bird_deadline']) >= time());
    $displayPrice = $isEarlyBird ? $event['early_bird_price'] : $event['regular_price'];
}
?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%); color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Event <span style="color: #D4AF37;">Registration</span></h1>
        <p class="lead">Secure your spot at our upcoming events</p>
    </div>
</section>

<!-- Registration Form -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <h2 class="mb-4" style="color: #4B0082;">Registration Form</h2>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php foreach ($errors as $error): ?>
                                    <div><?php echo htmlspecialchars($error); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" id="registrationForm">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Select Event </label>
                               <select name="event_id" class="form-select" required id="eventSelect" onchange="updateEventDetails()">
                               <option value="">Choose an event...</option>
                                 <?php foreach ($allEvents as $e): ?>
                                 <option value="<?php echo $e['id']; ?>" 
                                   data-title="<?php echo htmlspecialchars($e['title']); ?>"
                                   data-location="<?php echo htmlspecialchars($e['location']); ?>"
                                   data-date="<?php echo $e['event_date']; ?>"
                                   data-earlybird-deadline="<?php echo $e['early_bird_deadline']; ?>"
                                   data-earlybird-price="<?php echo $e['early_bird_price']; ?>"
                                   data-regular-price="<?php echo $e['regular_price']; ?>"
                             <?php echo ($eventId && $e['id'] == $eventId) ? 'selected' : ''; ?>>
                       <?php echo htmlspecialchars($e['title']); ?> - <?php echo date('M d, Y', strtotime($e['event_date'])); ?>
                          </option>
                     <?php endforeach; ?>
                           </select>

                            </div>
                            
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
                                    <label class="form-label fw-bold">Business/Organization Name</label>
                                    <input type="text" name="business_name" class="form-control">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Ticket Type </label>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-check card p-3">
                                            <input class="form-check-input" type="radio" name="ticket_type" id="vendor" value="vendor" required>
                                            <label class="form-check-label" for="vendor">
                                                <i class="fas fa-store me-2" style="color: #D4AF37;"></i>
                                                <strong>Vendor</strong><br>
                                                <small class="text-muted">Sell products/services</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check card p-3">
                                            <input class="form-check-input" type="radio" name="ticket_type" id="attendee" value="attendee" required>
                                            <label class="form-check-label" for="attendee">
                                                <i class="fas fa-user me-2" style="color: #D4AF37;"></i>
                                                <strong>Attendee</strong><br>
                                                <small class="text-muted">Network & learn</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check card p-3">
                                            <input class="form-check-input" type="radio" name="ticket_type" id="sponsor" value="sponsor" required>
                                            <label class="form-check-label" for="sponsor">
                                                <i class="fas fa-handshake me-2" style="color: #D4AF37;"></i>
                                                <strong>Sponsor</strong><br>
                                                <small class="text-muted">Support the event</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-gold btn-lg w-100">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Event Details Card -->
                <div class="card border-0 shadow-lg sticky-top" style="top: 100px;" id="eventDetailsCard">
                    <?php if ($event): ?>
                        <div class="card-body p-4">
                            <h4 class="mb-3" style="color: #4B0082;">Event Details</h4>
                            <h5 class="mb-3" id="eventTitle"><?php echo htmlspecialchars($event['title']); ?></h5>
                            <div class="mb-2">
                                <i class="fas fa-map-marker-alt me-2" style="color: #D4AF37;"></i>
                                <span id="eventLocation"><?php echo htmlspecialchars($event['location']); ?></span>
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-calendar me-2" style="color: #D4AF37;"></i>
                                <span id="eventDate"><?php echo date('F j, Y', strtotime($event['event_date'])); ?></span>
                            </div>
                            <hr>
                            <div class="mb-3" id="pricingInfo">
                                <?php if ($isEarlyBird): ?>
                                    <div class="alert alert-success mb-2">
                                        <i class="fas fa-tag me-2"></i><strong>Early Bird Active!</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Early Bird Price:</span>
                                        <strong style="color: #4B0082;">₦<?php echo number_format($event['early_bird_price'], 0); ?></strong>
                                    </div>
                                    <small class="text-muted">Valid until <?php echo date('M j, Y', strtotime($event['early_bird_deadline'])); ?></small>
                                <?php else: ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Regular Price:</span>
                                        <strong style="color: #4B0082;">₦<?php echo number_format($event['regular_price'], 0); ?></strong>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h6 mb-0">Total Amount:</span>
                                <h3 class="mb-0" style="color: #D4AF37;" id="totalAmount">₦<?php echo number_format($displayPrice, 0); ?></h3>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-calendar-alt fa-4x mb-3" style="color: #D4AF37;"></i>
                            <p class="text-muted">Select an event to view details</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Security Badge -->
                <div class="card border-0 bg-light mt-3">
                    <div class="card-body text-center p-3">
                        <i class="fas fa-lock fa-2x mb-2" style="color: #4B0082;"></i>
                        <p class="mb-0 small"><strong>Secure Payment</strong><br>Powered by Paystack</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function updateEventDetails() {
    const select = document.getElementById('eventSelect');
    const option = select.options[select.selectedIndex];
    const eventDetailsCard = document.getElementById('eventDetailsCard');
    
    if (option.value) {
        const title = option.getAttribute('data-title');
        const location = option.getAttribute('data-location');
        const date = option.getAttribute('data-date');
        const earlyBirdDeadline = option.getAttribute('data-earlybird-deadline');
        const earlyBirdPrice = parseFloat(option.getAttribute('data-earlybird-price'));
        const regularPrice = parseFloat(option.getAttribute('data-regular-price'));
        
        // Check if early bird is active
        const now = new Date();
        const deadline = new Date(earlyBirdDeadline);
        const isEarlyBird = deadline >= now;
        const displayPrice = isEarlyBird ? earlyBirdPrice : regularPrice;
        
        // Update UI
        document.getElementById('eventTitle').textContent = title;
        document.getElementById('eventLocation').textContent = location;
        document.getElementById('eventDate').textContent = new Date(date).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'});
        
        let pricingHTML = '';
        if (isEarlyBird) {
            pricingHTML = `
                <div class="alert alert-success mb-2">
                    <i class="fas fa-tag me-2"></i><strong>Early Bird Active!</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Early Bird Price:</span>
                    <strong style="color: #4B0082;">₦${earlyBirdPrice.toLocaleString()}</strong>
                </div>
                <small class="text-muted">Valid until ${new Date(earlyBirdDeadline).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</small>
            `;
        } else {
            pricingHTML = `
                <div class="d-flex justify-content-between mb-2">
                    <span>Regular Price:</span>
                    <strong style="color: #4B0082;">₦${regularPrice.toLocaleString()}</strong>
                </div>
            `;
        }
        
        document.getElementById('pricingInfo').innerHTML = pricingHTML;
        document.getElementById('totalAmount').textContent = '₦' + displayPrice.toLocaleString();
        
        // Show the event details
        const cardContent = `
            <div class="card-body p-4">
                <h4 class="mb-3" style="color: #4B0082;">Event Details</h4>
                <h5 class="mb-3" id="eventTitle">${title}</h5>
                <div class="mb-2">
                    <i class="fas fa-map-marker-alt me-2" style="color: #D4AF37;"></i>
                    <span id="eventLocation">${location}</span>
                </div>
                <div class="mb-3">
                    <i class="fas fa-calendar me-2" style="color: #D4AF37;"></i>
                    <span id="eventDate">${new Date(date).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</span>
                </div>
                <hr>
                <div class="mb-3" id="pricingInfo">
                    ${pricingHTML}
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h6 mb-0">Total Amount:</span>
                    <h3 class="mb-0" style="color: #D4AF37;" id="totalAmount">₦${displayPrice.toLocaleString()}</h3>
                </div>
            </div>
        `;
        
        eventDetailsCard.innerHTML = cardContent;
        eventDetailsCard.style.display = 'block';
    } else {
        // Show placeholder
        eventDetailsCard.innerHTML = `
            <div class="card-body p-4 text-center">
                <i class="fas fa-calendar-alt fa-4x mb-3" style="color: #D4AF37;"></i>
                <p class="text-muted">Select an event to view details</p>
            </div>
        `;
    }
}

// Initialize on page load if event is pre-selected
window.onload = function() {
    const select = document.getElementById('eventSelect');
    if (select.value) {
        updateEventDetails();
    }
};
</script>

<?php require_once 'includes/footer.php'; ?>
