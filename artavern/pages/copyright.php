<?php
// ── pages/copyright.php ──────────────────────────────────
require_once __DIR__ . '/../includes/config.php';
$user    = current_user();
$__title = 'Copyright & Legal — ARTavern';
$__page  = '';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="section-header">⚔️ Copyright & Legal Support</div>

<div style="max-width:640px;padding:28px 24px;line-height:1.75;font-size:14px;color:var(--text2);">

  <div style="background:var(--accent-bg);border:1px solid var(--accent);border-radius:var(--radius);
              padding:16px;margin-bottom:24px;font-size:14px;color:var(--text);">
    🛡 ARTavern is committed to protecting your work. If your art has been used without permission —
    inside or outside the platform — we can help connect you with resources and support.
  </div>

  <h3 style="font-size:17px;font-weight:700;color:var(--text);margin-bottom:12px;">Your Rights as an Artist</h3>
  <p>Under copyright law, the moment you create an original artwork you own the copyright to it.
     You do not need to register it, though registration strengthens your legal standing if you need to sue.
     ARTavern operates on the principle that <strong style="color:var(--accent2);">art belongs to the artist</strong>.</p>

  <h3 style="font-size:17px;font-weight:700;color:var(--text);margin:20px 0 10px;">What ARTavern Does</h3>
  <ul style="display:flex;flex-direction:column;gap:8px;padding-left:18px;">
    <li>Enforces a strict no-AI-generated-art policy across all posts and portfolios.</li>
    <li>Connects members with verified copyright agents and IP lawyers on request.</li>
    <li>Accepts and processes DMCA takedown notices against infringing content.</li>
    <li>Maintains an internal abuse-reporting system for IP violations.</li>
  </ul>

  <h3 style="font-size:17px;font-weight:700;color:var(--text);margin:20px 0 10px;">How to Report Infringement</h3>
  <p style="margin-bottom:10px;">Found your work being used without permission? Here's what to do:</p>
  <ol style="display:flex;flex-direction:column;gap:8px;padding-left:18px;">
    <li>Use the <strong>⋯ Report</strong> button on any post to flag IP violations directly.</li>
    <li>For formal DMCA takedowns, email <strong style="color:var(--accent2);">legal@artavern.art</strong> with proof of ownership.</li>
    <li>Need legal counsel? Use the form below to be connected with our network of IP lawyers.</li>
  </ol>

  <h3 style="font-size:17px;font-weight:700;color:var(--text);margin:20px 0 10px;">Request Legal Support</h3>
  <?php if ($user): ?>
  <form onsubmit="showToast('⚔️','Request submitted — we'll be in touch within 48h.');return false;"
        style="display:flex;flex-direction:column;gap:12px;">
    <div class="form-group">
      <label class="form-label">Describe the infringement</label>
      <textarea class="form-input" rows="4" placeholder="Where did you find your art? Any links or screenshots?" style="resize:vertical;"></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">Type of help needed</label>
      <select class="form-input meta-select" style="border-radius:var(--radius-sm);width:100%;">
        <option>DMCA Takedown</option>
        <option>Legal Consultation</option>
        <option>Platform Abuse Report</option>
        <option>Other</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary" style="align-self:flex-start;">Submit Request</button>
  </form>
  <?php else: ?>
  <div style="background:var(--surface);border-radius:var(--radius);padding:16px;text-align:center;">
    <p style="margin-bottom:12px;">Sign in to submit a legal support request.</p>
    <button class="btn btn-primary" onclick="openModal('login')">Sign in</button>
  </div>
  <?php endif; ?>

  <div style="margin-top:32px;padding-top:20px;border-top:1px solid var(--border);font-size:13px;color:var(--text3);">
    ARTavern provides facilitation services only and is not a law firm. Information here is not legal advice.
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
