// ── assets/js/main.js ───────────────────────────────────
const SITE_URL = document.currentScript?.dataset.siteurl ||
                 window.location.origin + '/artavern';

/* ── Modals ─────────────────────────────────────────────── */
function openModal(type) {
  document.getElementById(type + 'Modal').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeModal(type) {
  document.getElementById(type + 'Modal').classList.remove('open');
  document.body.style.overflow = '';
}
function switchModal(from, to) { closeModal(from); openModal(to); }

document.querySelectorAll('.modal-overlay').forEach(o => {
  o.addEventListener('click', e => {
    if (e.target === o) closeModal(o.id.replace('Modal',''));
  });
});

/* Open login on page load if ?login=1 */
if (new URLSearchParams(location.search).get('login') === '1') openModal('login');

/* ── Dropdown ────────────────────────────────────────────── */
function toggleDropdown() {
  document.getElementById('profileDropdown')?.classList.toggle('open');
}
document.addEventListener('click', e => {
  const m = document.getElementById('profileMenu');
  if (m && !m.contains(e.target))
    document.getElementById('profileDropdown')?.classList.remove('open');
});

/* ── Agreement ───────────────────────────────────────────── */
function toggleAgreement() {
  const p = document.getElementById('agreementPanel');
  const i = document.getElementById('agrIcon');
  if (!p) return;
  p.classList.toggle('visible');
  if (i) i.textContent = p.classList.contains('visible') ? '▼' : '▶';
}

/* ── Toast ───────────────────────────────────────────────── */
function showToast(icon, msg, duration = 3200) {
  const c = document.getElementById('toastContainer');
  if (!c) return;
  const t = document.createElement('div');
  t.className = 'toast';
  t.innerHTML = `<span>${icon}</span>${msg}`;
  c.appendChild(t);
  setTimeout(() => t.remove(), duration);
}

function requireLogin(feature) {
  showToast('🔒', (feature || 'This feature') + ' requires an account');
  setTimeout(() => openModal('login'), 600);
}

/* ── AUTH AJAX ───────────────────────────────────────────── */
async function postForm(url, data) {
  const resp = await fetch(url, {
    method: 'POST',
    headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
    body: JSON.stringify(data)
  });
  return resp.json();
}

// Login form
document.getElementById('loginForm')?.addEventListener('submit', async e => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const err = document.getElementById('loginError');
  err.textContent = '';
  const res = await postForm(SITE_URL + '/pages/api/auth.php?action=login', {
    identifier: fd.get('identifier'),
    password:   fd.get('password')
  });
  if (res.ok) { location.reload(); }
  else { err.textContent = res.errors?.join(' ') || 'Login failed.'; }
});

// Register form
document.getElementById('registerForm')?.addEventListener('submit', async e => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const err = document.getElementById('registerError');
  err.textContent = '';
  const res = await postForm(SITE_URL + '/pages/api/auth.php?action=register', {
    display_name: fd.get('display_name'),
    username:     fd.get('username'),
    email:        fd.get('email'),
    password:     fd.get('password'),
    art_styles:   fd.get('art_styles') || '',
    agreed_tos:   fd.get('agreed_tos') ? '1' : ''
  });
  if (res.ok) { location.reload(); }
  else { err.textContent = res.errors?.join(' ') || 'Registration failed.'; }
});

/* ── TAG INPUT (composer) ────────────────────────────────── */
let composerTags = [];

