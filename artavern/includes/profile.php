<?php
// ── includes/profile.php ────────────────────────────────
require_once __DIR__ . '/config.php';

function get_profile(string $username): ?array {
    $st = db()->prepare('SELECT id,display_name,username,bio,avatar_path,banner_path,
        art_styles,commission_open,role,created_at FROM users WHERE username=?');
    $st->execute([$username]);
    return $st->fetch() ?: null;
}

function get_follower_count(int $user_id): int {
    $st = db()->prepare('SELECT COUNT(*) FROM follows WHERE following_id=?');
    $st->execute([$user_id]);
    return (int) $st->fetchColumn();
}

function get_following_count(int $user_id): int {
    $st = db()->prepare('SELECT COUNT(*) FROM follows WHERE follower_id=?');
    $st->execute([$user_id]);
    return (int) $st->fetchColumn();
}

function is_following(int $follower_id, int $following_id): bool {
    $st = db()->prepare('SELECT 1 FROM follows WHERE follower_id=? AND following_id=?');
    $st->execute([$follower_id, $following_id]);
    return (bool) $st->fetch();
}

function toggle_follow(int $follower_id, int $following_id): array {
    if ($follower_id === $following_id) return ['following' => false];
    $pdo = db();
    if (is_following($follower_id, $following_id)) {
        $pdo->prepare('DELETE FROM follows WHERE follower_id=? AND following_id=?')
            ->execute([$follower_id, $following_id]);
        return ['following' => false];
    }
    $pdo->prepare('INSERT INTO follows (follower_id,following_id) VALUES (?,?)')
        ->execute([$follower_id, $following_id]);
    return ['following' => true];
}

function get_portfolio(int $user_id): array {
    $st = db()->prepare('SELECT * FROM portfolio_items WHERE user_id=? ORDER BY is_featured DESC, created_at DESC');
    $st->execute([$user_id]);
    return $st->fetchAll();
}

function add_portfolio_item(int $user_id, string $title, string $desc,
                            string $image_path, string $medium, string $source,
                            array $tag_names): int {
    $pdo = db();
    $pdo->prepare('INSERT INTO portfolio_items (user_id,title,description,image_path,medium,source)
                   VALUES (?,?,?,?,?,?)')->execute([$user_id,$title,$desc,$image_path,$medium,$source]);
    $item_id = (int) $pdo->lastInsertId();
    foreach ($tag_names as $raw) {
        $name = strtolower(trim($raw, " \t\n\r#"));
        if ($name === '') continue;
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $name);
        $pdo->prepare('INSERT INTO tags (name,slug) VALUES (?,?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)')
            ->execute([$name, $slug]);
        $tag_id = (int) $pdo->lastInsertId();
        $pdo->prepare('INSERT IGNORE INTO portfolio_item_tags (item_id,tag_id) VALUES (?,?)')
            ->execute([$item_id, $tag_id]);
    }
    return $item_id;
}

function update_profile(int $user_id, array $data): void {
    $fields = [];
    $params = [];
    $allowed = ['display_name','bio','art_styles','commission_open','avatar_path','banner_path'];
    foreach ($allowed as $f) {
        if (array_key_exists($f, $data)) {
            $fields[] = "$f=?";
            $params[] = $data[$f];
        }
    }
    if (!$fields) return;
    $params[] = $user_id;
    db()->prepare('UPDATE users SET ' . implode(',', $fields) . ' WHERE id=?')->execute($params);
}

function get_suggested_artists(int $exclude_id, int $limit = 5): array {
    $st = db()->prepare('SELECT u.id, u.display_name, u.username, u.avatar_path, u.art_styles,
        COUNT(f.follower_id) AS follower_count
        FROM users u
        LEFT JOIN follows f ON f.following_id = u.id
        WHERE u.id != ?
        GROUP BY u.id ORDER BY follower_count DESC LIMIT ?');
    $st->execute([$exclude_id, $limit]);
    return $st->fetchAll();
}
