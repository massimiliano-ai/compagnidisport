<?php
/**
 * Group Chat functionality for travels
 */

if (!defined('ABSPATH')) {
    exit;
}

class CDV_Group_Chat {

    /**
     * Check if user is participant (accepted or organizer) of a travel
     */
    public static function is_participant($activity_id, $user_id) {
        global $wpdb;

        // Check if user is the travel author (organizer)
        $post = get_post($activity_id);
        if ($post && $post->post_author == $user_id) {
            return true;
        }

        // Check if user is an accepted participant
        $table = $wpdb->prefix . 'cdv_activity_participants';
        $is_participant = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table
            WHERE activity_id = %d
            AND user_id = %d
            AND (status = 'accepted' OR is_organizer = 1)",
            $activity_id,
            $user_id
        ));

        return $is_participant > 0;
    }

    /**
     * Get all messages for a travel
     */
    public static function get_messages($activity_id, $limit = 50) {
        global $wpdb;
        $table = $wpdb->prefix . 'cdv_activity_group_messages';

        $messages = $wpdb->get_results($wpdb->prepare(
            "SELECT m.*, u.display_name, u.user_login
            FROM $table m
            LEFT JOIN {$wpdb->users} u ON m.user_id = u.ID
            WHERE m.activity_id = %d
            ORDER BY m.created_at DESC
            LIMIT %d",
            $activity_id,
            $limit
        ));

        // Reverse to show oldest first
        return array_reverse($messages);
    }

    /**
     * Send a message to group chat
     */
    public static function send_message($activity_id, $user_id, $message) {
        global $wpdb;

        // Check if user is participant
        if (!self::is_participant($activity_id, $user_id)) {
            return new WP_Error('not_participant', 'Non sei un partecipante di questo attivitào');
        }

        // Sanitize message
        $message = wp_kses_post($message);

        if (empty(trim($message))) {
            return new WP_Error('empty_message', 'Il messaggio non può essere vuoto');
        }

        $table = $wpdb->prefix . 'cdv_activity_group_messages';

        $inserted = $wpdb->insert(
            $table,
            array(
                'activity_id' => $activity_id,
                'user_id' => $user_id,
                'message' => $message,
                'created_at' => current_time('mysql'),
            ),
            array('%d', '%d', '%s', '%s')
        );

        if ($inserted === false) {
            return new WP_Error('db_error', 'Errore durante l\'invio del messaggio');
        }

        return $wpdb->insert_id;
    }

    /**
     * Get participants count for a travel
     */
    public static function get_participants_count($activity_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'cdv_activity_participants';

        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table
            WHERE activity_id = %d
            AND (status = 'accepted' OR is_organizer = 1)",
            $activity_id
        ));
    }

    /**
     * Get participants list for a travel
     */
    public static function get_participants($activity_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'cdv_activity_participants';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT p.user_id, p.is_organizer, u.display_name, u.user_login
            FROM $table p
            LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
            WHERE p.activity_id = %d
            AND (p.status = 'accepted' OR p.is_organizer = 1)
            ORDER BY p.is_organizer DESC, u.display_name ASC",
            $activity_id
        ));
    }

    /**
     * Format messages for display
     */
    public static function format_messages($messages, $current_user_id) {
        $formatted = array();

        foreach ($messages as $msg) {
            $formatted[] = array(
                'id' => $msg->id,
                'user_id' => $msg->user_id,
                'user_name' => $msg->display_name,
                'user_login' => $msg->user_login,
                'message' => nl2br(esc_html($msg->message)),
                'created_at' => $msg->created_at,
                'time_ago' => human_time_diff(strtotime($msg->created_at), current_time('timestamp')) . ' fa',
                'is_own' => ($msg->user_id == $current_user_id),
                'avatar' => get_avatar($msg->user_id, 40),
            );
        }

        return $formatted;
    }
}
