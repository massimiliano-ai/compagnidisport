<?php
/**
 * Template Name: Modifica Attività
 * Description: Form per modificare un attività esistente
 */

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// Get travel ID from URL
$activity_id = isset($_GET['activity_id']) ? intval($_GET['activity_id']) : 0;

if (!$activity_id) {
    wp_redirect(home_url('/dashboard'));
    exit;
}

// Get travel post
$travel = get_post($activity_id);

if (!$travel || $travel->post_type !== 'attivita') {
    wp_redirect(home_url('/dashboard'));
    exit;
}

// Check if current user is the organizer
$user_id = get_current_user_id();
if ($travel->post_author != $user_id) {
    wp_redirect(home_url('/dashboard'));
    exit;
}

// Get travel meta data
$destination = get_post_meta($activity_id, 'cdv_location', true);
$country = get_post_meta($activity_id, 'cdv_country', true);
$start_date = get_post_meta($activity_id, 'cdv_start_date', true);
$end_date = get_post_meta($activity_id, 'cdv_end_date', true);
$date_type = get_post_meta($activity_id, 'cdv_date_type', true) ?: 'precise';
$activity_month = get_post_meta($activity_id, 'cdv_activity_month', true);
$budget = get_post_meta($activity_id, 'cdv_budget', true);
$max_participants = get_post_meta($activity_id, 'cdv_max_participants', true);

// Optional fields
$transport = get_post_meta($activity_id, 'cdv_activity_transport', true);
$accommodation = get_post_meta($activity_id, 'cdv_activity_accommodation', true);
$difficulty = get_post_meta($activity_id, 'cdv_activity_difficulty', true);
$meals = get_post_meta($activity_id, 'cdv_activity_meals', true);
$guide_type = get_post_meta($activity_id, 'cdv_activity_guide_type', true);
$requirements = get_post_meta($activity_id, 'cdv_activity_requirements', true);

// Get travel types
$activity_types = wp_get_post_terms($activity_id, 'tipo_sport', array('fields' => 'ids'));

get_header();
?>

