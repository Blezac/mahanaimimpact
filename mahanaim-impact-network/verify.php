<?php
// === verify.php ===
require_once 'config/db.php';
require_once 'vendor/autoload.php'; // For PHPMailer and QR Code library

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$reference = $_GET['reference'] ?? null;
$success = false;
$error = null;
$registration = null;

if (!$reference) {
    $error = "Invalid payment reference";
} else {
    // Verify payment with Paystack
    define('PAYSTACK_SECRET_KEY', 'sk_test_your_secret_key_here');
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . $reference,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
            "Cache-Control: no-cache",
        ],
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    if ($err) {
        $error = "Payment verification failed: " . $err;
    } else {
        $result = json_decode($response, true);
        
        if ($result && $result['status'] && $result['data']['status'] === 'success') {
            // Payment successful, update database
            try {
                $pdo->beginTransaction();
                
                // Update registration status
                $stmt = $pdo->prepare("UPDATE registrations SET payment_status = 'paid' WHERE transaction_ref = ?");
                $stmt->execute([$reference]);
                
                // Fetch registration details
                $stmt = $pdo->prepare("SELECT r.*, e.title as event_title, e.location, e.event_date FROM registrations r JOIN events e ON r.event_id = e.id WHERE r.transaction_ref = ?");
                $stmt->execute([$reference]);
                $registration = $stmt->fetch();
                
                if ($registration) {
                    $registrationId = $registration['id'];
                    
                    // Generate QR Code
                    $qrData = "MIN-TICKET-" . $registrationId;
                    $qrCode = new QrCode($qrData);
                    $writer = new PngWriter();
                    $qrFilePath = "qrcodes/{$registrationId}.png";
                    $result = $writer->write($qrCode);
                    $result->saveToFile($qrFilePath);
                    
                    // Update QR code path
                    $stmt = $pdo->prepare("UPDATE registrations SET qr_code = ? WHERE id = ?");
                    $stmt->execute([$qrFilePath, $registrationId]);
                    
                    // Send confirmation email to participant
                    sendParticipantEmail($registration, $qrFilePath);
                    
                    // Send notification to admin
                    sendAdminNotification($registration);
                    
                    $pdo->commit();
                    $success = true;
                } else {
                    $pdo->rollBack();
                    $error = "Registration not found";
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Error processing registration: " . $e->getMessage();
            }
        } else {
            $error = "Payment verification failed. Please contact support.";
        }
    }
}

function sendParticipantEmail($registration, $qrPath) {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com'; // Replace
        $mail->Password = 'your_app_password'; // Replace
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('noreply@mahanaimnetwork.org', 'Mahanaim Impact Network');
        $mail->addAddress($registration['email'], $registration['full_name']);
        
        $mail->isHTML(true);
        $mail->Subject = 'Registration Confirmed - ' . $registration['event_title'];
        
        $eventDate = date('F j, Y', strtotime($registration['event_date']));
        
        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%); color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; background: #f9f9f9; }
                .ticket { background: white; padding: 20px; border-radius: 10px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .footer { text-align: center; padding: 20px; color: #666; }
                .button { display: inline-block; padding: 12px 30px; background: #D4AF37; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Registration Confirmed!</h1>
                </div>
                <div class='content'>
                    <h2>Hello {$registration['full_name']},</h2>
                    <p>Thank you for registering for <strong>{$registration['event_title']}</strong>!</p>
                    
                    <div class='ticket'>
                        <h3>Event Details</h3>
                        <p><strong>Event:</strong> {$registration['event_title']}</p>
                        <p><strong>Date:</strong> {$eventDate}</p>
                        <p><strong>Location:</strong> {$registration['location']}</p>
                        <p><strong>Ticket Type:</strong> " . ucfirst($registration['ticket_type']) . "</p>
                        <p><strong>Amount Paid:</strong> ₦" . number_format($registration['amount_paid'], 2) . "</p>
                        <p><strong>Transaction Ref:</strong> {$registration['transaction_ref']}</p>
                    </div>
                    
                    <p><strong>Your QR Code Ticket is attached to this email.</strong> Please present it at the event entrance for check-in.</p>
                    
                    <p>We look forward to seeing you at the event!</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Mahanaim Impact Network. All Rights Reserved.</p>
                    <p>Empowering Purpose. Building Legacy.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->addAttachment($qrPath, 'ticket-qr-code.png');
        
        $mail->send();
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
    }
}

function sendAdminNotification($registration) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com';
        $mail->Password = 'your_app_password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('noreply@mahanaimnetwork.org', 'Mahanaim Impact Network');
        $mail->addAddress('admin@mahanaimnetwork.org'); // Admin email
        
        $mail->isHTML(true);
        $mail->Subject = 'New Paid Registration - ' . $registration['event_title'];
        
        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>New Paid Registration Received</h2>
            <p><strong>Event:</strong> {$registration['event_title']}</p>
            <p><strong>Name:</strong> {$registration['full_name']}</p>
            <p><strong>Email:</strong> {$registration['email']}</p>
            <p><strong>Phone:</strong> {$registration['phone']}</p>
            <p><strong>Ticket Type:</strong> " . ucfirst($registration['ticket_type']) . "</p>
            <p><strong>Amount:</strong> ₦" . number_format($registration['amount_paid'], 2) . "</p>
            <p><strong>Transaction Ref:</strong> {$registration['transaction_ref']}</p>
        </body>
        </html>
        ";
        
        $mail->send();
    } catch (Exception $e) {
        error_log("Admin Email Error: " . $mail->ErrorInfo);
    }
}

