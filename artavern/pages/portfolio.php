<?php
// ── pages/portfolio.php ──────────────────────────────────
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/profile.php';
require_once __DIR__ . '/../includes/posts.php';

$username = trim($_GET['u'] ?? '');
if (!$username) { header('Location: '.SITE_URL.'/index.php'); exit; }

$profile   = get_profile($username);
if (!$profile) { http_response_code(404); die('User not found.'); }

$user      = current_user();
$portfolio = get_portfolio($profile['id']);
$is_mine   = $user && $user['id'] == $profile['id'];

$__title = h($profile['display_name']) . "'s Portfolio — ARTavern";
$__page  = 'portfolio';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="section-header">
  🎨 <?= h($profile['display_name']) ?>&rsquo;s Portfolio
  <?php if ($is_mine): ?>
    <button class="btn btn-primary btn-sm" onclick="toggleUploadForm()">+ Add Work</button>
  <?php endif; ?>
</div>

<!-- Upload form (mine only) -->
<?php if ($is_mine): ?>
<div id="uploadForm" style="display:none;padding:18px;border-bottom:1px solid var(--border);">
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="add_portfolio">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <div class="form-group">
        <label class="form-label">Title</label>
        <input class="form-input" name="title" placeholder="Artwork title" required>
      </div>
      <div class="form-group">
        <label class="form-label">Medium</label>
        <select class="form-input meta-select" name="medium" style="border-radius:var(--radius-sm);width:100%;">
          <option value="digital">🖥 Digital Art</option>
          <option value="traditional">✏️ Traditional</option>
          <option value="pixel_art">🕹 Pixel Art</option>
          <option value="3d">🧊 3D Art</option>
          <option value="mixed">🎭 Mixed Media</option>
          <option value="other">❓ Other</option>
        </select>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <div class="form-group">
        <label class="form-label">Source</label>
        <select class="form-input meta-select" name="source" style="border-radius:var(--radius-sm);width:100%;">
          <option value="uploaded">📤 Uploaded</option>
          <option value="created_on_site">🏠 Made on ARTavern</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Tags <span style="font-weight:400;color:var(--text3);">(comma-separated)</span></label>
        <input class="form-input" name="tags" placeholder="fantasy, portrait, digital">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Description</label>
      <textarea class="form-input" name="description" rows="2" style="resize:none;" placeholder="Tell us about this piece…"></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">Image file</label>
      <input type="file" name="image" accept="image/*" class="form-input" style="padding:6px;">
    </div>
    <div style="display:flex;gap:8px;">
      <button type="submit" class="btn btn-primary">Add to Portfolio</button>
      <button type="button" class="btn btn-ghost" onclick="toggleUploadForm()">Cancel</button>
    </div>
  </form>
</div>
<?php endif; ?>

<!-- Filter chips -->
<div style="padding:10px 16px;border-bottom:1px solid var(--border);display:flex;gap:6px;flex-wrap:wrap;">
  <button class="btn btn-ghost btn-sm" onclick="filterPortfolio('all')">All</button>
  <?php foreach(['digital'=>'🖥 Digital','traditional'=>'✏️ Traditional','pixel_art'=>'🕹 Pixel','3d'=>'🧊 3D','mixed'=>'🎭 Mixed'] as $v=>$l): ?>
  <button class="btn btn-ghost btn-sm" onclick="filterPortfolio('<?= $v ?>')"><?= $l ?></button>
  <?php endforeach; ?>
</div>

<?php if (!$portfolio): ?>
  <div style="padding:50px;text-align:center;color:var(--text3);">
    No portfolio items yet.<?= $is_mine ? ' Add your first piece!' : '' ?>
  </div>
<?php endif; ?>

<div class="portfolio-grid" id="portfolioGrid">
  <?php foreach ($portfolio as $item): ?>
  <div class="portfolio-item pf-item" data-medium="<?= h($item['medium']) ?>">
    <?php if ($item['image_path']): ?>
      <img src="<?= h(UPLOAD_URL.$item['image_path']) ?>" alt="<?= h($item['title']) ?>">
    <?php else: ?>
      <span style="font-size:36px;color:var(--text3);">🖼️</span>
    <?php endif; ?>
    <div class="portfolio-item-overlay">
      <div class="title"><?= h($item['title']) ?></div>
      <div class="meta"><?= h(medium_label($item['medium'])) ?> · <?= h(source_label($item['source'])) ?></div>
      <?php if ($item['description']): ?>
        <div class="meta" style="font-size:11px;"><?= h(substr($item['description'],0,60)) ?>…</div>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php
// Handle POST (add portfolio item)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_mine && ($_POST['action'] ?? '') === 'add_portfolio') {
    $title  = trim($_POST['title'] ?? '');
    $desc   = trim($_POST['description'] ?? '');
    $medium = $_POST['medium'] ?? 'digital';
    $source = $_POST['source'] ?? 'uploaded';
    $tags   = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
    $image  = null;

    if (!empty($_FILES['image']['tmp_name'])) {
        $ext  = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $name = uniqid('pf_') . '.' . $ext;
        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
        move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $name);
        $image = $name;
    }

    if ($title && $image) {
        add_portfolio_item((int)$user['id'], $title, $desc, $image, $medium, $source, $tags);
        header('Location: ' . SITE_URL . '/pages/portfolio.php?u=' . urlencode($username) . '&added=1');
        exit;
    }
}
?>

<script>
function toggleUploadForm() {
  const f = document.getElementById('uploadForm');
  f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
function filterPortfolio(m) {
  document.querySelectorAll('.pf-item').forEach(el => {
    el.style.display = (m === 'all' || el.dataset.medium === m) ? '' : 'none';
  });
}
<?php if (isset($_GET['added'])): ?>
showToast('✅','Added to portfolio!');
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