<main class="site-main">
    <div class="create-travel-page">
        <div class="container">
            <div class="create-travel-wrapper">
                <div class="page-header">
                    <h1>Modifica Annuncio</h1>
                    <p>Aggiorna i dettagli del tuo annuncio</p>
                </div>

                <form id="edit-travel-form" class="travel-form">
                    <input type="hidden" id="activity_id" name="activity_id" value="<?php echo esc_attr($activity_id); ?>">

                    <div class="form-section">
                        <h3>Informazioni Generali</h3>

                        <div class="form-group">
                            <label for="activity_title">Titolo dell'Annuncio <span class="required">*</span></label>
                            <input type="text" id="activity_title" name="activity_title" required placeholder="Es: Partita di calcetto, Escursione in montagna" value="<?php echo esc_attr($travel->post_title); ?>">
                        </div>

                        <div class="form-group">
                            <label for="activity_description">Descrizione <span class="required">*</span></label>
                            <textarea id="activity_description" name="activity_description" rows="6" required placeholder="Descrivi la tua attività sportiva: cosa farete, cosa rende speciale questa esperienza..."><?php echo esc_textarea($travel->post_content); ?></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Luogo</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="activity_destination">Luogo <span class="required">*</span></label>
                                <input type="text" id="activity_destination" name="activity_destination" required placeholder="Es: Venezia, Toscana" value="<?php echo esc_attr($destination); ?>">
                            </div>

                            <div class="form-group">
                                <label for="activity_country_select">Paese <span class="required">*</span></label>
                                <select id="activity_country_select" name="activity_country_select" required>
                                    <option value="">Seleziona un paese</option>

                                    <optgroup label="🇪🇺 Europa">
                                        <?php
                                        $european_countries = array('Italia', 'Francia', 'Spagna', 'Germania', 'Regno Unito', 'Portogallo', 'Grecia', 'Paesi Bassi', 'Svizzera', 'Austria', 'Croazia', 'Irlanda', 'Islanda', 'Norvegia', 'Svezia', 'Danimarca', 'Polonia', 'Repubblica Ceca', 'Ungheria', 'Romania', 'Bulgaria', 'Slovenia', 'Montenegro', 'Albania', 'Serbia', 'Bosnia ed Erzegovina', 'Macedonia del Nord', 'Belgio', 'Lussemburgo', 'Finlandia', 'Estonia', 'Lettonia', 'Lituania', 'Slovacchia', 'Malta', 'Cipro');
                                        foreach ($european_countries as $ec) {
                                            $selected = ($country === $ec) ? 'selected' : '';
                                            echo '<option value="' . esc_attr($ec) . '" ' . $selected . '>' . esc_html($ec) . '</option>';
                                        }
                                        ?>
                                    </optgroup>

                                    <optgroup label="🌍 Africa">
                                        <?php
                                        $african_countries = array('Marocco', 'Egitto', 'Tunisia', 'Sudafrica', 'Kenya', 'Tanzania', 'Madagascar', 'Namibia', 'Botswana', 'Zanzibar', 'Mauritius', 'Seychelles', 'Senegal', 'Etiopia');
                                        foreach ($african_countries as $ac) {
                                            $selected = ($country === $ac) ? 'selected' : '';
                                            echo '<option value="' . esc_attr($ac) . '" ' . $selected . '>' . esc_html($ac) . '</option>';
                                        }
                                        ?>
                                    </optgroup>

                                    <optgroup label="🌏 Asia">
                                        <?php
                                        $asian_countries = array('Giappone', 'Thailandia', 'Vietnam', 'Cina', 'India', 'Indonesia', 'Maldive', 'Sri Lanka', 'Emirati Arabi Uniti', 'Giordania', 'Israele', 'Turchia', 'Cambogia', 'Malesia', 'Singapore', 'Filippine', 'Nepal', 'Corea del Sud', 'Oman', 'Qatar', 'Bali');
                                        foreach ($asian_countries as $asc) {
                                            $selected = ($country === $asc) ? 'selected' : '';
                                            echo '<option value="' . esc_attr($asc) . '" ' . $selected . '>' . esc_html($asc) . '</option>';
                                        }
                                        ?>
                                    </optgroup>

                                    <optgroup label="🌎 Americhe">
                                        <?php
                                        $american_countries = array('Stati Uniti', 'Canada', 'Messico', 'Brasile', 'Argentina', 'Perù', 'Cile', 'Colombia', 'Costa Rica', 'Cuba', 'Repubblica Dominicana', 'Ecuador', 'Bolivia', 'Uruguay', 'Panama', 'Guatemala', 'Nicaragua');
                                        foreach ($american_countries as $amc) {
                                            $selected = ($country === $amc) ? 'selected' : '';
                                            echo '<option value="' . esc_attr($amc) . '" ' . $selected . '>' . esc_html($amc) . '</option>';
                                        }
                                        ?>
                                    </optgroup>

                                    <optgroup label="🌏 Oceania">
                                        <?php
                                        $oceania_countries = array('Australia', 'Nuova Zelanda', 'Polinesia Francese', 'Fiji');
                                        foreach ($oceania_countries as $oc) {
                                            $selected = ($country === $oc) ? 'selected' : '';
                                            echo '<option value="' . esc_attr($oc) . '" ' . $selected . '>' . esc_html($oc) . '</option>';
                                        }
                                        ?>
                                    </optgroup>

                                    <option value="altro" <?php echo ($country && !in_array($country, array_merge($european_countries, $african_countries, $asian_countries, $american_countries, $oceania_countries))) ? 'selected' : ''; ?>>📝 Altro (specifica)</option>
                                </select>

                                <!-- Campo "Altro" che appare quando selezionato -->
                                <input type="text" id="activity_country_other" name="activity_country_other" style="<?php echo ($country && !in_array($country, array_merge($european_countries, $african_countries, $asian_countries, $american_countries, $oceania_countries))) ? '' : 'display: none;'; ?> margin-top: 10px;" placeholder="Specifica il paese" value="<?php echo (!in_array($country, array_merge($european_countries, $african_countries, $asian_countries, $american_countries, $oceania_countries))) ? esc_attr($country) : ''; ?>">

                                <!-- Hidden field che conterrà il valore finale -->
                                <input type="hidden" id="activity_country" name="activity_country" value="<?php echo esc_attr($country); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Quando Partire</h3>

                        <div class="form-group">
                            <label>Tipo di Data <span class="required">*</span></label>
                            <div class="radio-group" style="display: flex; gap: calc(var(--spacing-unit) * 3); margin-bottom: calc(var(--spacing-unit) * 2);">
                                <label style="display: flex; align-items: center; gap: calc(var(--spacing-unit) * 1); cursor: pointer;">
                                    <input type="radio" name="date_type" value="precise" <?php checked($date_type, 'precise'); ?>>
                                    <span>Date precise</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: calc(var(--spacing-unit) * 1); cursor: pointer;">
                                    <input type="radio" name="date_type" value="month" <?php checked($date_type, 'month'); ?>>
                                    <span>Solo mese (date flessibili)</span>
                                </label>
                            </div>
                        </div>

                        <div id="precise-dates-container" style="<?php echo ($date_type === 'month') ? 'display: none;' : ''; ?>">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="activity_start_date">Data Inizio <span class="required">*</span></label>
                                    <input type="date" id="activity_start_date" name="activity_start_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo esc_attr($start_date); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="activity_end_date">Data Fine <span class="required">*</span></label>
                                    <input type="date" id="activity_end_date" name="activity_end_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo esc_attr($end_date); ?>">
                                </div>
                            </div>
                        </div>

                        <div id="month-container" style="<?php echo ($date_type === 'month') ? '' : 'display: none;'; ?>">
                            <div class="form-group">
                                <label for="activity_month">Mese di Partenza <span class="required">*</span></label>
                                <select id="activity_month" name="activity_month">
                                    <option value="">Seleziona il mese</option>
                                    <?php
                                    $months = array(
                                        '01' => 'Gennaio', '02' => 'Febbraio', '03' => 'Marzo',
                                        '04' => 'Aprile', '05' => 'Maggio', '06' => 'Giugno',
                                        '07' => 'Luglio', '08' => 'Agosto', '09' => 'Settembre',
                                        '10' => 'Ottobre', '11' => 'Novembre', '12' => 'Dicembre'
                                    );
                                    $current_month = (int)date('n');
                                    $current_year = (int)date('Y');

                                    // Mostra mesi dell'anno corrente (da questo mese in poi)
                                    for ($i = $current_month; $i <= 12; $i++) {
                                        $month_num = str_pad($i, 2, '0', STR_PAD_LEFT);
                                        $value = $current_year . '-' . $month_num;
                                        $selected = ($activity_month === $value) ? 'selected' : '';
                                        echo '<option value="' . $value . '" ' . $selected . '>' . $months[$month_num] . ' ' . $current_year . '</option>';
                                    }

                                    // Mostra tutti i mesi del prossimo anno
                                    $next_year = $current_year + 1;
                                    foreach ($months as $num => $name) {
                                        $value = $next_year . '-' . $num;
                                        $selected = ($activity_month === $value) ? 'selected' : '';
                                        echo '<option value="' . $value . '" ' . $selected . '>' . $name . ' ' . $next_year . '</option>';
                                    }
                                    ?>
                                </select>
                                <small style="display: block; margin-top: calc(var(--spacing-unit) * 0.5); color: #666;">
                                    L'annuncio sarà disponibile per tutto il mese selezionato (date flessibili)
                                </small>
                            </div>
                        </div>

                        <h3 style="margin-top: calc(var(--spacing-unit) * 4);">Budget</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="activity_budget">Budget per Persona (€) <span class="required">*</span></label>
                                <input type="number" id="activity_budget" name="activity_budget" min="0" required placeholder="500" value="<?php echo esc_attr($budget); ?>">
                            </div>

                            <div class="form-group">
                                <label for="activity_max_participants">Max Partecipanti <span class="required">*</span></label>
                                <input type="number" id="activity_max_participants" name="activity_max_participants" min="2" max="50" required value="<?php echo esc_attr($max_participants); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Tipo di Attività</h3>
                        <div class="checkbox-group">
                            <?php
                            $all_activity_types = get_terms(array(
                                'taxonomy' => 'tipo_sport',
                                'hide_empty' => false,
                            ));
                            if (!empty($all_activity_types) && !is_wp_error($all_activity_types)) :
                                foreach ($all_activity_types as $type) :
                                    $checked = in_array($type->term_id, $activity_types) ? 'checked' : '';
                            ?>
                                <label>
                                    <input type="checkbox" name="activity_types[]" value="<?php echo esc_attr($type->term_id); ?>" <?php echo $checked; ?>>
                                    <?php echo esc_html($type->name); ?>
                                </label>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Dettagli Aggiuntivi <span style="font-weight: normal; font-size: 0.9rem; color: var(--text-medium);">(Facoltativi)</span></h3>
                        <p style="color: var(--text-medium); margin-bottom: calc(var(--spacing-unit) * 3);">Questi dettagli aiutano i viaggiatori a capire meglio l'attività</p>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="activity_transport">🚗 Mezzi di Trasporto</label>
                                <div class="checkbox-group" style="grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));">
                                    <?php
                                    $transport_options = array(
                                        'aereo' => '✈️ Aereo',
                                        'treno' => '🚂 Treno',
                                        'bus' => '🚌 Bus',
                                        'auto_propria' => '🚗 Auto propria',
                                        'auto_noleggio' => '🚙 Auto a noleggio',
                                        'nave' => '🚢 Nave/Traghetto'
                                    );
                                    $transport_array = is_array($transport) ? $transport : array();
                                    foreach ($transport_options as $value => $label) :
                                        $checked = in_array($value, $transport_array) ? 'checked' : '';
                                    ?>
                                    <label>
                                        <input type="checkbox" name="activity_transport[]" value="<?php echo esc_attr($value); ?>" <?php echo $checked; ?>>
                                        <?php echo esc_html($label); ?>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="activity_accommodation">🏨 Tipologia Alloggio</label>
                                <select id="activity_accommodation" name="activity_accommodation">
                                    <option value="">Non specificato</option>
                                    <?php
                                    $accommodation_options = array(
                                        'hotel' => 'Hotel',
                                        'ostello' => 'Ostello',
                                        'bb' => 'B&B',
                                        'airbnb' => 'Airbnb/Casa vacanze',
                                        'camping' => 'Camping/Tenda',
                                        'rifugio' => 'Rifugio',
                                        'misto' => 'Misto',
                                        'altro' => 'Altro'
                                    );
                                    foreach ($accommodation_options as $value => $label) {
                                        $selected = ($accommodation === $value) ? 'selected' : '';
                                        echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="activity_difficulty">📈 Livello di Difficoltà</label>
                                <select id="activity_difficulty" name="activity_difficulty">
                                    <option value="">Non specificato</option>
                                    <?php
                                    $difficulty_options = array(
                                        'facile' => 'Facile - Per tutti',
                                        'moderato' => 'Moderato - Serve minima preparazione',
                                        'impegnativo' => 'Impegnativo - Richiede buona forma fisica',
                                        'molto_impegnativo' => 'Molto impegnativo - Solo esperti'
                                    );
                                    foreach ($difficulty_options as $value => $label) {
                                        $selected = ($difficulty === $value) ? 'selected' : '';
                                        echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="activity_meals">🍽️ Pasti</label>
                                <select id="activity_meals" name="activity_meals">
                                    <option value="">Non specificato</option>
                                    <?php
                                    $meals_options = array(
                                        'non_inclusi' => 'Non inclusi',
                                        'colazione' => 'Solo colazione inclusa',
                                        'mezza_pensione' => 'Mezza pensione',
                                        'pensione_completa' => 'Pensione completa'
                                    );
                                    foreach ($meals_options as $value => $label) {
                                        $selected = ($meals === $value) ? 'selected' : '';
                                        echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="activity_guide_type">👥 Organizzazione</label>
                                <select id="activity_guide_type" name="activity_guide_type">
                                    <option value="">Non specificato</option>
                                    <?php
                                    $guide_options = array(
                                        'autonomo' => 'Attività autonomo',
                                        'guida_locale' => 'Con guida locale',
                                        'tour_organizzato' => 'Tour organizzato'
                                    );
                                    foreach ($guide_options as $value => $label) {
                                        $selected = ($guide_type === $value) ? 'selected' : '';
                                        echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="activity_requirements">📝 Requisiti e Note Particolari</label>
                            <textarea id="activity_requirements" name="activity_requirements" rows="4" placeholder="Es: Documenti necessari (visto, passaporto), vaccinazioni richieste, equipaggiamento speciale, requisiti fisici specifici..."><?php echo esc_textarea($requirements); ?></textarea>
                            <small style="display: block; margin-top: 8px; color: #666;">Inserisci qui eventuali requisiti particolari, documenti necessari o informazioni importanti per i partecipanti</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="<?php echo esc_url(get_permalink($activity_id)); ?>" class="btn-secondary">Annulla</a>
                        <button type="submit" class="btn-primary btn-large">Aggiorna Annuncio 💾</button>
                    </div>

                    <div id="form-messages" style="margin-top: 20px;"></div>
                </form>
            </div>
        </div>
    </div>
</main>

<style>
.create-travel-page {
    padding: calc(var(--spacing-unit) * 6) 0;
    background: var(--bg-light);
    min-height: 80vh;
}

.create-travel-wrapper {
    max-width: 900px;
    margin: 0 auto;
    background: white;
    padding: calc(var(--spacing-unit) * 6);
    border-radius: 12px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
}

.page-header {
    text-align: center;
    margin-bottom: calc(var(--spacing-unit) * 6);
}

.page-header h1 {
    margin-bottom: calc(var(--spacing-unit) * 2);
    color: var(--primary-color);
}

.page-header p {
    font-size: 1.1rem;
    color: var(--text-medium);
}

.travel-form .form-section {
    margin-bottom: calc(var(--spacing-unit) * 5);
    padding-bottom: calc(var(--spacing-unit) * 5);
    border-bottom: 1px solid var(--border-color);
}

.travel-form .form-section:last-of-type {
    border-bottom: none;
    padding-bottom: 0;
}

.travel-form .form-section h3 {
    color: var(--text-dark);
    margin-bottom: calc(var(--spacing-unit) * 3);
    font-size: 1.3rem;
}

.required {
    color: var(--error-color);
}

.checkbox-group {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: calc(var(--spacing-unit) * 2);
}

.checkbox-group label {
    display: flex;
    align-items: center;
    gap: calc(var(--spacing-unit) * 1);
    padding: calc(var(--spacing-unit) * 1.5);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s;
}

.checkbox-group label:hover {
    border-color: var(--primary-color);
    background: rgba(var(--primary-rgb), 0.05);
}

.checkbox-group input[type="checkbox"] {
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: calc(var(--spacing-unit) * 2);
    justify-content: center;
    margin-top: calc(var(--spacing-unit) * 4);
}

.success-message {
    background: var(--success-color);
    color: white;
    padding: calc(var(--spacing-unit) * 3);
    border-radius: 8px;
    text-align: center;
}

.error-message {
    background: var(--error-color);
    color: white;
    padding: calc(var(--spacing-unit) * 3);
    border-radius: 8px;
    text-align: center;
}

.info-message {
    background: #3498db;
    color: white;
    padding: calc(var(--spacing-unit) * 3);
    border-radius: 8px;
    text-align: center;
}

.warning-message {
    background: #f39c12;
    color: white;
    padding: calc(var(--spacing-unit) * 3);
    border-radius: 8px;
    text-align: center;
}

@media (max-width: 768px) {
    .create-travel-wrapper {
        padding: calc(var(--spacing-unit) * 4);
    }

    .checkbox-group {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Toggle between precise dates and month
    $('input[name="date_type"]').on('change', function() {
        const dateType = $(this).val();

        if (dateType === 'precise') {
            $('#precise-dates-container').show();
            $('#month-container').hide();
            $('#activity_start_date').prop('required', true);
            $('#activity_end_date').prop('required', true);
            $('#activity_month').prop('required', false);
        } else {
            $('#precise-dates-container').hide();
            $('#month-container').show();
            $('#activity_start_date').prop('required', false);
            $('#activity_end_date').prop('required', false);
            $('#activity_month').prop('required', true);
        }
    });

    // Update end date min when start date changes
    $('#activity_start_date').on('change', function() {
        const startDate = $(this).val();
        $('#activity_end_date').attr('min', startDate);
    });

    // Handle country select with "Altro" option
    $('#activity_country_select').on('change', function() {
        const selectedValue = $(this).val();
        const $otherField = $('#activity_country_other');
        const $hiddenField = $('#activity_country');

        if (selectedValue === 'altro') {
            // Show the "other" text field
            $otherField.show().prop('required', true).focus();
            $hiddenField.val(''); // Clear hidden field
        } else {
            // Hide the "other" text field and set hidden field value
            $otherField.hide().prop('required', false).val('');
            $hiddenField.val(selectedValue);
        }
    });

    // Update hidden field when "other" text field changes
    $('#activity_country_other').on('input', function() {
        $('#activity_country').val($(this).val());
    });

    // Initialize on page load
    $('#activity_country_select').trigger('change');

    $('#edit-travel-form').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        const $messages = $('#form-messages');

        const dateType = $('input[name="date_type"]:checked').val();
        let dataToSend = {
            action: 'cdv_update_travel',
            nonce: cdvAjax.nonce,
            activity_id: $('#activity_id').val(),
            title: $('#activity_title').val(),
            description: $('#activity_description').val(),
            destination: $('#activity_destination').val(),
            country: $('#activity_country').val(),
            budget: $('#activity_budget').val(),
            max_participants: $('#activity_max_participants').val(),
            activity_types: [],
            activity_transport: [],
            activity_accommodation: $('#activity_accommodation').val(),
            activity_difficulty: $('#activity_difficulty').val(),
            activity_meals: $('#activity_meals').val(),
            activity_guide_type: $('#activity_guide_type').val(),
            activity_requirements: $('#activity_requirements').val()
        };

        // Get travel types
        $('input[name="activity_types[]"]:checked').each(function() {
            dataToSend.activity_types.push($(this).val());
        });

        // Get travel transport methods
        $('input[name="activity_transport[]"]:checked').each(function() {
            dataToSend.activity_transport.push($(this).val());
        });

        // Add date info based on type
        if (dateType === 'precise') {
            const startDate = $('#activity_start_date').val();
            const endDate = $('#activity_end_date').val();

            if (!startDate || !endDate) {
                $messages.html('<div class="error-message">Inserisci sia la data di inizio che di fine.</div>');
                return;
            }

            if (new Date(endDate) <= new Date(startDate)) {
                $messages.html('<div class="error-message">La data di fine deve essere successiva alla data di inizio.</div>');
                return;
            }

            dataToSend.start_date = startDate;
            dataToSend.end_date = endDate;
            dataToSend.date_type = 'precise';
        } else {
            const monthValue = $('#activity_month').val();

            if (!monthValue) {
                $messages.html('<div class="error-message">Seleziona il mese di partenza.</div>');
                return;
            }

            dataToSend.activity_month = monthValue;
            dataToSend.date_type = 'month';
        }

        // Disable submit button
        $submitBtn.prop('disabled', true).text('Aggiornamento in corso...');

        $.ajax({
            url: cdvAjax.ajaxurl,
            type: 'POST',
            data: dataToSend,
            success: function(response) {
                if (response.success) {
                    $messages.html('<div class="success-message">' + response.data.message + '</div>');

                    // Redirect to the travel page after 1 second
                    setTimeout(function() {
                        window.location.href = response.data.redirect_url;
                    }, 1000);
                } else {
                    $messages.html('<div class="error-message">' + response.data.message + '</div>');
                    $submitBtn.prop('disabled', false).text('Aggiorna Attività 💾');
                }
            },
            error: function() {
                $messages.html('<div class="error-message">Si è verificato un errore. Riprova più tardi.</div>');
                $submitBtn.prop('disabled', false).text('Aggiorna Attività 💾');
            }
        });
    });
});
</script>

<?php
get_footer();