$pageTitle = "Payment Confirmation";
require_once 'includes/header.php';
?>

<!-- Confirmation Section -->
<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($success && $registration): ?>
                    <div class="card border-0 shadow-lg text-center">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <i class="fas fa-check-circle fa-6x" style="color: #28a745;"></i>
                            </div>
                            <h1 class="mb-3" style="color: #4B0082;">Payment Successful!</h1>
                            <p class="lead mb-4">Thank you for registering for <strong><?php echo htmlspecialchars($registration['event_title']); ?></strong></p>
                            
                            <div class="alert alert-success mb-4">
                                <i class="fas fa-envelope me-2"></i>
                                A confirmation email with your QR code ticket has been sent to <strong><?php echo htmlspecialchars($registration['email']); ?></strong>
                            </div>
                            
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h4 class="mb-3">Your Ticket QR Code</h4>
                                    <?php if (file_exists($registration['qr_code'])): ?>
                                        <img src="<?php echo $registration['qr_code']; ?>" alt="QR Code" class="img-fluid mb-3" style="max-width: 250px;">
                                    <?php endif; ?>
                                    <p class="mb-2"><strong>Registration ID:</strong> MIN-TICKET-<?php echo $registration['id']; ?></p>
                                    <p class="mb-0"><strong>Transaction Ref:</strong> <?php echo htmlspecialchars($registration['transaction_ref']); ?></p>
                                </div>
                            </div>
                            
                            <div class="row text-start mb-4">
                                <div class="col-md-6">
                                    <h5 style="color: #4B0082;">Event Details</h5>
                                    <p class="mb-1"><i class="fas fa-calendar me-2"></i><?php echo date('F j, Y', strtotime($registration['event_date'])); ?></p>
                                    <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($registration['location']); ?></p>
                                    <p class="mb-0"><i class="fas fa-ticket-alt me-2"></i><?php echo ucfirst($registration['ticket_type']); ?> Ticket</p>
                                </div>
                                <div class="col-md-6">
                                    <h5 style="color: #4B0082;">Payment Details</h5>
                                    <p class="mb-1"><strong>Amount Paid:</strong> ₦<?php echo number_format($registration['amount_paid'], 2); ?></p>
                                    <p class="mb-0"><strong>Status:</strong> <span class="badge bg-success">Paid</span></p>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-3 justify-content-center">
                                <a href="index.php" class="btn btn-blue"><i class="fas fa-home me-2"></i>Back to Home</a>
                                <button onclick="window.print()" class="btn btn-gold"><i class="fas fa-print me-2"></i>Print Ticket</button>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-lg text-center">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <i class="fas fa-times-circle fa-6x" style="color: #dc3545;"></i>
                            </div>
                            <h1 class="mb-3" style="color: #dc3545;">Payment Failed</h1>
                            <p class="lead mb-4"><?php echo htmlspecialchars($error ?? 'An error occurred during payment verification'); ?></p>
                            <div class="d-flex gap-3 justify-content-center">
                                <a href="register.php" class="btn btn-blue"><i class="fas fa-redo me-2"></i>Try Again</a>
                                <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-home me-2"></i>Back to Home</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
