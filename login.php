<?php
/**
 * AIVORA - User Login Page (login.php)
 * Authenticates users using password_verify() and PDO prepared statements.
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

$pageTitle = "Sign In to Aivora - Personal Assistant";
$pageDesc  = "Log in to access your chat history, manage your profile, and interact with the Aivora AI Copilot.";

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verify CSRF
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = "Security token mismatch. Please try again.";
    }

    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = "Please provide both your email address and password.";
    }

    if (empty($errors)) {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Successful authentication
                login_user($user);
                set_flash('success', "Welcome back, <strong>" . escape($user['name']) . "</strong>!");
                redirect('dashboard.php');
            } else {
                $errors[] = "Invalid email address or password combination.";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $errors[] = "Database communication error. Please try again later.";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5 position-relative">
  <div class="glow-orb glow-orb-1"></div>
  <div class="glow-orb glow-orb-2"></div>

  <div class="row justify-content-center position-relative" style="z-index: 1;">
    <div class="col-md-6 col-lg-5">
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
          <h2 class="h3 fw-bold text-light mb-1">Welcome Back</h2>
          <p class="text-muted small">Sign in to your Aivora account.</p>
        </div>

        <!-- Errors -->
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger alert-dismissible fade show mb-4 small" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i>
            <?php echo implode('<br>', array_map('escape', $errors)); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
          <?php echo csrf_field(); ?>

          <!-- Email -->
          <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
              <span class="input-group-text bg-dark border-secondary border-opacity-50 text-muted"><i class="bi bi-envelope"></i></span>
              <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="<?php echo escape($email); ?>" required>
            </div>
          </div>

          <!-- Password -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="password" class="form-label mb-0">Password</label>
              <a href="contact.php?subject=Password%20Reset" class="text-cyan small">Forgot?</a>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-dark border-secondary border-opacity-50 text-muted"><i class="bi bi-lock"></i></span>
              <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
            </div>
          </div>

          <!-- Submit -->
          <button type="submit" class="btn btn-primary-gradient w-100 py-2 mb-3">
            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
          </button>

          <!-- University Demo Credentials Helper -->
          <div class="p-3 rounded-3 mb-3" style="background: rgba(255, 255, 255, 0.03); border: 1px dashed var(--border-color);">
            <div class="text-light small fw-bold mb-1"><i class="bi bi-info-circle text-cyan me-1"></i> University Examiner Demo Account:</div>
            <div class="text-muted small">Email: <code>demo@aivora.com</code></div>
            <div class="text-muted small">Password: <code>User@123</code></div>
          </div>

          <div class="text-center small text-muted">
            Don't have an account yet? <a href="register.php" class="text-cyan fw-bold">Create one free</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
