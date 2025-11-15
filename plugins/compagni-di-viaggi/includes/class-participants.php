<?php
/**
 * Travel participants management
 */

if (!defined('ABSPATH')) {
    exit;
}

class CDV_Participants {

    /**
     * Initialize
     */
    public static function init() {
        // Hooks will be added here
    }

    /**
     * Request to join a travel
     */
    public static function request_join($activity_id, $user_id, $message = '') {
        global $wpdb;

        $table = $wpdb->prefix . 'cdv_activity_participants';

        // Check if already requested
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE activity_id = %d AND user_id = %d",
            $activity_id,
            $user_id
        ));

        if ($existing) {
            return new WP_Error('already_requested', __('Hai già richiesto di partecipare a questo attivitào', 'compagni-di-sport'));
        }

        // Check if travel is full
        if (self::is_activity_full($activity_id)) {
            return new WP_Error('travel_full', __('Questo attivitào ha raggiunto il numero massimo di partecipanti', 'compagni-di-sport'));
        }

        // Check if travel is still open
        $status = get_post_meta($activity_id, 'cdv_activity_status', true);
        if ($status === 'completed' || $status === 'cancelled') {
            return new WP_Error('travel_closed', __('Questo attivitào non è più disponibile', 'compagni-di-sport'));
        }

        $result = $wpdb->insert(
            $table,
            array(
                'activity_id' => $activity_id,
                'user_id' => $user_id,
                'status' => 'pending',
                'message' => sanitize_textarea_field($message),
            ),
            array('%d', '%d', '%s', '%s')
        );

        if ($result) {
            // Get travel organizer
            $activity = get_post($activity_id);
            $organizer_id = $activity->post_author;

            // Create initial private message with the request
            if (!empty($message) && class_exists('CDV_Private_Messages')) {
                $formatted_message = "📋 Richiesta di Partecipazione:\n\n" . $message;
                CDV_Private_Messages::send_message($user_id, $organizer_id, $activity_id, $formatted_message);
            }

            // Notify organizer
            self::notify_organizer($activity_id, $user_id);
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * Accept participant
     */
    public static function accept_participant($activity_id, $user_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'cdv_activity_participants';

        // Check if travel is full
        if (self::is_activity_full($activity_id)) {
            return new WP_Error('travel_full', __('Il attivitào ha raggiunto il numero massimo di partecipanti', 'compagni-di-sport'));
        }

        $result = $wpdb->update(
            $table,
            array('status' => 'accepted'),
            array('activity_id' => $activity_id, 'user_id' => $user_id),
            array('%s'),
            array('%d', '%d')
        );

        if ($result) {
            // Create chat group if it doesn't exist
            $chat_group = CDV_Chat::get_chat_group($activity_id);
            if (!$chat_group) {
                CDV_Chat::create_chat_group($activity_id);
            }

            // Notify user
            self::notify_participant($activity_id, $user_id, 'accepted');
            return true;
        }

        return false;
    }

    /**
     * Reject participant
     */
    public static function reject_participant($activity_id, $user_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'cdv_activity_participants';

        $result = $wpdb->update(
            $table,
            array('status' => 'rejected'),
            array('activity_id' => $activity_id, 'user_id' => $user_id),
            array('%s'),
            array('%d', '%d')
        );

        if ($result) {
            // Notify user
            self::notify_participant($activity_id, $user_id, 'rejected');
            return true;
        }

        return false;
    }

    /**
     * Get participants for a travel
     */
    public static function get_participants($activity_id, $status = null) {
        global $wpdb;

        $table = $wpdb->prefix . 'cdv_activity_participants';

        if ($status) {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE activity_id = %d AND status = %s ORDER BY requested_at ASC",
                $activity_id,
                $status
            ));
        } else {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE activity_id = %d ORDER BY requested_at ASC",
                $activity_id
            ));
        }
    }

    /**
     * Check if user is a participant
     */
    public static function is_participant($activity_id, $user_id, $status = null) {
        global $wpdb;

        $table = $wpdb->prefix . 'cdv_activity_participants';

        if ($status) {
            $result = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE activity_id = %d AND user_id = %d AND status = %s",
                $activity_id,
                $user_id,
                $status
            ));
        } else {
            $result = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE activity_id = %d AND user_id = %d",
                $activity_id,
                $user_id
            ));
        }

        return !is_null($result);
    }

    /**
     * Check if travel is full
     */
    public static function is_activity_full($activity_id) {
        $max_participants = get_post_meta($activity_id, 'cdv_max_participants', true);

        if (empty($max_participants) || $max_participants == 0) {
            return false;
        }

        $accepted_count = count(self::get_participants($activity_id, 'accepted'));

        return $accepted_count >= $max_participants;
    }

    /**
     * Get participant count
     */
    public static function get_participant_count($activity_id, $status = 'accepted') {
        return count(self::get_participants($activity_id, $status));
    }

    /**
     * Notify organizer of new request
     */
    private static function notify_organizer($activity_id, $user_id) {
        $activity = get_post($activity_id);
        $organizer = get_user_by('id', $activity->post_author);
        $user = get_user_by('id', $user_id);

        // You can implement email notification here
        // For now, we'll just add a WordPress notification
        do_action('cdv_participant_requested', $activity_id, $user_id, $organizer->ID);
    }

    /**
     * Notify participant of status change
     */
    private static function notify_participant($activity_id, $user_id, $status) {
        // You can implement email notification here
        do_action('cdv_participant_status_changed', $activity_id, $user_id, $status);
    }

    /**
     * Remove participant
     */
    public static function remove_participant($activity_id, $user_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'cdv_activity_participants';

        return $wpdb->delete(
            $table,
            array('activity_id' => $activity_id, 'user_id' => $user_id),
            array('%d', '%d')
        );
    }
}
