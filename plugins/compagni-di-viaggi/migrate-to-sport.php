<?php
/**
 * Migration Script: Travel to Sport Platform
 *
 * This script migrates the database from travel-focused to sport-focused platform
 *
 * IMPORTANT: This should be run ONCE after deploying the new code
 * Run from WordPress admin or via WP-CLI: wp eval-file migrate-to-sport.php
 */

// Ensure this is run in WordPress context
if (!defined('ABSPATH')) {
    die('This script must be run in WordPress context');
}

class CDV_Migrate_To_Sport {

    public static function run() {
        global $wpdb;

        echo "Starting migration from Travel to Sport platform...\n\n";

        // Step 1: Migrate database tables
        self::migrate_tables();

        // Step 2: Migrate post types
        self::migrate_post_types();

        // Step 3: Migrate taxonomies
        self::migrate_taxonomies();

        // Step 4: Migrate meta fields
        self::migrate_meta_fields();

        // Step 5: Update options
        self::update_options();

        // Step 6: Flush rewrite rules
        flush_rewrite_rules();

        echo "\n✅ Migration completed successfully!\n";
        echo "Please verify the data in the admin panel.\n";
    }

    /**
     * Migrate database tables
     */
    private static function migrate_tables() {
        global $wpdb;

        echo "1. Migrating database tables...\n";

        // Rename tables (only if they don't exist yet)
        $tables_to_rename = array(
            'cdv_travel_participants' => 'cdv_activity_participants',
            'cdv_travel_group_messages' => 'cdv_activity_group_messages',
        );

        foreach ($tables_to_rename as $old_table => $new_table) {
            $old_full = $wpdb->prefix . $old_table;
            $new_full = $wpdb->prefix . $new_table;

            // Check if old table exists
            $old_exists = $wpdb->get_var("SHOW TABLES LIKE '$old_full'");
            $new_exists = $wpdb->get_var("SHOW TABLES LIKE '$new_full'");

            if ($old_exists && !$new_exists) {
                $wpdb->query("RENAME TABLE `$old_full` TO `$new_full`");
                echo "  ✓ Renamed $old_table to $new_table\n";
            } elseif ($new_exists) {
                echo "  ℹ Table $new_table already exists, skipping rename\n";
            } else {
                echo "  ⚠ Table $old_table not found, skipping\n";
            }
        }

        // Rename columns in activity_participants
        $participants_table = $wpdb->prefix . 'cdv_activity_participants';
        if ($wpdb->get_var("SHOW TABLES LIKE '$participants_table'")) {
            // Check if travel_id column exists
            $columns = $wpdb->get_results("SHOW COLUMNS FROM `$participants_table` LIKE 'travel_id'");
            if (!empty($columns)) {
                $wpdb->query("ALTER TABLE `$participants_table` CHANGE `travel_id` `activity_id` bigint(20) UNSIGNED NOT NULL");
                echo "  ✓ Renamed column travel_id to activity_id in participants table\n";
            }
        }

        // Rename columns in activity_group_messages
        $messages_table = $wpdb->prefix . 'cdv_activity_group_messages';
        if ($wpdb->get_var("SHOW TABLES LIKE '$messages_table'")) {
            $columns = $wpdb->get_var("SHOW COLUMNS FROM `$messages_table` LIKE 'travel_id'");
            if (!empty($columns)) {
                $wpdb->query("ALTER TABLE `$messages_table` CHANGE `travel_id` `activity_id` bigint(20) UNSIGNED NOT NULL");
                echo "  ✓ Renamed column travel_id to activity_id in messages table\n";
            }
        }

        // Rename columns in reviews table
        $reviews_table = $wpdb->prefix . 'cdv_reviews';
        if ($wpdb->get_var("SHOW TABLES LIKE '$reviews_table'")) {
            $columns = $wpdb->get_var("SHOW COLUMNS FROM `$reviews_table` LIKE 'travel_id'");
            if (!empty($columns)) {
                $wpdb->query("ALTER TABLE `$reviews_table` CHANGE `travel_id` `activity_id` bigint(20) UNSIGNED NOT NULL");
                echo "  ✓ Renamed column travel_id to activity_id in reviews table\n";
            }
        }

        echo "  Database tables migration complete!\n\n";
    }

    /**
     * Migrate post types
     */
    private static function migrate_post_types() {
        global $wpdb;

        echo "2. Migrating post types...\n";

        // Update post type from 'viaggio' to 'attivita'
        $updated = $wpdb->update(
            $wpdb->posts,
            array('post_type' => 'attivita'),
            array('post_type' => 'viaggio'),
            array('%s'),
            array('%s')
        );

        if ($updated !== false) {
            echo "  ✓ Updated $updated posts from 'viaggio' to 'attivita'\n";
        } else {
            echo "  ⚠ Error updating post types or no posts to update\n";
        }

        // Update post type for travel stories
        $updated_stories = $wpdb->update(
            $wpdb->posts,
            array('post_type' => 'storia_sport'),
            array('post_type' => 'racconto'),
            array('%s'),
            array('%s')
        );

        if ($updated_stories !== false && $updated_stories > 0) {
            echo "  ✓ Updated $updated_stories story posts from 'racconto' to 'storia_sport'\n";
        }

        echo "  Post types migration complete!\n\n";
    }

