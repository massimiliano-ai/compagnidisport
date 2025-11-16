<?php
/**
 * Fix Page Slugs - Admin Tool
 *
 * Temporary admin tool to fix page slugs with accents.
 * Access: WordPress Admin → Tools → Fix Slugs
 */

if (!defined('ABSPATH')) {
    exit;
}

class CDV_Fix_Slugs {

    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_menu'));
    }

    public static function add_menu() {
        add_management_page(
            'Fix Page Slugs',
            'Fix Slugs',
            'manage_options',
            'cdv-fix-slugs',
            array(__CLASS__, 'admin_page')
        );
    }

    public static function admin_page() {
        $fixed = array();

        if (isset($_POST['fix_slugs']) && check_admin_referer('cdv_fix_slugs')) {
            $fixed = self::fix_all_slugs();
        }

        ?>
        <div class="wrap">
            <h1>Fix Page Slugs</h1>

            <div class="card" style="max-width: 800px;">
                <h2>Correggi Slug delle Pagine</h2>
                <p>Questo strumento rimuove gli accenti dagli slug delle pagine per evitare problemi con gli URL.</p>

                <?php if (!empty($fixed)) : ?>
                    <div class="notice notice-success" style="margin-top: 20px;">
                        <p><strong>✓ Slug corretti con successo!</strong></p>
                        <ul>
                            <?php foreach ($fixed as $item) : ?>
                                <li>
                                    <strong><?php echo esc_html($item['title']); ?></strong><br>
                                    <code><?php echo esc_html($item['old']); ?></code> → <code><?php echo esc_html($item['new']); ?></code>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p>
                            <strong>⚠️ Importante:</strong>
                            Vai su <a href="<?php echo admin_url('options-permalink.php'); ?>">Impostazioni → Permalink</a>
                            e clicca "Salva Modifiche" per aggiornare le regole di riscrittura.
                        </p>
                    </div>
                <?php else : ?>
                    <form method="post" action="">
                        <?php wp_nonce_field('cdv_fix_slugs'); ?>

                        <h3>Pagine che saranno corrette:</h3>
                        <?php
                        $pages_to_fix = self::get_pages_to_fix();
                        if (empty($pages_to_fix)) {
                            echo '<p><strong>✓ Nessuna pagina da correggere.</strong> Tutti gli slug sono già corretti.</p>';
                        } else {
                            echo '<ul>';
                            foreach ($pages_to_fix as $page) {
                                $new_slug = remove_accents($page->post_name);
                                $new_slug = sanitize_title($new_slug);
                                echo '<li>';
                                echo '<strong>' . esc_html($page->post_title) . '</strong><br>';
                                echo '<code>' . esc_html($page->post_name) . '</code> → <code>' . esc_html($new_slug) . '</code>';
                                echo '</li>';
                            }
                            echo '</ul>';

                            echo '<p><button type="submit" name="fix_slugs" class="button button-primary button-large">Correggi Slug</button></p>';
                        }
                        ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    private static function get_pages_to_fix() {
        $pages = get_posts(array(
            'post_type' => 'page',
            'posts_per_page' => -1,
            'post_status' => 'any'
        ));

        $to_fix = array();

        foreach ($pages as $page) {
            $old_slug = $page->post_name;
            $new_slug = remove_accents($old_slug);
            $new_slug = sanitize_title($new_slug);

            // Check if slug needs fixing (has accents or encoded characters)
            if ($old_slug !== $new_slug || strpos($old_slug, '%') !== false) {
                $to_fix[] = $page;
            }
        }

        return $to_fix;
    }

    private static function fix_all_slugs() {
        $fixed = array();
        $pages = self::get_pages_to_fix();

        foreach ($pages as $page) {
            $old_slug = $page->post_name;
            $new_slug = remove_accents($old_slug);
            $new_slug = sanitize_title($new_slug);

            wp_update_post(array(
                'ID' => $page->ID,
                'post_name' => $new_slug
            ));

            $fixed[] = array(
                'title' => $page->post_title,
                'old' => $old_slug,
                'new' => $new_slug
            );
        }

        // Flush rewrite rules
        flush_rewrite_rules();

        return $fixed;
    }
}

// Initialize only in admin
if (is_admin()) {
    CDV_Fix_Slugs::init();
}
