<?php
/**
 * AIVORA - Contact & Support Page (contact.php)
 * Handles customer inquiries, server-side validation, CSRF protection,
 * honeypot anti-spam, and inserts into `contacts` table.
 * Course: Advanced Web Development in PHP (Code 2010043347)
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Contact & Academic Inquiries - Aivora AI";
$pageDesc  = "Get in touch with the Aivora team for technical support, university partnership inquiries, enterprise pricing, or feedback.";

$errors = [];
$name    = '';
$email   = '';
$subject = sanitize($_GET['subject'] ?? '');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verify CSRF Token
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = "Security token mismatch. Please refresh and try again.";
    }

    // 2. Honeypot check (bots populate hidden fields)
    if (!empty($_POST['website_hp'])) {
        // Silently redirect bot
        redirect('contact.php?status=sent');
    }

    // 3. Extract & Sanitize
    $name    = sanitize($_POST['name'] ?? '');
    $email   = strtolower(trim($_POST['email'] ?? ''));
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    // 4. Server-Side Validation
    if (empty($name) || mb_strlen($name) < 2) {
        $errors[] = "Please provide your full name (minimum 2 characters).";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }

    if (empty($subject) || mb_strlen($subject) < 3) {
        $errors[] = "Please specify a subject for your message (minimum 3 characters).";
    }

    if (empty($message) || mb_strlen($message) < 10) {
        $errors[] = "Message is too short. Please include at least 10 characters.";
    }

    // 5. If valid, insert into `contacts` table
    if (empty($errors)) {
        try {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO contacts (name, email, subject, message, created_at) VALUES (:name, :email, :subject, :message, NOW())");
            $stmt->execute([
                ':name'    => $name,
                ':email'   => $email,
                ':subject' => $subject,
                ':message' => $message
            ]);

            set_flash('success', "Thank you, <strong>" . escape($name) . "</strong>! Your message has been received. Our team will get back to you shortly.");
            redirect('contact.php');

        } catch (PDOException $e) {
            error_log("Contact form error: " . $e->getMessage());
            $errors[] = "An unexpected error occurred while saving your message. Please try again later.";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5 position-relative">
  <div class="glow-orb glow-orb-1"></div>
  <div class="glow-orb glow-orb-2"></div>

  <!-- Header -->
  <div class="row justify-content-center text-center mb-5 position-relative" style="z-index: 1;">
    <div class="col-lg-8">
      <div class="hero-badge mx-auto">
        <i class="bi bi-chat-quote-fill text-cyan"></i>
        <span>We Are Here to Assist</span>
      </div>
      <h1 class="display-5 fw-bold mb-3">Get in Touch with <span class="text-gradient">Aivora</span></h1>
      <p class="text-muted lead mx-auto" style="max-width: 600px;">
        Have a question about our models, student discounts, or technical implementation? Drop us a message below.
      </p>
    </div>
  </div>

  <div class="row g-5 position-relative" style="z-index: 1;">
    <!-- Contact Info Cards -->
    <div class="col-lg-5">
      <div class="d-flex flex-column gap-4">
        <!-- Card 1 -->
        <div class="glass-card p-4">
          <div class="d-flex align-items-center gap-3">
            <div class="feature-icon-box m-0" style="width: 50px; height: 50px; font-size: 1.4rem;">
              <i class="bi bi-envelope-at-fill"></i>
            </div>
            <div>
              <h5 class="h6 fw-bold text-light mb-1">Direct Email</h5>
              <p class="text-muted small mb-0">support@aivora.com</p>
              <p class="text-muted small mb-0">academic@aivora.com</p>
            </div>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="glass-card p-4">
          <div class="d-flex align-items-center gap-3">
            <div class="feature-icon-box m-0" style="width: 50px; height: 50px; font-size: 1.4rem;">
              <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div>
              <h5 class="h6 fw-bold text-light mb-1">Academic Assignment Info</h5>
              <p class="text-muted small mb-0">Course: Advanced Web Development in PHP</p>
              <p class="text-muted small mb-0">Code: 2010043347 | Sem V</p>
            </div>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="glass-card p-4">
          <div class="d-flex align-items-center gap-3">
            <div class="feature-icon-box m-0" style="width: 50px; height: 50px; font-size: 1.4rem;">
              <i class="bi bi-clock-fill"></i>
            </div>
            <div>
              <h5 class="h6 fw-bold text-light mb-1">Operational Hours</h5>
              <p class="text-muted small mb-0">Mon – Fri: 9:00 AM – 6:00 PM</p>
              <p class="text-muted small mb-0">Weekend: Automated Copilot Support</p>
            </div>
          </div>
        </div>

        <!-- Security Note -->
        <div class="p-3 rounded-3" style="background: rgba(124, 58, 237, 0.1); border: 1px solid rgba(124, 58, 237, 0.3);">
          <small class="text-light d-flex align-items-center gap-2">
            <i class="bi bi-shield-lock-fill text-cyan fs-5"></i>
            <span>Your information is protected with PDO prepared statements and never shared.</span>
          </small>
        </div>
      </div>
    </div>

    <!-- Contact Form -->
    <div class="col-lg-7">
      <div class="glass-card p-4 p-md-5">
        <h3 class="h4 fw-bold text-light mb-4">Send a Message</h3>

        <!-- Error Alert -->
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-1"></i> Please resolve the following:</div>
            <ul class="mb-0 small ps-3">
              <?php foreach ($errors as $err): ?>
                <li><?php echo escape($err); ?></li>
              <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <form method="POST" action="contact.php" novalidate>
          <?php echo csrf_field(); ?>

          <!-- Honeypot Field (Hidden from real users, caught by bots) -->
          <div style="display:none;" aria-hidden="true">
            <input type="text" name="website_hp" tabindex="-1" autocomplete="off">
          </div>

          <div class="row g-3">
            <!-- Name -->
            <div class="col-md-6">
              <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-dark border-secondary border-opacity-50 text-muted"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" value="<?php echo escape($name); ?>" required>
              </div>
            </div>

            <!-- Email -->
            <div class="col-md-6">
              <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-dark border-secondary border-opacity-50 text-muted"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" placeholder="john@example.com" value="<?php echo escape($email); ?>" required>
              </div>
            </div>

            <!-- Subject -->
            <div class="col-12">
              <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-dark border-secondary border-opacity-50 text-muted"><i class="bi bi-chat-left-text"></i></span>
                <input type="text" class="form-control" id="subject" name="subject" placeholder="e.g. Student Discount Inquiry or Technical Question" value="<?php echo escape($subject); ?>" required>
              </div>
            </div>

            <!-- Message -->
            <div class="col-12">
              <label for="message" class="form-label">Your Message <span class="text-danger">*</span></label>
              <textarea class="form-control" id="message" name="message" rows="5" placeholder="Describe your question or feedback in detail..." required><?php echo escape($message); ?></textarea>
              <div class="form-text text-muted small">Minimum 10 characters.</div>
            </div>

            <!-- Submit Button -->
            <div class="col-12 mt-4">
              <button type="submit" class="btn btn-primary-gradient w-100 py-3">
                <i class="bi bi-send-fill me-1"></i>
                <span>Send Inquiries Directly</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
