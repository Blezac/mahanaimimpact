<?php
// === install.php ===
// Simple installation checker and guide

$checks = [
    'php_version' => [
        'name' => 'PHP Version (>= 7.4)',
        'status' => version_compare(PHP_VERSION, '7.4.0', '>='),
        'message' => 'Current: ' . PHP_VERSION
    ],
    'pdo' => [
        'name' => 'PDO Extension',
        'status' => extension_loaded('pdo'),
        'message' => extension_loaded('pdo') ? 'Installed' : 'Not installed'
    ],
    'pdo_mysql' => [
        'name' => 'PDO MySQL Driver',
        'status' => extension_loaded('pdo_mysql'),
        'message' => extension_loaded('pdo_mysql') ? 'Installed' : 'Not installed'
    ],
    'gd' => [
        'name' => 'GD Library (for QR codes)',
        'status' => extension_loaded('gd'),
        'message' => extension_loaded('gd') ? 'Installed' : 'Not installed'
    ],
    'curl' => [
        'name' => 'cURL Extension',
        'status' => extension_loaded('curl'),
        'message' => extension_loaded('curl') ? 'Installed' : 'Not installed'
    ],
    'openssl' => [
        'name' => 'OpenSSL Extension',
        'status' => extension_loaded('openssl'),
        'message' => extension_loaded('openssl') ? 'Installed' : 'Not installed'
    ],
    'qrcodes_dir' => [
        'name' => 'QR Codes Directory Writable',
        'status' => is_writable('qrcodes'),
        'message' => is_writable('qrcodes') ? 'Writable' : 'Not writable - Run: chmod 755 qrcodes'
    ],
    'composer' => [
        'name' => 'Composer Dependencies',
        'status' => file_exists('vendor/autoload.php'),
        'message' => file_exists('vendor/autoload.php') ? 'Installed' : 'Not installed - Run: composer install'
    ]
];

$allPassed = true;
foreach ($checks as $check) {
    if (!$check['status']) {
        $allPassed = false;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Check - Mahanaim Impact Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .install-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .check-item {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .check-item:last-child {
            border-bottom: none;
        }
        .status-pass {
            color: #28a745;
        }
        .status-fail {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="install-card">
                    <div class="p-4 text-center" style="background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%); color: white; border-radius: 15px 15px 0 0;">
                        <i class="fas fa-cog fa-4x mb-3"></i>
                        <h2>Installation Check</h2>
                        <p class="mb-0">Mahanaim Impact Network</p>
                    </div>
                    
                    <div class="p-4">
                        <h4 class="mb-4">System Requirements</h4>
                        
                        <?php foreach ($checks as $key => $check): ?>
                            <div class="check-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo $check['name']; ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo $check['message']; ?></small>
                                </div>
                                <div>
                                    <?php if ($check['status']): ?>
                                        <i class="fas fa-check-circle fa-2x status-pass"></i>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle fa-2x status-fail"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <hr class="my-4">
                        
                        <?php if ($allPassed): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>All checks passed!</strong> You're ready to proceed with the installation.
                            </div>
                            
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Next Steps:</h5>
                                    <ol>
                                        <li>Create MySQL database named <code>mahanaim_db</code></li>
                                        <li>Import <code>database.sql</code> file</li>
                                        <li>Update database credentials in <code>config/db.php</code></li>
                                        <li>Configure Paystack API keys in <code>payments/paystack.php</code></li>
                                        <li>Set up SMTP email in <code>emails/mailer.php</code></li>
                                        <li>Delete this <code>install.php</code> file for security</li>
                                    </ol>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <a href="index.php" class="btn btn-lg" style="background: #4B0082; color: white;">
                                    <i class="fas fa-arrow-right me-2"></i>Go to Website
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Some requirements are not met.</strong> Please resolve the issues above before continuing.
                            </div>
                            
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Common Fixes:</h5>
                                    <ul>
                                        <li><strong>PHP Extensions:</strong> Install via your hosting control panel or <code>apt-get install php-[extension]</code></li>
                                        <li><strong>Composer:</strong> Run <code>composer install</code> in project directory</li>
                                        <li><strong>Directory Permissions:</strong> Run <code>chmod 755 qrcodes</code></li>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="README.md" class="text-white text-decoration-none">
                        <i class="fas fa-book me-1"></i>View Installation Guide
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
