<?php
require_once __DIR__ . '/../includes/config.php';
require_login();

$user    = current_user();
$__title = 'Commissions — ARTavern';
$__page  = 'commissions';
$pdo     = db();

// My incoming (as artist) + outgoing (as client) commissions
$incoming = $pdo->prepare('SELECT c.*, u.display_name AS client_name, u.username AS client_uname
    FROM commissions c JOIN users u ON u.id=c.client_id
    WHERE c.artist_id=? ORDER BY c.created_at DESC');
$incoming->execute([$user['id']]);
$incoming = $incoming->fetchAll();

$outgoing = $pdo->prepare('SELECT c.*, u.display_name AS artist_name, u.username AS artist_uname
    FROM commissions c JOIN users u ON u.id=c.artist_id
    WHERE c.client_id=? ORDER BY c.created_at DESC');
$outgoing->execute([$user['id']]);
$outgoing = $outgoing->fetchAll();

// Artists open for commissions
$open_artists = $pdo->query('SELECT id,display_name,username,avatar_path,art_styles,bio
    FROM users WHERE commission_open=1 LIMIT 20')->fetchAll();

require_once __DIR__ . '/../includes/header.php';

function status_class(string $s): string {
    return match($s) {
        'pending'=>'pending','accepted'=>'accepted',
        'completed'=>'completed','cancelled'=>'cancelled',
        default=>'pending'
    };
}
?>

<div class="section-header">📋 Commissions</div>

<!-- Open for commissions -->
<div style="padding:16px 18px 10px;font-size:15px;font-weight:600;border-bottom:1px solid var(--border);">
  🎨 Artists Open for Commissions
</div>
<div style="padding:14px 16px;display:flex;flex-direction:column;gap:8px;">
  <?php if (!$open_artists): ?>
    <div style="color:var(--text3);font-size:14px;">No artists have opened commissions yet.</div>
  <?php endif; ?>
  <?php foreach ($open_artists as $a): ?>
  <div class="commission-card" style="flex-direction:row;align-items:center;gap:12px;">
    <div class="suggest-avatar" style="background:linear-gradient(135deg,var(--accent),#7a4820);width:44px;height:44px;font-size:15px;">
      <?= strtoupper(substr($a['display_name'],0,2)) ?>
    </div>
    <div style="flex:1;">
      <div style="font-size:14px;font-weight:600;"><?= h($a['display_name']) ?></div>
      <div style="font-size:12px;color:var(--text3);">@<?= h($a['username']) ?>
        <?php if ($a['art_styles']): ?> · <?= h($a['art_styles']) ?><?php endif; ?>
      </div>
      <?php if ($a['bio']): ?><div style="font-size:13px;color:var(--text2);margin-top:3px;"><?= h(substr($a['bio'],0,80)) ?>…</div><?php endif; ?>
    </div>
    <button class="btn btn-outline btn-sm"
      onclick="showToast('📋','Commission request — messaging coming soon')">Request</button>
  </div>
  <?php endforeach; ?>
</div>

<!-- My commissions tabs -->
<div class="feed-tabs" style="position:static;">
  <div class="feed-tab active" id="tcIn"  onclick="showCTab('incoming')">Incoming</div>
  <div class="feed-tab"        id="tcOut" onclick="showCTab('outgoing')">My Requests</div>
</div>

<div id="cIncoming" style="padding:14px 16px;display:flex;flex-direction:column;gap:10px;">
  <?php if (!$incoming): ?>
    <div style="color:var(--text3);font-size:14px;padding:20px 0;text-align:center;">No incoming commissions yet.</div>
  <?php endif; ?>
  <?php foreach ($incoming as $c): ?>
  <div class="commission-card">
    <div class="commission-status <?= status_class($c['status']) ?>"><?= strtoupper($c['status']) ?></div>
    <div class="collab-title"><?= h($c['title']) ?></div>
    <div class="collab-meta">From @<?= h($c['client_uname']) ?> · <?= time_ago($c['created_at']) ?></div>
    <?php if ($c['description']): ?><div style="font-size:13px;color:var(--text2);"><?= h(substr($c['description'],0,100)) ?></div><?php endif; ?>
    <?php if ($c['price']): ?><div style="font-size:13px;color:var(--accent2);">$<?= number_format($c['price'],2) ?></div><?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>

<div id="cOutgoing" style="display:none;padding:14px 16px;display:flex;flex-direction:column;gap:10px;">
  <?php if (!$outgoing): ?>
    <div style="color:var(--text3);font-size:14px;padding:20px 0;text-align:center;">You haven't requested any commissions yet.</div>
  <?php endif; ?>
  <?php foreach ($outgoing as $c): ?>
  <div class="commission-card">
    <div class="commission-status <?= status_class($c['status']) ?>"><?= strtoupper($c['status']) ?></div>
    <div class="collab-title"><?= h($c['title']) ?></div>
    <div class="collab-meta">To @<?= h($c['artist_uname']) ?> · <?= time_ago($c['created_at']) ?></div>
    <?php if ($c['price']): ?><div style="font-size:13px;color:var(--accent2);">$<?= number_format($c['price'],2) ?></div><?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>

<script>
function showCTab(t) {
  document.getElementById('tcIn').classList.toggle('active',  t==='incoming');
  document.getElementById('tcOut').classList.toggle('active', t==='outgoing');
  document.getElementById('cIncoming').style.display = t==='incoming' ? 'flex' : 'none';
  document.getElementById('cOutgoing').style.display = t==='outgoing' ? 'flex' : 'none';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