    /**
     * Migrate taxonomies
     */
    private static function migrate_taxonomies() {
        global $wpdb;

        echo "3. Migrating taxonomies...\n";

        // Update taxonomy from 'tipo_viaggio' to 'tipo_sport'
        $updated = $wpdb->update(
            $wpdb->term_taxonomy,
            array('taxonomy' => 'tipo_sport'),
            array('taxonomy' => 'tipo_viaggio'),
            array('%s'),
            array('%s')
        );

        if ($updated !== false) {
            echo "  ✓ Updated $updated taxonomy terms from 'tipo_viaggio' to 'tipo_sport'\n";
        }

        // Update taxonomy from 'destinazione' to 'luogo'
        $updated_dest = $wpdb->update(
            $wpdb->term_taxonomy,
            array('taxonomy' => 'luogo'),
            array('taxonomy' => 'destinazione'),
            array('%s'),
            array('%s')
        );

        if ($updated_dest !== false) {
            echo "  ✓ Updated $updated_dest taxonomy terms from 'destinazione' to 'luogo'\n";
        }

        echo "  Taxonomies migration complete!\n\n";
    }

    /**
     * Migrate meta fields
     */
    private static function migrate_meta_fields() {
        global $wpdb;

        echo "4. Migrating meta fields...\n";

        // Update post meta keys
        $meta_keys_to_update = array(
            'cdv_travel_status' => 'cdv_activity_status',
            'cdv_travel_transport' => 'cdv_activity_transport', // Will be removed later
            'cdv_travel_accommodation' => 'cdv_activity_accommodation', // Will be removed later
            'cdv_travel_difficulty' => 'cdv_activity_level',
            'cdv_travel_month' => 'cdv_activity_month',
        );

        foreach ($meta_keys_to_update as $old_key => $new_key) {
            $updated = $wpdb->update(
                $wpdb->postmeta,
                array('meta_key' => $new_key),
                array('meta_key' => $old_key),
                array('%s'),
                array('%s')
            );

            if ($updated !== false && $updated > 0) {
                echo "  ✓ Updated meta key from '$old_key' to '$new_key' ($updated records)\n";
            }
        }

        // Add new sport-specific meta fields with default values to existing activities
        $activities = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attivita'");

        $defaults_added = 0;
        foreach ($activities as $activity) {
            // Add default activity time (if not exists)
            if (!get_post_meta($activity->ID, 'cdv_activity_time', true)) {
                add_post_meta($activity->ID, 'cdv_activity_time', '10:00', true);
                $defaults_added++;
            }

            // Add default duration (2 hours)
            if (!get_post_meta($activity->ID, 'cdv_activity_duration', true)) {
                add_post_meta($activity->ID, 'cdv_activity_duration', '120', true);
            }

            // Add default equipment required
            if (!get_post_meta($activity->ID, 'cdv_equipment_required', true)) {
                add_post_meta($activity->ID, 'cdv_equipment_required', array(), true);
            }

            // Add default facilities
            if (!get_post_meta($activity->ID, 'cdv_facilities', true)) {
                add_post_meta($activity->ID, 'cdv_facilities', array(), true);
            }
        }

        if ($defaults_added > 0) {
            echo "  ✓ Added default sport-specific meta fields to $defaults_added activities\n";
        }

        echo "  Meta fields migration complete!\n\n";
    }

    /**
     * Update plugin options
     */
    private static function update_options() {
        echo "5. Updating options...\n";

        // Update version
        update_option('cdv_db_version', '2.0.0');
        update_option('cdv_migration_completed', current_time('mysql'));

        echo "  ✓ Updated plugin version to 2.0.0\n";
        echo "  Options update complete!\n\n";
    }
}

// Check if we're in admin or CLI context
if (is_admin() || (defined('WP_CLI') && WP_CLI)) {
    echo "===========================================\n";
    echo "  MIGRATION: Travel → Sport Platform\n";
    echo "===========================================\n\n";

    // Run the migration
    CDV_Migrate_To_Sport::run();

    echo "\n===========================================\n";
    echo "  NEXT STEPS:\n";
    echo "===========================================\n";
    echo "1. Go to Settings → Permalinks and click 'Save Changes'\n";
    echo "2. Clear all caches (if using caching plugins)\n";
    echo "3. Test creating a new activity\n";
    echo "4. Test the frontend pages\n";
    echo "5. Update email templates if needed\n\n";
} else {
    echo "This script can only be run from admin panel or WP-CLI\n";
}
