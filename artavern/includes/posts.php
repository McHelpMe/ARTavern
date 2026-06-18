<?php
// ── includes/posts.php ───────────────────────────────────
require_once __DIR__ . '/config.php';

// ─── FEED ─────────────────────────────────────────────────
function get_feed(int $page = 1, int $per = 20, ?int $user_id = null, string $mode = 'all'): array {
    $offset = ($page - 1) * $per;
    $pdo = db();

    if ($mode === 'following' && $user_id) {
        $sql = 'SELECT p.*, u.display_name, u.username, u.avatar_path
                FROM posts p
                JOIN users u ON u.id = p.user_id
                JOIN follows f ON f.following_id = p.user_id AND f.follower_id = ?
                WHERE p.is_deleted = 0
                ORDER BY p.created_at DESC LIMIT ? OFFSET ?';
        $st = $pdo->prepare($sql);
        $st->execute([$user_id, $per, $offset]);
    } else {
        $sql = 'SELECT p.*, u.display_name, u.username, u.avatar_path
                FROM posts p
                JOIN users u ON u.id = p.user_id
                WHERE p.is_deleted = 0
                ORDER BY p.created_at DESC LIMIT ? OFFSET ?';
        $st = $pdo->prepare($sql);
        $st->execute([$per, $offset]);
    }

    $posts = $st->fetchAll();
    return attach_tags_to_posts($pdo, $posts);
}