function initTagInput() {
  const wrap  = document.getElementById('tagInputWrap');
  const input = document.getElementById('tagTypeInput');
  const ac    = document.getElementById('tagAutocomplete');
  if (!wrap || !input) return;

  input.addEventListener('input', async () => {
    const q = input.value.replace(/^#/, '').trim();
    ac.innerHTML = '';
    if (q.length < 1) { ac.style.display='none'; return; }
    const res = await fetch(SITE_URL + `/pages/api/tags.php?q=${encodeURIComponent(q)}`);
    const tags = await res.json();
    if (!tags.length) { ac.style.display='none'; return; }
    ac.style.display = 'block';
    tags.forEach(t => {
      const d = document.createElement('div');
      d.className = 'tag-autocomplete-item';
      d.innerHTML = `#${t.name}<span class="tag-autocomplete-count">${t.post_count} posts</span>`;
      d.onclick = () => { addTag(t.name); input.value=''; ac.style.display='none'; };
      ac.appendChild(d);
    });
  });

  input.addEventListener('keydown', e => {
    if (['Enter','Space',','].includes(e.key) || e.key === ' ') {
      e.preventDefault();
      const val = input.value.replace(/^#/,'').trim().replace(/,$/,'');
      if (val) { addTag(val); input.value=''; ac.style.display='none'; }
    }
    if (e.key === 'Backspace' && input.value === '' && composerTags.length) {
      removeTag(composerTags[composerTags.length - 1]);
    }
  });

  document.addEventListener('click', e => {
    if (!wrap.contains(e.target)) ac.style.display = 'none';
  });
}

function addTag(name) {
  name = name.toLowerCase().trim();
  if (!name || composerTags.includes(name) || composerTags.length >= 10) return;
  composerTags.push(name);
  renderTags();
}

function removeTag(name) {
  composerTags = composerTags.filter(t => t !== name);
  renderTags();
}

function renderTags() {
  const wrap  = document.getElementById('tagInputWrap');
  const input = document.getElementById('tagTypeInput');
  if (!wrap) return;
  // Remove existing pills
  wrap.querySelectorAll('.tag-pill-input').forEach(el => el.remove());
  composerTags.forEach(name => {
    const pill = document.createElement('span');
    pill.className = 'tag-pill-input';
    pill.innerHTML = `#${name}<span class="remove-tag" onclick="removeTag('${name}')">✕</span>`;
    wrap.insertBefore(pill, input);
  });
}

/* ── COMPOSER EXPAND ────────────────────────────────────── */
function expandComposer(el) {
  el.rows = 3;
  document.getElementById('composerActionsRow').style.display = 'flex';
  document.getElementById('composerMeta').style.display       = 'flex';
  document.getElementById('tagRow').style.display             = 'flex';
}

/* ── SUBMIT POST ─────────────────────────────────────────── */
async function submitPost() {
  const body   = document.getElementById('composerTextarea')?.value.trim();
  const medium = document.getElementById('composerMedium')?.value;
  const source = document.getElementById('composerSource')?.value;
  if (!body && composerTags.length === 0) {
    showToast('⚠️', 'Write something or add tags first!');
    return;
  }
  const res = await postForm(SITE_URL + '/pages/api/posts.php?action=create', {
    body, medium, source, tags: composerTags
  });
  if (res.ok) {
    showToast('✅', 'Post published!');
    composerTags = [];
    renderTags();
    document.getElementById('composerTextarea').value = '';
    document.getElementById('composerTextarea').rows  = 2;
    document.getElementById('composerActionsRow').style.display = 'none';
    document.getElementById('composerMeta').style.display       = 'none';
    document.getElementById('tagRow').style.display             = 'none';
    // Prepend new post card (simple reload for now)
    setTimeout(() => location.reload(), 500);
  } else {
    showToast('❌', res.error || 'Could not post.');
  }
}

/* ── LIKE ────────────────────────────────────────────────── */
async function toggleLike(btn, postId) {
  const res = await postForm(SITE_URL + '/pages/api/posts.php?action=like', { post_id: postId });
  if (!res.ok) { requireLogin('Liking posts'); return; }
  const liked = res.liked;
  const count = parseInt(btn.dataset.count || '0') + (liked ? 1 : -1);
  btn.dataset.count = count;
  btn.classList.toggle('liked', liked);
  btn.innerHTML = (liked ? '❤️' : '🤍') + ' ' + count;
}

/* ── BOOKMARK ────────────────────────────────────────────── */
async function toggleBookmark(btn, postId) {
  const res = await postForm(SITE_URL + '/pages/api/posts.php?action=bookmark', { post_id: postId });
  if (!res.ok) { requireLogin('Bookmarks'); return; }
  btn.classList.toggle('bookmarked', res.bookmarked);
  showToast(res.bookmarked ? '🔖' : '📌', res.bookmarked ? 'Saved to bookmarks' : 'Removed from bookmarks');
}

/* ── FOLLOW ──────────────────────────────────────────────── */
async function toggleFollow(btn, userId) {
  const res = await postForm(SITE_URL + '/pages/api/profile.php?action=follow', { target_id: userId });
  if (!res.ok) { requireLogin('Following artists'); return; }
  btn.classList.toggle('following', res.following);
  btn.textContent = res.following ? 'Following' : 'Follow';
  showToast(res.following ? '✨' : '👋', res.following ? 'Now following!' : 'Unfollowed');
}

/* ── Feed tabs ───────────────────────────────────────────── */
function setFeedTab(el, tab) {
  document.querySelectorAll('.feed-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  if (tab === 'Following') {
    const loggedIn = !!document.getElementById('profileMenu');
    if (!loggedIn) { openModal('login'); return; }
  }
  // In real implementation, AJAX-load the correct feed
  const url = new URL(location.href);
  url.searchParams.set('tab', tab.toLowerCase());
  history.pushState({}, '', url);
}

/* ── Init ────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  initTagInput();
});

/* ── guestNav ────────────────────────────────────────────── */
// Used by elements with data-url that need login guard
function guestNav(el) {
  const loggedIn = !!document.getElementById('profileMenu');
  const url = el.dataset.url;
  if (loggedIn && url) {
    location.href = url;
  } else {
    requireLogin('This feature');
  }
}
