<?php
/**
 * AIVORA - Custom 404 Not Found Page
 * Course: Advanced Web Development in PHP (Code 2010043347)
 */

require_once __DIR__ . '/includes/config.php';
http_response_code(404);

$pageTitle = "404 - Neural Route Not Found | Aivora";
$pageDesc = "The requested neural path does not exist on the Aivora platform.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5 text-center my-auto position-relative" style="min-height: 60vh;">
  <div class="glow-orb glow-orb-1"></div>
  <div class="glow-orb glow-orb-2"></div>

  <div class="row justify-content-center position-relative" style="z-index: 1;">
    <div class="col-lg-7">
      <div class="glass-card p-5 mt-4 text-center">
        <div class="display-1 fw-bold text-gradient mb-2">404</div>
        <div class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50 px-3 py-2 rounded-pill mb-3">
          <i class="bi bi-slash-circle me-1"></i> Missing Endpoint
        </div>
        <h2 class="h3 fw-bold text-light mb-3">This Page Has Vanished into Latent Space</h2>
        <p class="text-muted mb-4 lead fs-6">
          The link you followed may be broken, expired, or the page may have been moved. Even our advanced neural models could not locate this document.
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3">
          <a href="index.php" class="btn btn-primary-gradient px-4 py-2">
            <i class="bi bi-house-door-fill me-1"></i> Return Home
          </a>
          <a href="demo.php" class="btn btn-outline-glow px-4 py-2">
            <i class="bi bi-chat-dots-fill me-1"></i> Ask Aivora
          </a>
          <a href="contact.php" class="btn btn-outline-glow px-4 py-2">
            <i class="bi bi-envelope-fill me-1"></i> Contact Support
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
