<?php
/**
 * AIVORA - Interactive Live Demo Page (demo.php)
 * Live chat interface communicating with chat_api.php via fetch().
 * Course: Advanced Web Development in PHP (Code 2010043347)
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Try Aivora - Live Interactive AI Assistant";
$pageDesc  = "Experience Aivora's universal intelligence in real-time. Ask anything: math, science, history, coding, writing, translations, or general knowledge.";

// Pass chat.js as extra script
$extraScripts = ['assets/js/chat.js'];

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4 position-relative">
  <div class="glow-orb glow-orb-1"></div>
  <div class="glow-orb glow-orb-2"></div>

  <!-- Page Title Header -->
  <div class="row justify-content-center text-center mb-4 position-relative" style="z-index: 1;">
    <div class="col-lg-8">
      <div class="hero-badge mx-auto mb-2">
        <i class="bi bi-sparkles text-cyan"></i>
        <span>Real-Time Neural Engine</span>
      </div>
      <h1 class="h3 fw-bold text-light mb-2">Interactive AI Playground</h1>
      <p class="text-muted small mb-0">
        Type any question or prompt below. Powered by keyword heuristics and logged via PHP PDO prepared statements.
      </p>
    </div>
  </div>

  <div class="row g-4 position-relative" style="z-index: 1;">
    <!-- Left Sidebar: Quick Prompts & Suggestions -->
    <div class="col-lg-4 order-lg-1 order-2">
      <!-- Quick Prompts Card -->
      <div class="glass-card p-4 mb-4">
        <h5 class="h6 fw-bold text-light mb-3 d-flex align-items-center gap-2">
          <i class="bi bi-lightbulb-fill text-warning"></i>
          <span>Sample Prompt Inquiries</span>
        </h5>
        <p class="text-muted small mb-3">Click any chip below to test Aivora across all domains:</p>

        <div class="d-flex flex-column gap-2">
          <button type="button" class="btn btn-sm text-start quick-prompt-pill" data-prompt="Write a Python function to check if a string is a palindrome.">
            <i class="bi bi-code-slash text-cyan me-1"></i> Python: Palindrome function
          </button>
          <button type="button" class="btn btn-sm text-start quick-prompt-pill" data-prompt="Solve 3x + 15 = 45 step by step.">
            <i class="bi bi-calculator text-warning me-1"></i> Math: Solve 3x + 15 = 45
          </button>
          <button type="button" class="btn btn-sm text-start quick-prompt-pill" data-prompt="Explain how photosynthesis works in 3 bullet points.">
            <i class="bi bi-flower1 text-success me-1"></i> Science: Photosynthesis
          </button>
          <button type="button" class="btn btn-sm text-start quick-prompt-pill" data-prompt="Who was the first person to walk on the moon and in what year?">
            <i class="bi bi-globe-americas text-info me-1"></i> History: First moon landing
          </button>
          <button type="button" class="btn btn-sm text-start quick-prompt-pill" data-prompt="Write a short 2-line inspirational quote for students.">
            <i class="bi bi-stars text-warning me-1"></i> Creative: Inspirational quote
          </button>
          <button type="button" class="btn btn-sm text-start quick-prompt-pill" data-prompt="Explain recursion in PHP with a code example.">
            <i class="bi bi-braces text-primary me-1"></i> PHP: Recursion explained
          </button>
          <button type="button" class="btn btn-sm text-start quick-prompt-pill" data-prompt="Write a professional follow-up email after a job interview.">
            <i class="bi bi-envelope text-cyan me-1"></i> Writing: Job follow-up email
          </button>
          <button type="button" class="btn btn-sm text-start quick-prompt-pill" data-prompt="Hello Aivora! Who are you and what can you do?">
            <i class="bi bi-emoji-smile text-success me-1"></i> About Aivora
          </button>
        </div>
      </div>

      <!-- System Status Card -->
      <div class="glass-card p-4">
        <h5 class="h6 fw-bold text-light mb-3 d-flex align-items-center gap-2">
          <i class="bi bi-hdd-network-fill text-cyan"></i>
          <span>System Environment</span>
        </h5>
        <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
          <li class="d-flex justify-content-between">
            <span>Engine:</span>
            <span class="text-light fw-semibold">PHP 8 & PDO</span>
          </li>
          <li class="d-flex justify-content-between">
            <span>Database:</span>
            <span class="text-light fw-semibold">MySQL (SmarterASP ready)</span>
          </li>
          <li class="d-flex justify-content-between">
            <span>Rule Engine:</span>
            <span class="badge bg-success bg-opacity-25 text-success">15+ Intent Rules</span>
          </li>
          <li class="d-flex justify-content-between">
            <span>Session Auth:</span>
            <span class="text-light"><?php echo is_logged_in() ? '<span class="text-success"><i class="bi bi-check2"></i> Logged In</span>' : 'Guest Mode'; ?></span>
          </li>
        </ul>

        <?php if (!is_logged_in()): ?>
          <div class="mt-3 pt-3 border-top border-secondary border-opacity-25 text-center">
            <small class="text-muted d-block mb-2">Want to save your chat history permanently?</small>
            <a href="login.php" class="btn btn-sm btn-outline-glow w-100 py-1">Sign In to Save Chats</a>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Right Main Chat Window -->
    <div class="col-lg-8 order-lg-2 order-1">
      <div class="chat-container-card">
        <!-- Chat Header -->
        <div class="chat-header">
          <div class="d-flex align-items-center gap-2">
            <div class="chat-avatar bot" style="width: 32px; height: 32px; font-size: 0.95rem;">
              <i class="bi bi-stars"></i>
            </div>
            <div>
              <div class="fw-bold text-light small lh-1">Aivora Copilot</div>
              <small class="text-success" style="font-size: 0.72rem;"><i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Active Neural Agent</small>
            </div>
          </div>

          <div class="d-flex align-items-center gap-2">
            <button type="button" id="clearChatBtn" class="btn btn-sm text-muted p-1" title="Clear conversation">
              <i class="bi bi-trash3 fs-6"></i>
            </button>
            <span class="badge bg-secondary bg-opacity-25 text-cyan border border-cyan border-opacity-25">Live Preview</span>
          </div>
        </div>

        <!-- Chat Messages Scroll Area -->
        <div class="chat-body-scroll" id="chatContainer">
          <!-- Initial Welcome Message from Bot -->
          <div class="chat-msg bot">
            <div class="chat-avatar bot"><i class="bi bi-stars"></i></div>
            <div class="msg-bubble">
              <div class="fw-semibold text-cyan mb-1">Welcome to Aivora!</div>
              <div>Hello! I am <strong>Aivora</strong> — your universal AI assistant. I can answer questions on <strong>math, science, history, coding, creative writing, general knowledge</strong>, and much more. How may I help you today?</div>
              <div class="text-muted small mt-1" style="font-size: 0.72rem;">Just now</div>
            </div>
          </div>
        </div>

        <!-- Chat Input Bar -->
        <div class="chat-input-bar">
          <form id="chatForm" class="d-flex gap-2">
            <?php echo csrf_field(); ?>
            <div class="input-group">
              <input type="text" id="chatInput" class="form-control" placeholder="Ask Aivora anything: code, math, essays, science, history, general knowledge..." autocomplete="off" required>
              <button class="btn btn-primary-gradient px-4" type="submit" id="sendBtn">
                <span class="d-none d-sm-inline">Send</span>
                <i class="bi bi-send-fill ms-1"></i>
              </button>
            </div>
          </form>
          <div class="d-flex justify-content-between align-items-center mt-2 px-1">
            <small class="text-muted" style="font-size: 0.75rem;">
              <i class="bi bi-shield-lock text-cyan me-1"></i> Prepared statements active. Conversations securely logged.
            </small>
            <small class="text-muted" style="font-size: 0.75rem;">Press Enter to send</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
