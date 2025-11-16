-- =====================================================
-- FIX PAGE SLUGS - Remove problematic accents
-- =====================================================

-- Update "Crea Attività" page slug
UPDATE wp_posts
SET post_name = 'crea-attivita'
WHERE post_name LIKE '%crea-attivit%'
  AND post_type = 'page';

-- Update "Modifica Attività" page slug (if exists)
UPDATE wp_posts
SET post_name = 'modifica-attivita'
WHERE post_name LIKE '%modifica-attivit%'
  AND post_type = 'page';

-- Update "Calendario Attività" page slug (if exists)
UPDATE wp_posts
SET post_name = 'calendario-attivita'
WHERE post_name LIKE '%calendario-attivit%'
  AND post_type = 'page';

-- Update any other pages with problematic slugs
UPDATE wp_posts
SET post_name = 'storia-sport'
WHERE post_name LIKE '%storia%'
  AND post_name LIKE '%sport%'
  AND post_type = 'page';

-- Show updated pages
SELECT ID, post_title, post_name, post_type
FROM wp_posts
WHERE post_type = 'page'
  AND post_name IN ('crea-attivita', 'modifica-attivita', 'calendario-attivita', 'storia-sport');

-- =====================================================
-- POST-FIX INSTRUCTIONS:
-- =====================================================
-- After running this script:
-- 1. Go to WordPress Admin → Settings → Permalinks
-- 2. Click "Save Changes" (without changing anything)
-- 3. This will flush the rewrite rules
-- =====================================================
