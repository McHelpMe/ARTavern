<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/posts.php';
require_once __DIR__ . '/includes/profile.php';

$__title = 'ARTavern — Home';
$__page  = 'home';
$user    = current_user();

$tab    = $_GET['tab'] ?? 'all';
$page   = max(1, (int)($_GET['p'] ?? 1));
$posts  = get_feed($page, 20, $user['id'] ?? null, $tab === 'following' ? 'following' : 'all');
$trending = get_trending_tags(8);
$suggested = $user ? get_suggested_artists($user['id'], 4) : [];

// Right sidebar HTML
ob_start(); ?>
<!-- Trending Tags -->
<div class="widget-card">
  <div class="widget-title">🔥 Trending Tags</div>
  <div class="widget-body" style="padding:8px 12px;">
    <?php foreach ($trending as $i => $t): ?>
    <div class="trending-item" onclick="location.href='<?= SITE_URL ?>/pages/tag.php?slug=<?= h($t['slug']) ?>'">
      <div class="trending-rank"><?= $i+1 ?> · Trending</div>
      <div class="trending-name">#<?= h($t['name']) ?></div>
      <div class="trending-count"><?= number_format($t['post_count']) ?> posts</div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Open Collabs -->
<div class="widget-card">
  <div class="widget-title">🤝 Open Collabs</div>
  <div class="widget-body" style="display:flex;flex-direction:column;gap:8px;">
    <div class="collab-card" data-url="<?= SITE_URL ?>/pages/collaborate.php" onclick="guestNav(this)">
      <div class="collab-type tutoring">📚 Tutoring Available</div>
      <div class="collab-title">Figure Drawing Fundamentals</div>
      <div class="collab-meta">2 spots open</div>
    </div>
    <div class="collab-card" data-url="<?= SITE_URL ?>/pages/collaborate.php" onclick="guestNav(this)">
      <div class="collab-type shared">🎨 Shared Canvas</div>
      <div class="collab-title">Abstract Jam Session</div>
      <div class="collab-meta">Open to all · Live now</div>
    </div>
    <div class="collab-card" data-url="<?= SITE_URL ?>/pages/collaborate.php" onclick="guestNav(this)">
      <div class="collab-type match">🎲 Matchmaking</div>
      <div class="collab-title">Style Swap Challenge</div>
      <div class="collab-meta">12 artists waiting</div>
    </div>
  </div>
</div>

<?php if ($suggested): ?>
<!-- Suggested Artists -->
<div class="widget-card">
  <div class="widget-title">✨ Artists to Follow</div>
  <div class="widget-body" style="display:flex;flex-direction:column;gap:4px;">
    <?php foreach ($suggested as $a): ?>
    <div class="suggest-item">
      <div class="suggest-avatar" style="background:linear-gradient(135deg,var(--accent),#7a4820);">
        <?php if ($a['avatar_path']): ?>
          <img src="<?= h(UPLOAD_URL.$a['avatar_path']) ?>" alt="">
        <?php else: ?>
          <?= strtoupper(substr($a['display_name'],0,2)) ?>
        <?php endif; ?>
      </div>
      <div>
        <div class="suggest-name"><?= h($a['display_name']) ?></div>
        <div class="suggest-handle">@<?= h($a['username']) ?></div>
      </div>
      <button class="follow-btn" onclick="toggleFollow(this,<?= $a['id'] ?>)">Follow</button>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<div style="padding:12px;border:1px solid var(--border);border-radius:var(--radius);font-size:13px;color:var(--text3);line-height:1.6;">
  <div style="font-size:14px;font-weight:600;color:var(--text2);margin-bottom:5px;">⚔️ ARTavern Promise</div>
  A gathering place for human artists only. No AI-generated art. Legal support available for all members.
</div>
<?php $__right_sidebar = ob_get_clean(); ?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<!-- Login banner -->
<?php if (!$user): ?>
<div class="login-prompt">
  <p><strong>Browse freely.</strong> Sign in to post, collab, and protect your work.</p>
  <div class="login-prompt-btns">
    <button class="btn btn-ghost btn-sm" onclick="openModal('login')">Sign in</button>
    <button class="btn btn-primary btn-sm" onclick="openModal('register')">Join</button>
  </div>
</div>
<?php endif; ?>

<!-- Feed tabs -->
<div class="feed-tabs">
  <div class="feed-tab <?= $tab==='all'?'active':'' ?>" onclick="setFeedTab(this,'all')">For You</div>
  <div class="feed-tab <?= $tab==='following'?'active':'' ?>" onclick="setFeedTab(this,'Following')">Following</div>
  <div class="feed-tab <?= $tab==='trending'?'active':'' ?>" onclick="setFeedTab(this,'trending')">Trending</div>
</div>

