<?php
/**
 * AIVORA - Pricing & Plans Page (pricing.php)
 * 3 Plans loaded from `plans` table, Monthly/Yearly switch, Feature Matrix, FAQ Accordion.
 * Course: Advanced Web Development in PHP (Code 2010043347)
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Pricing Plans & Subscriptions - Aivora AI";
$pageDesc  = "Simple, transparent pricing for students, independent creators, and development teams. Start free with no credit card required.";

$plans = [];

try {
    $db = getDB();
    $stmtPlans = $db->query("SELECT * FROM plans ORDER BY id ASC");
    $plans = $stmtPlans->fetchAll();
} catch (PDOException $e) {
    error_log("Pricing page query error: " . $e->getMessage());
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
      <i class="bi bi-gem text-cyan"></i>
      <span>Flexible Subscriptions</span>
    </div>
    <h1 class="display-5 fw-bold mb-3">Invest in Your <span class="text-gradient">Productivity</span></h1>
    <p class="text-muted lead mx-auto mb-4" style="max-width: 650px;">
      Transparent pricing tailored for individuals, university scholars, and growing engineering teams.
    </p>

    <!-- Monthly / Yearly Billing Toggle Switch -->
    <div class="d-inline-flex align-items-center gap-3 p-2 rounded-pill glass-card mb-4">
      <span class="small fw-semibold text-muted">Billed Monthly</span>
      <div class="form-check form-switch m-0">
        <input class="form-check-input" type="checkbox" id="billingToggle" style="cursor: pointer; width: 3em; height: 1.5em;">
      </div>
      <span class="small fw-semibold text-light">Billed Annually</span>
      <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 rounded-pill small yearly-badge" style="display: none;">Save ~20%</span>
    </div>
  </div>
</section>

<!-- ============================================================
     PLANS GRID (LOADED FROM DB)
     ============================================================ -->
<section class="py-5">
  <div class="container">
    <div class="row g-4 align-items-stretch justify-content-center">
      <?php if (!empty($plans)): ?>
        <?php foreach ($plans as $p): ?>
          <?php $isPopular = (bool)$p['is_popular']; ?>
          <div class="col-lg-4 col-md-6">
            <div class="glass-card p-4 p-md-5 h-100 pricing-card d-flex flex-column <?php echo $isPopular ? 'popular' : ''; ?>">
              <?php if ($isPopular): ?>
                <div class="popular-badge"><i class="bi bi-fire me-1"></i> Recommended</div>
              <?php endif; ?>

              <div class="mb-3">
                <h3 class="h4 fw-bold text-light mb-1"><?php echo escape($p['name']); ?></h3>
                <p class="text-muted small">Ideal for casual learning and quick everyday tasks.</p>
              </div>

              <!-- Price -->
              <div class="mb-4">
                <span class="plan-price text-gradient">$<span class="plan-price-val" data-monthly="<?php echo (int)$p['price_monthly']; ?>" data-yearly="<?php echo (int)$p['price_yearly']; ?>"><?php echo (int)$p['price_monthly']; ?></span></span>
                <span class="text-muted plan-period">/month</span>
              </div>

              <!-- Action Button -->
              <?php if ((float)$p['price_monthly'] == 0): ?>
                <a href="demo.php" class="btn btn-outline-glow w-100 py-2 mb-4">
                  <span>Start Free Now</span>
                  <i class="bi bi-arrow-right ms-1"></i>
                </a>
              <?php elseif ($isPopular): ?>
                <a href="register.php?plan=pro" class="btn btn-primary-gradient w-100 py-2 mb-4">
                  <span>Get Started with Pro</span>
                  <i class="bi bi-arrow-right ms-1"></i>
                </a>
              <?php else: ?>
                <a href="contact.php?subject=Enterprise%20Inquiry" class="btn btn-outline-glow w-100 py-2 mb-4">
                  <span>Contact Sales</span>
                  <i class="bi bi-envelope ms-1"></i>
                </a>
              <?php endif; ?>

              <!-- Feature Checklist -->
              <div class="border-top border-secondary border-opacity-25 pt-4 flex-grow-1">
                <div class="small fw-bold text-light mb-3">Plan inclusions:</div>
                <ul class="list-unstyled small text-muted d-flex flex-column gap-2 mb-0">
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
     DETAILED FEATURE COMPARISON TABLE
     ============================================================ -->
<section class="py-5 bg-surface border-top border-bottom border-secondary border-opacity-25">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-6 fw-bold mb-3">Compare All Capabilities</h2>
      <p class="text-muted lead mx-auto" style="max-width: 600px;">
        A side-by-side comparison of plan features to find your ideal match.
      </p>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered border-secondary border-opacity-25 text-light align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th class="text-start p-3" style="width: 40%;">Feature</th>
            <th class="p-3" style="width: 20%;">Free Starter</th>
            <th class="p-3 text-cyan" style="width: 20%;">Pro Creator</th>
            <th class="p-3" style="width: 20%;">Enterprise</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-start p-3 fw-semibold">Daily Queries</td>
            <td>30 / day</td>
            <td class="text-cyan fw-bold">Unlimited</td>
            <td>Unlimited Custom</td>
          </tr>
          <tr>
            <td class="text-start p-3 fw-semibold">Response Inference Speed</td>
            <td>Standard</td>
            <td class="text-cyan fw-bold">Priority Low-Latency</td>
            <td>Dedicated GPU Cluster</td>
          </tr>
          <tr>
            <td class="text-start p-3 fw-semibold">Code Copilot & Debugging</td>
            <td>Basic syntax</td>
            <td><i class="bi bi-check-circle-fill text-success"></i> Full Multi-language</td>
            <td><i class="bi bi-check-circle-fill text-success"></i> Custom Repo Sync</td>
          </tr>
          <tr>
            <td class="text-start p-3 fw-semibold">Document Summarization</td>
            <td>Up to 3 pages</td>
            <td><i class="bi bi-check-circle-fill text-success"></i> Up to 100 pages</td>
            <td><i class="bi bi-check-circle-fill text-success"></i> Unlimited Volumes</td>
          </tr>
          <tr>
            <td class="text-start p-3 fw-semibold">Export Transcripts (.txt)</td>
            <td><i class="bi bi-x-circle text-muted"></i></td>
            <td><i class="bi bi-check-circle-fill text-success"></i></td>
            <td><i class="bi bi-check-circle-fill text-success"></i></td>
          </tr>
          <tr>
            <td class="text-start p-3 fw-semibold">Support Level</td>
            <td>Community Forum</td>
            <td>Priority Email (24h)</td>
            <td>24/7 Dedicated Manager</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- ============================================================
     FAQ ACCORDION
     ============================================================ -->
<section class="py-5 my-5">
  <div class="container">
    <div class="text-center mb-5">
      <div class="hero-badge mx-auto">
        <i class="bi bi-question-circle-fill text-cyan"></i>
        <span>Common Inquiries</span>
      </div>
      <h2 class="display-6 fw-bold mb-3">Frequently Asked Questions</h2>
      <p class="text-muted lead mx-auto" style="max-width: 600px;">
        Got questions? We have clear answers. If you need further help, feel free to contact us.
      </p>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="accordion accordion-flush" id="pricingFaqAccordion">
          <!-- FAQ 1 -->
          <div class="accordion-item glass-card mb-3 border-0 rounded-3 overflow-hidden">
            <h2 class="accordion-header" id="headingOne">
              <button class="accordion-button collapsed bg-transparent text-light fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                Can I really use Aivora completely free?
              </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#pricingFaqAccordion">
              <div class="accordion-body text-muted small">
                Yes! Our Free Starter plan gives you 30 queries every 24 hours with access to conversational chat, writing suggestions, and basic coding help with no credit card required.
              </div>
            </div>
          </div>

          <!-- FAQ 2 -->
          <div class="accordion-item glass-card mb-3 border-0 rounded-3 overflow-hidden">
            <h2 class="accordion-header" id="headingTwo">
              <button class="accordion-button collapsed bg-transparent text-light fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Are there discounts for university students?
              </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#pricingFaqAccordion">
              <div class="accordion-body text-muted small">
                Absolutely! Enrolled university students with an active <code>.edu</code> email address receive an additional 35% discount on the Pro Creator annual plan. Contact our academic desk on the contact page.
              </div>
            </div>
          </div>

          <!-- FAQ 3 -->
          <div class="accordion-item glass-card mb-3 border-0 rounded-3 overflow-hidden">
            <h2 class="accordion-header" id="headingThree">
              <button class="accordion-button collapsed bg-transparent text-light fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                Can I cancel or switch my subscription at any time?
              </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#pricingFaqAccordion">
              <div class="accordion-body text-muted small">
                Yes, you can upgrade, downgrade, or cancel anytime directly from your account settings. There are no lock-in contracts or cancellation penalties.
              </div>
            </div>
          </div>

          <!-- FAQ 4 -->
          <div class="accordion-item glass-card mb-3 border-0 rounded-3 overflow-hidden">
            <h2 class="accordion-header" id="headingFour">
              <button class="accordion-button collapsed bg-transparent text-light fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                How is my proprietary code and data safeguarded?
              </button>
            </h2>
            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#pricingFaqAccordion">
              <div class="accordion-body text-muted small">
                We strictly enforce encrypted transmission (HTTPS), server-side sanitization, and SQL prepared statements. Your conversation history is stored strictly for your own account and is never sold or used in third-party model training.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
