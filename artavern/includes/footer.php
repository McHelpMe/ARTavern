<?php
// ── includes/footer.php ──────────────────────────────────
require_once __DIR__ . '/config.php';
$__user = current_user();
?>
</main><!-- /main-content -->

<!-- RIGHT SIDEBAR injected by page if needed -->
<?php if (!empty($__right_sidebar)): ?>
  <aside class="right-sidebar">
    <?= $__right_sidebar ?>
  </aside>
<?php endif; ?>

</div><!-- /app-layout -->

<!-- ─── LOGIN MODAL ─── -->
<div class="modal-overlay" id="loginModal">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('login')">&#10005;</button>
    <div class="modal-logo">
      <div class="modal-logo-icon">🍺</div>
      <span class="modal-logo-name">ARTavern</span>
    </div>
    <h2>Welcome back</h2>
    <p>Sign in to post, collaborate, and protect your work.</p>
    <form id="loginForm">
      <div class="form-group">
        <label class="form-label">Email or username</label>
        <input class="form-input" type="text" name="identifier" placeholder="you@example.com" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input class="form-input" type="password" name="password" placeholder="••••••••" required>
      </div>
      <div class="form-error" id="loginError"></div>
      <button type="submit" class="form-submit">Sign in</button>
    </form>
    <div class="modal-switch">
      Don't have an account? <a onclick="switchModal('login','register')">Join ARTavern →</a>
    </div>
  </div>
</div>

<!-- ─── REGISTER MODAL ─── -->
<div class="modal-overlay" id="registerModal">
  <div class="modal modal-tall">
    <button class="modal-close" onclick="closeModal('register')">&#10005;</button>
    <div class="modal-logo">
      <div class="modal-logo-icon">🍺</div>
      <span class="modal-logo-name">ARTavern</span>
    </div>
    <h2>Join ARTavern</h2>
    <p>A gathering place for human artists.</p>
    <form id="registerForm">
      <div class="form-group">
        <label class="form-label">Display name</label>
        <input class="form-input" type="text" name="display_name" placeholder="Your artist name" required>
      </div>
      <div class="form-group">
        <label class="form-label">Username</label>
        <input class="form-input" type="text" name="username" placeholder="handle (letters, numbers, _ .)" required>
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input class="form-input" type="email" name="email" placeholder="you@example.com" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input class="form-input" type="password" name="password" placeholder="At least 8 characters" required>
      </div>
      <div class="form-group">
        <label class="form-label">Art style / specialty <span style="font-weight:400;color:var(--text3);">(optional)</span></label>
        <input class="form-input" type="text" name="art_styles" placeholder="e.g. Digital, Watercolor, Pixel Art…">
      </div>

      <!-- User Agreement -->
      <button type="button" class="agreement-toggle" onclick="toggleAgreement()">
        <span id="agrIcon">&#9654;</span> Read User Agreement
      </button>
      <div class="agreement-panel" id="agreementPanel">
        <h4>ARTavern User Agreement</h4>
        <p>By registering you agree to these terms governing your use of ARTavern, an online platform built for and by human artists.</p>
        <h4>1. Who We Are</h4>
        <p>ARTavern is an online platform dedicated to human artists. It provides tools to create, share, collaborate, and protect artwork. Our core principle: <em>art belongs to the artist</em>.</p>
        <h4>2. Eligibility</h4>
        <p>You must be at least 13 years of age to register. Users under 18 require parental consent.</p>
        <h4>3. Human-Made Art Only</h4>
        <p>ARTavern enforces a strict no-AI-generated art policy. All work posted must be created by a human artist. Violations result in removal and may lead to suspension.</p>
        <h4>4. Your Content & Ownership</h4>
        <p>You retain full ownership of all artwork you post. You grant ARTavern a limited, non-exclusive license to display and distribute your work within the platform only.</p>
        <h4>5. Collaboration & Community</h4>
        <p>ARTavern offers tutoring, shared canvases, and matchmaking. You agree to treat fellow artists with respect and not misuse collaborative features.</p>
        <h4>6. Commissions & References</h4>
        <p>ARTavern facilitates commission requests and reference model access. ARTavern is not a party to commission contracts.</p>
        <h4>7. Copyright & Legal Support</h4>
        <p>ARTavern connects members with legal support and copyright agents. We facilitate but do not directly provide legal advice.</p>
        <h4>8. Prohibited Conduct</h4>
        <p>You agree not to post AI-generated artwork, infringe IP rights, harass other artists, scrape content, or use the platform for unlawful purposes.</p>
        <h4>9. Account Termination</h4>
        <p>ARTavern may suspend or terminate accounts that violate this Agreement.</p>
        <h4>10. Changes</h4>
        <p>We may update these terms. Continued use constitutes acceptance. Material changes will be notified via email.</p>
      </div>

      <div class="form-check">
        <input type="checkbox" id="agreeCheck" name="agreed_tos" required>
        <label for="agreeCheck">
          I have read and agree to the <a href="javascript:toggleAgreement()">ARTavern User Agreement</a>, including the no-AI-art policy.
        </label>
      </div>
      <div class="form-check">
        <input type="checkbox" id="ageCheck" required>
        <label for="ageCheck">I confirm I am at least 13 years old (or have parental consent).</label>
      </div>

      <div class="form-error" id="registerError"></div>
      <button type="submit" class="form-submit">Create my account</button>
    </form>
    <div class="modal-switch">
      Already have an account? <a onclick="switchModal('register','login')">Sign in →</a>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast-container" id="toastContainer"></div>

<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<?php if (!empty($__extra_js)) echo $__extra_js; ?>
</body>
</html>