<!-- Composer -->
<?php if ($user): ?>
<div class="composer">
  <div class="composer-avatar">
    <?php if ($user['avatar_path']): ?>
      <img src="<?= h(UPLOAD_URL.$user['avatar_path']) ?>" alt="">
    <?php else: ?>
      <?= strtoupper(substr($user['display_name'],0,2)) ?>
    <?php endif; ?>
  </div>
  <div class="composer-right" style="flex:1;">
    <textarea id="composerTextarea" class="composer-input" placeholder="Share your art, thoughts, or progress…"
      rows="2" onclick="expandComposer(this)"></textarea>

    <!-- Tag row -->
    <div id="tagRow" style="display:none;position:relative;">
      <div class="tag-input-wrap" id="tagInputWrap">
        <input type="text" id="tagTypeInput" placeholder="Add tags… (#fantasy, #digitalart)">
      </div>
      <div class="tag-autocomplete" id="tagAutocomplete" style="display:none;"></div>
    </div>

    <!-- Medium + Source -->
    <div class="composer-meta" id="composerMeta" style="display:none;">
      <select class="meta-select" id="composerMedium">
        <option value="digital">🖥 Digital Art</option>
        <option value="traditional">✏️ Traditional</option>
        <option value="pixel_art">🕹 Pixel Art</option>
        <option value="3d">🧊 3D Art</option>
        <option value="mixed">🎭 Mixed Media</option>
        <option value="other">❓ Other</option>
      </select>
      <select class="meta-select" id="composerSource">
        <option value="created_on_site">🏠 Made on ARTavern</option>
        <option value="uploaded">📤 Uploaded</option>
      </select>
    </div>

    <!-- Action row -->
    <div class="composer-actions" id="composerActionsRow" style="display:none;">
      <button class="composer-icon-btn" title="Attach image" onclick="showToast('🖼️','Upload coming soon')">🖼️</button>
      <button class="composer-post-btn" onclick="submitPost()">Post</button>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- FEED -->
<?php foreach ($posts as $post): ?>
<div class="post-card" onclick="location.href='<?= SITE_URL ?>/pages/post.php?id=<?= $post['id'] ?>'">
  <div class="post-header">
    <div class="post-avatar" style="background:linear-gradient(135deg,var(--accent),#7a4820);">
      <?php if ($post['avatar_path']): ?>
        <img src="<?= h(UPLOAD_URL.$post['avatar_path']) ?>" alt="">
      <?php else: ?>
        <?= strtoupper(substr($post['display_name'],0,2)) ?>
      <?php endif; ?>
    </div>
    <div>
      <div class="post-meta-name"><?= h($post['display_name']) ?></div>
      <div class="post-meta-handle">@<?= h($post['username']) ?></div>
    </div>
    <div class="post-meta-time"><?= time_ago($post['created_at']) ?></div>
  </div>

  <?php if ($post['body']): ?>
    <p class="post-text"><?= nl2br(h($post['body'])) ?></p>
  <?php endif; ?>

  <!-- Medium & Source chips -->
  <div class="post-chips">
    <span class="chip chip-medium"><?= h(medium_label($post['medium'])) ?></span>
    <span class="chip chip-source"><?= h(source_label($post['source'])) ?></span>
  </div>

  <!-- Artwork image -->
  <?php if ($post['image_path']): ?>
  <div class="post-image">
    <img src="<?= h(UPLOAD_URL.$post['image_path']) ?>" alt="artwork"
         onclick="event.stopPropagation(); location.href='<?= SITE_URL ?>/pages/post.php?id=<?= $post['id'] ?>'">
  </div>
  <?php endif; ?>

  <!-- Tags -->
  <?php if ($post['tags']): ?>
  <div class="post-tags">
    <?php foreach ($post['tags'] as $tag): ?>
      <span class="tag" onclick="event.stopPropagation(); location.href='<?= SITE_URL ?>/pages/tag.php?slug=<?= h($tag['slug']) ?>'"><?= h($tag['name']) ?></span>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Actions -->
  <div class="post-actions" onclick="event.stopPropagation()">
    <button class="post-action" data-count="<?= $post['like_count'] ?>"
      onclick="toggleLike(this,<?= $post['id'] ?>)">
      🤍 <?= number_format($post['like_count']) ?>
    </button>
    <button class="post-action"
      data-href="<?= SITE_URL ?>/pages/post.php?id=<?= $post['id'] ?>"
      onclick="<?= $user ? 'location.href=this.dataset.href' : 'requireLogin(this.dataset.login)' ?>">
      💬 <?= number_format($post['comment_count']) ?>
    </button>
    <button class="post-action"
      onclick="<?= $user ? 'showToast(&quot;🔁&quot;,&quot;Repost coming soon&quot;)' : 'requireLogin(&quot;Reposts&quot;)' ?>">
      🔁 <?= number_format($post['repost_count']) ?>
    </button>
    <button class="post-action" onclick="toggleBookmark(this,<?= $post['id'] ?>)">🔖</button>
  </div>
</div>
<?php endforeach; ?>

<?php if (empty($posts)): ?>
  <div style="padding:40px;text-align:center;color:var(--text3);">
    No posts yet. Be the first to share your art! 🎨
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
