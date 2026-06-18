<?php
// ── includes/config.php ──────────────────────────────────
// Copy this file to your server and fill in your credentials.

define('DB_HOST', 'localhost');
define('DB_NAME', 'artavern');
define('DB_USER', 'root');          // change in production
define('DB_PASS', '');              // change in production
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'ARTavern');
define('SITE_URL',  'http://localhost/artavern');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

// ── PDO singleton ─────────────────────────────────────────
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

// ── Session ───────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . SITE_URL . '/index.php?login=1');
        exit;
    }
}

// ── Helpers ───────────────────────────────────────────────
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function time_ago(string $datetime): string {
    $diff = time() - strtotime($datetime);
    return match(true) {
        $diff < 60     => 'just now',
        $diff < 3600   => floor($diff / 60) . 'm ago',
        $diff < 86400  => floor($diff / 3600) . 'h ago',
        $diff < 604800 => floor($diff / 86400) . 'd ago',
        default        => date('M j, Y', strtotime($datetime)),
    };
}

function medium_label(string $m): string {
    return match($m) {
        'digital'     => 'Digital Art',
        'traditional' => 'Traditional',
        'pixel_art'   => 'Pixel Art',
        '3d'          => '3D Art',
        'mixed'       => 'Mixed Media',
        default       => 'Other',
    };
}

function source_label(string $s): string {
    return $s === 'created_on_site' ? 'Made on ARTavern' : 'Uploaded';
}