// ─── SINGLE POST ──────────────────────────────────────────
function get_post(int $id): ?array {
    $st = db()->prepare('SELECT p.*, u.display_name, u.username, u.avatar_path
        FROM posts p JOIN users u ON u.id=p.user_id
        WHERE p.id=? AND p.is_deleted=0');
    $st->execute([$id]);
    $post = $st->fetch();
    if (!$post) return null;
    $posts = attach_tags_to_posts(db(), [$post]);
    return $posts[0];
}

// ─── ATTACH TAGS ──────────────────────────────────────────
function attach_tags_to_posts(PDO $pdo, array $posts): array {
    if (!$posts) return [];
    $ids = array_column($posts, 'id');
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $st  = $pdo->prepare("SELECT pt.post_id, t.name, t.slug
                           FROM post_tags pt JOIN tags t ON t.id=pt.tag_id
                           WHERE pt.post_id IN ($in)");
    $st->execute($ids);
    $tagMap = [];
    foreach ($st->fetchAll() as $r) {
        $tagMap[$r['post_id']][] = $r;
    }
    foreach ($posts as &$p) {
        $p['tags'] = $tagMap[$p['id']] ?? [];
    }
    return $posts;
}

// ─── CREATE POST ──────────────────────────────────────────
function create_post(int $user_id, string $body, string $medium, string $source,
                     ?string $image_path, array $tag_names): int {
    $pdo = db();
    $st = $pdo->prepare('INSERT INTO posts (user_id,body,medium,source,image_path)
                         VALUES (?,?,?,?,?)');
    $st->execute([$user_id, $body, $medium, $source, $image_path]);
    $post_id = (int) $pdo->lastInsertId();

    // Resolve / create tags
    foreach ($tag_names as $raw) {
        $name = strtolower(trim($raw, " \t\n\r#"));
        if ($name === '') continue;
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $name);

        $st2 = $pdo->prepare('INSERT INTO tags (name,slug) VALUES (?,?)
                               ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id), post_count=post_count+1');
        $st2->execute([$name, $slug]);
        $tag_id = (int) $pdo->lastInsertId();

        $pdo->prepare('INSERT IGNORE INTO post_tags (post_id,tag_id) VALUES (?,?)')
            ->execute([$post_id, $tag_id]);
    }

    return $post_id;
}

// ─── LIKE / UNLIKE ────────────────────────────────────────
function toggle_like(int $user_id, int $post_id): array {
    $pdo = db();
    $st = $pdo->prepare('SELECT 1 FROM likes WHERE user_id=? AND post_id=?');
    $st->execute([$user_id, $post_id]);
    if ($st->fetch()) {
        $pdo->prepare('DELETE FROM likes WHERE user_id=? AND post_id=?')->execute([$user_id, $post_id]);
        $pdo->prepare('UPDATE posts SET like_count=GREATEST(0,like_count-1) WHERE id=?')->execute([$post_id]);
        return ['liked' => false];
    }
    $pdo->prepare('INSERT INTO likes (user_id,post_id) VALUES (?,?)')->execute([$user_id, $post_id]);
    $pdo->prepare('UPDATE posts SET like_count=like_count+1 WHERE id=?')->execute([$post_id]);
    return ['liked' => true];
}

// ─── BOOKMARK ─────────────────────────────────────────────
function toggle_bookmark(int $user_id, int $post_id): array {
    $pdo = db();
    $st = $pdo->prepare('SELECT 1 FROM bookmarks WHERE user_id=? AND post_id=?');
    $st->execute([$user_id, $post_id]);
    if ($st->fetch()) {
        $pdo->prepare('DELETE FROM bookmarks WHERE user_id=? AND post_id=?')->execute([$user_id, $post_id]);
        return ['bookmarked' => false];
    }
    $pdo->prepare('INSERT INTO bookmarks (user_id,post_id) VALUES (?,?)')->execute([$user_id, $post_id]);
    return ['bookmarked' => true];
}

// ─── COMMENTS ─────────────────────────────────────────────
function get_comments(int $post_id): array {
    $st = db()->prepare('SELECT c.*, u.display_name, u.username, u.avatar_path
        FROM comments c JOIN users u ON u.id=c.user_id
        WHERE c.post_id=? AND c.is_deleted=0 ORDER BY c.created_at ASC');
    $st->execute([$post_id]);
    return $st->fetchAll();
}

function add_comment(int $user_id, int $post_id, string $body, ?int $parent_id = null): int {
    $pdo = db();
    $pdo->prepare('INSERT INTO comments (post_id,user_id,body,parent_id) VALUES (?,?,?,?)')
        ->execute([$post_id, $user_id, $body, $parent_id]);
    $pdo->prepare('UPDATE posts SET comment_count=comment_count+1 WHERE id=?')->execute([$post_id]);
    return (int) $pdo->lastInsertId();
}

// ─── TRENDING TAGS ────────────────────────────────────────
function get_trending_tags(int $limit = 8): array {
    $st = db()->prepare('SELECT name, slug, post_count FROM tags ORDER BY post_count DESC LIMIT ?');
    $st->execute([$limit]);
    return $st->fetchAll();
}

// ─── TAG PAGE ─────────────────────────────────────────────
function get_posts_by_tag(string $slug, int $page = 1, int $per = 20): array {
    $offset = ($page - 1) * $per;
    $st = db()->prepare('SELECT p.*, u.display_name, u.username, u.avatar_path
        FROM posts p
        JOIN users u ON u.id=p.user_id
        JOIN post_tags pt ON pt.post_id=p.id
        JOIN tags t ON t.id=pt.tag_id
        WHERE t.slug=? AND p.is_deleted=0
        ORDER BY p.created_at DESC LIMIT ? OFFSET ?');
    $st->execute([$slug, $per, $offset]);
    $posts = $st->fetchAll();
    return attach_tags_to_posts(db(), $posts);
}

// ─── USER POSTS ───────────────────────────────────────────
function get_user_posts(int $user_id, int $page = 1, int $per = 20): array {
    $offset = ($page - 1) * $per;
    $st = db()->prepare('SELECT p.*, u.display_name, u.username, u.avatar_path
        FROM posts p JOIN users u ON u.id=p.user_id
        WHERE p.user_id=? AND p.is_deleted=0
        ORDER BY p.created_at DESC LIMIT ? OFFSET ?');
    $st->execute([$user_id, $per, $offset]);
    $posts = $st->fetchAll();
    return attach_tags_to_posts(db(), $posts);
}

// ─── TAG AUTOCOMPLETE (JSON endpoint) ────────────────────
function search_tags(string $q, int $limit = 8): array {
    $st = db()->prepare('SELECT name, slug, post_count FROM tags
                         WHERE name LIKE ? ORDER BY post_count DESC LIMIT ?');
    $st->execute(['%' . $q . '%', $limit]);
    return $st->fetchAll();
}
