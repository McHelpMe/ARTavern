<?php
// ── pages/tag.php ────────────────────────────────────────
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/posts.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: '.SITE_URL.'/index.php'); exit; }

$st = db()->prepare('SELECT * FROM tags WHERE slug=?');
$st->execute([$slug]);
$tag = $st->fetch();
if (!$tag) { http_response_code(404); die('Tag not found.'); }

$page  = max(1,(int)($_GET['p'] ?? 1));
$posts = get_posts_by_tag($slug, $page);
$user  = current_user();

$__title = '#' . h($tag['name']) . ' — ARTavern';
$__page  = 'explore';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="section-header">
  #<?= h($tag['name']) ?>
  <span style="font-size:14px;font-weight:400;color:var(--text3);"><?= number_format($tag['post_count']) ?> posts</span>
</div>

<?php if (!$posts): ?>
  <div style="padding:40px;text-align:center;color:var(--text3);">No posts with this tag yet.</div>
<?php endif; ?>

<?php foreach ($posts as $post): ?>
<div class="post-card">
  <div class="post-header">
    <div class="post-avatar" style="background:linear-gradient(135deg,var(--accent),#7a4820);">
      <?= strtoupper(substr($post['display_name'],0,2)) ?>
    </div>
    <div>
      <div class="post-meta-name"><?= h($post['display_name']) ?></div>
      <div class="post-meta-handle">@<?= h($post['username']) ?></div>
    </div>
    <div class="post-meta-time"><?= time_ago($post['created_at']) ?></div>
  </div>
  <?php if ($post['body']): ?><p class="post-text"><?= nl2br(h($post['body'])) ?></p><?php endif; ?>
  <div class="post-chips">
    <span class="chip chip-medium"><?= h(medium_label($post['medium'])) ?></span>
    <span class="chip chip-source"><?= h(source_label($post['source'])) ?></span>
  </div>
  <?php if ($post['image_path']): ?>
    <div class="post-image"><img src="<?= h(UPLOAD_URL.$post['image_path']) ?>" alt="artwork"></div>
  <?php endif; ?>
  <div class="post-tags">
    <?php foreach ($post['tags'] as $t): ?>
      <span class="tag" onclick="location.href='<?= SITE_URL ?>/pages/tag.php?slug=<?= h($t['slug']) ?>'"><?= h($t['name']) ?></span>
    <?php endforeach; ?>
  </div>
  <div class="post-actions">
    <button class="post-action" data-count="<?= $post['like_count'] ?>"
      onclick="toggleLike(this,<?= $post['id'] ?>)">🤍 <?= number_format($post['like_count']) ?></button>
    <button class="post-action">💬 <?= number_format($post['comment_count']) ?></button>
    <button class="post-action" onclick="toggleBookmark(this,<?= $post['id'] ?>)">🔖</button>
  </div>
</div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
