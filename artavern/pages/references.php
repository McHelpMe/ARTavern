<?php
// ── pages/references.php ─────────────────────────────────
require_once __DIR__ . '/../includes/config.php';
require_login();

$user    = current_user();
$__title = 'Reference Models — ARTavern';
$__page  = 'references';
$pdo     = db();

$refs = $pdo->query("SELECT r.*, u.display_name, u.username
    FROM reference_models r JOIN users u ON u.id=r.uploader_id
    ORDER BY r.created_at DESC LIMIT 40")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="section-header">
  📷 Reference Models
  <button class="btn btn-outline btn-sm"
    onclick="showToast('📤','Upload coming soon — model submission in next release')">+ Upload</button>
</div>

<div style="padding:12px 16px;border-bottom:1px solid var(--border);
            display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
  <span style="font-size:13px;color:var(--text3);">Filter:</span>
  <?php foreach(['photo_ref'=>'📸 Photo Ref','pose_pack'=>'🧍 Pose Pack','3d_model'=>'🧊 3D Model','other'=>'📦 Other'] as $v=>$l): ?>
  <button class="btn btn-ghost btn-sm" style="padding:4px 12px;"
    onclick="filterRef('<?= $v ?>')">
    <?= $l ?>
  </button>
  <?php endforeach; ?>
  <button class="btn btn-ghost btn-sm" style="padding:4px 12px;" onclick="filterRef('all')">All</button>
</div>

<?php if (!$refs): ?>
<div style="padding:40px;text-align:center;color:var(--text3);">
  No reference models yet. Be the first to upload! 📷
</div>
<?php endif; ?>

<div id="refGrid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:2px;">
  <?php foreach ($refs as $r): ?>
  <div class="portfolio-item ref-item" data-type="<?= h($r['type']) ?>"
    onclick="showToast('📷','Viewing reference: <?= h(addslashes($r['title'])) ?>')">
    <?php if ($r['file_path']): ?>
      <img src="<?= h(UPLOAD_URL.$r['file_path']) ?>" alt="<?= h($r['title']) ?>">
    <?php else: ?>
      <span style="font-size:40px;color:var(--text3);">
        <?= match($r['type']) { '3d_model'=>'🧊', 'pose_pack'=>'🧍', 'photo_ref'=>'📸', default=>'📦' } ?>
      </span>
    <?php endif; ?>
    <div class="portfolio-item-overlay">
      <div class="title"><?= h($r['title']) ?></div>
      <div class="meta">by @<?= h($r['username']) ?></div>
      <div class="meta"><?= $r['is_free'] ? '🆓 Free' : '💰 Paid' ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<script>
function filterRef(type) {
  document.querySelectorAll('.ref-item').forEach(el => {
    el.style.display = (type === 'all' || el.dataset.type === type) ? '' : 'none';
  });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
