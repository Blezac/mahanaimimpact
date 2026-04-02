<?php
// === 404.php ===
$pageTitle = "Page Not Found";
require_once 'includes/header.php';
?>

<section class="section-padding text-center" style="min-height: 60vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <i class="fas fa-exclamation-triangle fa-6x mb-4" style="color: #D4AF37;"></i>
                <h1 class="display-1 fw-bold mb-3" style="color: #4B0082;">404</h1>
                <h2 class="mb-4">Page Not Found</h2>
                <p class="lead mb-4">Sorry, the page you're looking for doesn't exist or has been moved.</p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="index.php" class="btn btn-blue btn-lg">
                        <i class="fas fa-home me-2"></i>Go Home
                    </a>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
