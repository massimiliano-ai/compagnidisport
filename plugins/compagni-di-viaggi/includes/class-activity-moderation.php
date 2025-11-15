<?php
/**
 * Travel Moderation System
 */

if (!defined('ABSPATH')) {
    exit;
}

class CDV_Activity_Moderation {

    /**
     * Initialize
     */
    public static function init() {
        // Force pending status for new travels
        add_filter('wp_insert_post_data', array(__CLASS__, 'force_pending_status'), 10, 2);

        // Add moderation columns to admin
        add_filter('manage_attivitào_posts_columns', array(__CLASS__, 'add_moderation_column'));
        add_action('manage_attivitào_posts_custom_column', array(__CLASS__, 'moderation_column_content'), 10, 2);

        // Bulk actions
        add_filter('bulk_actions-edit-attivitào', array(__CLASS__, 'add_bulk_actions'));
        add_filter('handle_bulk_actions-edit-attivitào', array(__CLASS__, 'handle_bulk_actions'), 10, 3);

        // Quick approve/reject
        add_action('wp_ajax_cdv_approve_travel', array(__CLASS__, 'ajax_approve_travel'));
        add_action('wp_ajax_cdv_reject_travel', array(__CLASS__, 'ajax_reject_travel'));

        // Admin notices
        add_action('admin_notices', array(__CLASS__, 'pending_travels_notice'));
    }

    /**
     * Force pending status for non-admin users
     */
    public static function force_pending_status($data, $postarr) {
        // Only for attivitào post type
        if ($data['post_type'] !== 'attivita') {
            return $data;
        }

        // Skip for admins
        if (current_user_can('publish_posts')) {
            return $data;
        }

        // Force pending status
        if ($data['post_status'] === 'publish' || $data['post_status'] === 'auto-draft') {
            $data['post_status'] = 'pending';
        }

        return $data;
    }

    /**
     * Add moderation column
     */
    public static function add_moderation_column($columns) {
        $new_columns = array();

        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;

            if ($key === 'title') {
                $new_columns['moderation'] = __('Moderazione', 'compagni-di-sport');
                $new_columns['organizer'] = __('Organizzatore', 'compagni-di-sport');
            }
        }

