<?php
// ── pages/trending.php ───────────────────────────────────
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/posts.php';

$__title = 'Trending — ARTavern';
$__page  = 'trending';
$user    = current_user();

$period = $_GET['period'] ?? 'week'; // week | month | alltime
$pdo    = db();

// Trending tags — ranked by post_count
$trending_tags = get_trending_tags(20);

// Trending posts — most liked in the last N days
$days = match($period) { 'month' => 30, 'alltime' => 36500, default => 7 };

$st = $pdo->prepare("
    SELECT p.*, u.display_name, u.username, u.avatar_path,
           COUNT(l.post_id) AS period_likes
    FROM posts p
    JOIN users u ON u.id = p.user_id
    LEFT JOIN likes l ON l.post_id = p.id AND l.created_at >= NOW() - INTERVAL ? DAY
    WHERE p.is_deleted = 0 AND p.created_at >= NOW() - INTERVAL ? DAY
    GROUP BY p.id
    ORDER BY period_likes DESC, p.like_count DESC
    LIMIT 30
");
$st->execute([$days, $days]);
$raw   = $st->fetchAll();
$posts = $raw ? attach_tags_to_posts($pdo, $raw) : [];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="section-header">
  📈 Trending
  <div style="display:flex;gap:6px;">
    <a href="?period=week"    class="btn btn-sm <?= $period==='week'    ? 'btn-primary' : 'btn-ghost' ?>">This Week</a>
    <a href="?period=month"   class="btn btn-sm <?= $period==='month'   ? 'btn-primary' : 'btn-ghost' ?>">This Month</a>
    <a href="?period=alltime" class="btn btn-sm <?= $period==='alltime' ? 'btn-primary' : 'btn-ghost' ?>">All Time</a>
  </div>
</div>

<!-- Trending Tags -->
<?php if ($trending_tags): ?>
<div style="padding:14px 18px;border-bottom:1px solid var(--border);">
  <div style="font-size:12px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px;">
    🏷️ Top Tags
  </div>
  <div style="display:flex;flex-wrap:wrap;gap:7px;">
    <?php foreach ($trending_tags as $i => $t): ?>
    <a href="<?= SITE_URL ?>/pages/tag.php?slug=<?= h($t['slug']) ?>"
       style="display:inline-flex;align-items:center;gap:6px;
              background:var(--surface);border:1px solid var(--border2);
              border-radius:50px;padding:5px 13px;font-size:13px;
              color:var(--text2);text-decoration:none;transition:var(--transition);"
       onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent2)'"
       onmouseout="this.style.borderColor='var(--border2)';this.style.color='var(--text2)'">
      <span style="font-size:11px;color:var(--text3);font-weight:700;">#<?= $i+1 ?></span>
      #<?= h($t['name']) ?>
      <span style="font-size:11px;color:var(--text3);"><?= number_format($t['post_count']) ?></span>
    </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- Trending Posts -->
<?php if (empty($posts)): ?>
<div style="padding:60px 24px;text-align:center;color:var(--text3);">
  <div style="font-size:40px;margin-bottom:12px;">📭</div>
  <div style="font-size:16px;font-weight:600;color:var(--text2);margin-bottom:6px;">
    No trending posts <?= $period === 'week' ? 'this week' : ($period === 'month' ? 'this month' : '') ?> yet
  </div>
  <div style="font-size:14px;">Be the first to post something — it might end up here!</div>
  <?php if ($user): ?>
    <button class="btn btn-primary" style="margin-top:16px;" onclick="location.href='<?= SITE_URL ?>/index.php'">Back to Feed</button>
  <?php else: ?>
    <button class="btn btn-primary" style="margin-top:16px;" onclick="openModal('register')">Join ARTavern</button>
  <?php endif; ?>
</div>
<?php else: ?>

<div style="padding:10px 18px 4px;font-size:12px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.08em;">
  🔥 Top Posts
</div>

<?php foreach ($posts as $rank => $post): ?>
<div class="post-card" onclick="location.href='<?= SITE_URL ?>/pages/post.php?id=<?= $post['id'] ?>'">
  <div class="post-header">
    <div style="font-size:18px;font-weight:800;color:var(--accent);min-width:28px;opacity:0.6;">
      <?= $rank + 1 ?>
    </div>
    <div class="post-avatar" style="background:linear-gradient(135deg,var(--accent),#7a4820);">
      <?php if ($post['avatar_path']): ?>
        <img src="<?= h(UPLOAD_URL.$post['avatar_path']) ?>" alt="">
      <?php else: ?>
        <?= strtoupper(substr($post['display_name'],0,2)) ?>
      <?php endif; ?>
    </div>
    <div>
      <a class="post-meta-name"
         href="<?= SITE_URL ?>/pages/profile.php?u=<?= h($post['username']) ?>"
         onclick="event.stopPropagation()"
         style="text-decoration:none;color:var(--text);"><?= h($post['display_name']) ?></a>
      <div class="post-meta-handle">@<?= h($post['username']) ?></div>
    </div>
    <div class="post-meta-time"><?= time_ago($post['created_at']) ?></div>
  </div>

  <?php if ($post['body']): ?>
    <p class="post-text"><?= nl2br(h(substr($post['body'], 0, 200))) ?><?= strlen($post['body']) > 200 ? '…' : '' ?></p>
  <?php endif; ?>

  <div class="post-chips">
    <span class="chip chip-medium"><?= h(medium_label($post['medium'])) ?></span>
    <span class="chip chip-source"><?= h(source_label($post['source'])) ?></span>
  </div>

  <?php if (!empty($post['tags'])): ?>
  <div class="post-tags">
    <?php foreach ($post['tags'] as $tag): ?>
      <span class="tag" onclick="event.stopPropagation();location.href='<?= SITE_URL ?>/pages/tag.php?slug=<?= h($tag['slug']) ?>'"><?= h($tag['name']) ?></span>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="post-actions" onclick="event.stopPropagation()">
    <button class="post-action" data-count="<?= $post['like_count'] ?>"
      onclick="toggleLike(this,<?= $post['id'] ?>)">
      🤍 <?= number_format($post['like_count']) ?>
    </button>
    <button class="post-action"
      data-href="<?= SITE_URL ?>/pages/post.php?id=<?= $post['id'] ?>"
      onclick="<?= $user ? 'location.href=this.dataset.href' : 'requireLogin(&quot;Comments&quot;)' ?>">
      💬 <?= number_format($post['comment_count']) ?>
    </button>
    <button class="post-action" onclick="toggleBookmark(this,<?= $post['id'] ?>)">🔖</button>
  </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
