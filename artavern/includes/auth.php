<?php
// ── includes/auth.php ────────────────────────────────────
require_once __DIR__ . '/config.php';

// ── REGISTER ─────────────────────────────────────────────
function register_user(array $data): array {
    $errors = [];

    $name     = trim($data['display_name'] ?? '');
    $username = strtolower(trim($data['username'] ?? ''));
    $email    = trim($data['email'] ?? '');
    $pass     = $data['password'] ?? '';
    $style    = trim($data['art_styles'] ?? '');
    $agreed   = !empty($data['agreed_tos']);

    if (strlen($name) < 2)              $errors[] = 'Display name must be at least 2 characters.';
    if (!preg_match('/^[a-z0-9_.]{3,40}$/', $username))
                                         $errors[] = 'Username: 3–40 chars, letters/numbers/_.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
    if (strlen($pass) < 8)              $errors[] = 'Password must be at least 8 characters.';
    if (!$agreed)                        $errors[] = 'You must accept the User Agreement.';

    if ($errors) return ['ok' => false, 'errors' => $errors];

    $pdo = db();

    // Check uniqueness
    $st = $pdo->prepare('SELECT id FROM users WHERE username=? OR email=? LIMIT 1');
    $st->execute([$username, $email]);
    if ($st->fetch()) {
        $errors[] = 'Username or email is already taken.';
        return ['ok' => false, 'errors' => $errors];
    }

    $hash = password_hash($pass, PASSWORD_BCRYPT);
    $st = $pdo->prepare('INSERT INTO users
        (display_name, username, email, password_hash, art_styles, agreed_tos)
        VALUES (?,?,?,?,?,1)');
    $st->execute([$name, $username, $email, $hash, $style]);

    $uid = (int) $pdo->lastInsertId();
    $user = fetch_user($uid);
    $_SESSION['user'] = $user;
    return ['ok' => true, 'user' => $user];
}

// ── LOGIN ─────────────────────────────────────────────────
function login_user(string $identifier, string $pass): array {
    $pdo = db();
    $field = str_contains($identifier, '@') ? 'email' : 'username';
    $st = $pdo->prepare("SELECT * FROM users WHERE {$field}=? LIMIT 1");
    $st->execute([$identifier]);
    $user = $st->fetch();

    if (!$user || !password_verify($pass, $user['password_hash'])) {
        return ['ok' => false, 'errors' => ['Invalid credentials.']];
    }

    unset($user['password_hash']);
    $_SESSION['user'] = $user;
    return ['ok' => true, 'user' => $user];
}

// ── LOGOUT ───────────────────────────────────────────────
function logout_user(): void {
    $_SESSION = [];
    session_destroy();
}

// ── FETCH ─────────────────────────────────────────────────
function fetch_user(int $id): ?array {
    $st = db()->prepare('SELECT id,display_name,username,email,bio,avatar_path,
        banner_path,art_styles,commission_open,role,created_at FROM users WHERE id=?');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}
