<?php
/**
 * AIVORA - User Registration Page (register.php)
 * Handles new user signup with password_hash() and PDO prepared statements.
 * Course: Advanced Web Development in PHP (Code 2010043347)
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// If already logged in, redirect to dashboard
if (is_logged_in()) {
    redirect('dashboard.php');
}

$pageTitle = "Create Your Account - Aivora AI";
$pageDesc  = "Sign up for Aivora to access conversational AI tools, save chat history, and personalize your experience.";

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verify CSRF
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = "Security token expired. Please try again.";
    }

    $name     = sanitize($_POST['name'] ?? '');
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($name) || mb_strlen($name) < 2) {
        $errors[] = "Please provide your full name (minimum 2 characters).";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (mb_strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // Check if email already exists
    if (empty($errors)) {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);

            if ($stmt->fetch()) {
                $errors[] = "An account with this email address already exists. Please log in.";
            } else {
                // Hash password securely with Bcrypt
                $hash = password_hash($password, PASSWORD_BCRYPT);

                $insertStmt = $db->prepare("INSERT INTO users (name, email, password, created_at) VALUES (:name, :email, :pass, NOW())");
                $insertStmt->execute([
                    ':name'  => $name,
                    ':email' => $email,
                    ':pass'  => $hash
                ]);

                $newUserId = (int)$db->lastInsertId();

                // Automatically log user in
                login_user([
                    'id'    => $newUserId,
                    'name'  => $name,
                    'email' => $email
                ]);

                set_flash('success', "Welcome to Aivora, <strong>" . escape($name) . "</strong>! Your account has been created.");
                redirect('dashboard.php');
            }
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $errors[] = "A database error occurred during registration. Please try again later.";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5 position-relative">
  <div class="glow-orb glow-orb-1"></div>
  <div class="glow-orb glow-orb-2"></div>

  <div class="row justify-content-center position-relative" style="z-index: 1;">
    <div class="col-md-7 col-lg-5">
      <div class="glass-card p-4 p-md-5 my-3">
        <!-- Brand Header -->
        <div class="text-center mb-4">
          <div class="d-inline-block mb-3">
            <svg width="48" height="48" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect width="100" height="100" rx="24" fill="#7C3AED"/>
              <circle cx="50" cy="50" r="22" fill="#06B6D4"/>
              <path d="M50 15 L50 85 M15 50 L85 50" stroke="#FFFFFF" stroke-width="8" stroke-linecap="round"/>
            </svg>
          </div>
          <h2 class="h3 fw-bold text-light mb-1">Create an Account</h2>
          <p class="text-muted small">Start using Aivora's neural capabilities today.</p>
        </div>

        <!-- Errors -->
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger alert-dismissible fade show mb-4 small" role="alert">
            <ul class="mb-0 ps-3">
              <?php foreach ($errors as $err): ?>
                <li><?php echo escape($err); ?></li>
              <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <form method="POST" action="register.php" novalidate>
          <?php echo csrf_field(); ?>

          <!-- Full Name -->
          <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <div class="input-group">
              <span class="input-group-text bg-dark border-secondary border-opacity-50 text-muted"><i class="bi bi-person"></i></span>
              <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" value="<?php echo escape($name); ?>" required>
            </div>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
              <span class="input-group-text bg-dark border-secondary border-opacity-50 text-muted"><i class="bi bi-envelope"></i></span>
              <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="<?php echo escape($email); ?>" required>
            </div>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
              <span class="input-group-text bg-dark border-secondary border-opacity-50 text-muted"><i class="bi bi-lock"></i></span>
              <input type="password" class="form-control" id="password" name="password" placeholder="At least 6 characters" required>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="mb-4">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <div class="input-group">
              <span class="input-group-text bg-dark border-secondary border-opacity-50 text-muted"><i class="bi bi-shield-check"></i></span>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
            </div>
          </div>

          <!-- Submit -->
          <button type="submit" class="btn btn-primary-gradient w-100 py-2 mb-3">
            <i class="bi bi-person-plus-fill me-1"></i> Register Account
          </button>

          <div class="text-center small text-muted">
            Already have an account? <a href="login.php" class="text-cyan fw-bold">Sign In here</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
