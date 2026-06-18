<?php
// ── pages/api/profile.php ────────────────────────────────
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/profile.php';
header('Content-Type: application/json');

$user   = current_user();
$action = $_GET['action'] ?? '';
$data   = json_decode(file_get_contents('php://input'), true) ?? [];

if (!$user) { echo json_encode(['ok'=>false,'error'=>'Login required']); exit; }

switch ($action) {
    case 'follow':
        $res = toggle_follow((int)$user['id'], (int)($data['target_id'] ?? 0));
        echo json_encode(['ok'=>true] + $res);
        break;
    default:
        echo json_encode(['ok'=>false,'error'=>'Unknown action']);
}
