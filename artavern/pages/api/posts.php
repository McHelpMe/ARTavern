<?php
// ── pages/api/posts.php ──────────────────────────────────
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/posts.php';
header('Content-Type: application/json');

$user   = current_user();
$action = $_GET['action'] ?? '';
$data   = json_decode(file_get_contents('php://input'), true) ?? [];

if (!$user && in_array($action, ['create','like','bookmark'])) {
    echo json_encode(['ok'=>false,'error'=>'Login required']);
    exit;
}

switch ($action) {
    case 'create':
        $tags  = array_slice(array_filter(array_map('trim', $data['tags'] ?? [])), 0, 10);
        $image = null; // file uploads handled separately via multipart form
        $id    = create_post(
            $user['id'],
            trim($data['body'] ?? ''),
            $data['medium'] ?? 'digital',
            $data['source'] ?? 'uploaded',
            $image,
            $tags
        );
        echo json_encode(['ok'=>true,'post_id'=>$id]);
        break;

    case 'like':
        $res = toggle_like((int)$user['id'], (int)($data['post_id'] ?? 0));
        echo json_encode(['ok'=>true] + $res);
        break;

    case 'bookmark':
        $res = toggle_bookmark((int)$user['id'], (int)($data['post_id'] ?? 0));
        echo json_encode(['ok'=>true] + $res);
        break;

    case 'comment':
        $body = trim($data['body'] ?? '');
        if (!$body) { echo json_encode(['ok'=>false,'error'=>'Empty comment']); break; }
        $cid = add_comment((int)$user['id'], (int)$data['post_id'], $body, $data['parent_id'] ?? null);
        echo json_encode(['ok'=>true,'comment_id'=>$cid]);
        break;

    default:
        echo json_encode(['ok'=>false,'error'=>'Unknown action']);
}
