<?php
// ── pages/api/auth.php ───────────────────────────────────
require_once __DIR__ . '/../../includes/auth.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok'=>false,'errors'=>['Method not allowed']]);
    exit;
}

$data   = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $_GET['action'] ?? '';

match($action) {
    'login'    => respond(login_user($data['identifier'] ?? '', $data['password'] ?? '')),
    'register' => respond(register_user($data)),
    default    => respond(['ok'=>false,'errors'=>['Unknown action']])
};

function respond(array $r): void {
    // Remove password from response just in case
    if (isset($r['user']['password_hash'])) unset($r['user']['password_hash']);
    echo json_encode($r);
    exit;
}
