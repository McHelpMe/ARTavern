<?php
// ── pages/explore.php ────────────────────────────────────
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/posts.php';

$q      = trim($_GET['q'] ?? '');
$filter = $_GET['filter'] ?? 'posts'; // posts | tags | artists
$user   = current_user();
$__title = $q ? "Search: $q — ARTavern" : 'Explore — ARTavern';
$__page  = 'explore';
$pdo     = db();

$results_posts   = [];
$results_tags    = [];
$results_artists = [];

if ($q) {
    if ($filter === 'posts' || $filter === 'all') {
        $st = $pdo->prepare("SELECT p.*, u.display_name, u.username, u.avatar_path
            FROM posts p JOIN users u ON u.id=p.user_id
            WHERE p.is_deleted=0 AND (p.body LIKE ? OR u.display_name LIKE ? OR u.username LIKE ?)
            ORDER BY p.created_at DESC LIMIT 30");
        $like = "%$q%";
        $st->execute([$like,$like,$like]);
        $results_posts = attach_tags_to_posts($pdo, $st->fetchAll());
    }
    if ($filter === 'tags' || $filter === 'all') {
        $st = $pdo->prepare("SELECT * FROM tags WHERE name LIKE ? ORDER BY post_count DESC LIMIT 20");
        $st->execute(["%$q%"]);
        $results_tags = $st->fetchAll();
    }
    if ($filter === 'artists' || $filter === 'all') {
        $st = $pdo->prepare("SELECT id,display_name,username,avatar_path,bio,art_styles FROM users
            WHERE display_name LIKE ? OR username LIKE ? LIMIT 20");
        $like = "%$q%";
        $st->execute([$like,$like]);
        $results_artists = $st->fetchAll();
    }
}

$trending_tags = get_trending_tags(16);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="section-header">🔭 Explore</div>

<!-- Search bar inline -->
<form method="GET" action="" style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;gap:10px;">
  <input class="form-input" name="q" value="<?= h($q) ?>" placeholder="Search posts, tags, artists…" style="flex:1;">
  <select class="meta-select" name="filter" style="border-radius:var(--radius-sm);">
    <option value="all"     <?= $filter==='all'?'selected':'' ?>>All</option>
    <option value="posts"   <?= $filter==='posts'?'selected':'' ?>>Posts</option>
    <option value="tags"    <?= $filter==='tags'?'selected':'' ?>>Tags</option>
    <option value="artists" <?= $filter==='artists'?'selected':'' ?>>Artists</option>
  </select>
  <button type="submit" class="btn btn-primary">Search</button>
</form>

<?php if (!$q): ?>
<!-- Trending tags grid -->
<div style="padding:16px 18px;">
  <div style="font-size:15px;font-weight:700;margin-bottom:12px;">🔥 Trending Tags</div>
  <div style="display:flex;flex-wrap:wrap;gap:8px;">
    <?php foreach ($trending_tags as $i => $t): ?>
    <a href="<?= SITE_URL ?>/pages/tag.php?slug=<?= h($t['slug']) ?>"
       style="display:inline-flex;align-items:center;gap:6px;background:var(--surface);
              border:1px solid var(--border2);border-radius:50px;padding:6px 14px;
              font-size:13px;color:var(--text2);text-decoration:none;transition:var(--transition);"
       onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent2)'"
       onmouseout="this.style.borderColor='var(--border2)';this.style.color='var(--text2)'">
      #<?= h($t['name']) ?>
      <span style="font-size:11px;color:var(--text3);"><?= number_format($t['post_count']) ?></span>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php else: ?>

<!-- Results -->
<?php if ($results_artists): ?>
<div style="padding:14px 18px;border-bottom:1px solid var(--border);">
  <div style="font-size:13px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px;">Artists</div>
  <div style="display:flex;flex-direction:column;gap:8px;">
    <?php foreach ($results_artists as $a): ?>
    <a href="<?= SITE_URL ?>/pages/profile.php?u=<?= h($a['username']) ?>"
       style="display:flex;align-items:center;gap:10px;padding:8px;border-radius:var(--radius-sm);
              background:var(--surface);text-decoration:none;transition:var(--transition);"
       onmouseover="this.style.background='var(--surface2)'"
       onmouseout="this.style.background='var(--surface)'">
      <div class="post-avatar" style="width:38px;height:38px;font-size:13px;background:linear-gradient(135deg,var(--accent),#7a4820);">
        <?= strtoupper(substr($a['display_name'],0,2)) ?>
      </div>
      <div>
        <div style="font-size:14px;font-weight:600;color:var(--text);"><?= h($a['display_name']) ?></div>
        <div style="font-size:12px;color:var(--text3);">@<?= h($a['username']) ?>
          <?php if ($a['art_styles']): ?> · <?= h($a['art_styles']) ?><?php endif; ?>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php if ($results_tags): ?>
<div style="padding:14px 18px;border-bottom:1px solid var(--border);">
  <div style="font-size:13px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px;">Tags</div>
  <div style="display:flex;flex-wrap:wrap;gap:8px;">
    <?php foreach ($results_tags as $t): ?>
    <a href="<?= SITE_URL ?>/pages/tag.php?slug=<?= h($t['slug']) ?>"
       style="display:inline-flex;align-items:center;gap:6px;background:var(--accent-bg);
              border-radius:50px;padding:5px 13px;font-size:13px;color:var(--accent2);text-decoration:none;">
      #<?= h($t['name']) ?>
      <span style="font-size:11px;color:var(--text3);"><?= number_format($t['post_count']) ?></span>
    </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php if ($results_posts): ?>
<div>
  <div style="padding:14px 18px 4px;font-size:13px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.07em;">Posts</div>
  <?php foreach ($results_posts as $post): ?>
  <div class="post-card" onclick="location.href='<?= SITE_URL ?>/pages/post.php?id=<?= $post['id'] ?>'">
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
    <?php if ($post['body']): ?><p class="post-text"><?= nl2br(h(substr($post['body'],0,200))) ?><?= strlen($post['body'])>200?'…':'' ?></p><?php endif; ?>
    <div class="post-chips">
      <span class="chip chip-medium"><?= h(medium_label($post['medium'])) ?></span>
      <span class="chip chip-source"><?= h(source_label($post['source'])) ?></span>
    </div>
    <?php if ($post['tags']): ?>
    <div class="post-tags">
      <?php foreach ($post['tags'] as $tag): ?>
        <span class="tag" onclick="event.stopPropagation();location.href='<?= SITE_URL ?>/pages/tag.php?slug=<?= h($tag['slug']) ?>'"><?= h($tag['name']) ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="post-actions" onclick="event.stopPropagation()">
      <button class="post-action" data-count="<?= $post['like_count'] ?>"
        onclick="toggleLike(this,<?= $post['id'] ?>)">🤍 <?= number_format($post['like_count']) ?></button>
      <button class="post-action">💬 <?= number_format($post['comment_count']) ?></button>
      <button class="post-action" onclick="toggleBookmark(this,<?= $post['id'] ?>)">🔖</button>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!$results_posts && !$results_tags && !$results_artists): ?>
<div style="padding:50px;text-align:center;color:var(--text3);">
  No results for "<?= h($q) ?>". Try a different search or browse tags!
</div>
<?php endif; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
