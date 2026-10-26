<?php
/**
 * AIVORA - About Page (about.php)
 * Mission, Vision, Dynamic Team Members from DB, and Timeline
 * Course: Advanced Web Development in PHP (Code 2010043347)
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "About Aivora - Mission, Team & Engineering Vision";
$pageDesc  = "Learn about Aivora's mission to democratize state-of-the-art AI assistance for students, developers, and educators.";

$teamMembers = [];

try {
    $db = getDB();
    $stmtTeam = $db->query("SELECT * FROM team ORDER BY id ASC");
    $teamMembers = $stmtTeam->fetchAll();
} catch (PDOException $e) {
    error_log("About page query error: " . $e->getMessage());
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     HERO / BANNER
     ============================================================ -->
<section class="py-5 text-center position-relative">
  <div class="glow-orb glow-orb-1"></div>
  <div class="glow-orb glow-orb-2"></div>

  <div class="container position-relative py-4" style="z-index: 1;">
    <div class="hero-badge mx-auto">
      <i class="bi bi-info-circle-fill text-cyan"></i>
      <span>Our Story & Values</span>
    </div>
    <h1 class="display-5 fw-bold mb-3">Empowering Minds Through <span class="text-gradient">Intelligent Computing</span></h1>
    <p class="text-muted lead mx-auto" style="max-width: 680px;">
      Aivora was conceived at the intersection of university research and cutting-edge software engineering. Our goal is to augment human creativity, not replace it.
    </p>
  </div>
</section>

<!-- ============================================================
     MISSION & VISION
     ============================================================ -->
<section class="py-5 bg-surface border-top border-bottom border-secondary border-opacity-25">
  <div class="container">
    <div class="row g-4 align-items-center">
      <!-- Mission Card -->
      <div class="col-lg-6">
        <div class="glass-card p-5 h-100">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="feature-icon-box m-0" style="width: 50px; height: 50px; font-size: 1.4rem;">
              <i class="bi bi-compass-fill"></i>
            </div>
            <h3 class="h4 fw-bold text-light mb-0">Our Core Mission</h3>
          </div>
          <p class="text-muted mb-3">
            To provide every student, engineer, and writer with an accessible, high-performance cognitive assistant that demystifies complex code, accelerates academic research, and turns rough ideas into polished reality.
          </p>
          <ul class="list-unstyled small text-muted d-flex flex-column gap-2 mb-0">
            <li><i class="bi bi-check2 text-cyan me-2"></i>Ethical AI adoption focused on genuine comprehension.</li>
            <li><i class="bi bi-check2 text-cyan me-2"></i>Ultra-low latency inference accessible across devices.</li>
            <li><i class="bi bi-check2 text-cyan me-2"></i>Rigorous security and user data privacy by design.</li>
          </ul>
        </div>
      </div>

      <!-- Vision Card -->
      <div class="col-lg-6">
        <div class="glass-card p-5 h-100">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="feature-icon-box m-0" style="width: 50px; height: 50px; font-size: 1.4rem;">
              <i class="bi bi-eye-fill"></i>
            </div>
            <h3 class="h4 fw-bold text-light mb-0">Our Forward Vision</h3>
          </div>
          <p class="text-muted mb-3">
            A future where artificial intelligence acts as a frictionless thinking partner—seamlessly embedded into everyday web tools, operating under open standards, and engineered with transparent principles.
          </p>
          <ul class="list-unstyled small text-muted d-flex flex-column gap-2 mb-0">
            <li><i class="bi bi-check2 text-cyan me-2"></i>Collaborative multi-agent workflows for technical tasks.</li>
            <li><i class="bi bi-check2 text-cyan me-2"></i>Multilingual preservation and dialect comprehension.</li>
            <li><i class="bi bi-check2 text-cyan me-2"></i>Uncompromising server-side resilience via standard PHP 8 & PDO.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     MEET THE TEAM (LOADED FROM DB)
     ============================================================ -->
<section class="py-5 my-5">
  <div class="container">
    <div class="text-center mb-5">
      <div class="hero-badge mx-auto">
        <i class="bi bi-people-fill text-cyan"></i>
        <span>Leadership & Architects</span>
      </div>
      <h2 class="display-6 fw-bold mb-3">Meet the Team Behind <span class="text-gradient">Aivora</span></h2>
      <p class="text-muted lead mx-auto" style="max-width: 600px;">
        A passionate multidisciplinary group of researchers, software architects, and design thinkers.
      </p>
    </div>

    <div class="row g-4">
      <?php if (!empty($teamMembers)): ?>
        <?php foreach ($teamMembers as $m): ?>
          <div class="col-lg-3 col-md-6">
            <div class="glass-card h-100 overflow-hidden d-flex flex-column">
              <img src="<?php echo escape($m['photo']); ?>" alt="<?php echo escape($m['name']); ?>" class="team-img" onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400';">
              <div class="p-4 d-flex flex-column flex-grow-1">
                <h4 class="h5 fw-bold text-light mb-1"><?php echo escape($m['name']); ?></h4>
                <div class="text-cyan small fw-semibold mb-3"><?php echo escape($m['role']); ?></div>
                <p class="text-muted small mb-3 flex-grow-1"><?php echo escape($m['bio']); ?></p>
                <div class="d-flex gap-2 pt-2 border-top border-secondary border-opacity-25">
                  <a href="https://linkedin.com" target="_blank" rel="noopener" class="text-muted small hover-cyan"><i class="bi bi-linkedin"></i></a>
                  <a href="https://twitter.com" target="_blank" rel="noopener" class="text-muted small hover-cyan"><i class="bi bi-twitter-x"></i></a>
                  <a href="https://github.com" target="_blank" rel="noopener" class="text-muted small hover-cyan"><i class="bi bi-github"></i></a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ============================================================
     COMPANY TIMELINE
     ============================================================ -->
<section class="py-5 bg-surface border-top border-secondary border-opacity-25">
  <div class="container">
    <div class="text-center mb-5">
      <div class="hero-badge mx-auto">
        <i class="bi bi-clock-history text-cyan"></i>
        <span>Milestones</span>
      </div>
      <h2 class="display-6 fw-bold mb-3">Our Engineering Journey</h2>
      <p class="text-muted lead mx-auto" style="max-width: 600px;">
        From a university laboratory research prototype to an enterprise-grade AI assistant.
      </p>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="d-flex flex-column gap-4">
          <!-- Timeline Item 1 -->
          <div class="glass-card p-4 d-flex gap-3">
            <div class="step-number flex-shrink-0" style="width: 48px; height: 48px; font-size: 0.95rem;">2023</div>
            <div>
              <h5 class="h6 fw-bold text-light mb-1">Inception & Academic Research Paper</h5>
              <p class="text-muted small mb-0">Initial research into transformer token distillation, prompt engineering workflows, and semantic keyword matching algorithms.</p>
            </div>
          </div>

          <!-- Timeline Item 2 -->
          <div class="glass-card p-4 d-flex gap-3">
            <div class="step-number flex-shrink-0" style="width: 48px; height: 48px; font-size: 0.95rem;">2024</div>
            <div>
              <h5 class="h6 fw-bold text-light mb-1">Aivora v1.0 Launch</h5>
              <p class="text-muted small mb-0">First public deployment with core conversational chat, code assistance, and document summarization. Over 10,000 active student users.</p>
            </div>
          </div>

          <!-- Timeline Item 3 -->
          <div class="glass-card p-4 d-flex gap-3">
            <div class="step-number flex-shrink-0" style="width: 48px; height: 48px; font-size: 0.95rem;">2025</div>
            <div>
              <h5 class="h6 fw-bold text-light mb-1">Multilingual & Enterprise Scalability</h5>
              <p class="text-muted small mb-0">Integration of 85+ world languages, team workspace sharing, and migration to high-performance PDO database architecture.</p>
            </div>
          </div>

          <!-- Timeline Item 4 -->
          <div class="glass-card p-4 d-flex gap-3 border-cyan" style="border-color: rgba(6, 182, 212, 0.4);">
            <div class="step-number flex-shrink-0" style="width: 48px; height: 48px; font-size: 0.95rem; background: var(--gradient-primary);">2026</div>
            <div>
              <h5 class="h6 fw-bold text-cyan mb-1">Aivora 2.0 & University Assignment Integration</h5>
              <p class="text-muted small mb-0">Advanced rule-based conversational heuristics, zero-framework lightweight PHP 8 engine, and deployment readiness for SmarterASP.NET.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