        return $new_columns;
    }

    /**
     * Moderation column content
     */
    public static function moderation_column_content($column, $post_id) {
        if ($column === 'moderation') {
            $status = get_post_status($post_id);

            if ($status === 'pending') {
                echo '<span class="cdv-pending">⏳ In Attesa</span><br>';
                echo '<button class="button button-small cdv-approve-travel" data-travel-id="' . $post_id . '">✓ Approva</button> ';
                echo '<button class="button button-small cdv-reject-travel" data-travel-id="' . $post_id . '">✗ Rifiuta</button>';
            } elseif ($status === 'publish') {
                echo '<span class="cdv-approved">✓ Approvato</span>';
            } else {
                echo '<span class="cdv-rejected">✗ Rifiutato</span>';
            }
        }

        if ($column === 'organizer') {
            $author_id = get_post_field('post_author', $post_id);
            $author = get_user_by('id', $author_id);
            $approved = CDV_User_Roles::is_user_approved($author_id);

            echo get_avatar($author_id, 32) . ' ';
            echo esc_html($author->display_name);

            if (!$approved) {
                echo ' <span class="cdv-user-pending" title="Utente non approvato">⚠️</span>';
            }
        }
    }

    /**
     * Add bulk actions
     */
    public static function add_bulk_actions($actions) {
        $actions['cdv_approve'] = __('Approva Attività', 'compagni-di-sport');
        $actions['cdv_reject'] = __('Rifiuta Attività', 'compagni-di-sport');
        return $actions;
    }

    /**
     * Handle bulk actions
     */
    public static function handle_bulk_actions($redirect_to, $action, $post_ids) {
        if ($action === 'cdv_approve') {
            foreach ($post_ids as $post_id) {
                self::approve_travel($post_id);
            }

            $redirect_to = add_query_arg('cdv_approved', count($post_ids), $redirect_to);
        }

        if ($action === 'cdv_reject') {
            foreach ($post_ids as $post_id) {
                self::reject_travel($post_id);
            }

            $redirect_to = add_query_arg('cdv_rejected', count($post_ids), $redirect_to);
        }

        return $redirect_to;
    }

    /**
     * AJAX: Approve travel
     */
    public static function ajax_approve_travel() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!current_user_can('approve_attività')) {
            wp_send_json_error(array('message' => 'Permessi insufficienti'));
        }

        $activity_id = intval($_POST['activity_id']);

        if (self::approve_travel($activity_id)) {
            wp_send_json_success(array('message' => 'Attività approvato'));
        } else {
            wp_send_json_error(array('message' => 'Errore durante l\'approvazione'));
        }
    }

    /**
     * AJAX: Reject travel
     */
    public static function ajax_reject_travel() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!current_user_can('approve_attività')) {
            wp_send_json_error(array('message' => 'Permessi insufficienti'));
        }

        $activity_id = intval($_POST['activity_id']);
        $reason = isset($_POST['reason']) ? sanitize_textarea_field($_POST['reason']) : '';

        if (self::reject_travel($activity_id, $reason)) {
            wp_send_json_success(array('message' => 'Attività rifiutato'));
        } else {
            wp_send_json_error(array('message' => 'Errore durante il rifiuto'));
        }
    }

    /**
     * Approve travel
     */
    public static function approve_travel($activity_id) {
        $result = wp_update_post(array(
            'ID' => $activity_id,
            'post_status' => 'publish',
        ));

        if ($result) {
            update_post_meta($activity_id, 'cdv_approved_date', current_time('mysql'));
            update_post_meta($activity_id, 'cdv_activity_status', 'open');

            // Notify author
            self::notify_author_approved($activity_id);

            do_action('cdv_activity_approved', $activity_id);
        }

        return $result;
    }

    /**
     * Reject travel
     */
    public static function reject_travel($activity_id, $reason = '') {
        $result = wp_update_post(array(
            'ID' => $activity_id,
            'post_status' => 'draft',
        ));

        if ($result) {
            update_post_meta($activity_id, 'cdv_rejected_date', current_time('mysql'));

            if ($reason) {
                update_post_meta($activity_id, 'cdv_rejection_reason', $reason);
            }

            // Notify author
            self::notify_author_rejected($activity_id, $reason);

            do_action('cdv_activity_rejected', $activity_id, $reason);
        }

        return $result;
    }

    /**
     * Notify author of approval
     */
    private static function notify_author_approved($activity_id) {
        $post = get_post($activity_id);
        $author = get_user_by('id', $post->post_author);

        $subject = 'Il tuo attivitào è stato approvato!';
        $message = sprintf(
            "Ciao %s,\n\nIl tuo attivitào \"%s\" è stato approvato ed è ora visibile sulla piattaforma!\n\nVedi il attivitào: %s\n\nBuona organizzazione!\nIl team di Compagni di Attività",
            $author->display_name,
            $post->post_title,
            get_permalink($activity_id)
        );

        wp_mail($author->user_email, $subject, $message);
    }

    /**
     * Notify author of rejection
     */
    private static function notify_author_rejected($activity_id, $reason) {
        $post = get_post($activity_id);
        $author = get_user_by('id', $post->post_author);

        $subject = 'Il tuo attivitào non è stato approvato';
        $message = sprintf(
            "Ciao %s,\n\nPurtroppo il tuo attivitào \"%s\" non è stato approvato.\n\n",
            $author->display_name,
            $post->post_title
        );

        if ($reason) {
            $message .= "Motivo: $reason\n\n";
        }

        $message .= "Puoi modificare il attivitào e ripubblicarlo per una nuova valutazione.\n\nModifica qui: " . get_edit_post_link($activity_id, '') . "\n\nIl team di Compagni di Attività";

        wp_mail($author->user_email, $subject, $message);
    }

    /**
     * Admin notice for pending travels
     */
    public static function pending_travels_notice() {
        $screen = get_current_screen();

        if ($screen->id !== 'edit-attivitào') {
            return;
        }

        $pending_count = wp_count_posts('attivita')->pending;

        if ($pending_count > 0) {
            echo '<div class="notice notice-warning">';
            echo '<p><strong>' . $pending_count . ' attività in attesa di approvazione</strong></p>';
            echo '</div>';
        }

        // Bulk action success messages
        if (isset($_GET['cdv_approved'])) {
            $count = intval($_GET['cdv_approved']);
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>' . $count . ' attivitào/i approvato/i con successo.</p>';
            echo '</div>';
        }

        if (isset($_GET['cdv_rejected'])) {
            $count = intval($_GET['cdv_rejected']);
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p>' . $count . ' attivitào/i rifiutato/i.</p>';
            echo '</div>';
        }
    }

    /**
     * Get pending travels count
     */
    public static function get_pending_travels_count() {
        return wp_count_posts('attivita')->pending;
    }

    /**
     * Check if user can publish travel
     */
    public static function can_user_publish_travel($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        // Must be approved user
        if (!CDV_User_Roles::is_user_approved($user_id)) {
            return false;
        }

        return true;
    }
}
