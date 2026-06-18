<?php
// ── pages/api/tags.php ───────────────────────────────────
require_once __DIR__ . '/../../includes/posts.php';
header('Content-Type: application/json');
$q = trim($_GET['q'] ?? '');
echo json_encode($q ? search_tags($q) : []);
