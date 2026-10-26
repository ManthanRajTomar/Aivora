<?php
/**
 * AIVORA - Single Blog Post Page (blog-single.php)
 * Displays detailed article loaded from `blog_posts` table via PDO prepared statement.
 * Course: Advanced Web Development in PHP (Code 2010043347)
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$postId   = (int)($_GET['id'] ?? 0);
$postSlug = trim($_GET['slug'] ?? '');

if ($postId <= 0 && empty($postSlug)) {
    set_flash('danger', 'Invalid article specified.');
    redirect('blog.php');
}

$post = null;
$recentPosts = [];

try {
    $db = getDB();

    // Fetch the single article by ID or slug
    if ($postId > 0) {
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $postId]);
    } else {
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = :slug LIMIT 1");
        $stmt->execute([':slug' => $postSlug]);
    }
    $post = $stmt->fetch();

    if (!$post) {
        set_flash('warning', 'The requested article could not be located.');
        redirect('blog.php');
    }

    $currentId = (int)$post['id'];

    // Fetch 3 other recent posts for sidebar
    $recentStmt = $db->prepare("SELECT id, title, image, created_at FROM blog_posts WHERE id != :id ORDER BY created_at DESC LIMIT 3");
    $recentStmt->execute([':id' => $currentId]);
    $recentPosts = $recentStmt->fetchAll();

} catch (PDOException $e) {
    error_log("Blog single query error: " . $e->getMessage());
    set_flash('danger', 'Database error loading article.');
    redirect('blog.php');
}

$pageTitle = escape($post['title']) . " - Aivora Blog";
$pageDesc  = escape($post['excerpt']);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5 position-relative">
  <div class="glow-orb glow-orb-1"></div>

  <div class="row g-5 position-relative" style="z-index: 1;">
    <!-- Main Article Body -->
    <div class="col-lg-8">
      <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
          <li class="breadcrumb-item"><a href="index.php" class="text-muted">Home</a></li>
          <li class="breadcrumb-item"><a href="blog.php" class="text-muted">Blog</a></li>
          <li class="breadcrumb-item active text-cyan" aria-current="page">Article</li>
        </ol>
      </nav>

      <div class="glass-card p-4 p-md-5 mb-5">
        <h1 class="display-6 fw-bold text-light mb-3"><?php echo escape($post['title']); ?></h1>

        <!-- Meta info -->
        <div class="d-flex flex-wrap align-items-center gap-3 text-muted small pb-4 mb-4 border-bottom border-secondary border-opacity-25">
          <span class="d-flex align-items-center gap-1">
            <i class="bi bi-person-circle text-cyan"></i>
            <strong class="text-light"><?php echo escape($post['author']); ?></strong>
          </span>
          <span>&bull;</span>
          <span class="d-flex align-items-center gap-1">
            <i class="bi bi-calendar3"></i>
            <?php echo date('F d, Y', strtotime($post['created_at'])); ?>
          </span>
          <span>&bull;</span>
          <span class="badge bg-secondary bg-opacity-25 text-cyan border border-cyan border-opacity-25">
            <i class="bi bi-clock me-1"></i> 5 min read
          </span>
        </div>

        <!-- Featured Image -->
        <?php if (!empty($post['image'])): ?>
          <div class="mb-4 overflow-hidden rounded-3 shadow">
            <img src="<?php echo escape($post['image']); ?>" alt="<?php echo escape($post['title']); ?>" class="w-100" style="max-height: 420px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800';">
          </div>
        <?php endif; ?>

        <!-- Post Lead / Excerpt -->
        <p class="lead text-light opacity-90 fw-semibold mb-4 pb-3 border-bottom border-secondary border-opacity-25">
          <?php echo escape($post['excerpt']); ?>
        </p>

        <!-- Main HTML Content from DB -->
        <div class="blog-content">
          <?php echo $post['content']; // Seed contains curated sanitized HTML markup ?>
        </div>

        <!-- Share & Tags -->
        <div class="mt-5 pt-4 border-top border-secondary border-opacity-25 d-flex flex-wrap justify-content-between align-items-center gap-3">
          <div class="d-flex align-items-center gap-2">
            <span class="small fw-bold text-muted">Share:</span>
            <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($post['title']); ?>&url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" rel="noopener" class="social-icon-btn" title="Share on X / Twitter">
              <i class="bi bi-twitter-x"></i>
            </a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" rel="noopener" class="social-icon-btn" title="Share on LinkedIn">
              <i class="bi bi-linkedin"></i>
            </a>
          </div>

          <a href="blog.php" class="btn btn-outline-glow py-1 px-3 small">
            <i class="bi bi-arrow-left me-1"></i> Back to All Articles
          </a>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
      <!-- Author Profile Card -->
      <div class="glass-card p-4 mb-4">
        <h5 class="h6 fw-bold text-light mb-3">About the Author</h5>
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="chat-avatar bot" style="width: 46px; height: 46px;">
            <i class="bi bi-person-fill"></i>
          </div>
          <div>
            <h6 class="fw-bold text-light mb-0"><?php echo escape($post['author']); ?></h6>
            <small class="text-cyan">Aivora Research Contributor</small>
          </div>
        </div>
        <p class="text-muted small mb-0">
          Dedicated to demystifying artificial intelligence systems, modern PHP architectures, and high-performance full-stack workflows.
        </p>
      </div>

      <!-- Recent Posts Widget -->
      <div class="glass-card p-4 mb-4">
        <h5 class="h6 fw-bold text-light mb-3">Recent Research</h5>
        <div class="d-flex flex-column gap-3">
          <?php if (!empty($recentPosts)): ?>
            <?php foreach ($recentPosts as $rp): ?>
              <a href="blog-single.php?id=<?php echo (int)$rp['id']; ?>" class="text-decoration-none group d-flex gap-3 align-items-center">
                <img src="<?php echo escape($rp['image']); ?>" alt="" class="rounded-2 flex-shrink-0" style="width: 64px; height: 64px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=100';">
                <div>
                  <h6 class="small fw-bold text-light mb-1 hover-cyan lh-sm"><?php echo escape(truncate_words($rp['title'], 8)); ?></h6>
                  <small class="text-muted" style="font-size: 0.72rem;"><?php echo time_ago($rp['created_at']); ?></small>
                </div>
              </a>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-muted small mb-0">No other articles available.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Newsletter Promo Box -->
      <div class="glass-card p-4 text-center" style="background: linear-gradient(135deg, rgba(124, 58, 237, 0.2), rgba(6, 182, 212, 0.1));">
        <i class="bi bi-envelope-paper-heart-fill text-cyan fs-2 mb-2 d-block"></i>
        <h5 class="h6 fw-bold text-light mb-2">Subscribe to Weekly AI Briefs</h5>
        <p class="text-muted small mb-3">Get practical prompt patterns and PHP development tips in your inbox.</p>
        <a href="#newsletterForm" class="btn btn-sm btn-primary-gradient w-100">Subscribe Below</a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
