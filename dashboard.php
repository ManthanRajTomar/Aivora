<?php
/**
 * AIVORA - User Dashboard Page (dashboard.php)
 * Authenticated user portal with chat history, profile details, and .txt export.
 * Course: Advanced Web Development in PHP (Code 2010043347)
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Enforce login
require_login('login.php');

$currentUser = current_user();
$userId = (int)$currentUser['id'];
$db = getDB();

// -------------------------------------------------------------
// 1. Export Chat History as .TXT
// -------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] === 'export_txt') {
    try {
        $exportStmt = $db->prepare("SELECT user_message, bot_response, created_at FROM chat_logs WHERE user_id = :uid ORDER BY created_at ASC");
        $exportStmt->execute([':uid' => $userId]);
        $logs = $exportStmt->fetchAll();

        $filename = "aivora_transcripts_" . date('Y-m-d_His') . ".txt";

        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo "========================================================\r\n";
        echo " AIVORA AI ASSISTANT - CONVERSATION TRANSCRIPT\r\n";
        echo " User: " . $currentUser['name'] . " (" . $currentUser['email'] . ")\r\n";
        echo " Export Date: " . date('Y-m-d H:i:s') . "\r\n";
        echo " Total Queries: " . count($logs) . "\r\n";
        echo "========================================================\r\n\r\n";

        foreach ($logs as $i => $log) {
            $num = $i + 1;
            echo "--------------------------------------------------------\r\n";
            echo "[#{$num}] Time: " . $log['created_at'] . "\r\n";
            echo "USER: " . $log['user_message'] . "\r\n\r\n";
            echo "AIVORA: " . $log['bot_response'] . "\r\n\r\n";
        }

        exit;
    } catch (PDOException $e) {
        error_log("Transcript export error: " . $e->getMessage());
        set_flash('danger', 'Unable to generate transcript file at this time.');
        redirect('dashboard.php');
    }
}

// -------------------------------------------------------------
// 2. Clear Personal Chat History
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear_history') {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        try {
            $clearStmt = $db->prepare("DELETE FROM chat_logs WHERE user_id = :uid");
            $clearStmt->execute([':uid' => $userId]);
            set_flash('success', 'Your personal chat history has been cleared.');
            redirect('dashboard.php');
        } catch (PDOException $e) {
            error_log("Clear history error: " . $e->getMessage());
            set_flash('danger', 'Error clearing history.');
        }
    } else {
        set_flash('danger', 'Security verification failed.');
    }
}

// -------------------------------------------------------------
// 3. Delete Single Chat Record
// -------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] === 'delete_item' && isset($_GET['id'])) {
    $delId = (int)$_GET['id'];
    if (verify_csrf($_GET['token'] ?? '')) {
        try {
            $delStmt = $db->prepare("DELETE FROM chat_logs WHERE id = :id AND user_id = :uid");
            $delStmt->execute([':id' => $delId, ':uid' => $userId]);
            set_flash('success', 'Conversation record deleted.');
        } catch (PDOException $e) {
            error_log("Delete item error: " . $e->getMessage());
            set_flash('danger', 'Error deleting conversation.');
        }
    } else {
        set_flash('danger', 'Security token mismatch.');
    }
    redirect('dashboard.php');
}

// -------------------------------------------------------------
// 4. Update Profile Details & Password
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        $newName         = sanitize($_POST['name'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($newName) || mb_strlen($newName) < 2) {
            set_flash('danger', 'Name must contain at least 2 characters.');
            redirect('dashboard.php');
        }

        try {
            if (!empty($newPassword)) {
                if (mb_strlen($newPassword) < 6) {
                    set_flash('danger', 'New password must be at least 6 characters.');
                    redirect('dashboard.php');
                }
                if ($newPassword !== $confirmPassword) {
                    set_flash('danger', 'New passwords do not match.');
                    redirect('dashboard.php');
                }

                $pwdCheckStmt = $db->prepare("SELECT password FROM users WHERE id = :id LIMIT 1");
                $pwdCheckStmt->execute([':id' => $userId]);
                $userRow = $pwdCheckStmt->fetch();

                if (!$userRow || !password_verify($currentPassword, $userRow['password'])) {
                    set_flash('danger', 'Current password is incorrect.');
                    redirect('dashboard.php');
                }

                $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
                $upStmt  = $db->prepare("UPDATE users SET name = :name, password = :pwd WHERE id = :id");
                $upStmt->execute([':name' => $newName, ':pwd' => $newHash, ':id' => $userId]);
            } else {
                $upStmt = $db->prepare("UPDATE users SET name = :name WHERE id = :id");
                $upStmt->execute([':name' => $newName, ':id' => $userId]);
            }

            $_SESSION['user_name'] = $newName;
            set_flash('success', 'Profile updated successfully.');
            redirect('dashboard.php');

        } catch (PDOException $e) {
            error_log("Profile update error: " . $e->getMessage());
            set_flash('danger', 'Database error updating profile.');
            redirect('dashboard.php');
        }
    } else {
        set_flash('danger', 'Security verification failed.');
        redirect('dashboard.php');
    }
}

// -------------------------------------------------------------
// 5. Fetch User Statistics & History
// -------------------------------------------------------------
$chatLogs = [];
$totalQueries = 0;
$userRecord = null;

try {
    // User record details
    $uStmt = $db->prepare("SELECT name, email, created_at FROM users WHERE id = :uid LIMIT 1");
    $uStmt->execute([':uid' => $userId]);
    $userRecord = $uStmt->fetch();

    // Total queries count
    $countStmt = $db->prepare("SELECT COUNT(*) FROM chat_logs WHERE user_id = :uid");
    $countStmt->execute([':uid' => $userId]);
    $totalQueries = (int)$countStmt->fetchColumn();

    // Recent 20 chats
    $histStmt = $db->prepare("SELECT * FROM chat_logs WHERE user_id = :uid ORDER BY created_at DESC LIMIT 20");
    $histStmt->execute([':uid' => $userId]);
    $chatLogs = $histStmt->fetchAll();

} catch (PDOException $e) {
    error_log("Dashboard query error: " . $e->getMessage());
}

$pageTitle = "My Dashboard - Aivora AI";
$pageDesc  = "Manage your Aivora account, view your query history, and export conversations.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5 position-relative">
  <div class="glow-orb glow-orb-1"></div>

  <!-- Page Header -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5 position-relative" style="z-index: 1;">
    <div>
      <div class="hero-badge mb-2">
        <i class="bi bi-shield-check text-success"></i>
        <span>Authenticated Workspace</span>
      </div>
      <h1 class="h3 fw-bold text-light mb-1">
        Hello, <span class="text-gradient"><?php echo escape($_SESSION['user_name'] ?? $currentUser['name']); ?></span>!
      </h1>
      <p class="text-muted small mb-0">Welcome to your personal Aivora neural control center.</p>
    </div>

    <div class="d-flex flex-wrap gap-2">
      <a href="demo.php" class="btn btn-primary-gradient">
        <i class="bi bi-chat-dots-fill me-1"></i> Start New Conversation
      </a>
      <a href="dashboard.php?action=export_txt" class="btn btn-outline-glow">
        <i class="bi bi-download me-1"></i> Export History (.txt)
      </a>
    </div>
  </div>

  <div class="row g-4 position-relative" style="z-index: 1;">
    <!-- Profile & Metrics Column -->
    <div class="col-lg-4">
      <!-- Profile Card -->
      <div class="glass-card p-4 mb-4">
        <div class="text-center pb-3 border-bottom border-secondary border-opacity-25 mb-3">
          <div class="chat-avatar bot mx-auto mb-3" style="width: 64px; height: 64px; font-size: 1.8rem;">
            <i class="bi bi-person-fill"></i>
          </div>
          <h5 class="fw-bold text-light mb-1"><?php echo escape($_SESSION['user_name'] ?? $currentUser['name']); ?></h5>
          <span class="badge bg-primary bg-opacity-25 text-cyan border border-cyan border-opacity-25 px-3 py-1 rounded-pill small">
            Free Starter Tier
          </span>
        </div>

        <ul class="list-unstyled small text-muted mb-3 d-flex flex-column gap-2">
          <li class="d-flex justify-content-between">
            <span>Email:</span>
            <span class="text-light fw-semibold"><?php echo escape($currentUser['email']); ?></span>
          </li>
          <li class="d-flex justify-content-between">
            <span>Member Since:</span>
            <span class="text-light"><?php echo !empty($userRecord['created_at']) ? date('M d, Y', strtotime($userRecord['created_at'])) : 'N/A'; ?></span>
          </li>
          <li class="d-flex justify-content-between">
            <span>Session Status:</span>
            <span class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> Active</span>
          </li>
        </ul>

        <button type="button" class="btn btn-outline-glow btn-sm w-100 py-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
          <i class="bi bi-gear-fill me-1"></i> Account Settings
        </button>
      </div>

      <!-- Quick Metrics -->
      <div class="glass-card p-4 mb-4">
        <h6 class="fw-bold text-light mb-3"><i class="bi bi-bar-chart-fill text-cyan me-2"></i>Usage Overview</h6>
        <div class="row g-2 text-center">
          <div class="col-6">
            <div class="p-3 rounded-3" style="background: rgba(0, 0, 0, 0.2);">
              <div class="fs-3 fw-bold text-cyan"><?php echo $totalQueries; ?></div>
              <small class="text-muted">Total Queries</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 rounded-3" style="background: rgba(0, 0, 0, 0.2);">
              <div class="fs-3 fw-bold text-success">30</div>
              <small class="text-muted">Daily Limit</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Clear History Form -->
      <?php if ($totalQueries > 0): ?>
        <div class="glass-card p-4">
          <h6 class="fw-bold text-light mb-2"><i class="bi bi-trash3 text-danger me-2"></i>Data Management</h6>
          <p class="text-muted small mb-3">Permanently remove all your logged conversations from the server database.</p>
          <form method="POST" action="dashboard.php" onsubmit="return confirm('Are you sure you want to delete all your saved chat logs? This cannot be undone.');">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="clear_history">
            <button type="submit" class="btn btn-outline-danger btn-sm w-100 py-2">
              <i class="bi bi-trash me-1"></i> Clear My Chat History
            </button>
          </form>
        </div>
      <?php endif; ?>
    </div>

    <!-- Chat Logs Column -->
    <div class="col-lg-8">
      <div class="glass-card p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary border-opacity-25">
          <h5 class="fw-bold text-light mb-0">
            <i class="bi bi-clock-history text-cyan me-2"></i>Your Saved Interactions
          </h5>
          <span class="badge bg-secondary bg-opacity-25 text-light small">
            Showing <?php echo count($chatLogs); ?> recent
          </span>
        </div>

        <?php if (!empty($chatLogs)): ?>
          <div class="d-flex flex-column gap-3">
            <?php foreach ($chatLogs as $log): ?>
              <div class="p-3 rounded-3" style="background: rgba(0, 0, 0, 0.25); border: 1px solid var(--border-color);">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary bg-opacity-25 text-cyan border border-cyan border-opacity-25 small">
                      <i class="bi bi-person me-1"></i> You
                    </span>
                    <strong class="text-light small"><?php echo escape(truncate_words($log['user_message'], 12)); ?></strong>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <small class="text-muted" style="font-size: 0.72rem;">
                      <?php echo time_ago($log['created_at']); ?>
                    </small>
                    <a href="dashboard.php?action=delete_item&id=<?php echo $log['id']; ?>&token=<?php echo csrf_token(); ?>" class="btn btn-sm text-danger p-0 ms-1" title="Delete record" onclick="return confirm('Delete this conversation entry?');">
                      <i class="bi bi-x-circle small"></i>
                    </a>
                  </div>
                </div>

                <div class="ps-3 border-start border-cyan border-2 my-2 py-1">
                  <div class="small text-muted fw-bold mb-1 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-stars text-cyan me-1"></i> Aivora Reply:</span>
                    <button type="button" class="btn btn-sm text-muted p-0 copy-db-btn" data-text="<?php echo escape($log['bot_response']); ?>" title="Copy answer">
                      <i class="bi bi-clipboard small"></i>
                    </button>
                  </div>
                  <div class="small text-light opacity-90" style="white-space: pre-wrap; max-height: 140px; overflow-y: auto;">
                    <?php echo escape($log['bot_response']); ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-5 text-muted">
            <i class="bi bi-chat-dots fs-1 text-muted mb-3 d-block"></i>
            <h6>No Chat History Yet</h6>
            <p class="small mb-3">You haven't asked any questions while signed in yet.</p>
            <a href="demo.php" class="btn btn-primary-gradient px-4 py-2">
              <i class="bi bi-lightning-charge-fill me-1"></i> Launch Aivora Chat
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-card border border-secondary border-opacity-50" style="background: var(--bg-surface);">
      <div class="modal-header border-bottom border-secondary border-opacity-25">
        <h5 class="modal-title h6 fw-bold text-light" id="editProfileLabel">
          <i class="bi bi-person-gear text-cyan me-2"></i>Account & Security Settings
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="dashboard.php">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="update_profile">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small text-muted">Registered Email (Cannot be changed)</label>
            <input type="text" class="form-control" value="<?php echo escape($currentUser['email']); ?>" disabled>
          </div>
          <div class="mb-3">
            <label for="profile_name" class="form-label small">Display Name <span class="text-danger">*</span></label>
            <input type="text" id="profile_name" name="name" class="form-control" value="<?php echo escape($_SESSION['user_name'] ?? $currentUser['name']); ?>" required>
          </div>
          <hr class="border-secondary border-opacity-25 my-3">
          <div class="small fw-bold text-light mb-2"><i class="bi bi-key-fill text-warning me-1"></i> Change Password (Leave blank to keep unchanged)</div>
          <div class="mb-2">
            <label class="form-label small text-muted">Current Password</label>
            <input type="password" name="current_password" class="form-control" placeholder="Required only if changing password">
          </div>
          <div class="row g-2">
            <div class="col-md-6 mb-2">
              <label class="form-label small text-muted">New Password</label>
              <input type="password" name="new_password" class="form-control" placeholder="Min. 6 chars">
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label small text-muted">Confirm New</label>
              <input type="password" name="confirm_password" class="form-control" placeholder="Repeat new">
            </div>
          </div>
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-25">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-primary-gradient px-3">
            <i class="bi bi-check2-circle me-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.copy-db-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const text = btn.getAttribute('data-text');
      if (text) {
        navigator.clipboard.writeText(text).then(() => {
          btn.innerHTML = '<i class="bi bi-check2 text-success small"></i>';
          setTimeout(() => { btn.innerHTML = '<i class="bi bi-clipboard small"></i>'; }, 1500);
        });
      }
    });
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
