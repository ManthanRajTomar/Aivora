<?php
/**
 * AIVORA - Detailed Features Page (features.php)
 * Deep dive into AI capabilities loaded dynamically from the `features` table.
 * Course: Advanced Web Development in PHP (Code 2010043347)
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Features & Capabilities - Aivora AI";
$pageDesc  = "Explore Aivora's suite of AI capabilities: Conversational Chat, Writing Helper, Code Copilot, Document Summarizer, and Multilingual Intelligence.";

$features = [];

try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM features ORDER BY id ASC");
    $features = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Features page query error: " . $e->getMessage());
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     HERO BANNER
     ============================================================ -->
<section class="py-5 text-center position-relative">
  <div class="glow-orb glow-orb-1"></div>
  <div class="glow-orb glow-orb-2"></div>

  <div class="container position-relative py-4" style="z-index: 1;">
    <div class="hero-badge mx-auto">
      <i class="bi bi-lightning-charge-fill text-cyan"></i>
      <span>Comprehensive AI Toolkit</span>
    </div>
    <h1 class="display-5 fw-bold mb-3">Designed to Unleash Your <span class="text-gradient">Potential</span></h1>
    <p class="text-muted lead mx-auto" style="max-width: 680px;">
      Explore our intelligent modular tools built to handle complex writing, multi-language coding, instant document synthesis, and conversational problem-solving.
    </p>
  </div>
</section>

<!-- ============================================================
     DETAILED FEATURES BREAKDOWN (LOADED FROM DB)
     ============================================================ -->
<section class="py-5">
  <div class="container">
    <div class="row g-4">
      <?php if (!empty($features)): ?>
        <?php foreach ($features as $index => $f): ?>
          <div class="col-lg-6">
            <div class="glass-card p-4 p-md-5 h-100 d-flex flex-column">
              <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="feature-icon-box m-0">
                  <i class="bi <?php echo escape($f['icon'] ?: 'bi-stars'); ?>"></i>
                </div>
                <span class="badge bg-secondary bg-opacity-25 text-cyan border border-cyan border-opacity-25 px-3 py-2 rounded-pill small">
                  Module #0<?php echo ($index + 1); ?>
                </span>
              </div>

              <h3 class="h4 fw-bold text-light mb-3"><?php echo escape($f['title']); ?></h3>
              <p class="text-muted mb-4 lead fs-6 flex-grow-1">
                <?php echo escape($f['description']); ?>
              </p>

              <!-- Feature Highlights based on ID/index -->
              <div class="p-3 rounded-3 mb-4" style="background: rgba(0, 0, 0, 0.25); border: 1px solid var(--border-color);">
                <div class="small fw-bold text-light mb-2"><i class="bi bi-check2-circle text-cyan me-1"></i> Key Capabilities:</div>
                <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-1">
                  <?php if ($f['id'] == 1): ?>
                    <li>&bull; Multi-turn contextual reasoning & memory retention</li>
                    <li>&bull; Role-playing and persona customization</li>
                    <li>&bull; Low-latency streaming responses</li>
                  <?php elseif ($f['id'] == 2): ?>
                    <li>&bull; Tone modulation (Academic, Professional, Casual, Persuasive)</li>
                    <li>&bull; Real-time syntax and grammar enhancement</li>
                    <li>&bull; Long-form essay and article generation</li>
                  <?php elseif ($f['id'] == 3): ?>
                    <li>&bull; 30+ Programming languages (PHP, Python, JS, C++, SQL)</li>
                    <li>&bull; Automated bug finding and refactoring suggestions</li>
                    <li>&bull; Unit test synthesis and algorithmic breakdown</li>
                  <?php elseif ($f['id'] == 4): ?>
                    <li>&bull; Abstractive & extractive summarization modes</li>
                    <li>&bull; Key takeaways & action items extraction</li>
                    <li>&bull; Long-document processing and chapter breakdowns</li>
                  <?php elseif ($f['id'] == 5): ?>
                    <li>&bull; 85+ Global languages with cultural context awareness</li>
                    <li>&bull; Idiom and regional nuance preservation</li>
                    <li>&bull; Dual-language parallel view generation</li>
                  <?php else: ?>
                    <li>&bull; Natural speech dictation and real-time transcription</li>
                    <li>&bull; Neural voice synthesis for audio playback</li>
                    <li>&bull; Hands-free study and commute mode</li>
                  <?php endif; ?>
                </ul>
              </div>

              <div>
                <a href="demo.php" class="btn btn-outline-glow w-100 py-2">
                  <i class="bi bi-play-circle-fill me-1"></i> Try This Feature in Live Demo
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center text-muted py-5">
          <p>No features found in database. Please ensure <code>database.sql</code> is imported.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ============================================================
     COMPARISON CALLOUT
     ============================================================ -->
<section class="py-5 bg-surface border-top border-bottom border-secondary border-opacity-25">
  <div class="container text-center">
    <div class="hero-badge mx-auto">
      <i class="bi bi-speedometer text-cyan"></i>
      <span>Next Level Experience</span>
    </div>
    <h2 class="display-6 fw-bold mb-3">Why Aivora Stands Out</h2>
    <p class="text-muted lead mx-auto mb-5" style="max-width: 620px;">
      Unlike cumbersome enterprise tools, Aivora is lean, lightning fast, and privacy-oriented.
    </p>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="glass-card p-4 h-100">
          <i class="bi bi-shield-lock-fill text-cyan fs-2 mb-3 d-block"></i>
          <h4 class="h5 fw-bold text-light mb-2">Private & Non-Training</h4>
          <p class="text-muted small">Your proprietary code and university assignments are never used to train global public models.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="glass-card p-4 h-100">
          <i class="bi bi-stopwatch-fill text-warning fs-2 mb-3 d-block"></i>
          <h4 class="h5 fw-bold text-light mb-2">Near-Instant Latency</h4>
          <p class="text-muted small">Optimized query pipeline delivers responses in sub-200ms with negligible wait times.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="glass-card p-4 h-100">
          <i class="bi bi-mortarboard-fill text-primary fs-2 mb-3 d-block"></i>
          <h4 class="h5 fw-bold text-light mb-2">Student & Educator Friendly</h4>
          <p class="text-muted small">Specially tuned reasoning prompts for university coursework, lab exercises, and thesis formatting.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
