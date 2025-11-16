<?php
/**
 * Fix page slugs - Remove accents from WordPress page slugs
 * Run this once to fix problematic page slugs with accents
 */

// Load WordPress
require_once __DIR__ . '/wp-load.php';

if (!current_user_can('manage_options') && php_sapi_name() !== 'cli') {
    die('Unauthorized');
}

echo "=== Fixing Page Slugs ===\n\n";

// Get all pages
$pages = get_posts(array(
    'post_type' => 'page',
    'posts_per_page' => -1,
    'post_status' => 'any'
));

$fixed_count = 0;

foreach ($pages as $page) {
    $old_slug = $page->post_name;

    // Remove accents from slug
    $new_slug = remove_accents($old_slug);

    // Sanitize the slug
    $new_slug = sanitize_title($new_slug);

    if ($old_slug !== $new_slug && strpos($old_slug, '%') !== false || preg_match('/[àèéìòù]/i', $old_slug)) {
        echo "Fixing: '{$page->post_title}'\n";
        echo "  Old slug: {$old_slug}\n";
        echo "  New slug: {$new_slug}\n";

        // Update the post
        wp_update_post(array(
            'ID' => $page->ID,
            'post_name' => $new_slug
        ));

        $fixed_count++;
        echo "  ✓ Fixed!\n\n";
    }
}

if ($fixed_count === 0) {
    echo "No pages with problematic slugs found.\n";
} else {
    echo "\n=== Summary ===\n";
    echo "Fixed {$fixed_count} page slug(s).\n";
    echo "\nDon't forget to:\n";
    echo "1. Go to Settings → Permalinks in WordPress admin\n";
    echo "2. Click 'Save Changes' to flush rewrite rules\n";
}

echo "\nDone!\n";
