<?php
// ── pages/post.php ───────────────────────────────────────
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/posts.php';

$id   = (int)($_GET['id'] ?? 0);
$post = get_post($id);
if (!$post) { http_response_code(404); die('Post not found.'); }

$user     = current_user();
$comments = get_comments($id);
$__title  = h($post['display_name']) . "'s post — ARTavern";
$__page   = '';

// Submit comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $body = trim($_POST['comment_body'] ?? '');
    if ($body) {
        add_comment((int)$user['id'], $id, $body);
        header('Location: ' . SITE_URL . '/pages/post.php?id=' . $id . '#comments');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Post -->
<div class="post-card" style="cursor:default;">
  <div class="post-header">
    <div class="post-avatar" style="background:linear-gradient(135deg,var(--accent),#7a4820);">
      <?php if ($post['avatar_path']): ?>
        <img src="<?= h(UPLOAD_URL.$post['avatar_path']) ?>" alt="">
      <?php else: ?>
        <?= strtoupper(substr($post['display_name'],0,2)) ?>
      <?php endif; ?>
    </div>
    <div>
      <a class="post-meta-name" href="<?= SITE_URL ?>/pages/profile.php?u=<?= h($post['username']) ?>"
         style="text-decoration:none;"><?= h($post['display_name']) ?></a>
      <div class="post-meta-handle">@<?= h($post['username']) ?></div>
    </div>
    <div class="post-meta-time"><?= time_ago($post['created_at']) ?></div>
  </div>

  <?php if ($post['body']): ?>
    <p class="post-text"><?= nl2br(h($post['body'])) ?></p>
  <?php endif; ?>

  <div class="post-chips">
    <span class="chip chip-medium"><?= h(medium_label($post['medium'])) ?></span>
    <span class="chip chip-source"><?= h(source_label($post['source'])) ?></span>
  </div>

  <?php if ($post['image_path']): ?>
  <div class="post-image" style="aspect-ratio:auto;max-height:600px;">
    <img src="<?= h(UPLOAD_URL.$post['image_path']) ?>" alt="artwork" style="max-height:600px;">
  </div>
  <?php endif; ?>

  <?php if ($post['tags']): ?>
  <div class="post-tags">
    <?php foreach ($post['tags'] as $tag): ?>
      <span class="tag" onclick="location.href='<?= SITE_URL ?>/pages/tag.php?slug=<?= h($tag['slug']) ?>'"><?= h($tag['name']) ?></span>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="post-actions">
    <button class="post-action" data-count="<?= $post['like_count'] ?>"
      onclick="toggleLike(this,<?= $post['id'] ?>)">
      🤍 <?= number_format($post['like_count']) ?>
    </button>
    <button class="post-action">💬 <?= number_format($post['comment_count']) ?></button>
    <button class="post-action" onclick="toggleBookmark(this,<?= $post['id'] ?>)">🔖</button>
  </div>
</div>

<!-- Comments -->
<div id="comments" style="border-top:1px solid var(--border);">
  <div style="padding:14px 18px;font-size:15px;font-weight:700;">
    💬 Comments (<?= count($comments) ?>)
  </div>

  <!-- Comment form -->
  <?php if ($user): ?>
  <form method="POST" style="padding:0 18px 14px;display:flex;gap:12px;align-items:flex-start;">
    <div class="post-avatar" style="background:linear-gradient(135deg,var(--accent),#7a4820);flex-shrink:0;">
      <?= strtoupper(substr($user['display_name'],0,2)) ?>
    </div>
    <div style="flex:1;">
      <textarea class="form-input" name="comment_body" rows="2"
        placeholder="Add a comment…" style="resize:none;" required></textarea>
      <button type="submit" class="btn btn-primary btn-sm" style="margin-top:8px;">Post comment</button>
    </div>
  </form>
  <?php else: ?>
  <div style="padding:14px 18px;">
    <button class="btn btn-outline" onclick="openModal('login')">Sign in to comment</button>
  </div>
  <?php endif; ?>

  <!-- Comment list -->
  <?php if (!$comments): ?>
    <div style="padding:20px 18px;color:var(--text3);font-size:14px;">No comments yet. Be the first!</div>
  <?php endif; ?>

  <?php foreach ($comments as $c): ?>
  <div style="padding:12px 18px;border-top:1px solid var(--border);display:flex;gap:10px;">
    <div class="post-avatar" style="background:linear-gradient(135deg,var(--accent),#7a4820);width:34px;height:34px;font-size:12px;flex-shrink:0;">
      <?php if ($c['avatar_path']): ?>
        <img src="<?= h(UPLOAD_URL.$c['avatar_path']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
      <?php else: ?>
        <?= strtoupper(substr($c['display_name'],0,2)) ?>
      <?php endif; ?>
    </div>
    <div>
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
        <a href="<?= SITE_URL ?>/pages/profile.php?u=<?= h($c['username']) ?>"
           style="font-size:13px;font-weight:700;color:var(--text);"><?= h($c['display_name']) ?></a>
        <span style="font-size:12px;color:var(--text3);"><?= time_ago($c['created_at']) ?></span>
      </div>
      <div style="font-size:14px;line-height:1.55;color:var(--text2);"><?= nl2br(h($c['body'])) ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
