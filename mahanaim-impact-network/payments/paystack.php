<?php
// === payments/paystack.php ===

// Paystack Configuration
define('PAYSTACK_SECRET_KEY', 'sk_test_your_secret_key_here'); // Replace with your secret key
define('PAYSTACK_PUBLIC_KEY', 'pk_test_your_public_key_here'); // Replace with your public key

$reference = $_GET['reference'] ?? null;
$email = $_GET['email'] ?? null;
$amount = $_GET['amount'] ?? null;

if (!$reference || !$email || !$amount) {
    die("Invalid payment parameters");
}

// Callback URL
$callback_url = 'http://' . $_SERVER['HTTP_HOST'] . '/verify.php?reference=' . $reference;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment - Mahanaim Impact Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .payment-card {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            text-align: center;
        }
        .spinner {
            width: 60px;
            height: 60px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #D4AF37;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 2rem;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="payment-card">
        <div class="spinner"></div>
        <h2 style="color: #4B0082;" class="mb-3">Initializing Payment</h2>
        <p class="text-muted mb-4">Please wait while we redirect you to the secure payment gateway...</p>
        <div class="text-center">
            <i class="fas fa-lock fa-2x" style="color: #D4AF37;"></i>
            <p class="small text-muted mt-2">Secured by Paystack</p>
        </div>
    </div>

    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        function payWithPaystack() {
            var handler = PaystackPop.setup({
                key: '<?php echo PAYSTACK_PUBLIC_KEY; ?>',
                email: '<?php echo $email; ?>',
                amount: <?php echo $amount; ?>,
                currency: 'NGN',
                ref: '<?php echo $reference; ?>',
                callback: function(response) {
                    window.location = '<?php echo $callback_url; ?>';
                },
                onClose: function() {
                    alert('Payment window closed. You can return to registration or try again.');
                    window.location = '../register.php';
                }
            });
            handler.openIframe();
        }
        
        // Auto-initiate payment after 1 second
        setTimeout(payWithPaystack, 1000);
    </script>
</body>
</html>
