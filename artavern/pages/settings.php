<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/profile.php';
require_login();

$user    = current_user();
$__title = 'Settings — ARTavern';
$__page  = '';

$success = '';
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'display_name'    => trim($_POST['display_name'] ?? ''),
        'bio'             => trim($_POST['bio'] ?? ''),
        'art_styles'      => trim($_POST['art_styles'] ?? ''),
        'commission_open' => !empty($_POST['commission_open']) ? 1 : 0,
    ];
    if (strlen($data['display_name']) < 2) $errors[] = 'Display name too short.';
    if (!$errors) {
        update_profile((int)$user['id'], $data);
        // Refresh session
        $updated = \fetch_user((int)$user['id']);
        $_SESSION['user'] = $updated;
        $success = 'Profile updated!';
        $user = $updated;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="section-header">⚙️ Settings</div>

<div style="max-width:520px;padding:24px;">
  <?php if ($success): ?>
    <div style="background:var(--teal-bg);color:var(--teal);padding:10px 14px;border-radius:var(--radius-sm);margin-bottom:16px;font-size:14px;">
      ✅ <?= h($success) ?>
    </div>
  <?php endif; ?>
  <?php foreach ($errors as $e): ?>
    <div class="form-error"><?= h($e) ?></div>
  <?php endforeach; ?>

  <form method="POST">
    <div class="form-group">
      <label class="form-label">Display name</label>
      <input class="form-input" name="display_name" value="<?= h($user['display_name']) ?>" required>
    </div>
    <div class="form-group">
      <label class="form-label">Username <span style="color:var(--text3);font-weight:400;">(cannot change)</span></label>
      <input class="form-input" value="@<?= h($user['username']) ?>" disabled style="opacity:0.5;">
    </div>
    <div class="form-group">
      <label class="form-label">Email <span style="color:var(--text3);font-weight:400;">(cannot change here)</span></label>
      <input class="form-input" value="<?= h($user['email']) ?>" disabled style="opacity:0.5;">
    </div>
    <div class="form-group">
      <label class="form-label">Bio</label>
      <textarea class="form-input" name="bio" rows="4" placeholder="Tell the tavern about yourself…" style="resize:vertical;"><?= h($user['bio'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">Art styles / specialties</label>
      <input class="form-input" name="art_styles" value="<?= h($user['art_styles'] ?? '') ?>" placeholder="e.g. Digital, Watercolor, Pixel Art">
    </div>
    <div class="form-check" style="margin-bottom:20px;">
      <input type="checkbox" id="commOpen" name="commission_open" <?= $user['commission_open'] ? 'checked' : '' ?>>
      <label for="commOpen">Open for commissions</label>
    </div>
    <button type="submit" class="btn btn-primary">Save changes</button>
  </form>

  <div style="margin-top:32px;padding-top:20px;border-top:1px solid var(--border);">
    <div style="font-size:15px;font-weight:600;margin-bottom:12px;color:var(--rose);">Danger Zone</div>
    <button class="btn btn-ghost" style="border-color:var(--rose);color:var(--rose);"
      onclick="showToast('⚠️','Account deletion — contact support')">Delete Account</button>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
