<?php
// ── includes/header.php ─────────────────────────────────
require_once __DIR__ . '/config.php';
$__user = current_user();
$__page = $__page ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($__title ?? SITE_NAME) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
</head>
<body>

<header class="site-header">
  <a class="logo" href="<?= SITE_URL ?>/index.php">
    <div class="logo-icon">🍺</div>
    <span class="logo-name">ARTavern</span>
  </a>

  <form class="search-bar" action="<?= SITE_URL ?>/pages/search.php" method="GET">
    <span class="search-icon">&#128269;</span>
    <input type="text" name="q" placeholder="Search artists, tags, styles…"
           value="<?= h($_GET['q'] ?? '') ?>">
  </form>

  <nav class="header-nav">
    <?php if ($__user): ?>
      <div class="profile-menu" id="profileMenu">
        <div class="profile-avatar" id="profileAvatarBtn" onclick="toggleDropdown()">
          <?php if ($__user['avatar_path']): ?>
            <img src="<?= h(UPLOAD_URL . $__user['avatar_path']) ?>" alt="">
          <?php else: ?>
            <?= strtoupper(substr($__user['display_name'], 0, 2)) ?>
          <?php endif; ?>
        </div>
        <div class="profile-dropdown" id="profileDropdown">
          <div class="dropdown-header">
            <div class="dropdown-avatar">
              <?= strtoupper(substr($__user['display_name'], 0, 2)) ?>
            </div>
            <div>
              <div class="dd-name"><?= h($__user['display_name']) ?></div>
              <div class="dd-handle">@<?= h($__user['username']) ?></div>
            </div>
          </div>
          <a class="dropdown-item" href="<?= SITE_URL ?>/pages/profile.php?u=<?= h($__user['username']) ?>">
            &#128100; My Profile
          </a>
          <a class="dropdown-item" href="<?= SITE_URL ?>/pages/portfolio.php?u=<?= h($__user['username']) ?>">
            &#127912; Portfolio
          </a>
          <a class="dropdown-item" href="<?= SITE_URL ?>/pages/collaborate.php">
            &#129309; Collaborations
          </a>
          <a class="dropdown-item" href="<?= SITE_URL ?>/pages/commissions.php">
            &#128221; Commissions
          </a>
          <a class="dropdown-item" href="<?= SITE_URL ?>/pages/messages.php">
            &#128172; Messages
            <?php
            $unread = 0;
            if ($__user) {
                $st = db()->prepare('SELECT COUNT(*) FROM messages WHERE receiver_id=? AND is_read=0');
                $st->execute([$__user['id']]);
                $unread = (int)$st->fetchColumn();
            }
            if ($unread > 0): ?>
              <span class="badge"><?= $unread ?></span>
            <?php endif; ?>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?= SITE_URL ?>/pages/settings.php">&#9881; Settings</a>
          <a class="dropdown-item" href="<?= SITE_URL ?>/pages/copyright.php">&#9878; Copyright & Legal</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item danger" href="<?= SITE_URL ?>/pages/logout.php">&#10148; Sign out</a>
        </div>
      </div>
    <?php else: ?>
      <button class="btn btn-ghost" onclick="openModal('login')">Sign in</button>
      <button class="btn btn-primary" onclick="openModal('register')">Join ARTavern</button>
    <?php endif; ?>
  </nav>
</header>

<div class="app-layout">

<!-- LEFT SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-section-label">Browse</div>
  <a class="sidebar-item <?= $__page==='home'?'active':'' ?>" href="<?= SITE_URL ?>/index.php">
    &#127968; Home
  </a>
  <a class="sidebar-item <?= $__page==='explore'?'active':'' ?>" href="<?= SITE_URL ?>/pages/explore.php">
    &#128269; Explore
  </a>
  <a class="sidebar-item <?= $__page==='trending'?'active':'' ?>" href="<?= SITE_URL ?>/pages/trending.php">
    &#128200; Trending
  </a>

  <div class="sidebar-section-label">Create</div>
  <a class="sidebar-item <?= $__page==='portfolio'?'active':'' ?>"
     href="<?= $__user ? SITE_URL.'/pages/portfolio.php?u='.h($__user['username']) : 'javascript:requireLogin()' ?>">
    &#127912; My Portfolio
  </a>

  <div class="sidebar-section-label">Community</div>
  <a class="sidebar-item <?= $__page==='collaborate'?'active':'' ?>"
     href="<?= $__user ? SITE_URL.'/pages/collaborate.php' : 'javascript:requireLogin()' ?>">
    &#129309; Collaborate
  </a>
  <a class="sidebar-item <?= $__page==='commissions'?'active':'' ?>"
     href="<?= $__user ? SITE_URL.'/pages/commissions.php' : 'javascript:requireLogin()' ?>">
    &#128221; Commissions
  </a>
  <a class="sidebar-item <?= $__page==='references'?'active':'' ?>"
     href="<?= $__user ? SITE_URL.'/pages/references.php' : 'javascript:requireLogin()' ?>">
    &#128247; References
  </a>
  <a class="sidebar-item <?= $__page==='messages'?'active':'' ?>"
     href="<?= $__user ? SITE_URL.'/pages/messages.php' : 'javascript:requireLogin()' ?>">
    &#128172; Messages
    <?php if (!empty($unread) && $unread > 0): ?>
      <span class="sidebar-badge"><?= $unread ?></span>
    <?php endif; ?>
  </a>

  <div class="sidebar-section-label">Protection</div>
  <a class="sidebar-item" href="<?= SITE_URL ?>/pages/copyright.php">
    &#9878; Copyright & Legal
  </a>
</aside>

<main class="main-content">
