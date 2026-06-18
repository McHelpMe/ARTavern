<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$user    = current_user();
$__title = 'Collaborate — ARTavern';
$__page  = 'collaborate';

$pdo    = db();
$open   = $pdo->query('SELECT c.*, u.display_name, u.username,
    (SELECT COUNT(*) FROM collaboration_members cm WHERE cm.collab_id=c.id) AS member_count
    FROM collaborations c JOIN users u ON u.id=c.host_id
    WHERE c.status="open" ORDER BY c.created_at DESC LIMIT 30')->fetchAll();

// Handle new collab creation
$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $type  = $_POST['type'] ?? 'shared_canvas';
    $max   = max(2, min(10, (int)($_POST['max_members'] ?? 2)));
    if (!$title) { $errors[] = 'Title is required.'; }
    if (!$errors) {
        $pdo->prepare('INSERT INTO collaborations (host_id,title,description,type,max_members) VALUES (?,?,?,?,?)')
            ->execute([$user['id'],$title,$desc,$type,$max]);
        $success = 'Collaboration created!';
        header('Location: ' . SITE_URL . '/pages/collaborate.php?created=1');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="section-header">
  🤝 Collaborations
  <button class="btn btn-primary btn-sm" onclick="toggleForm()">+ New Collab</button>
</div>

<!-- Create form -->
<div id="collabForm" style="display:none;padding:18px;border-bottom:1px solid var(--border);">
  <form method="POST">
    <div class="form-group">
      <label class="form-label">Title</label>
      <input class="form-input" name="title" placeholder="Name your collaboration" required>
    </div>
    <div class="form-group">
      <label class="form-label">Description</label>
      <textarea class="form-input" name="description" rows="3" placeholder="What's it about? Style, goals, tools…" style="resize:vertical;"></textarea>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
      <div class="form-group" style="flex:1;">
        <label class="form-label">Type</label>
        <select class="form-input meta-select" name="type" style="width:100%;border-radius:var(--radius-sm);">
          <option value="shared_canvas">🎨 Shared Canvas</option>
          <option value="tutoring">📚 Tutoring</option>
          <option value="matchmaking">🎲 Matchmaking</option>
        </select>
      </div>
      <div class="form-group" style="flex:1;">
        <label class="form-label">Max members</label>
        <input class="form-input" type="number" name="max_members" min="2" max="10" value="2">
      </div>
    </div>
    <?php foreach ($errors as $e): ?><div class="form-error"><?= h($e) ?></div><?php endforeach; ?>
    <button type="submit" class="btn btn-primary">Create</button>
    <button type="button" class="btn btn-ghost" onclick="toggleForm()">Cancel</button>
  </form>
</div>

<?php if (isset($_GET['created'])): ?>
  <div style="padding:14px 18px;background:var(--teal-bg);color:var(--teal);font-size:14px;border-bottom:1px solid var(--border);">
    ✅ Collaboration created!
  </div>
<?php endif; ?>

<!-- Collab listings -->
<?php if (!$open): ?>
  <div style="padding:40px;text-align:center;color:var(--text3);">No open collaborations yet. Start one!</div>
<?php endif; ?>

<div style="padding:16px;display:flex;flex-direction:column;gap:12px;">
<?php foreach ($open as $c): ?>
<div class="collab-card">
  <div style="display:flex;align-items:center;justify-content:space-between;">
    <div class="collab-type <?= h($c['type']) ?>">
      <?= match($c['type']) {
        'tutoring'      => '📚 Tutoring',
        'shared_canvas' => '🎨 Shared Canvas',
        'matchmaking'   => '🎲 Matchmaking',
        default         => '🤝 Collab'
      } ?>
    </div>
    <div style="font-size:12px;color:var(--text3);"><?= time_ago($c['created_at']) ?></div>
  </div>
  <div class="collab-title"><?= h($c['title']) ?></div>
  <?php if ($c['description']): ?>
    <div class="collab-meta"><?= h(substr($c['description'],0,120)) ?><?= strlen($c['description'])>120?'…':'' ?></div>
  <?php endif; ?>
  <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;">
    <div class="collab-meta">
      by <a href="<?= SITE_URL ?>/pages/profile.php?u=<?= h($c['username']) ?>"
            style="color:var(--accent2);">@<?= h($c['username']) ?></a>
      · <?= $c['member_count'] ?>/<?= $c['max_members'] ?> members
    </div>
    <button class="btn btn-outline btn-sm"
      onclick="showToast('🤝','Joining collaboration — canvas coming soon!')">Join</button>
  </div>
</div>
<?php endforeach; ?>
</div>

<script>
function toggleForm() {
  const f = document.getElementById('collabForm');
  f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
