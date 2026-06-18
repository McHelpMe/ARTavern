<?php
// ── pages/profile.php ────────────────────────────────────
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/profile.php';
require_once __DIR__ . '/../includes/posts.php';

$username = trim($_GET['u'] ?? '');
if (!$username) { header('Location: '.SITE_URL.'/index.php'); exit; }

$profile   = get_profile($username);
if (!$profile) { http_response_code(404); die('User not found.'); }

$me           = current_user();
$followers    = get_follower_count((int)$profile['id']);
$following    = get_following_count((int)$profile['id']);
$am_following = $me ? is_following((int)$me['id'], (int)$profile['id']) : false;
$is_me        = $me && (int)$me['id'] === (int)$profile['id'];
$user_posts   = get_user_posts((int)$profile['id']);
$portfolio    = get_portfolio((int)$profile['id']);
$post_count   = count($user_posts);

$__title = h($profile['display_name']) . ' — ARTavern';
$__page  = 'profile';

require_once __DIR__ . '/../includes/header.php';
?>

<style>
/* ── Profile-page overrides ─────────────────────────── */
.profile-page { width: 100%; }

.profile-banner {
  height: 200px;
  background: linear-gradient(135deg, #2a1f0e 0%, #3d2e18 50%, #1e1714 100%);
  position: relative; overflow: hidden;
}
.profile-banner img {
  width: 100%; height: 100%; object-fit: cover; display: block;
}
.profile-banner-edit {
  position: absolute; bottom: 12px; right: 14px;
  background: rgba(0,0,0,0.55); border: 1px solid rgba(255,255,255,0.15);
  color: var(--text2); font-size: 12px; padding: 5px 12px;
  border-radius: 50px; cursor: pointer; backdrop-filter: blur(4px);
  transition: var(--transition);
}
.profile-banner-edit:hover { background: rgba(0,0,0,0.8); color: var(--text); }

.profile-body {
  padding: 0 22px 18px;
  border-bottom: 1px solid var(--border);
  position: relative;
}

/* Avatar overlaps banner */
.profile-avatar-wrap {
  display: flex; align-items: flex-end;
  justify-content: space-between;
  margin-top: -46px; margin-bottom: 14px;
}
.profile-avatar-large {
  width: 92px; height: 92px; border-radius: 50%;
  border: 4px solid var(--bg);
  background: linear-gradient(135deg, var(--accent), #7a4820);
  display: flex; align-items: center; justify-content: center;
  font-size: 30px; font-weight: 700; overflow: hidden; flex-shrink: 0;
  position: relative;
}
.profile-avatar-large img { width: 100%; height: 100%; object-fit: cover; }
.profile-avatar-edit-btn {
  position: absolute; inset: 0; border-radius: 50%;
  background: rgba(0,0,0,0.5); display: none;
  align-items: center; justify-content: center;
  font-size: 18px; cursor: pointer;
}
.profile-avatar-large:hover .profile-avatar-edit-btn { display: flex; }

.profile-action-row {
  display: flex; gap: 8px; align-items: center; padding-top: 50px;
}

.profile-name    { font-size: 21px; font-weight: 700; line-height: 1.2; }
.profile-handle  { font-size: 14px; color: var(--text3); margin: 3px 0 10px; }
.profile-badges  { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
.profile-bio-text{ font-size: 14px; color: var(--text2); line-height: 1.65; margin-bottom: 10px; max-width: 520px; }
.profile-styles  { display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 12px; }

.profile-stats {
  display: flex; gap: 22px; font-size: 14px; color: var(--text2);
}
.profile-stats a {
  color: var(--text2); text-decoration: none; transition: var(--transition);
}
.profile-stats a:hover { color: var(--text); }
.profile-stats strong { font-weight: 700; color: var(--text); margin-right: 3px; }

/* Tabs */
.profile-tabs {
  display: flex; border-bottom: 1px solid var(--border);
  position: sticky; top: var(--header-h); z-index: 10;
  background: rgba(14,12,10,0.94); backdrop-filter: blur(12px);
}
.profile-tab {
  flex: 1; padding: 14px; text-align: center;
  font-size: 14px; font-weight: 500; color: var(--text3);
  cursor: pointer; transition: var(--transition);
  border-bottom: 2px solid transparent;
}
.profile-tab:hover  { color: var(--text2); background: var(--surface); }
.profile-tab.active { color: var(--accent2); border-bottom-color: var(--accent); }

/* Post tab */
.profile-posts  { }
.profile-post-card {
  padding: 16px 22px; border-bottom: 1px solid var(--border);
  cursor: pointer; transition: var(--transition);
}
.profile-post-card:hover { background: var(--bg2); }

/* Portfolio tab */
.portfolio-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 2px;
}
.portfolio-item {
  aspect-ratio: 1; background: var(--surface);
  display: flex; align-items: center; justify-content: center;
  overflow: hidden; cursor: pointer; position: relative;
}
.portfolio-item img { width: 100%; height: 100%; object-fit: cover; display: block; }
.portfolio-overlay {
  position: absolute; inset: 0; background: rgba(14,12,10,0.75);
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 4px; opacity: 0; transition: var(--transition); padding: 10px; text-align: center;
}
.portfolio-item:hover .portfolio-overlay { opacity: 1; }
.portfolio-overlay .pi-title { font-size: 13px; font-weight: 600; color: var(--text); }
.portfolio-overlay .pi-meta  { font-size: 11px; color: var(--text2); }

/* Likes tab placeholder */
.profile-likes { }

/* Empty state */
.profile-empty {
  padding: 50px 24px; text-align: center; color: var(--text3);
}
.profile-empty .empty-icon { font-size: 38px; margin-bottom: 10px; }
.profile-empty .empty-title { font-size: 15px; font-weight: 600; color: var(--text2); margin-bottom: 5px; }
.profile-empty .empty-sub   { font-size: 13px; }
</style>

<div class="profile-page">

  <!-- ── BANNER ── -->
  <div class="profile-banner">
    <?php if ($profile['banner_path']): ?>
      <img src="<?= h(UPLOAD_URL . $profile['banner_path']) ?>" alt="banner">
    <?php endif; ?>
    <?php if ($is_me): ?>
      <button class="profile-banner-edit" onclick="showToast('🖼️','Banner upload coming soon')">
        Change banner
      </button>
    <?php endif; ?>
  </div>

  <!-- ── PROFILE BODY ── -->
  <div class="profile-body">

    <div class="profile-avatar-wrap">
      <!-- Avatar -->
      <div class="profile-avatar-large">
        <?php if ($profile['avatar_path']): ?>
          <img src="<?= h(UPLOAD_URL . $profile['avatar_path']) ?>" alt="">
        <?php else: ?>
          <?= strtoupper(substr($profile['display_name'], 0, 2)) ?>
        <?php endif; ?>
        <?php if ($is_me): ?>
          <div class="profile-avatar-edit-btn"
               onclick="showToast('📷','Avatar upload coming soon')">📷</div>
        <?php endif; ?>
      </div>

      <!-- Action buttons -->
      <div class="profile-action-row">
        <?php if ($is_me): ?>
          <a href="<?= SITE_URL ?>/pages/settings.php"
             class="btn btn-ghost btn-sm">Edit Profile</a>
        <?php elseif ($me): ?>
          <button class="btn btn-sm follow-btn <?= $am_following ? 'following btn-primary' : 'btn-outline' ?>"
                  id="followBtn"
                  onclick="handleFollow(this, <?= (int)$profile['id'] ?>)">
            <?= $am_following ? 'Following' : 'Follow' ?>
          </button>
          <a href="<?= SITE_URL ?>/pages/messages.php?to=<?= h($profile['username']) ?>"
             class="btn btn-ghost btn-sm">Message</a>
        <?php else: ?>
          <button class="btn btn-primary btn-sm"
                  onclick="openModal('login')">Follow</button>
        <?php endif; ?>
      </div>
    </div>

    <!-- Name & handle -->
    <div class="profile-name"><?= h($profile['display_name']) ?></div>
    <div class="profile-handle">@<?= h($profile['username']) ?></div>

    <!-- Badges -->
    <div class="profile-badges">
      <?php if ($profile['role'] === 'admin'): ?>
        <span class="chip chip-source">⚙️ Admin</span>
      <?php endif; ?>
      <?php if ($profile['role'] === 'moderator'): ?>
        <span class="chip chip-source">🛡 Moderator</span>
      <?php endif; ?>
      <?php if ($profile['is_verified']): ?>
        <span class="chip" style="background:rgba(77,184,154,0.12);color:var(--teal);">✓ Verified</span>
      <?php endif; ?>
      <?php if ($profile['commission_open']): ?>
        <span class="chip chip-medium">🎨 Open for Commissions</span>
      <?php endif; ?>
    </div>

    <!-- Bio -->
    <?php if ($profile['bio']): ?>
      <p class="profile-bio-text"><?= nl2br(h($profile['bio'])) ?></p>
    <?php endif; ?>

    <!-- Art styles -->
    <?php if ($profile['art_styles']): ?>
      <div class="profile-styles">
        <?php foreach (array_filter(array_map('trim', explode(',', $profile['art_styles']))) as $s): ?>
          <span class="chip chip-medium"><?= h($s) ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="profile-stats">
      <a href="#" onclick="showFollowersModal(); return false;">
        <strong id="followerCount"><?= number_format($followers) ?></strong> Followers
      </a>
      <a href="#" onclick="showFollowingModal(); return false;">
        <strong><?= number_format($following) ?></strong> Following
      </a>
      <span>
        <strong><?= number_format($post_count) ?></strong> Posts
      </span>
      <span>
        <strong><?= number_format(count($portfolio)) ?></strong> Portfolio
      </span>
    </div>
  </div>

  <!-- ── TABS ── -->
  <div class="profile-tabs">
    <div class="profile-tab active" id="tabPosts"     onclick="switchTab('posts')">Posts</div>
    <div class="profile-tab"        id="tabPortfolio" onclick="switchTab('portfolio')">Portfolio</div>
    <div class="profile-tab"        id="tabLikes"     onclick="switchTab('likes')">Likes</div>
  </div>

  <!-- ── POSTS TAB ── -->
  <div id="panePosts">
    <?php if (empty($user_posts)): ?>
      <div class="profile-empty">
        <div class="empty-icon">🎨</div>
        <div class="empty-title">No posts yet</div>
        <div class="empty-sub">
          <?= $is_me ? 'Share your first piece with the tavern!' : h($profile['display_name']) . ' hasn\'t posted yet.' ?>
        </div>
        <?php if ($is_me): ?>
          <button class="btn btn-primary" style="margin-top:14px;"
                  onclick="location.href='<?= SITE_URL ?>/index.php'">
            Go to Feed
          </button>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <?php foreach ($user_posts as $post): ?>
      <div class="profile-post-card"
           onclick="location.href='<?= SITE_URL ?>/pages/post.php?id=<?= (int)$post['id'] ?>'">
        <div class="post-header">
          <div class="post-avatar"
               style="background:linear-gradient(135deg,var(--accent),#7a4820);">
            <?php if ($profile['avatar_path']): ?>
              <img src="<?= h(UPLOAD_URL.$profile['avatar_path']) ?>" alt="">
            <?php else: ?>
              <?= strtoupper(substr($profile['display_name'],0,2)) ?>
            <?php endif; ?>
          </div>
          <div>
            <div class="post-meta-name"><?= h($profile['display_name']) ?></div>
            <div class="post-meta-handle">@<?= h($profile['username']) ?></div>
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
          <div class="post-image">
            <img src="<?= h(UPLOAD_URL.$post['image_path']) ?>" alt="artwork">
          </div>
        <?php endif; ?>

        <?php if (!empty($post['tags'])): ?>
          <div class="post-tags">
            <?php foreach ($post['tags'] as $tag): ?>
              <span class="tag"
                    onclick="event.stopPropagation();
                             location.href='<?= SITE_URL ?>/pages/tag.php?slug=<?= h($tag['slug']) ?>'">
                <?= h($tag['name']) ?>
              </span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="post-actions" onclick="event.stopPropagation()">
          <button class="post-action <?= '' /* like state from DB would go here */ ?>"
                  data-count="<?= (int)$post['like_count'] ?>"
                  onclick="toggleLike(this, <?= (int)$post['id'] ?>)">
            🤍 <?= number_format($post['like_count']) ?>
          </button>
          <button class="post-action"
                  data-href="<?= SITE_URL ?>/pages/post.php?id=<?= (int)$post['id'] ?>"
                  onclick="location.href=this.dataset.href">
            💬 <?= number_format($post['comment_count']) ?>
          </button>
          <button class="post-action"
                  onclick="toggleBookmark(this, <?= (int)$post['id'] ?>)">
            🔖
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- ── PORTFOLIO TAB ── -->
  <div id="panePortfolio" style="display:none;">

    <?php if ($is_me): ?>
      <div style="padding:12px 18px; border-bottom:1px solid var(--border);
                  display:flex; align-items:center; justify-content:space-between;">
        <span style="font-size:13px;color:var(--text3);">
          <?= number_format(count($portfolio)) ?> piece<?= count($portfolio) !== 1 ? 's' : '' ?>
        </span>
        <button class="btn btn-outline btn-sm"
                onclick="location.href='<?= SITE_URL ?>/pages/portfolio.php?u=<?= h($profile['username']) ?>'">
          Manage Portfolio →
        </button>
      </div>
    <?php endif; ?>

    <?php if (empty($portfolio)): ?>
      <div class="profile-empty">
        <div class="empty-icon">🖼️</div>
        <div class="empty-title">No portfolio pieces yet</div>
        <div class="empty-sub">
          <?= $is_me ? 'Add your best work to build your portfolio.' : h($profile['display_name']) . ' hasn\'t added portfolio pieces yet.' ?>
        </div>
        <?php if ($is_me): ?>
          <button class="btn btn-primary" style="margin-top:14px;"
                  onclick="location.href='<?= SITE_URL ?>/pages/portfolio.php?u=<?= h($profile['username']) ?>'">
            Add Work
          </button>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <!-- Filter bar -->
      <div style="padding:10px 14px;border-bottom:1px solid var(--border);
                  display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
        <button class="btn btn-ghost btn-sm pf-filter active" data-m="all"
                onclick="filterPF(this,'all')">All</button>
        <?php
        $mediums_used = array_unique(array_column($portfolio,'medium'));
        $medium_labels = ['digital'=>'🖥 Digital','traditional'=>'✏️ Traditional',
                          'pixel_art'=>'🕹 Pixel','3d'=>'🧊 3D','mixed'=>'🎭 Mixed','other'=>'❓ Other'];
        foreach ($mediums_used as $mv):
          if (isset($medium_labels[$mv])): ?>
        <button class="btn btn-ghost btn-sm pf-filter" data-m="<?= h($mv) ?>"
                onclick="filterPF(this,'<?= h($mv) ?>')"><?= $medium_labels[$mv] ?></button>
        <?php endif; endforeach; ?>
      </div>

      <div class="portfolio-grid" id="pfGrid">
        <?php foreach ($portfolio as $item): ?>
        <div class="portfolio-item pf-item" data-medium="<?= h($item['medium']) ?>"
             onclick="location.href='<?= SITE_URL ?>/pages/portfolio.php?u=<?= h($profile['username']) ?>'">
          <?php if ($item['image_path']): ?>
            <img src="<?= h(UPLOAD_URL.$item['image_path']) ?>" alt="<?= h($item['title']) ?>">
          <?php else: ?>
            <span style="font-size:36px;color:var(--text3);">🖼️</span>
          <?php endif; ?>
          <div class="portfolio-overlay">
            <div class="pi-title"><?= h($item['title']) ?></div>
            <div class="pi-meta"><?= h(medium_label($item['medium'])) ?></div>
            <?php if ($item['is_featured']): ?>
              <div class="pi-meta" style="color:var(--accent2);">⭐ Featured</div>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- ── LIKES TAB ── -->
  <div id="paneLikes" style="display:none;">
    <?php
    // Fetch posts this user has liked
    $liked_posts = [];
    if ($is_me || true) { // show liked posts publicly for now
        $st = db()->prepare("
            SELECT p.*, u.display_name, u.username, u.avatar_path
            FROM likes l
            JOIN posts p ON p.id = l.post_id AND p.is_deleted = 0
            JOIN users u ON u.id = p.user_id
            WHERE l.user_id = ?
            ORDER BY l.created_at DESC
            LIMIT 30
        ");
        $st->execute([(int)$profile['id']]);
        $liked_posts = $st->fetchAll();
        if ($liked_posts) {
            $liked_posts = attach_tags_to_posts(db(), $liked_posts);
        }
    }
    ?>
    <?php if (empty($liked_posts)): ?>
      <div class="profile-empty">
        <div class="empty-icon">🤍</div>
        <div class="empty-title">No liked posts yet</div>
        <div class="empty-sub">
          <?= $is_me ? 'Posts you like will show up here.' : h($profile['display_name']) . ' hasn\'t liked any posts yet.' ?>
        </div>
      </div>
    <?php else: ?>
      <?php foreach ($liked_posts as $post): ?>
      <div class="profile-post-card"
           onclick="location.href='<?= SITE_URL ?>/pages/post.php?id=<?= (int)$post['id'] ?>'">
        <div class="post-header">
          <div class="post-avatar"
               style="background:linear-gradient(135deg,var(--accent),#7a4820);">
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
          <p class="post-text"><?= nl2br(h(substr($post['body'],0,200))) ?>
            <?= strlen($post['body']) > 200 ? '…' : '' ?></p>
        <?php endif; ?>
        <div class="post-chips">
          <span class="chip chip-medium"><?= h(medium_label($post['medium'])) ?></span>
          <span class="chip chip-source"><?= h(source_label($post['source'])) ?></span>
        </div>
        <?php if ($post['image_path']): ?>
          <div class="post-image">
            <img src="<?= h(UPLOAD_URL.$post['image_path']) ?>" alt="artwork">
          </div>
        <?php endif; ?>
        <?php if (!empty($post['tags'])): ?>
          <div class="post-tags">
            <?php foreach ($post['tags'] as $tag): ?>
              <span class="tag"
                onclick="event.stopPropagation();
                         location.href='<?= SITE_URL ?>/pages/tag.php?slug=<?= h($tag['slug']) ?>'">
                <?= h($tag['name']) ?>
              </span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <div class="post-actions" onclick="event.stopPropagation()">
          <button class="post-action liked"
                  data-count="<?= (int)$post['like_count'] ?>"
                  onclick="toggleLike(this, <?= (int)$post['id'] ?>)">
            ❤️ <?= number_format($post['like_count']) ?>
          </button>
          <button class="post-action"
                  data-href="<?= SITE_URL ?>/pages/post.php?id=<?= (int)$post['id'] ?>"
                  onclick="location.href=this.dataset.href">
            💬 <?= number_format($post['comment_count']) ?>
          </button>
          <button class="post-action bookmarked"
                  onclick="toggleBookmark(this, <?= (int)$post['id'] ?>)">
            🔖
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div><!-- /profile-page -->

<script>
/* ── Tab switching ──────────────────────────────────────── */
const tabs  = { posts: 'tabPosts', portfolio: 'tabPortfolio', likes: 'tabLikes' };
const panes = { posts: 'panePosts', portfolio: 'panePortfolio', likes: 'paneLikes' };

function switchTab(name) {
  Object.keys(tabs).forEach(k => {
    document.getElementById(tabs[k]).classList.toggle('active', k === name);
    document.getElementById(panes[k]).style.display = k === name ? '' : 'none';
  });
}

/* ── Portfolio filter ───────────────────────────────────── */
function filterPF(btn, medium) {
  document.querySelectorAll('.pf-filter').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.pf-item').forEach(el => {
    el.style.display = (medium === 'all' || el.dataset.medium === medium) ? '' : 'none';
  });
}

/* ── Follow with live counter update ───────────────────── */
async function handleFollow(btn, userId) {
  const res = await postForm('<?= SITE_URL ?>/pages/api/profile.php?action=follow',
                             { target_id: userId });
  if (!res.ok) { requireLogin('Following artists'); return; }
  const following = res.following;
  btn.classList.toggle('following', following);
  btn.classList.toggle('btn-primary', following);
  btn.classList.toggle('btn-outline', !following);
  btn.textContent = following ? 'Following' : 'Follow';
  // Update follower count
  const fc = document.getElementById('followerCount');
  if (fc) fc.textContent = (parseInt(fc.textContent.replace(/,/g,'')) + (following ? 1 : -1)).toLocaleString();
  showToast(following ? '✨' : '👋', following ? 'Now following!' : 'Unfollowed');
}

/* ── Stub modals for followers/following lists ──────────── */
function showFollowersModal() { showToast('👥', 'Followers list coming soon'); }
function showFollowingModal() { showToast('👥', 'Following list coming soon'); }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
