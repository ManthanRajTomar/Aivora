<?php
/**
 * AIVORA - Home Page (index.php)
 * Premium AI SaaS Landing Page
 * Course: Advanced Web Development in PHP (Code 2010043347)
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Aivora - Your Intelligent Everyday Assistant | AI SaaS";
$pageDesc  = "Experience next-generation AI assistance. Aivora helps students, developers, and professionals write, code, summarize, and automate workflows effortlessly.";

// Fetch dynamic data from database using PDO
$features = [];
$testimonials = [];
$plans = [];

try {
    $db = getDB();

    // 1. Fetch 6 Features
    $stmtFeatures = $db->query("SELECT * FROM features ORDER BY id ASC LIMIT 6");
    $features = $stmtFeatures->fetchAll();

    // 2. Fetch Testimonials
    $stmtTestimonials = $db->query("SELECT * FROM testimonials ORDER BY id ASC LIMIT 4");
    $testimonials = $stmtTestimonials->fetchAll();

    // 3. Fetch Pricing Plans
    $stmtPlans = $db->query("SELECT * FROM plans ORDER BY id ASC LIMIT 3");
    $plans = $stmtPlans->fetchAll();

} catch (PDOException $e) {
    error_log("Index page query error: " . $e->getMessage());
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     HERO SECTION
     ============================================================ -->
<section class="hero-section position-relative">
  <!-- Glowing ambient light orbs -->
  <div class="glow-orb glow-orb-1"></div>
  <div class="glow-orb glow-orb-2"></div>

  <div class="container position-relative" style="z-index: 1;">
    <div class="row align-items-center g-5">
      <!-- Left Hero Text -->
      <div class="col-lg-6">
        <div class="hero-badge">
          <i class="bi bi-stars text-cyan"></i>
          <span>Next-Gen Neural Assistant 2.0</span>
        </div>

        <h1 class="hero-title">
          Think Faster.<br>
          Create Better with <span class="text-gradient">Aivora</span>.
        </h1>

        <p class="lead text-muted mb-4 fs-5" style="max-width: 540px;">
          Your intelligent copilot for conversational reasoning, rapid code debugging, document synthesis, and creative writing. Engineered for students, creators, and modern teams.
        </p>

        <!-- CTA Buttons -->
        <div class="d-flex flex-wrap gap-3 mb-4">
          <a href="demo.php" class="btn btn-primary-gradient px-4 py-3 fs-6">
            <i class="bi bi-chat-quote-fill"></i>
            <span>Try Interactive Demo</span>
          </a>
          <a href="#features" class="btn btn-outline-glow px-4 py-3 fs-6">
            <i class="bi bi-grid-fill text-cyan"></i>
            <span>Explore Capabilities</span>
          </a>
        </div>

        <!-- Social Proof Stats -->
        <div class="d-flex align-items-center gap-3 pt-3 border-top border-secondary border-opacity-25">
          <div class="d-flex text-warning fs-6">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
          </div>
          <div class="text-muted small">
            <strong class="text-light">4.9/5 Rating</strong> from 25,000+ university students & engineers
          </div>
        </div>
      </div>

      <!-- Right Interactive Mockup Preview -->
      <div class="col-lg-6">
        <div class="hero-mockup-wrapper">
          <div class="hero-mockup">
            <!-- Mockup Header -->
            <div class="mockup-header">
              <div class="mockup-dots">
                <span class="mockup-dot red"></span>
                <span class="mockup-dot yellow"></span>
                <span class="mockup-dot green"></span>
              </div>
              <div class="small text-muted fw-semibold d-flex align-items-center gap-1">
                <i class="bi bi-shield-check text-success"></i> Aivora Neural Interface v1.0
              </div>
              <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50">Online</span>
            </div>

            <!-- Mockup Chat Conversation -->
            <div class="mockup-body">
              <!-- User Message -->
              <div class="chat-msg user">
                <div class="chat-avatar user"><i class="bi bi-person-fill"></i></div>
                <div class="msg-bubble">
                  Can you optimize this PHP PDO query and explain SQL injection prevention?
                </div>
              </div>

              <!-- Bot Response -->
              <div class="chat-msg bot">
                <div class="chat-avatar bot"><i class="bi bi-stars"></i></div>
                <div class="msg-bubble">
                  <div class="fw-semibold text-cyan mb-1"><i class="bi bi-check2-circle me-1"></i> Aivora Copilot:</div>
                  Always utilize <strong>prepared statements</strong> with parameterized tokens to neutralize SQL injection vulnerabilities:
                  <pre class="p-2 my-2 rounded bg-dark text-cyan small border border-secondary border-opacity-25"><code>$stmt = $pdo-&gt;prepare("SELECT * FROM users WHERE email = :email");
$stmt-&gt;execute([':email' =&gt; $email]);
$user = $stmt-&gt;fetch();</code></pre>
                  This ensures malicious input is treated as literal data, never executable syntax.
                </div>
              </div>

              <!-- Live typing preview -->
              <div class="chat-msg bot">
                <div class="chat-avatar bot"><i class="bi bi-stars"></i></div>
                <div class="msg-bubble py-2 d-flex align-items-center gap-2">
                  <span class="small text-muted">Aivora is ready to assist you</span>
                  <div class="typing-dots">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Action Footer -->
            <div class="p-3 bg-dark bg-opacity-50 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
              <span class="small text-muted"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Response latency: 140ms</span>
              <a href="demo.php" class="btn btn-sm btn-primary-gradient py-1 px-3">Launch Full Chat</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     STATS COUNTERS SECTION
     ============================================================ -->
<section class="py-5 bg-surface border-top border-bottom border-secondary border-opacity-25">
  <div class="container">
    <div class="row g-4 text-center">
      <div class="col-6 col-md-3">
        <div class="stat-box">
          <div class="stat-number" data-target="99" data-suffix="%">99%</div>
          <div class="text-muted fw-semibold small mt-1">Accuracy & Precision</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-box">
          <div class="stat-number" data-target="15" data-suffix="M+">15M+</div>
          <div class="text-muted fw-semibold small mt-1">Queries Processed</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-box">
          <div class="stat-number" data-target="85" data-suffix="+">85+</div>
          <div class="text-muted fw-semibold small mt-1">Languages Supported</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-box">
          <div class="stat-number" data-target="24" data-suffix="/7">24/7</div>
          <div class="text-muted fw-semibold small mt-1">Availability & Uptime</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     FEATURES GRID SECTION (LOADED FROM DB)
     ============================================================ -->
<section class="py-5 my-5 position-relative" id="features">
  <div class="glow-orb glow-orb-3"></div>

  <div class="container position-relative">
    <div class="text-center mb-5">
      <div class="hero-badge mx-auto">
        <i class="bi bi-cpu-fill text-cyan"></i>
        <span>Engineered For Maximum Productivity</span>
      </div>
      <h2 class="display-6 fw-bold mb-3">Six Superpowers in <span class="text-gradient">One Platform</span></h2>
      <p class="text-muted lead mx-auto" style="max-width: 650px;">
        Designed to simplify your cognitive workflow. Everything you need to write, compute, analyze, and build with confidence.
      </p>
    </div>

    <div class="row g-4">
      <?php if (!empty($features)): ?>
        <?php foreach ($features as $f): ?>
          <div class="col-lg-4 col-md-6">
            <div class="glass-card p-4 h-100 d-flex flex-column">
              <div class="feature-icon-box">
                <i class="bi <?php echo escape($f['icon'] ?: 'bi-stars'); ?>"></i>
              </div>
              <h3 class="h5 fw-bold text-light mb-2"><?php echo escape($f['title']); ?></h3>
              <p class="text-muted small flex-grow-1"><?php echo escape($f['description']); ?></p>
              <div class="mt-3 pt-3 border-top border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                <a href="features.php" class="text-cyan small fw-semibold">Learn more <i class="bi bi-arrow-right"></i></a>
                <span class="badge bg-secondary bg-opacity-25 text-light small">AI Powered</span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center text-muted py-4">
          <p>Features loading from database. Please import <code>database.sql</code>.</p>
        </div>
      <?php endif; ?>
    </div>

    <div class="text-center mt-5">
      <a href="features.php" class="btn btn-outline-glow px-4 py-2">
        <span>View Full Capabilities Breakdown</span>
        <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  </div>
</section>

<!-- ============================================================
     HOW IT WORKS (3 STEPS)
     ============================================================ -->
<section class="py-5 bg-surface border-top border-bottom border-secondary border-opacity-25">
  <div class="container">
    <div class="text-center mb-5">
      <div class="hero-badge mx-auto">
        <i class="bi bi-gear-wide-connected text-cyan"></i>
        <span>Effortless Workflow</span>
      </div>
      <h2 class="display-6 fw-bold mb-3">How Aivora <span class="text-gradient">Works</span></h2>
      <p class="text-muted lead mx-auto" style="max-width: 600px;">
        Get started in three seamless steps. No complicated setups, no credit card required.
      </p>
    </div>

    <div class="row g-4">
      <!-- Step 1 -->
      <div class="col-md-4">
        <div class="glass-card p-4 h-100 text-center">
          <div class="step-number mx-auto">1</div>
          <h4 class="h5 fw-bold text-light mb-3">Prompt or Ask Anything</h4>
          <p class="text-muted small">
            Type questions in natural language, upload documents, or paste code snippets across dozens of programming frameworks.
          </p>
        </div>
      </div>

      <!-- Step 2 -->
      <div class="col-md-4">
        <div class="glass-card p-4 h-100 text-center">
          <div class="step-number mx-auto">2</div>
          <h4 class="h5 fw-bold text-light mb-3">Neural Processing</h4>
          <p class="text-muted small">
            Our multi-tier language model evaluates context, resolves logic trees, checks grammar, and synthesizes answers in under a second.
          </p>
        </div>
      </div>

      <!-- Step 3 -->
      <div class="col-md-4">
        <div class="glass-card p-4 h-100 text-center">
          <div class="step-number mx-auto">3</div>
          <h4 class="h5 fw-bold text-light mb-3">Execute & Export</h4>
          <p class="text-muted small">
            Copy formatted code, download conversation transcripts, or export polished documents directly into your university assignments or workflow.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     TESTIMONIALS SECTION (LOADED FROM DB)
     ============================================================ -->
<section class="py-5 my-5">
  <div class="container">
    <div class="text-center mb-5">
      <div class="hero-badge mx-auto">
        <i class="bi bi-chat-heart-fill text-cyan"></i>
        <span>User Stories</span>
      </div>
      <h2 class="display-6 fw-bold mb-3">Loved by Students, Engineers & Researchers</h2>
      <p class="text-muted lead mx-auto" style="max-width: 620px;">
        Real experiences from our global community of academic scholars and technology leaders.
      </p>
    </div>

    <div class="row g-4">
      <?php if (!empty($testimonials)): ?>
        <?php foreach ($testimonials as $t): ?>
          <div class="col-lg-6">
            <div class="glass-card p-4 h-100 d-flex flex-column">
              <div class="star-rating">
                <?php for ($i = 0; $i < (int)$t['rating']; $i++): ?>
                  <i class="bi bi-star-fill"></i>
                <?php endfor; ?>
              </div>
              <p class="text-light fst-italic mb-4 flex-grow-1">"<?php echo escape($t['message']); ?>"</p>
              <div class="d-flex align-items-center gap-3 pt-3 border-top border-secondary border-opacity-25">
                <img src="<?php echo escape($t['avatar']); ?>" alt="<?php echo escape($t['name']); ?>" class="testimonial-avatar" onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100';">
                <div>
                  <h5 class="h6 fw-bold mb-0 text-light"><?php echo escape($t['name']); ?></h5>
                  <small class="text-cyan"><?php echo escape($t['role']); ?></small>
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
     PRICING PREVIEW (LOADED FROM DB)
     ============================================================ -->
<section class="py-5 bg-surface border-top border-secondary border-opacity-25" id="pricing-preview">
  <div class="container">
    <div class="text-center mb-5">
      <div class="hero-badge mx-auto">
        <i class="bi bi-tag-fill text-cyan"></i>
        <span>Predictable Pricing</span>
      </div>
      <h2 class="display-6 fw-bold mb-3">Simple, Transparent Plans</h2>
      <p class="text-muted lead mx-auto mb-4" style="max-width: 600px;">
        Start 100% free. Upgrade anytime as your intelligence needs scale.
      </p>

      <!-- Monthly / Yearly Billing Toggle -->
      <div class="d-inline-flex align-items-center gap-3 p-2 rounded-pill glass-card mb-4">
        <span class="small fw-semibold text-muted">Monthly</span>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" type="checkbox" id="billingToggle" style="cursor: pointer; width: 2.8em; height: 1.4em;">
        </div>
        <span class="small fw-semibold text-light">Yearly</span>
        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 rounded-pill small yearly-badge" style="display: none;">Save 20%</span>
      </div>
    </div>

    <div class="row g-4 align-items-center">
      <?php if (!empty($plans)): ?>
        <?php foreach ($plans as $p): ?>
          <?php $isPopular = (bool)$p['is_popular']; ?>
          <div class="col-lg-4 col-md-6">
            <div class="glass-card p-4 h-100 pricing-card <?php echo $isPopular ? 'popular' : ''; ?>">
              <?php if ($isPopular): ?>
                <div class="popular-badge"><i class="bi bi-fire me-1"></i> Most Popular</div>
              <?php endif; ?>

              <h4 class="h5 fw-bold text-light mb-2"><?php echo escape($p['name']); ?></h4>
              <p class="text-muted small mb-4">Everything you need to supercharge your workflow.</p>

              <div class="mb-4">
                <span class="plan-price text-gradient">$<span class="plan-price-val" data-monthly="<?php echo (int)$p['price_monthly']; ?>" data-yearly="<?php echo (int)$p['price_yearly']; ?>"><?php echo (int)$p['price_monthly']; ?></span></span>
                <span class="text-muted plan-period">/month</span>
              </div>

              <a href="<?php echo ((float)$p['price_monthly'] == 0) ? 'demo.php' : 'pricing.php'; ?>" class="btn <?php echo $isPopular ? 'btn-primary-gradient' : 'btn-outline-glow'; ?> w-100 mb-4 py-2">
                <?php echo ((float)$p['price_monthly'] == 0) ? 'Get Started Free' : 'Choose Plan'; ?>
              </a>

              <div class="border-top border-secondary border-opacity-25 pt-3">
                <div class="small fw-bold text-light mb-3">Included features:</div>
                <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                  <?php 
                    $lines = explode("\n", str_replace("\r", "", $p['features_list']));
                    foreach ($lines as $line):
                      if (!empty(trim($line))):
                  ?>
                    <li class="d-flex align-items-center gap-2">
                      <i class="bi bi-check-circle-fill text-cyan"></i>
                      <span><?php echo escape(trim($line)); ?></span>
                    </li>
                  <?php 
                      endif;
                    endforeach; 
                  ?>
                </ul>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ============================================================
     FINAL CTA SECTION
     ============================================================ -->
<section class="py-5 position-relative overflow-hidden">
  <div class="glow-orb glow-orb-1" style="top: 20%; left: 30%;"></div>
  <div class="container position-relative" style="z-index: 1;">
    <div class="glass-card p-5 text-center my-4" style="background: linear-gradient(135deg, rgba(124, 58, 237, 0.25), rgba(6, 182, 212, 0.15)); border: 1px solid rgba(124, 58, 237, 0.4);">
      <h2 class="display-6 fw-bold mb-3 text-light">Experience Next-Level Intelligence Today</h2>
      <p class="text-muted lead mx-auto mb-4" style="max-width: 600px;">
        Join thousands of students and creators who rely on Aivora to streamline academic studies, coding projects, and everyday productivity.
      </p>
      <div class="d-flex flex-wrap justify-content-center gap-3">
        <a href="demo.php" class="btn btn-primary-gradient px-4 py-3">
          <i class="bi bi-stars me-1"></i> Try Aivora Free
        </a>
        <a href="contact.php" class="btn btn-outline-glow px-4 py-3">
          <i class="bi bi-envelope me-1"></i> Speak with Team
        </a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
