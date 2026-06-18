<?php
// ── pages/messages.php ───────────────────────────────────
require_once __DIR__ . '/../includes/config.php';
require_login();

$user    = current_user();
$__title = 'Messages — ARTavern';
$__page  = 'messages';
$pdo     = db();

// Conversation list: distinct users the current user has chatted with
$convos = $pdo->prepare("
    SELECT
        u.id, u.display_name, u.username, u.avatar_path,
        MAX(m.created_at) AS last_time,
        SUM(CASE WHEN m.receiver_id=? AND m.is_read=0 THEN 1 ELSE 0 END) AS unread_count,
        (SELECT body FROM messages
         WHERE (sender_id=? AND receiver_id=u.id) OR (sender_id=u.id AND receiver_id=?)
         ORDER BY created_at DESC LIMIT 1) AS last_msg
    FROM messages m
    JOIN users u ON u.id = IF(m.sender_id=?, m.receiver_id, m.sender_id)
    WHERE m.sender_id=? OR m.receiver_id=?
    GROUP BY u.id
    ORDER BY last_time DESC
");
$uid = (int)$user['id'];
$convos->execute([$uid,$uid,$uid,$uid,$uid,$uid]);
$conversations = $convos->fetchAll();

// Active conversation
$with = trim($_GET['to'] ?? '');
$active_user = null;
$thread = [];

if ($with) {
    $st = $pdo->prepare('SELECT id,display_name,username,avatar_path FROM users WHERE username=?');
    $st->execute([$with]);
    $active_user = $st->fetch();

    if ($active_user) {
        // Mark as read
        $pdo->prepare('UPDATE messages SET is_read=1 WHERE sender_id=? AND receiver_id=?')
            ->execute([$active_user['id'], $uid]);

        $st2 = $pdo->prepare('SELECT m.*, u.display_name, u.username, u.avatar_path
            FROM messages m JOIN users u ON u.id=m.sender_id
            WHERE (m.sender_id=? AND m.receiver_id=?) OR (m.sender_id=? AND m.receiver_id=?)
            ORDER BY m.created_at ASC');
        $st2->execute([$uid,$active_user['id'],$active_user['id'],$uid]);
        $thread = $st2->fetchAll();
    }
}

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $active_user) {
    $body = trim($_POST['body'] ?? '');
    if ($body) {
        $pdo->prepare('INSERT INTO messages (sender_id,receiver_id,body) VALUES (?,?,?)')
            ->execute([$uid, $active_user['id'], $body]);
        header('Location: ' . SITE_URL . '/pages/messages.php?to=' . urlencode($active_user['username']));
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.msg-layout     { display:flex; height:calc(100vh - var(--header-h)); overflow:hidden; }
.msg-sidebar    { width:280px; flex-shrink:0; border-right:1px solid var(--border); overflow-y:auto; }
.msg-panel      { flex:1; display:flex; flex-direction:column; }
.msg-panel-head { padding:14px 18px; border-bottom:1px solid var(--border);
                  display:flex; align-items:center; gap:10px; }
.msg-thread     { flex:1; overflow-y:auto; padding:16px 18px;
                  display:flex; flex-direction:column; gap:10px; }
.msg-bubble     { max-width:68%; padding:10px 14px; border-radius:16px;
                  font-size:14px; line-height:1.5; }
.msg-bubble.mine  { background:var(--accent); color:#fff; align-self:flex-end;
                    border-bottom-right-radius:4px; }
.msg-bubble.theirs{ background:var(--surface2); color:var(--text); align-self:flex-start;
                    border-bottom-left-radius:4px; }
.msg-time       { font-size:11px; color:var(--text3); margin-top:3px;
                  text-align:right; }
.msg-time.theirs{ text-align:left; }
.msg-compose    { padding:12px 16px; border-top:1px solid var(--border);
                  display:flex; gap:10px; align-items:flex-end; }
.msg-compose textarea { flex:1; background:var(--surface); border:1px solid var(--border2);
                         border-radius:var(--radius-sm); padding:9px 13px; color:var(--text);
                         font-family:'DM Sans',sans-serif; font-size:14px; resize:none;
                         outline:none; min-height:42px; max-height:120px;
                         transition:var(--transition); }
.msg-compose textarea:focus { border-color:var(--accent); }
.msg-compose button { background:var(--accent); border:none; border-radius:50%;
                       width:40px; height:40px; color:#fff; font-size:18px;
                       cursor:pointer; flex-shrink:0; transition:var(--transition); }
.msg-compose button:hover { background:var(--accent2); color:#0e0c0a; }
.convo-item { display:flex; align-items:center; gap:10px; padding:12px 14px;
              cursor:pointer; transition:var(--transition); border-bottom:1px solid var(--border); }
.convo-item:hover, .convo-item.active { background:var(--surface); }
.convo-name   { font-size:14px; font-weight:600; }
.convo-preview{ font-size:12px; color:var(--text3); white-space:nowrap;
                overflow:hidden; text-overflow:ellipsis; max-width:140px; }
.convo-time   { font-size:11px; color:var(--text3); margin-left:auto; white-space:nowrap; }
.convo-avatar { width:38px; height:38px; border-radius:50%; flex-shrink:0;
                background:linear-gradient(135deg,var(--accent),#7a4820);
                display:flex; align-items:center; justify-content:center;
                font-size:13px; font-weight:600; }
.empty-panel  { flex:1; display:flex; align-items:center; justify-content:center;
                color:var(--text3); font-size:15px; flex-direction:column; gap:10px; }
</style>

<div class="msg-layout">

  <!-- Conversation list -->
  <div class="msg-sidebar">
    <div style="padding:14px 16px;font-size:15px;font-weight:700;border-bottom:1px solid var(--border);
                display:flex;align-items:center;justify-content:space-between;">
      Messages
      <button class="btn btn-outline btn-sm" onclick="showToast('✉️','New message — search coming soon')">+ New</button>
    </div>

    <?php if (!$conversations): ?>
      <div style="padding:30px;text-align:center;color:var(--text3);font-size:13px;">
        No conversations yet.<br>Visit an artist's profile to message them.
      </div>
    <?php endif; ?>

    <?php foreach ($conversations as $c): ?>
    <a class="convo-item <?= ($active_user && $active_user['id']==$c['id']) ? 'active' : '' ?>"
       href="<?= SITE_URL ?>/pages/messages.php?to=<?= h($c['username']) ?>">
      <div class="convo-avatar">
        <?php if ($c['avatar_path']): ?>
          <img src="<?= h(UPLOAD_URL.$c['avatar_path']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
        <?php else: ?>
          <?= strtoupper(substr($c['display_name'],0,2)) ?>
        <?php endif; ?>
      </div>
      <div style="flex:1;min-width:0;">
        <div style="display:flex;align-items:center;gap:6px;">
          <div class="convo-name"><?= h($c['display_name']) ?></div>
          <?php if ($c['unread_count'] > 0): ?>
            <span class="badge"><?= $c['unread_count'] ?></span>
          <?php endif; ?>
        </div>
        <div class="convo-preview"><?= h($c['last_msg'] ?? '') ?></div>
      </div>
      <div class="convo-time"><?= time_ago($c['last_time']) ?></div>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Message thread -->
  <div class="msg-panel">
    <?php if ($active_user): ?>
      <div class="msg-panel-head">
        <div class="convo-avatar" style="width:36px;height:36px;font-size:12px;">
          <?= strtoupper(substr($active_user['display_name'],0,2)) ?>
        </div>
        <div>
          <div style="font-size:14px;font-weight:700;"><?= h($active_user['display_name']) ?></div>
          <a href="<?= SITE_URL ?>/pages/profile.php?u=<?= h($active_user['username']) ?>"
             style="font-size:12px;color:var(--text3);">@<?= h($active_user['username']) ?></a>
        </div>
      </div>

      <div class="msg-thread" id="msgThread">
        <?php if (!$thread): ?>
          <div style="text-align:center;color:var(--text3);font-size:14px;margin-top:40px;">
            Start the conversation! 👋
          </div>
        <?php endif; ?>
        <?php foreach ($thread as $msg): ?>
          <?php $mine = ($msg['sender_id'] == $uid); ?>
          <div>
            <div class="msg-bubble <?= $mine?'mine':'theirs' ?>">
              <?= nl2br(h($msg['body'])) ?>
            </div>
            <div class="msg-time <?= $mine?'':'theirs' ?>"><?= time_ago($msg['created_at']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <form class="msg-compose" method="POST">
        <textarea name="body" placeholder="Write a message…" rows="1"
          onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.form.submit();}"></textarea>
        <button type="submit">➤</button>
      </form>

    <?php else: ?>
      <div class="empty-panel">
        <div style="font-size:40px;">💬</div>
        <div>Select a conversation or visit an artist's profile to message them.</div>
      </div>
    <?php endif; ?>
  </div>

</div>

<script>
// Scroll to bottom of thread
const t = document.getElementById('msgThread');
if (t) t.scrollTop = t.scrollHeight;
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
