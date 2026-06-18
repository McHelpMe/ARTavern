-- ============================================================
--  ARTavern Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS artavern CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE artavern;

-- ─── USERS ────────────────────────────────────────────────
CREATE TABLE users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    display_name  VARCHAR(80)  NOT NULL,
    username      VARCHAR(40)  NOT NULL UNIQUE,
    email         VARCHAR(180) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    bio           TEXT,
    avatar_path   VARCHAR(255),
    banner_path   VARCHAR(255),
    art_styles    VARCHAR(255),          -- comma-separated e.g. "Digital,Watercolor"
    commission_open TINYINT(1) DEFAULT 0,
    role          ENUM('user','moderator','admin') DEFAULT 'user',
    agreed_tos    TINYINT(1) DEFAULT 0,
    is_verified   TINYINT(1) DEFAULT 0,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ─── FOLLOWS ──────────────────────────────────────────────
CREATE TABLE follows (
    follower_id INT UNSIGNED NOT NULL,
    following_id INT UNSIGNED NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (follower_id, following_id),
    FOREIGN KEY (follower_id)  REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ─── TAGS ─────────────────────────────────────────────────
CREATE TABLE tags (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(80)  NOT NULL UNIQUE,   -- stored lowercase, no #
    slug       VARCHAR(80)  NOT NULL UNIQUE,
    post_count INT UNSIGNED DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ─── POSTS ────────────────────────────────────────────────
CREATE TABLE posts (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED NOT NULL,
    body          TEXT,
    -- Medium / Source
    medium        ENUM('digital','traditional','pixel_art','3d','mixed','other') DEFAULT 'digital',
    source        ENUM('created_on_site','uploaded') DEFAULT 'uploaded',
    -- Optional image
    image_path    VARCHAR(255),
    -- Counts (denormalized for speed)
    like_count    INT UNSIGNED DEFAULT 0,
    comment_count INT UNSIGNED DEFAULT 0,
    repost_count  INT UNSIGNED DEFAULT 0,
    is_deleted    TINYINT(1) DEFAULT 0,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ─── POST ↔ TAG ────────────────────────────────────────────
CREATE TABLE post_tags (
    post_id INT UNSIGNED NOT NULL,
    tag_id  INT UNSIGNED NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id)  REFERENCES tags(id)  ON DELETE CASCADE
);

-- ─── LIKES ────────────────────────────────────────────────
CREATE TABLE likes (
    user_id    INT UNSIGNED NOT NULL,
    post_id    INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- ─── COMMENTS ─────────────────────────────────────────────
CREATE TABLE comments (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id    INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL,
    parent_id  INT UNSIGNED DEFAULT NULL,   -- for nested replies
    body       TEXT NOT NULL,
    is_deleted TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id)   REFERENCES posts(id)    ON DELETE CASCADE,
    FOREIGN KEY (user_id)   REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE SET NULL
);

-- ─── REPOSTS ──────────────────────────────────────────────
CREATE TABLE reposts (
    user_id    INT UNSIGNED NOT NULL,
    post_id    INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- ─── BOOKMARKS ────────────────────────────────────────────
CREATE TABLE bookmarks (
    user_id    INT UNSIGNED NOT NULL,
    post_id    INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- ─── PORTFOLIO ────────────────────────────────────────────
CREATE TABLE portfolio_items (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    title       VARCHAR(160) NOT NULL,
    description TEXT,
    image_path  VARCHAR(255) NOT NULL,
    medium      ENUM('digital','traditional','pixel_art','3d','mixed','other') DEFAULT 'digital',
    source      ENUM('created_on_site','uploaded') DEFAULT 'uploaded',
    is_featured TINYINT(1) DEFAULT 0,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE portfolio_item_tags (
    item_id INT UNSIGNED NOT NULL,
    tag_id  INT UNSIGNED NOT NULL,
    PRIMARY KEY (item_id, tag_id),
    FOREIGN KEY (item_id) REFERENCES portfolio_items(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id)  REFERENCES tags(id)            ON DELETE CASCADE
);

-- ─── COLLABORATIONS ───────────────────────────────────────
CREATE TABLE collaborations (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    host_id     INT UNSIGNED NOT NULL,
    title       VARCHAR(160) NOT NULL,
    description TEXT,
    type        ENUM('tutoring','shared_canvas','matchmaking') DEFAULT 'shared_canvas',
    status      ENUM('open','in_progress','closed') DEFAULT 'open',
    max_members TINYINT UNSIGNED DEFAULT 2,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (host_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE collaboration_members (
    collab_id  INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL,
    joined_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (collab_id, user_id),
    FOREIGN KEY (collab_id) REFERENCES collaborations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)   REFERENCES users(id)          ON DELETE CASCADE
);

-- ─── COMMISSIONS ──────────────────────────────────────────
CREATE TABLE commissions (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    artist_id    INT UNSIGNED NOT NULL,
    client_id    INT UNSIGNED NOT NULL,
    title        VARCHAR(160) NOT NULL,
    description  TEXT,
    price        DECIMAL(10,2),
    status       ENUM('pending','accepted','in_progress','completed','cancelled') DEFAULT 'pending',
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (artist_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ─── MESSAGES ─────────────────────────────────────────────
CREATE TABLE messages (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id   INT UNSIGNED NOT NULL,
    receiver_id INT UNSIGNED NOT NULL,
    body        TEXT NOT NULL,
    is_read     TINYINT(1) DEFAULT 0,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id)   REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ─── NOTIFICATIONS ────────────────────────────────────────
CREATE TABLE notifications (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    type       ENUM('like','comment','follow','repost','commission','collab','mention') NOT NULL,
    ref_id     INT UNSIGNED,      -- id of the related post/comment/user
    ref_type   VARCHAR(40),       -- 'post','comment','user', etc.
    is_read    TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ─── REFERENCE MODELS ─────────────────────────────────────
CREATE TABLE reference_models (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uploader_id INT UNSIGNED NOT NULL,
    title       VARCHAR(160) NOT NULL,
    description TEXT,
    file_path   VARCHAR(255) NOT NULL,
    type        ENUM('3d_model','photo_ref','pose_pack','other') DEFAULT 'photo_ref',
    is_free     TINYINT(1) DEFAULT 1,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploader_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ─── SEED: default tags ───────────────────────────────────
INSERT INTO tags (name, slug) VALUES
('fantasy',      'fantasy'),
('sci-fi',       'sci-fi'),
('modern',       'modern'),
('horror',       'horror'),
('romance',      'romance'),
('nature',       'nature'),
('portrait',     'portrait'),
('character',    'character'),
('concept',      'concept'),
('fanart',       'fanart'),
('oc',           'oc'),
('landscape',    'landscape'),
('abstract',     'abstract'),
('chibi',        'chibi'),
('animation',    'animation');
