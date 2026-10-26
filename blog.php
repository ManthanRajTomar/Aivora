<?php
/**
 * AIVORA - Blog & Insights Page (blog.php)
 * Article listing loaded from `blog_posts` table with Search and Pagination.
 * Course: Advanced Web Development in PHP (Code 2010043347)
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "AI Insights & Research Blog - Aivora";
$pageDesc  = "Explore deep dives into generative AI, prompt engineering guides, university ethics, and code assistant productivity.";

// Search query
$searchQuery = trim($_GET['q'] ?? '');

// Pagination settings
$perPage = 4;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$posts = [];
$totalPosts = 0;
$totalPages = 1;

try {
    $db = getDB();

    if (!empty($searchQuery)) {
        // Count matching
        $countStmt = $db->prepare("SELECT COUNT(*) FROM blog_posts WHERE title LIKE :q1 OR excerpt LIKE :q2 OR content LIKE :q3");
        $term = "%{$searchQuery}%";
        $countStmt->execute([':q1' => $term, ':q2' => $term, ':q3' => $term]);
        $totalPosts = (int)$countStmt->fetchColumn();

        // Fetch matching with limit & offset
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE title LIKE :q1 OR excerpt LIKE :q2 OR content LIKE :q3 ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':q1', $term, PDO::PARAM_STR);
        $stmt->bindValue(':q2', $term, PDO::PARAM_STR);
        $stmt->bindValue(':q3', $term, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll();
    } else {
        // Count all
        $totalPosts = (int)$db->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();

        // Fetch all paginated
        $stmt = $db->prepare("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll();
    }

    $totalPages = max(1, ceil($totalPosts / $perPage));

} catch (PDOException $e) {
    error_log("Blog listing error: " . $e->getMessage());
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
      <i class="bi bi-journal-text text-cyan"></i>
      <span>Research & Guides</span>
    </div>
    <h1 class="display-5 fw-bold mb-3">Aivora <span class="text-gradient">Insights</span></h1>
    <p class="text-muted lead mx-auto mb-4" style="max-width: 650px;">
      Articles on prompt engineering, large language model research, coding workflows, and academic ethics.
    </p>

    <!-- Search Form -->
    <div class="row justify-content-center">
      <div class="col-md-6">
        <form method="GET" action="blog.php" class="d-flex gap-2">
          <input type="text" name="q" class="form-control" placeholder="Search articles by title or keyword..." value="<?php echo escape($searchQuery); ?>">
          <button type="submit" class="btn btn-primary-gradient px-4">
            <i class="bi bi-search"></i>
          </button>
          <?php if (!empty($searchQuery)): ?>
            <a href="blog.php" class="btn btn-outline-glow" title="Clear search"><i class="bi bi-x-lg"></i></a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     ARTICLES GRID
     ============================================================ -->
<section class="py-5">
  <div class="container">
    <?php if (!empty($searchQuery)): ?>
      <div class="alert alert-info py-2 px-3 mb-4 rounded-3 d-flex justify-content-between align-items-center">
        <span>Found <strong><?php echo $totalPosts; ?></strong> article(s) matching "<em><?php echo escape($searchQuery); ?></em>"</span>
        <a href="blog.php" class="text-info fw-bold small text-decoration-underline">Show all</a>
      </div>
    <?php endif; ?>

    <div class="row g-4">
      <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
          <div class="col-lg-6">
            <div class="glass-card h-100 d-flex flex-column overflow-hidden">
              <a href="blog-single.php?id=<?php echo (int)$post['id']; ?>">
                <img src="<?php echo escape($post['image']); ?>" alt="<?php echo escape($post['title']); ?>" class="blog-card-img" onerror="this.src='https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800';">
              </a>
              <div class="p-4 d-flex flex-column flex-grow-1">
                <div class="d-flex align-items-center gap-3 text-muted small mb-2">
                  <span><i class="bi bi-person-fill text-cyan me-1"></i><?php echo escape($post['author']); ?></span>
                  <span>&bull;</span>
                  <span><i class="bi bi-calendar3 me-1"></i><?php echo time_ago($post['created_at']); ?></span>
                </div>

                <h3 class="h5 fw-bold mb-2">
                  <a href="blog-single.php?id=<?php echo (int)$post['id']; ?>" class="text-light text-decoration-none hover-cyan">
                    <?php echo escape($post['title']); ?>
                  </a>
                </h3>

                <p class="text-muted small mb-4 flex-grow-1">
                  <?php echo escape($post['excerpt']); ?>
                </p>

                <div class="pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                  <a href="blog-single.php?id=<?php echo (int)$post['id']; ?>" class="text-cyan small fw-semibold">
                    Read Article <i class="bi bi-arrow-right ms-1"></i>
                  </a>
                  <span class="badge bg-secondary bg-opacity-25 text-light small"><i class="bi bi-clock me-1"></i> 4 min read</span>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center text-muted py-5">
          <i class="bi bi-journal-x fs-1 text-muted mb-3 d-block"></i>
          <h4>No Articles Found</h4>
          <p class="small">Try adjusting your search terms or view all published articles.</p>
          <a href="blog.php" class="btn btn-outline-glow mt-2">View All Posts</a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <nav aria-label="Blog Pagination" class="mt-5">
        <ul class="pagination justify-content-center gap-2">
          <?php if ($page > 1): ?>
            <li class="page-item">
              <a class="page-link glass-card text-light border-0" href="?page=<?php echo ($page - 1); ?><?php echo !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : ''; ?>" aria-label="Previous">
                <i class="bi bi-chevron-left"></i>
              </a>
            </li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
              <a class="page-link border-0 <?php echo ($i === $page) ? 'btn-primary-gradient text-white' : 'glass-card text-light'; ?>" href="?page=<?php echo $i; ?><?php echo !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : ''; ?>">
                <?php echo $i; ?>
              </a>
            </li>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <li class="page-item">
              <a class="page-link glass-card text-light border-0" href="?page=<?php echo ($page + 1); ?><?php echo !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : ''; ?>" aria-label="Next">
                <i class="bi bi-chevron-right"></i>
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
