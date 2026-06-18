-- ============================================================
--  ARTavern — Showcase Seed Data
--  Run AFTER artavern.sql
-- ============================================================
USE artavern;

-- ─── USERS ────────────────────────────────────────────────
-- All passwords: Password123
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO users (id, display_name, username, email, password_hash, bio, art_styles, commission_open, role, agreed_tos, is_verified) VALUES
(1,  'Reika Kurosawa',   'reika',        'reika@artavern.art',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fantasy & nature digital painter. I live for glowing skies and impossible forests.', 'Digital,Watercolor', 1, 'user', 1, 1),
(2,  'Marco Stelios',    'marcostelios', 'marco@artavern.art',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Traditional ink artist. Bristol board or bust. Character design enthusiast.', 'Traditional,Ink', 1, 'user', 1, 1),
(3,  'Yuki Nakano',      'yukiink',      'yuki@artavern.art',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Illustrator working on my first indie comic. Lover of warm palettes and soft linework.', 'Digital,Illustration', 0, 'user', 1, 1),
(4,  'Amara Lowe',       'amaraart',     'amara@artavern.art',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sci-fi concept artist by day, pixel hobbyist by night. Open for commissions!', 'Concept Art,Pixel Art', 1, 'user', 1, 1),
(5,  'Diego Ferreira',   'diegoferreira','diego@artavern.art',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3D sculptor & environment artist. ZBrush + Blender workflow. Will sculpt monsters for food.', '3D,Sculpting', 1, 'user', 1, 1),
(6,  'Sofia Park',       'sofiapark',    'sofia@artavern.art',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Portrait painter. I find beauty in ordinary faces. Traditional oil and digital mixed.', 'Traditional,Portrait,Digital', 0, 'user', 1, 1),
(7,  'Lena Mori',        'lenamori',     'lena@artavern.art',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Watercolor witch. Nature, mythology, folklore. Every painting is a tiny world.', 'Watercolor,Traditional', 1, 'user', 1, 1),
(8,  'Kai Brennan',      'kaibrennan',   'kai@artavern.art',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pixel art and retro game aesthetics. Making sprites since I could hold a mouse.', 'Pixel Art,Sprite Art', 0, 'user', 1, 1),
(9,  'Priya Sharma',     'priyadraws',   'priya@artavern.art',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Animation student. Character animator and storyboard artist. Motion is life!', 'Animation,Character Design', 0, 'user', 1, 1),
(10, 'Theo Vance',       'theovance',    'theo@artavern.art',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Abstract mixed media. Paint, collage, photography. Nothing is off limits.', 'Mixed Media,Abstract', 0, 'user', 1, 1),
(11, 'Nadia Cruz',       'nadiacruz',    'nadia@artavern.art',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Chibi and cute art specialist. Fanart, OCs, and all things adorable.', 'Digital,Chibi', 1, 'user', 1, 1),
(12, 'ARTavern Admin',   'admin',        'admin@artavern.art',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Official ARTavern account. Announcements, support, and community updates.', '', 0, 'admin', 1, 1);

-- ─── ADDITIONAL TAGS ──────────────────────────────────────
INSERT INTO tags (name, slug, post_count) VALUES
('watercolor','watercolor',0),('ink','ink',0),('oilpainting','oilpainting',0),
('sketch','sketch',0),('wip','wip',0),('speedpaint','speedpaint',0),
('environment','environment',0),('creature','creature',0),('mythology','mythology',0),
('cyberpunk','cyberpunk',0),('medieval','medieval',0),('cute','cute',0),
('pastel','pastel',0),('monochrome','monochrome',0),('comic','comic',0),
('procreate','procreate',0),('blender','blender',0),('zbrush','zbrush',0),
('photoshop','photoshop',0),('clipstudiopaint','clipstudiopaint',0),
('krita','krita',0),('pixelart','pixelart',0),('retrogaming','retrogaming',0),
('sprite','sprite',0),('storyboard','storyboard',0),('manga','manga',0),
('webtoon','webtoon',0),('originalcharacter','originalcharacter',0),('collab','collab',0)
ON DUPLICATE KEY UPDATE post_count=post_count;

-- ─── POSTS ────────────────────────────────────────────────
INSERT INTO posts (id, user_id, body, medium, source, image_path, like_count, comment_count, repost_count, created_at) VALUES
(1,  1,'Finally wrapped up this piece after three weeks of on-and-off work. The background lighting was killing me but I think I finally cracked it. Let me know what you think!','digital','created_on_site',NULL,284,6,18,NOW() - INTERVAL 2 HOUR),
(2,  1,'Quick 45-minute speedpaint before bed. Sometimes the most fun pieces come from zero pressure sessions.','digital','created_on_site',NULL,97,0,5,NOW() - INTERVAL 1 DAY),
(3,  1,'WIP of a forest spirit commission. Client wanted something soft but otherworldly. Getting there!','digital','uploaded',NULL,143,0,8,NOW() - INTERVAL 3 DAY),
(4,  2,'Full character design sheet — front, 3/4, and back. Ink on Bristol board, no pencil underdrawing. Living dangerously.','traditional','uploaded',NULL,512,4,63,NOW() - INTERVAL 5 HOUR),
(5,  2,'Inktober warm-up. Just a skull and some roses. Nothing deep, just vibes.','traditional','uploaded',NULL,201,0,11,NOW() - INTERVAL 2 DAY),
(6,  2,'Been studying Hokusai woodblock prints for a new project. Traditional media will never get old.','traditional','uploaded',NULL,376,0,29,NOW() - INTERVAL 5 DAY),
(7,  3,'Looking for a collab partner for a fantasy anthology project! I handle illustration, looking for someone to co-write or co-illustrate. DM me or hit the Collaborate tab!','digital','created_on_site',NULL,97,0,14,NOW() - INTERVAL 8 HOUR),
(8,  3,'Page 12 of my webcomic "The Ember Road". Our protagonist finally makes it to the city. Things are about to get messy.','digital','created_on_site',NULL,448,5,52,NOW() - INTERVAL 3 DAY),
(9,  3,'Experimenting with a warmer palette this week. Usually I lean cool blues but this feels so cozy. Might be a new direction?','digital','uploaded',NULL,188,0,17,NOW() - INTERVAL 6 DAY),
(10, 4,'Sci-fi cityscape timelapse — rough sketch to final render. Full process video in my portfolio. Neon City 2087.','digital','created_on_site',NULL,1204,5,204,NOW() - INTERVAL 12 HOUR),
(11, 4,'Pixel art mockup of a retro RPG overworld. Throwback to the games that made me fall in love with art as a kid.','pixel_art','created_on_site',NULL,892,0,144,NOW() - INTERVAL 2 DAY),
(12, 4,'Creature concept for a client project (shared with permission). Bioluminescent deep-sea predator. I love designing things that shouldnt exist.','digital','uploaded',NULL,673,0,76,NOW() - INTERVAL 4 DAY),
(13, 5,'Finished this creature sculpt in ZBrush — about 60 hours total. Took heavy reference from deep-sea fish and big cats. Retopology next.','3d','uploaded',NULL,761,4,87,NOW() - INTERVAL 1 DAY),
(14, 5,'Environment blockout in Blender for a dark fantasy dungeon. Lighting pass WIP. Still so much to do but the mood is right.','3d','created_on_site',NULL,334,0,33,NOW() - INTERVAL 4 DAY),
(15, 5,'Quick base mesh practice — 30 min stylized head sculpt. Speed runs like this are the best way to shake off stiffness.','3d','uploaded',NULL,218,0,14,NOW() - INTERVAL 7 DAY),
(16, 6,'Portrait study from life — my neighbor agreed to sit for me. Oil on canvas, 40x50cm. There is something irreplaceable about painting a real person in the room with you.','traditional','uploaded',NULL,624,0,41,NOW() - INTERVAL 6 HOUR),
(17, 6,'Digital portrait warmup. Trying to retain the looseness of oil sketching in my digital work. Mixed results but learning a lot.','digital','created_on_site',NULL,289,0,22,NOW() - INTERVAL 3 DAY),
(18, 7,'Folklore series entry 4: The Water Maiden. Watercolor on 300gsm cold press. These mythological pieces are the most personal work I have ever made.','traditional','uploaded',NULL,934,5,98,NOW() - INTERVAL 3 HOUR),
(19, 7,'Tiny 4x4 inch watercolor thumbnail studies. Testing color schemes for the next big piece.','traditional','uploaded',NULL,421,0,36,NOW() - INTERVAL 2 DAY),
(20, 7,'Process shot: wet-on-wet background wash while still drying. Watercolor is terrifying and I love it.','traditional','uploaded',NULL,287,0,19,NOW() - INTERVAL 5 DAY),
(21, 8,'Finished the sprite sheet for my indie game protagonist — 8 directions, 12 frames each. Only took 3 months. The walk cycle is finally smooth!','pixel_art','created_on_site',NULL,1089,4,201,NOW() - INTERVAL 4 HOUR),
(22, 8,'32x32 pixel art tileset for a dungeon crawler I am prototyping. Keeping the palette to 16 colors. Constraints breed creativity!','pixel_art','created_on_site',NULL,567,0,89,NOW() - INTERVAL 2 DAY),
(23, 8,'Animated campfire pixel art loop — 8 frames. Made this as a free asset for anyone building retro RPGs. Download in my portfolio!','pixel_art','created_on_site',NULL,2341,5,412,NOW() - INTERVAL 8 DAY),
(24, 9,'Second year animation assignment — walk cycle with personality. Character is a tired librarian who has seen everything.','digital','created_on_site',NULL,743,0,88,NOW() - INTERVAL 5 HOUR),
(25, 9,'Rough storyboard panels for a short I am developing. Still figuring out the pacing but the visual language is clicking. Thoughts?','digital','uploaded',NULL,312,0,27,NOW() - INTERVAL 3 DAY),
(26, 9,'Character turnaround for my animation thesis — meet Baya, a spirit who collects lost things.','digital','created_on_site',NULL,891,0,103,NOW() - INTERVAL 6 DAY),
(27, 10,'Mixed media piece: acrylic base, newspaper collage, ink linework, and digital color grading. No rules. Just feelings.','mixed','uploaded',NULL,456,0,38,NOW() - INTERVAL 7 HOUR),
(28, 10,'Abstract exploration — thinking about urban loneliness. Spray paint, acrylic, and torn magazine on wood panel. 60x80cm.','mixed','uploaded',NULL,334,0,23,NOW() - INTERVAL 4 DAY),
(29, 11,'Chibi commission batch finished! Six characters, five clients, one very caffeinated artist. DMs are open for the next slot!','digital','created_on_site',NULL,678,0,67,NOW() - INTERVAL 2 HOUR),
(30, 11,'Redesigned my OC Miko for the third time and I think she is finally right. Pastel goth princess energy all the way.','digital','created_on_site',NULL,892,3,91,NOW() - INTERVAL 1 DAY),
(31, 11,'Quick fanart of my current obsession. No spoilers on what it is from but if you know, you know.','digital','uploaded',NULL,1204,0,156,NOW() - INTERVAL 3 DAY),
(32, 12,'Welcome to ARTavern! We are a community-built platform for human artists. No AI art, no compromise on creator rights. Your art, your rules. Glad you are here!','digital','uploaded',NULL,2891,6,567,NOW() - INTERVAL 14 DAY),
(33, 12,'New feature: Tag system is now live! Add genre tags, medium tags, and source tags to all your posts. Make your work discoverable!','digital','uploaded',NULL,1243,0,234,NOW() - INTERVAL 7 DAY);

-- ─── POST TAGS ────────────────────────────────────────────
INSERT INTO post_tags (post_id, tag_id) SELECT 1,  id FROM tags WHERE name IN ('fantasy','nature','portrait','procreate');
INSERT INTO post_tags (post_id, tag_id) SELECT 2,  id FROM tags WHERE name IN ('speedpaint','landscape','digital','procreate');
INSERT INTO post_tags (post_id, tag_id) SELECT 3,  id FROM tags WHERE name IN ('wip','fantasy','nature','character');
INSERT INTO post_tags (post_id, tag_id) SELECT 4,  id FROM tags WHERE name IN ('character','ink','traditional','monochrome');
INSERT INTO post_tags (post_id, tag_id) SELECT 5,  id FROM tags WHERE name IN ('ink','traditional','sketch','horror');
INSERT INTO post_tags (post_id, tag_id) SELECT 6,  id FROM tags WHERE name IN ('traditional','ink','concept','monochrome');
INSERT INTO post_tags (post_id, tag_id) SELECT 7,  id FROM tags WHERE name IN ('collab','fantasy','illustration');
INSERT INTO post_tags (post_id, tag_id) SELECT 8,  id FROM tags WHERE name IN ('comic','fantasy','webtoon','character','clipstudiopaint');
INSERT INTO post_tags (post_id, tag_id) SELECT 9,  id FROM tags WHERE name IN ('wip','landscape','digital');
INSERT INTO post_tags (post_id, tag_id) SELECT 10, id FROM tags WHERE name IN ('sci-fi','cyberpunk','environment','concept','photoshop');
INSERT INTO post_tags (post_id, tag_id) SELECT 11, id FROM tags WHERE name IN ('pixelart','retrogaming','environment','concept');
INSERT INTO post_tags (post_id, tag_id) SELECT 12, id FROM tags WHERE name IN ('creature','sci-fi','concept','digital','photoshop');
INSERT INTO post_tags (post_id, tag_id) SELECT 13, id FROM tags WHERE name IN ('creature','zbrush','3d','concept','fantasy');
INSERT INTO post_tags (post_id, tag_id) SELECT 14, id FROM tags WHERE name IN ('environment','blender','3d','medieval','fantasy');
INSERT INTO post_tags (post_id, tag_id) SELECT 15, id FROM tags WHERE name IN ('sketch','zbrush','3d','portrait');
INSERT INTO post_tags (post_id, tag_id) SELECT 16, id FROM tags WHERE name IN ('portrait','oilpainting','traditional','modern');
INSERT INTO post_tags (post_id, tag_id) SELECT 17, id FROM tags WHERE name IN ('portrait','digital','procreate','sketch');
INSERT INTO post_tags (post_id, tag_id) SELECT 18, id FROM tags WHERE name IN ('watercolor','mythology','fantasy','nature','traditional');
INSERT INTO post_tags (post_id, tag_id) SELECT 19, id FROM tags WHERE name IN ('watercolor','wip','traditional','sketch');
INSERT INTO post_tags (post_id, tag_id) SELECT 20, id FROM tags WHERE name IN ('watercolor','wip','traditional','nature');
INSERT INTO post_tags (post_id, tag_id) SELECT 21, id FROM tags WHERE name IN ('pixelart','sprite','retrogaming','animation','character');
INSERT INTO post_tags (post_id, tag_id) SELECT 22, id FROM tags WHERE name IN ('pixelart','sprite','retrogaming','environment','medieval');
INSERT INTO post_tags (post_id, tag_id) SELECT 23, id FROM tags WHERE name IN ('pixelart','animation','retrogaming','sprite');
INSERT INTO post_tags (post_id, tag_id) SELECT 24, id FROM tags WHERE name IN ('animation','character','digital','clipstudiopaint');
INSERT INTO post_tags (post_id, tag_id) SELECT 25, id FROM tags WHERE name IN ('storyboard','comic','animation','concept');
INSERT INTO post_tags (post_id, tag_id) SELECT 26, id FROM tags WHERE name IN ('character','originalcharacter','animation','digital','fantasy');
INSERT INTO post_tags (post_id, tag_id) SELECT 27, id FROM tags WHERE name IN ('abstract','mixed','modern');
INSERT INTO post_tags (post_id, tag_id) SELECT 28, id FROM tags WHERE name IN ('abstract','mixed','modern','monochrome');
INSERT INTO post_tags (post_id, tag_id) SELECT 29, id FROM tags WHERE name IN ('chibi','cute','digital','character','procreate');
INSERT INTO post_tags (post_id, tag_id) SELECT 30, id FROM tags WHERE name IN ('originalcharacter','chibi','cute','digital','pastel','oc');
INSERT INTO post_tags (post_id, tag_id) SELECT 31, id FROM tags WHERE name IN ('fanart','cute','chibi','digital','procreate');
INSERT INTO post_tags (post_id, tag_id) SELECT 32, id FROM tags WHERE name IN ('modern');
INSERT INTO post_tags (post_id, tag_id) SELECT 33, id FROM tags WHERE name IN ('modern');

UPDATE tags t SET post_count = (SELECT COUNT(*) FROM post_tags pt WHERE pt.tag_id = t.id);

-- ─── FOLLOWS ──────────────────────────────────────────────
INSERT INTO follows (follower_id, following_id) VALUES
(1,12),(2,12),(3,12),(4,12),(5,12),(6,12),(7,12),(8,12),(9,12),(10,12),(11,12),
(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),
(1,2),(3,2),(4,2),(7,2),(9,2),(11,2),
(1,3),(2,3),(4,3),(6,3),(8,3),(11,3),
(1,4),(2,4),(3,4),(5,4),(8,4),(9,4),(10,4),(11,4),
(1,5),(2,5),(4,5),(6,5),(9,5),(10,5),
(1,6),(3,6),(7,6),(9,6),(11,6),
(1,7),(2,7),(3,7),(6,7),(9,7),(11,7),
(1,8),(4,8),(5,8),(9,8),(10,8),(11,8),
(1,9),(3,9),(4,9),(7,9),(8,9),(11,9),
(1,10),(2,10),(4,10),(6,10),(8,10),
(1,11),(3,11),(6,11),(7,11),(9,11);

-- ─── LIKES ────────────────────────────────────────────────
INSERT IGNORE INTO likes (user_id, post_id) VALUES
(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),
(1,4),(3,4),(4,4),(5,4),(6,4),(7,4),(8,4),(9,4),(10,4),(11,4),
(1,8),(2,8),(4,8),(5,8),(6,8),(7,8),(8,8),(9,8),(10,8),(11,8),
(1,10),(2,10),(3,10),(5,10),(6,10),(7,10),(8,10),(9,10),(11,10),(12,10),
(1,13),(2,13),(3,13),(4,13),(6,13),(7,13),(8,13),(9,13),(10,13),(11,13),
(1,18),(2,18),(3,18),(4,18),(5,18),(6,18),(7,18),(9,18),(10,18),(11,18),(12,18),
(1,21),(2,21),(3,21),(4,21),(5,21),(6,21),(7,21),(9,21),(10,21),(11,21),(12,21),
(1,23),(2,23),(3,23),(4,23),(5,23),(6,23),(7,23),(8,23),(9,23),(10,23),(11,23),(12,23),
(1,24),(2,24),(3,24),(4,24),(5,24),(6,24),(7,24),(8,24),(10,24),(11,24),(12,24),
(1,30),(2,30),(3,30),(4,30),(5,30),(6,30),(7,30),(8,30),(9,30),(10,30),(12,30),
(1,31),(2,31),(3,31),(4,31),(5,31),(6,31),(7,31),(8,31),(9,31),(10,31),(12,31),
(1,32),(2,32),(3,32),(4,32),(5,32),(6,32),(7,32),(8,32),(9,32),(10,32),(11,32),
(1,5),(3,5),(7,5),(2,6),(4,6),(1,7),(5,7),(8,7),(2,11),(6,11),(7,11),(3,14),(6,14),
(2,16),(4,16),(7,16),(3,17),(8,19),(3,20),(6,20),(1,22),(5,22),(7,22),(3,25),(6,26),
(1,28),(4,28),(2,29),(6,29),(7,29),(3,33),(5,33),(7,33),(8,33),(9,33);

-- ─── COMMENTS ─────────────────────────────────────────────
INSERT INTO comments (post_id, user_id, body, created_at) VALUES
(1,2,'The lighting on the canopy is insane. How did you approach the rim lighting on the character?',NOW() - INTERVAL 100 MINUTE),
(1,7,'Those colors!! The warm vs cool contrast is doing so much heavy lifting here. Stunning Reika.',NOW() - INTERVAL 90 MINUTE),
(1,3,'Three weeks well spent. The depth in that background is unreal.',NOW() - INTERVAL 80 MINUTE),
(1,11,'I want to live in this painting.',NOW() - INTERVAL 60 MINUTE),
(1,9,'Your color harmony is always so good. What palette did you start with?',NOW() - INTERVAL 45 MINUTE),
(1,1,'Thank you all! I started with a muted teal base and built warm glazes over it, it kind of just worked.',NOW() - INTERVAL 30 MINUTE),
(4,1,'No pencil underdrawing?? You absolute maniac. This is incredible though.',NOW() - INTERVAL 4 HOUR),
(4,9,'The line weight variation in the clothing folds is so clean. Really studying this one.',NOW() - INTERVAL 3 HOUR),
(4,5,'The silhouette reads perfectly at every angle. Solid character design fundamentals.',NOW() - INTERVAL 2 HOUR),
(4,11,'I need this character to be in a game immediately please.',NOW() - INTERVAL 90 MINUTE),
(8,1,'I have been following this comic since page 1 and the glow-up is REAL. So proud of how far you have come Yuki!',NOW() - INTERVAL 2 DAY),
(8,4,'The city architecture design is so well thought out. The mix of old and new feels lived-in.',NOW() - INTERVAL 2 DAY),
(8,6,'The expression on her face in panel 3 says everything without a word. Great visual storytelling.',NOW() - INTERVAL 1 DAY),
(8,11,'FINALLY she made it!! I have been waiting for this page for three weeks.',NOW() - INTERVAL 23 HOUR),
(8,2,'Your inking style has gotten so much more confident. The hatching on the buildings is chef kiss.',NOW() - INTERVAL 20 HOUR),
(10,1,'The aerial perspective is perfect. You can feel the scale of this city.',NOW() - INTERVAL 11 HOUR),
(10,5,'The environmental storytelling here is incredible. Every building tells a story.',NOW() - INTERVAL 10 HOUR),
(10,8,'As a pixel artist I have to ask — was any of this inspired by oldschool isometric games? It has that energy.',NOW() - INTERVAL 9 HOUR),
(10,4,'Kind of! The walkways were definitely influenced by Syndicate. Good eye!',NOW() - INTERVAL 8 HOUR),
(10,9,'The neon reflections in the rain are SO good. Posting this as motivation on my wall.',NOW() - INTERVAL 7 HOUR),
(13,4,'The integration of deep-sea and big cat reference is genius. It feels genuinely alien but also believable.',NOW() - INTERVAL 23 HOUR),
(13,1,'Sixty hours!! You absolute legend. The secondary surface detail is insane up close.',NOW() - INTERVAL 22 HOUR),
(13,2,'As a 2D artist I am both inspired and intimidated. The volume reading is perfect.',NOW() - INTERVAL 20 HOUR),
(13,7,'It looks like something that would eat you and you would consider it an honor.',NOW() - INTERVAL 18 HOUR),
(18,1,'This is genuinely one of the best watercolors I have seen on this platform. The soft edges on the hair.',NOW() - INTERVAL 2 HOUR),
(18,7,'The blooms in the background were totally unplanned but I kept them. Happy accidents!',NOW() - INTERVAL 100 MINUTE),
(18,6,'As a fellow traditional artist — the restraint in the lighter areas is so hard to achieve and you nailed it.',NOW() - INTERVAL 90 MINUTE),
(18,3,'This whole folklore series has been incredible. Please make it a book.',NOW() - INTERVAL 80 MINUTE),
(18,11,'The mythology series is the reason I joined ARTavern. No cap.',NOW() - INTERVAL 60 MINUTE),
(21,4,'Three months of that and it shows. The walk cycle personality is perfect.',NOW() - INTERVAL 3 HOUR),
(21,9,'As an animator: this secondary motion on the backpack is PRISTINE. Overlapping action goals right here.',NOW() - INTERVAL 2 HOUR),
(21,3,'The pixel art shading style is so clean. What is your process for keeping consistency across frames?',NOW() - INTERVAL 90 MINUTE),
(21,8,'I use a strict color palette locked in Aseprite and a layer mask for the shadow — happy to do a tutorial!',NOW() - INTERVAL 60 MINUTE),
(23,1,'I have been using this in my game for a week and it is perfect. Thank you so much for making it free!!',NOW() - INTERVAL 7 DAY),
(23,4,'Downloaded immediately. The flicker timing feels so natural.',NOW() - INTERVAL 7 DAY),
(23,5,'The heat shimmer effect in frame 6 is so subtle and so good.',NOW() - INTERVAL 6 DAY),
(23,3,'Using this in my visual novel!! You are a legend Kai.',NOW() - INTERVAL 6 DAY),
(23,9,'Honestly this is better than most paid assets I have used. Community is everything.',NOW() - INTERVAL 5 DAY),
(30,7,'Miko is PERFECT. The pastel goth vibe is immaculate. Redesign was worth it!',NOW() - INTERVAL 23 HOUR),
(30,3,'The little details in the accessories!! I love her.',NOW() - INTERVAL 22 HOUR),
(30,1,'Third time is the charm and wow it really is. She feels so complete now.',NOW() - INTERVAL 20 HOUR),
(32,1,'So happy this place exists. Finally somewhere that actually respects artist rights.',NOW() - INTERVAL 13 DAY),
(32,8,'Been waiting for a platform like this for years. Let us build something great together.',NOW() - INTERVAL 13 DAY),
(32,4,'No AI art policy is the bare minimum and I am glad it is day one here. Thank you!',NOW() - INTERVAL 12 DAY),
(32,7,'The community so far is already so warm and supportive. Great start!',NOW() - INTERVAL 12 DAY),
(32,2,'Glad to be here. Let us make this the best art community on the internet.',NOW() - INTERVAL 11 DAY),
(32,11,'Already my favorite platform and I just signed up.',NOW() - INTERVAL 10 DAY);

-- ─── REPOSTS ──────────────────────────────────────────────
INSERT IGNORE INTO reposts (user_id, post_id) VALUES
(1,23),(2,23),(3,23),(4,23),(5,23),(6,23),(7,23),(9,23),(10,23),(11,23),
(1,32),(2,32),(3,32),(4,32),(5,32),(6,32),(7,32),(8,32),(9,32),(10,32),(11,32),
(1,10),(3,10),(5,10),(7,10),(9,10),(11,10),
(2,18),(4,18),(6,18),(8,18),(10,18),
(1,21),(3,21),(5,21),(7,21),(9,21),(11,21),
(4,8),(6,8),(8,8),(10,8),
(2,26),(4,26),(6,26),(8,26),(10,26);

-- ─── BOOKMARKS ────────────────────────────────────────────
INSERT IGNORE INTO bookmarks (user_id, post_id) VALUES
(1,4),(1,10),(1,13),(1,18),(1,21),(1,23),(1,30),
(2,1),(2,10),(2,18),(2,21),(2,23),(2,26),
(3,10),(3,13),(3,18),(3,23),(3,32),
(4,13),(4,18),(4,21),(4,23),(4,26),
(5,1),(5,10),(5,23),(5,30),
(6,18),(6,21),(6,23),(6,32),
(7,1),(7,4),(7,21),(7,23),
(8,10),(8,23),(8,26),(8,32),
(9,1),(9,13),(9,18),(9,21),(9,23),
(10,18),(10,21),(10,23),(10,30),
(11,1),(11,8),(11,18),(11,23),(11,26);

-- ─── PORTFOLIO ITEMS ──────────────────────────────────────
INSERT INTO portfolio_items (id, user_id, title, description, image_path, medium, source, is_featured, created_at) VALUES
(1,1,'Dusk at the Edge','Fantasy landscape. Personal piece, 3 weeks.',NULL,'digital','created_on_site',1,NOW() - INTERVAL 2 HOUR),
(2,1,'Forest Spirit Commission','Commission for a private client, shared with permission.',NULL,'digital','created_on_site',0,NOW() - INTERVAL 3 DAY),
(3,1,'Ember Season','Autumn spirits personal project.',NULL,'digital','created_on_site',0,NOW() - INTERVAL 10 DAY),
(4,2,'The Wanderer — Design Sheet','Full turnaround with notes. Ink, no underdrawing.',NULL,'traditional','uploaded',1,NOW() - INTERVAL 5 HOUR),
(5,2,'Skull Study No.12','Inktober warm-up.',NULL,'traditional','uploaded',0,NOW() - INTERVAL 2 DAY),
(6,3,'Ember Road — Cover','Comic cover illustration.',NULL,'digital','created_on_site',1,NOW() - INTERVAL 3 DAY),
(7,3,'Ember Road — Page 12','Interior page. Ink and digital color.',NULL,'digital','created_on_site',0,NOW() - INTERVAL 3 DAY),
(8,4,'Neon City 2087','Sci-fi cityscape concept. Personal project.',NULL,'digital','created_on_site',1,NOW() - INTERVAL 12 HOUR),
(9,4,'Deep Predator','Creature concept. Bioluminescent deep-sea design.',NULL,'digital','uploaded',0,NOW() - INTERVAL 4 DAY),
(10,4,'Retro RPG Overworld','Pixel art mockup for an imaginary game.',NULL,'pixel_art','created_on_site',0,NOW() - INTERVAL 2 DAY),
(11,5,'Void Stalker','ZBrush creature sculpt. 60h total.',NULL,'3d','uploaded',1,NOW() - INTERVAL 1 DAY),
(12,5,'Dungeon Environment — WIP','Dark fantasy dungeon in Blender. Lighting WIP.',NULL,'3d','created_on_site',0,NOW() - INTERVAL 4 DAY),
(13,6,'Study in Light','Oil on canvas. 40x50cm. Portrait from life.',NULL,'traditional','uploaded',1,NOW() - INTERVAL 6 HOUR),
(14,7,'The Water Maiden','Folklore series no.4. Watercolor on 300gsm cold press.',NULL,'traditional','uploaded',1,NOW() - INTERVAL 3 HOUR),
(15,7,'The Bone Keeper','Folklore series no.1. The piece that started it all.',NULL,'traditional','uploaded',0,NOW() - INTERVAL 30 DAY),
(16,7,'Moonlit Traveler','Folklore series no.2.',NULL,'traditional','uploaded',0,NOW() - INTERVAL 20 DAY),
(17,8,'Hero — Full Sprite Sheet','Protagonist sprite, 8 directions, 12 frames each.',NULL,'pixel_art','created_on_site',1,NOW() - INTERVAL 4 HOUR),
(18,8,'Campfire Loop','Free 8-frame animated campfire. CC0 license.',NULL,'pixel_art','created_on_site',0,NOW() - INTERVAL 8 DAY),
(19,8,'Dungeon Tileset v1','16-color dungeon tileset. Free for indie devs.',NULL,'pixel_art','created_on_site',0,NOW() - INTERVAL 2 DAY),
(20,9,'Tired Librarian Walk Cycle','Animation assignment. Personality walk with full secondary motion.',NULL,'digital','created_on_site',1,NOW() - INTERVAL 5 HOUR),
(21,9,'Baya — Character Turnaround','Thesis character. Spirit who collects lost things.',NULL,'digital','created_on_site',0,NOW() - INTERVAL 6 DAY),
(22,10,'Urban Solitude','Acrylic, newspaper, ink, digital. 60x80cm wood panel.',NULL,'mixed','uploaded',1,NOW() - INTERVAL 4 DAY),
(23,11,'Commission Batch June','Six chibi character commissions.',NULL,'digital','created_on_site',1,NOW() - INTERVAL 2 HOUR),
(24,11,'Miko v3','My OC, third redesign. Final form.',NULL,'digital','created_on_site',0,NOW() - INTERVAL 1 DAY);

INSERT INTO portfolio_item_tags (item_id, tag_id) SELECT 1,  id FROM tags WHERE name IN ('fantasy','landscape','digital');
INSERT INTO portfolio_item_tags (item_id, tag_id) SELECT 4,  id FROM tags WHERE name IN ('character','traditional','ink','monochrome');
INSERT INTO portfolio_item_tags (item_id, tag_id) SELECT 8,  id FROM tags WHERE name IN ('sci-fi','cyberpunk','environment','concept');
INSERT INTO portfolio_item_tags (item_id, tag_id) SELECT 11, id FROM tags WHERE name IN ('creature','zbrush','3d','fantasy');
INSERT INTO portfolio_item_tags (item_id, tag_id) SELECT 14, id FROM tags WHERE name IN ('watercolor','mythology','fantasy','traditional');
INSERT INTO portfolio_item_tags (item_id, tag_id) SELECT 17, id FROM tags WHERE name IN ('pixelart','sprite','character','animation');
INSERT INTO portfolio_item_tags (item_id, tag_id) SELECT 20, id FROM tags WHERE name IN ('animation','character','digital');

-- ─── COLLABORATIONS ───────────────────────────────────────
INSERT INTO collaborations (id, host_id, title, description, type, status, max_members, created_at) VALUES
(1,7,'Fantasy Anthology — Seeking Co-Illustrator','Building a 10-page illustrated anthology of folklore-inspired short stories. Looking for another illustrator to handle half the pages. Any style welcome.','shared_canvas','open',2,NOW() - INTERVAL 8 HOUR),
(2,1,'Figure Drawing Session — Open Practice','Weekly open figure drawing practice. Share pose references, draw for 20 minutes, share results, friendly feedback. No skill floor.','shared_canvas','open',8,NOW() - INTERVAL 2 DAY),
(3,6,'Portrait Master Class — Limited Spots','Walking through my full portrait process live on the whiteboard. Oil painting principles applied to digital. 3 student spots, 2 filled.','tutoring','open',4,NOW() - INTERVAL 4 DAY),
(4,8,'Pixel Art Fundamentals Tutoring','Teaching pixel art basics: dithering, limited palettes, animation loops. Beginners welcome. One-on-one sessions on the ARTavern whiteboard.','tutoring','open',2,NOW() - INTERVAL 1 DAY),
(5,4,'Style Swap Challenge','Randomized pairing — you draw in your partners style, they draw in yours. Sign up and get matched!','matchmaking','open',20,NOW() - INTERVAL 3 DAY),
(6,9,'Storyboard Feedback Circle','Share storyboard panels and get structured feedback from animation peers.','shared_canvas','open',6,NOW() - INTERVAL 5 DAY),
(7,5,'3D and 2D Collab: Concept to Model','Looking for a 2D concept artist. You design the creature, I sculpt it in ZBrush. We both keep the final assets.','shared_canvas','in_progress',2,NOW() - INTERVAL 6 DAY),
(8,3,'Webcomic Critique Group','Small group for sharing webcomic pages and giving detailed craft-level feedback. Script, pacing, paneling, lettering.','shared_canvas','open',4,NOW() - INTERVAL 7 DAY);

INSERT INTO collaboration_members (collab_id, user_id, joined_at) VALUES
(1,7,NOW() - INTERVAL 8 HOUR),(2,1,NOW() - INTERVAL 2 DAY),(2,3,NOW() - INTERVAL 1 DAY),
(2,11,NOW() - INTERVAL 20 HOUR),(3,6,NOW() - INTERVAL 4 DAY),(3,7,NOW() - INTERVAL 3 DAY),
(3,1,NOW() - INTERVAL 2 DAY),(4,8,NOW() - INTERVAL 1 DAY),(5,4,NOW() - INTERVAL 3 DAY),
(5,1,NOW() - INTERVAL 2 DAY),(5,11,NOW() - INTERVAL 2 DAY),(5,9,NOW() - INTERVAL 1 DAY),
(6,9,NOW() - INTERVAL 5 DAY),(6,3,NOW() - INTERVAL 4 DAY),(7,5,NOW() - INTERVAL 6 DAY),
(7,4,NOW() - INTERVAL 5 DAY),(8,3,NOW() - INTERVAL 7 DAY),(8,7,NOW() - INTERVAL 6 DAY);

-- ─── COMMISSIONS ──────────────────────────────────────────
INSERT INTO commissions (artist_id, client_id, title, description, price, status, created_at) VALUES
(1,3,'Forest Spirit Illustration','Full illustration of a forest spirit for use as a chapter header in the Ember Road webcomic.',120.00,'completed',NOW() - INTERVAL 14 DAY),
(1,11,'OC Portrait — Miko','Digital portrait of OC Miko in Reika style. Full body, simple background.',80.00,'in_progress',NOW() - INTERVAL 3 DAY),
(2,4,'Creature Design Sheet','Full character design sheet with orthographic views and detail callouts. Ink, traditional.',200.00,'accepted',NOW() - INTERVAL 5 DAY),
(4,8,'Pixel Art — Game UI Kit','Complete pixel art UI kit for retro RPG: HUD, menus, inventory icons. 16-color palette.',350.00,'in_progress',NOW() - INTERVAL 7 DAY),
(5,4,'Creature Sculpt — Deep Predator v2','Follow-up sculpt with higher detail pass, full retopology, and texture bake.',500.00,'accepted',NOW() - INTERVAL 2 DAY),
(6,1,'Portrait Commission — Digital','Digital portrait in Sofia painterly style. Reference photos provided.',150.00,'pending',NOW() - INTERVAL 1 DAY),
(7,6,'Watercolor Botanical Series x3','Three A5 watercolor botanical illustrations for framing. Lavender, Wisteria, Jasmine.',240.00,'completed',NOW() - INTERVAL 20 DAY),
(11,3,'Chibi Versions — Ember Road Characters x4','Chibi versions of four main characters for stickers and merch.',160.00,'in_progress',NOW() - INTERVAL 4 DAY),
(11,7,'Chibi OC — Folklore Edition','Chibi version of the Water Maiden character in Nadia cute art style.',60.00,'completed',NOW() - INTERVAL 10 DAY),
(9,5,'Character Animation — Idle Loop','Idle animation loop for creature sculpt presentation reel.',180.00,'pending',NOW() - INTERVAL 12 HOUR);

-- ─── MESSAGES ─────────────────────────────────────────────
INSERT INTO messages (sender_id, receiver_id, body, is_read, created_at) VALUES
(3,1,'Hey Reika! Would you be open for a commission? I need a forest spirit illustration for my comic chapter header.',1,NOW() - INTERVAL 15 DAY),
(1,3,'Hi Yuki! Yes definitely open. What style are you thinking? Do you have reference images?',1,NOW() - INTERVAL 15 DAY),
(3,1,'Something soft and otherworldly! Tall, willowy, glowing eyes, surrounded by fireflies. Warm greens and golds.',1,NOW() - INTERVAL 14 DAY),
(1,3,'Love the brief! Starting with rough sketches. Timeline is about 2 weeks, is that okay?',1,NOW() - INTERVAL 14 DAY),
(3,1,'The finished piece was absolutely stunning. Thank you so much.',1,NOW() - INTERVAL 10 DAY),
(1,3,'So glad you loved it! Your comic is going to be amazing.',1,NOW() - INTERVAL 10 DAY),
(4,8,'Kai! I love your pixel art style. I have a commission proposal — a full UI kit for my retro RPG prototype.',1,NOW() - INTERVAL 8 DAY),
(8,4,'Oh that sounds really fun! What is the scope? How many screens and elements are you thinking?',1,NOW() - INTERVAL 8 DAY),
(4,8,'Main HUD, pause menu, inventory grid with about 20 icons, and a dialogue box. All in your 16-color palette style.',1,NOW() - INTERVAL 7 DAY),
(8,4,'I can do that! Probably around $350 given the scope, turnaround 2 weeks?',1,NOW() - INTERVAL 7 DAY),
(4,8,'Deal! Sending the game reference docs now.',1,NOW() - INTERVAL 7 DAY),
(8,4,'Got them, starting on the HUD first. I will share WIPs as I go!',1,NOW() - INTERVAL 6 DAY),
(7,2,'Marco your Hokusai-influenced piece is incredible. Have you studied his print technique much?',1,NOW() - INTERVAL 4 DAY),
(2,7,'Thank you! Yes I spent about a month with reproductions studying the line economy. Woodblock printing has so much to teach ink artists.',1,NOW() - INTERVAL 4 DAY),
(7,2,'The negative space decisions are so deliberate. Would you ever want to collab? Your ink and my watercolor could be something special.',1,NOW() - INTERVAL 3 DAY),
(2,7,'I have actually thought about that! Let us plan something. Maybe a folklore-themed piece?',1,NOW() - INTERVAL 3 DAY),
(7,2,'Yes!! I am already obsessed with this idea. Let us use the ARTavern whiteboard when it is ready.',0,NOW() - INTERVAL 2 DAY),
(3,11,'Hi Nadia! I would love to commission chibi versions of my four Ember Road characters for stickers!',1,NOW() - INTERVAL 5 DAY),
(11,3,'Yuki!! Yes please, I love your comic! Send me references for all four and I will put together a quote.',1,NOW() - INTERVAL 5 DAY),
(3,11,'Sending now! The main four: Kara, Bren, Sil, and the old merchant. Including personality notes too.',1,NOW() - INTERVAL 4 DAY),
(11,3,'These designs are SO fun. I am already sketching Kara in my head. Starting this week!',0,NOW() - INTERVAL 4 DAY),
(12,1,'Hi Reika! You have been selected as one of ARTavern featured artists for our launch showcase. We would love to highlight your portfolio on the explore page.',1,NOW() - INTERVAL 12 DAY),
(1,12,'Oh wow, thank you so much!! I would be honored. What do you need from me?',1,NOW() - INTERVAL 12 DAY),
(12,1,'Just a brief artist statement and permission to feature up to 3 pieces. That is it!',1,NOW() - INTERVAL 11 DAY),
(1,12,'Done! Statement sent via the support form. Thank you again for building this place.',0,NOW() - INTERVAL 11 DAY);

-- ─── REFERENCE MODELS ─────────────────────────────────────
INSERT INTO reference_models (uploader_id, title, description, file_path, type, is_free) VALUES
(6,'Natural Light Portrait Pack','40 high-res portrait photos under natural window lighting. Various angles, ages, expressions.',NULL,'photo_ref',1),
(6,'Dramatic Lighting Portrait Pack','30 photos with Rembrandt, split, and rim lighting setups.',NULL,'photo_ref',1),
(5,'Stylized Head Base Mesh','Low-poly stylized head base mesh. OBJ and Blender file.',NULL,'3d_model',1),
(5,'Creature Anatomy Ref — Quadrupeds','ZBrush reference sphere collection: big cat, wolf, horse anatomy breakdowns.',NULL,'3d_model',1),
(8,'100 Pixel Character Poses','100 pixel art character pose references. 32x32 and 64x64 formats.',NULL,'pose_pack',1),
(9,'Animation Pose Pack — Vol. 1','200 dynamic action poses for animation reference. Drawn in rough silhouette style.',NULL,'pose_pack',1),
(9,'Storyboard Template Pack','Blank storyboard panel templates in 16:9 and 4:3. PDF and PSD.',NULL,'other',1),
(7,'Botanical Photo Reference — Flowers','60 macro photos of flowers under natural light. Great for watercolor reference.',NULL,'photo_ref',1),
(2,'Cloth and Fold Study Sheets','30 scanned ink studies of fabric folds — cotton, silk, leather, denim.',NULL,'other',1),
(4,'Sci-Fi Architecture Ref Pack','80 curated photos of brutalist, industrial, and futuristic architecture.',NULL,'photo_ref',1);

-- ─── NOTIFICATIONS ────────────────────────────────────────
INSERT INTO notifications (user_id, type, ref_id, ref_type, is_read, created_at) VALUES
(1,'like',4,'post',1,NOW() - INTERVAL 2 HOUR),
(1,'comment',1,'post',1,NOW() - INTERVAL 100 MINUTE),
(1,'follow',7,'user',0,NOW() - INTERVAL 30 MINUTE),
(1,'comment',1,'post',0,NOW() - INTERVAL 20 MINUTE),
(8,'like',23,'post',1,NOW() - INTERVAL 6 DAY),
(8,'comment',23,'post',1,NOW() - INTERVAL 6 DAY),
(8,'follow',4,'user',1,NOW() - INTERVAL 3 DAY),
(8,'commission',4,'user',0,NOW() - INTERVAL 7 DAY),
(7,'like',18,'post',1,NOW() - INTERVAL 2 HOUR),
(7,'comment',18,'post',0,NOW() - INTERVAL 90 MINUTE),
(7,'follow',3,'user',0,NOW() - INTERVAL 1 DAY),
(11,'commission',3,'user',0,NOW() - INTERVAL 5 DAY),
(11,'like',30,'post',1,NOW() - INTERVAL 23 HOUR),
(11,'follow',1,'user',1,NOW() - INTERVAL 12 DAY),
(3,'like',8,'post',1,NOW() - INTERVAL 1 DAY),
(3,'comment',8,'post',0,NOW() - INTERVAL 20 HOUR),
(3,'collab',8,'collab',0,NOW() - INTERVAL 6 DAY);

-- ─── SYNC COUNTS ──────────────────────────────────────────
UPDATE posts p SET comment_count = (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id AND c.is_deleted = 0);
UPDATE posts p SET repost_count  = (SELECT COUNT(*) FROM reposts  r WHERE r.post_id = p.id);
UPDATE tags  t SET post_count    = (SELECT COUNT(*) FROM post_tags pt WHERE pt.tag_id = t.id);

