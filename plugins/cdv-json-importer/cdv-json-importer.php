<?php
/**
 * Plugin Name: CDV JSON Activity Importer
 * Plugin URI: https://www.compagnidisport.it
 * Description: Importa annunci di attività sportive da file JSON tramite interfaccia admin
 * Version: 1.0.0
 * Author: Compagni di Sport
 * License: GPL v2 or later
 * Text Domain: cdv-json-importer
 */

if (!defined('ABSPATH')) {
    exit;
}

class CDV_JSON_Activity_Importer {

    /**
     * Initialize plugin
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
        add_action('wp_ajax_cdv_validate_json', array(__CLASS__, 'ajax_validate_json'));
        add_action('wp_ajax_cdv_import_activities', array(__CLASS__, 'ajax_import_activities'));
    }

    /**
     * Add admin menu
     */
    public static function add_admin_menu() {
        add_management_page(
            'Importa Attività da JSON',
            'Importa Attività',
            'manage_options',
            'cdv-json-importer',
            array(__CLASS__, 'admin_page')
        );
    }

    /**
     * Enqueue scripts and styles
     */
    public static function enqueue_scripts($hook) {
        if ($hook !== 'tools_page_cdv-json-importer') {
            return;
        }

        wp_enqueue_style(
            'cdv-json-importer',
            plugin_dir_url(__FILE__) . 'assets/style.css',
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            'cdv-json-importer',
            plugin_dir_url(__FILE__) . 'assets/script.js',
            array('jquery'),
            '1.0.0',
            true
        );

        wp_localize_script('cdv-json-importer', 'cdvImporter', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cdv_importer_nonce'),
        ));
    }

    /**
     * Admin page
     */
    public static function admin_page() {
        ?>
        <div class="wrap cdv-importer-wrap">
            <h1>🚀 Importa Attività Sportive da JSON</h1>
            <p class="description">Carica un file JSON contenente annunci di attività sportive per importarli automaticamente nel database.</p>

            <div class="cdv-importer-container">
                <!-- Step 1: Upload JSON -->
                <div class="cdv-step" id="step-upload">
                    <div class="cdv-card">
                        <h2>📁 Step 1: Carica File JSON</h2>

                        <div class="cdv-upload-area" id="upload-area">
                            <div class="upload-content">
                                <span class="dashicons dashicons-upload"></span>
                                <p><strong>Trascina il file JSON qui</strong> oppure</p>
                                <label for="json-file" class="button button-primary">Scegli File</label>
                                <input type="file" id="json-file" accept=".json" style="display:none;">
                            </div>
                        </div>

                        <div id="file-info" style="display:none;">
                            <div class="file-selected">
                                <span class="dashicons dashicons-media-document"></span>
                                <span id="file-name"></span>
                                <span id="file-size"></span>
                                <button type="button" class="button" id="remove-file">✕ Rimuovi</button>
                            </div>
                            <button type="button" class="button button-primary button-large" id="validate-btn">
                                Valida e Visualizza Anteprima →
                            </button>
                        </div>

                        <div id="upload-progress" style="display:none;">
                            <div class="progress-bar">
                                <div class="progress-fill"></div>
                            </div>
                            <p class="progress-text">Lettura file in corso...</p>
                        </div>
                    </div>

                    <div class="cdv-card cdv-info-card">
                        <h3>ℹ️ Informazioni</h3>
                        <ul>
                            <li><strong>Formato:</strong> File JSON valido</li>
                            <li><strong>Dimensione massima:</strong> 10 MB</li>
                            <li><strong>Struttura:</strong> Array di oggetti annuncio</li>
                            <li><strong>Campi obbligatori:</strong> title, description, destination, dates, budget, level</li>
                        </ul>
                        <p>
                            <a href="<?php echo plugin_dir_url(__FILE__); ?>esempio-annunci-attivita.json" class="button" download>
                                📥 Scarica Esempio JSON
                            </a>
                            <a href="<?php echo plugin_dir_url(__FILE__); ?>ISTRUZIONI-GENERAZIONE-ANNUNCI.md" class="button" target="_blank">
                                📖 Guida Completa
                            </a>
                        </p>
                    </div>
                </div>

                <!-- Step 2: Preview -->
                <div class="cdv-step" id="step-preview" style="display:none;">
                    <div class="cdv-card">
                        <h2>👁️ Step 2: Anteprima Annunci</h2>

                        <div id="validation-results"></div>

                        <div id="preview-summary" style="display:none;">
                            <div class="summary-stats">
                                <div class="stat-box stat-total">
                                    <span class="stat-number" id="total-count">0</span>
                                    <span class="stat-label">Annunci Totali</span>
                                </div>
                                <div class="stat-box stat-valid">
                                    <span class="stat-number" id="valid-count">0</span>
                                    <span class="stat-label">Validi</span>
                                </div>
                                <div class="stat-box stat-errors">
                                    <span class="stat-number" id="error-count">0</span>
                                    <span class="stat-label">Con Errori</span>
                                </div>
                            </div>
                        </div>

                        <div id="preview-content"></div>

                        <div id="preview-actions" style="display:none;">
                            <button type="button" class="button" id="back-to-upload">← Indietro</button>
                            <button type="button" class="button button-primary button-large" id="start-import">
                                ✓ Importa Annunci
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Import Progress -->
                <div class="cdv-step" id="step-import" style="display:none;">
                    <div class="cdv-card">
                        <h2>⚙️ Step 3: Importazione in Corso</h2>

                        <div class="import-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" id="import-progress-fill"></div>
                            </div>
                            <p class="progress-text" id="import-status">Preparazione importazione...</p>
                            <p class="progress-detail" id="import-detail"></p>
                        </div>

                        <div id="import-log"></div>
                    </div>
                </div>

                <!-- Step 4: Complete -->
                <div class="cdv-step" id="step-complete" style="display:none;">
                    <div class="cdv-card">
                        <h2>✅ Importazione Completata!</h2>

                        <div id="import-summary"></div>

                        <div class="complete-actions">
                            <a href="<?php echo admin_url('edit.php?post_type=attivita'); ?>" class="button button-primary button-large">
                                📋 Visualizza Annunci
                            </a>
                            <button type="button" class="button button-large" id="import-more">
                                🔄 Importa Altri Annunci
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX: Validate JSON
     */
    public static function ajax_validate_json() {
        check_ajax_referer('cdv_importer_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permessi insufficienti'));
        }

        if (empty($_POST['json_data'])) {
            wp_send_json_error(array('message' => 'Nessun dato JSON fornito'));
        }

        $json_data = stripslashes($_POST['json_data']);
        $activities = json_decode($json_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(array(
                'message' => 'JSON non valido: ' . json_last_error_msg()
            ));
        }

        if (!is_array($activities)) {
            wp_send_json_error(array(
                'message' => 'Il JSON deve contenere un array di annunci'
            ));
        }

        $validation_results = self::validate_activities($activities);

        wp_send_json_success($validation_results);
    }

    /**
     * AJAX: Import activities
     */
    public static function ajax_import_activities() {
        check_ajax_referer('cdv_importer_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permessi insufficienti'));
        }

        $batch_index = isset($_POST['batch_index']) ? intval($_POST['batch_index']) : 0;
        $activities = json_decode(stripslashes($_POST['activities']), true);

        if (!is_array($activities)) {
            wp_send_json_error(array('message' => 'Dati non validi'));
        }

        // Import batch (5 activities at a time for performance)
        $batch_size = 5;
        $start = $batch_index * $batch_size;
        $batch = array_slice($activities, $start, $batch_size);

        $results = array();
        foreach ($batch as $activity) {
            $results[] = self::import_single_activity($activity);
        }

        $has_more = ($start + $batch_size) < count($activities);

        wp_send_json_success(array(
            'results' => $results,
            'has_more' => $has_more,
            'next_batch' => $batch_index + 1,
            'total' => count($activities),
            'processed' => min($start + $batch_size, count($activities))
        ));
    }

    /**
     * Validate activities array
     */
    private static function validate_activities($activities) {
        $total = count($activities);
        $valid = 0;
        $errors = array();
        $previews = array();

        foreach ($activities as $index => $activity) {
            $activity_errors = self::validate_single_activity($activity);

            if (empty($activity_errors)) {
                $valid++;
            } else {
                $errors[$index] = $activity_errors;
            }

            // Create preview
            $previews[] = array(
                'index' => $index,
                'title' => $activity['activity_title'] ?? 'Senza titolo',
                'destination' => $activity['activity_destination'] ?? '-',
                'level' => $activity['activity_level'] ?? '-',
                'budget' => $activity['activity_budget'] ?? '-',
                'date_type' => $activity['date_type'] ?? '-',
                'valid' => empty($activity_errors),
                'errors' => $activity_errors
            );
        }

        return array(
            'total' => $total,
            'valid' => $valid,
            'invalid' => $total - $valid,
            'errors' => $errors,
            'previews' => $previews
        );
    }

    /**
     * Validate single activity
     */
    private static function validate_single_activity($activity) {
        $errors = array();

        $required_fields = array(
            'activity_title' => 'Titolo',
            'activity_description' => 'Descrizione',
            'activity_destination' => 'Destinazione',
            'activity_country' => 'Paese',
            'date_type' => 'Tipo Data',
            'activity_time' => 'Orario',
            'activity_duration' => 'Durata',
            'activity_budget' => 'Budget',
            'activity_max_participants' => 'Max Partecipanti',
            'activity_level' => 'Livello'
        );

        foreach ($required_fields as $field => $label) {
            if (empty($activity[$field]) && $activity[$field] !== '0') {
                $errors[] = "$label mancante";
            }
        }

        // Validate date type
        if (!empty($activity['date_type'])) {
            if ($activity['date_type'] === 'precise') {
                if (empty($activity['activity_start_date'])) {
                    $errors[] = "Data inizio mancante (richiesta per date precise)";
                }
                if (empty($activity['activity_end_date'])) {
                    $errors[] = "Data fine mancante (richiesta per date precise)";
                }
            } elseif ($activity['date_type'] === 'month') {
                if (empty($activity['activity_month'])) {
                    $errors[] = "Mese mancante (richiesto per date flessibili)";
                }
            } else {
                $errors[] = "Tipo data non valido (solo 'precise' o 'month')";
            }
        }

        // Validate level
        $valid_levels = array('principiante', 'intermedio', 'avanzato', 'esperto');
        if (!empty($activity['activity_level']) && !in_array($activity['activity_level'], $valid_levels)) {
            $errors[] = "Livello non valido";
        }

        return $errors;
    }

    /**
     * Import single activity
     */
    private static function import_single_activity($activity) {
        try {
            // Create post
            $post_data = array(
                'post_type'    => 'attivita',
                'post_title'   => sanitize_text_field($activity['activity_title']),
                'post_content' => wp_kses_post($activity['activity_description']),
                'post_status'  => 'publish',
                'post_author'  => get_current_user_id(),
            );

            $post_id = wp_insert_post($post_data, true);

            if (is_wp_error($post_id)) {
                return array(
                    'success' => false,
                    'title' => $activity['activity_title'],
                    'error' => $post_id->get_error_message()
                );
            }

            // Add meta fields
            $meta_fields = array(
                'cdv_destination' => $activity['activity_destination'],
                'cdv_country' => $activity['activity_country'],
                'cdv_date_type' => $activity['date_type'],
                'cdv_start_date' => $activity['activity_start_date'] ?? null,
                'cdv_end_date' => $activity['activity_end_date'] ?? null,
                'cdv_activity_month' => $activity['activity_month'] ?? null,
                'cdv_activity_time' => $activity['activity_time'],
                'cdv_activity_duration' => $activity['activity_duration'],
                'cdv_budget' => $activity['activity_budget'],
                'cdv_max_participants' => $activity['activity_max_participants'],
                'cdv_activity_level' => $activity['activity_level'],
                'cdv_equipment' => $activity['equipment'] ?? array(),
                'cdv_facilities' => $activity['facilities'] ?? array(),
                'cdv_activity_requirements' => $activity['activity_requirements'] ?? '',
                'cdv_activity_status' => 'open',
            );

            foreach ($meta_fields as $key => $value) {
                if ($value !== null && $value !== '') {
                    update_post_meta($post_id, $key, $value);
                }
            }

            return array(
                'success' => true,
                'title' => $activity['activity_title'],
                'post_id' => $post_id
            );

        } catch (Exception $e) {
            return array(
                'success' => false,
                'title' => $activity['activity_title'] ?? 'Sconosciuto',
                'error' => $e->getMessage()
            );
        }
    }
}

// Initialize plugin
add_action('plugins_loaded', array('CDV_JSON_Activity_Importer', 'init'));
