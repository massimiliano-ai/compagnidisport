<?php
/**
 * Template Name: Crea Attività
 * Description: Form per creare un nuovo attività
 */

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// Check if user has capability to create travels
$user_id = get_current_user_id();
if (!current_user_can('create_attivita')) {
    wp_redirect(home_url('/dashboard'));
    exit;
}

get_header();
?>

<main class="site-main">
    <div class="create-travel-page">
        <div class="container">
            <div class="create-travel-wrapper">
                <div class="page-header">
                    <h1>Crea un Nuovo Annuncio</h1>
                    <p>Compila il form per proporre la tua attività sportiva e trovare compagni!</p>
                </div>

                <form id="create-travel-form" class="travel-form">
                    <div class="form-section">
                        <h3>Informazioni Generali</h3>

                        <div class="form-group">
                            <label for="activity_title">Titolo dell'Annuncio <span class="required">*</span></label>
                            <input type="text" id="activity_title" name="activity_title" required placeholder="Es: Partita di calcetto, Escursione in montagna">
                        </div>

                        <div class="form-group">
                            <label for="activity_description">Descrizione <span class="required">*</span></label>
                            <textarea id="activity_description" name="activity_description" rows="6" required placeholder="Descrivi la tua attività sportiva: cosa farete, cosa rende speciale questa esperienza..."></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Luogo</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="activity_destination">Luogo <span class="required">*</span></label>
                                <input type="text" id="activity_destination" name="activity_destination" required placeholder="Es: Venezia, Toscana">
                            </div>

                            <div class="form-group">
                                <label for="activity_country_select">Paese <span class="required">*</span></label>
                                <select id="activity_country_select" name="activity_country_select" required>
                                    <option value="">Seleziona un paese</option>

                                    <optgroup label="🇪🇺 Europa">
                                        <option value="Italia">Italia</option>
                                        <option value="Francia">Francia</option>
                                        <option value="Spagna">Spagna</option>
                                        <option value="Germania">Germania</option>
                                        <option value="Regno Unito">Regno Unito</option>
                                        <option value="Portogallo">Portogallo</option>
                                        <option value="Grecia">Grecia</option>
                                        <option value="Paesi Bassi">Paesi Bassi</option>
                                        <option value="Svizzera">Svizzera</option>
                                        <option value="Austria">Austria</option>
                                        <option value="Croazia">Croazia</option>
                                        <option value="Irlanda">Irlanda</option>
                                        <option value="Islanda">Islanda</option>
                                        <option value="Norvegia">Norvegia</option>
                                        <option value="Svezia">Svezia</option>
                                        <option value="Danimarca">Danimarca</option>
                                        <option value="Polonia">Polonia</option>
                                        <option value="Repubblica Ceca">Repubblica Ceca</option>
                                        <option value="Ungheria">Ungheria</option>
                                        <option value="Romania">Romania</option>
                                        <option value="Bulgaria">Bulgaria</option>
                                        <option value="Slovenia">Slovenia</option>
                                        <option value="Montenegro">Montenegro</option>
                                        <option value="Albania">Albania</option>
                                        <option value="Serbia">Serbia</option>
                                        <option value="Bosnia ed Erzegovina">Bosnia ed Erzegovina</option>
                                        <option value="Macedonia del Nord">Macedonia del Nord</option>
                                        <option value="Belgio">Belgio</option>
                                        <option value="Lussemburgo">Lussemburgo</option>
                                        <option value="Finlandia">Finlandia</option>
                                        <option value="Estonia">Estonia</option>
                                        <option value="Lettonia">Lettonia</option>
                                        <option value="Lituania">Lituania</option>
                                        <option value="Slovacchia">Slovacchia</option>
                                        <option value="Malta">Malta</option>
                                        <option value="Cipro">Cipro</option>
                                    </optgroup>

                                    <optgroup label="🌍 Africa">
                                        <option value="Marocco">Marocco</option>
                                        <option value="Egitto">Egitto</option>
                                        <option value="Tunisia">Tunisia</option>
                                        <option value="Sudafrica">Sudafrica</option>
                                        <option value="Kenya">Kenya</option>
                                        <option value="Tanzania">Tanzania</option>
                                        <option value="Madagascar">Madagascar</option>
                                        <option value="Namibia">Namibia</option>
                                        <option value="Botswana">Botswana</option>
                                        <option value="Zanzibar">Zanzibar</option>
                                        <option value="Mauritius">Mauritius</option>
                                        <option value="Seychelles">Seychelles</option>
                                        <option value="Senegal">Senegal</option>
                                        <option value="Etiopia">Etiopia</option>
                                    </optgroup>

                                    <optgroup label="🌏 Asia">
                                        <option value="Giappone">Giappone</option>
                                        <option value="Thailandia">Thailandia</option>
                                        <option value="Vietnam">Vietnam</option>
                                        <option value="Cina">Cina</option>
                                        <option value="India">India</option>
                                        <option value="Indonesia">Indonesia</option>
                                        <option value="Maldive">Maldive</option>
                                        <option value="Sri Lanka">Sri Lanka</option>
                                        <option value="Emirati Arabi Uniti">Emirati Arabi Uniti</option>
                                        <option value="Giordania">Giordania</option>
                                        <option value="Israele">Israele</option>
                                        <option value="Turchia">Turchia</option>
                                        <option value="Cambogia">Cambogia</option>
                                        <option value="Malesia">Malesia</option>
                                        <option value="Singapore">Singapore</option>
                                        <option value="Filippine">Filippine</option>
                                        <option value="Nepal">Nepal</option>
                                        <option value="Corea del Sud">Corea del Sud</option>
                                        <option value="Oman">Oman</option>
                                        <option value="Qatar">Qatar</option>
                                        <option value="Bali">Bali</option>
                                    </optgroup>

                                    <optgroup label="🌎 Americhe">
                                        <option value="Stati Uniti">Stati Uniti</option>
                                        <option value="Canada">Canada</option>
                                        <option value="Messico">Messico</option>
                                        <option value="Brasile">Brasile</option>
                                        <option value="Argentina">Argentina</option>
                                        <option value="Perù">Perù</option>
                                        <option value="Cile">Cile</option>
                                        <option value="Colombia">Colombia</option>
                                        <option value="Costa Rica">Costa Rica</option>
                                        <option value="Cuba">Cuba</option>
                                        <option value="Repubblica Dominicana">Repubblica Dominicana</option>
                                        <option value="Ecuador">Ecuador</option>
                                        <option value="Bolivia">Bolivia</option>
                                        <option value="Uruguay">Uruguay</option>
                                        <option value="Panama">Panama</option>
                                        <option value="Guatemala">Guatemala</option>
                                        <option value="Nicaragua">Nicaragua</option>
                                    </optgroup>

                                    <optgroup label="🌏 Oceania">
                                        <option value="Australia">Australia</option>
                                        <option value="Nuova Zelanda">Nuova Zelanda</option>
                                        <option value="Polinesia Francese">Polinesia Francese</option>
                                        <option value="Fiji">Fiji</option>
                                    </optgroup>

                                    <option value="altro">📝 Altro (specifica)</option>
                                </select>

                                <!-- Campo "Altro" che appare quando selezionato -->
                                <input type="text" id="activity_country_other" name="activity_country_other" style="display: none; margin-top: 10px;" placeholder="Specifica il paese">

                                <!-- Hidden field che conterrà il valore finale -->
                                <input type="hidden" id="activity_country" name="activity_country">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Quando Partire</h3>

                        <div class="form-group">
                            <label>Tipo di Data <span class="required">*</span></label>
                            <div class="radio-group" style="display: flex; gap: calc(var(--spacing-unit) * 3); margin-bottom: calc(var(--spacing-unit) * 2);">
                                <label style="display: flex; align-items: center; gap: calc(var(--spacing-unit) * 1); cursor: pointer;">
                                    <input type="radio" name="date_type" value="precise" checked>
                                    <span>Date precise</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: calc(var(--spacing-unit) * 1); cursor: pointer;">
                                    <input type="radio" name="date_type" value="month">
                                    <span>Solo mese (date flessibili)</span>
                                </label>
                            </div>
                        </div>

                        <div id="precise-dates-container">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="activity_start_date">Data Inizio <span class="required">*</span></label>
                                    <input type="date" id="activity_start_date" name="activity_start_date" min="<?php echo date('Y-m-d'); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="activity_end_date">Data Fine <span class="required">*</span></label>
                                    <input type="date" id="activity_end_date" name="activity_end_date" min="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                        </div>

                        <div id="month-container" style="display: none;">
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
                                        echo '<option value="' . $current_year . '-' . $month_num . '">' . $months[$month_num] . ' ' . $current_year . '</option>';
                                    }

                                    // Mostra tutti i mesi del prossimo anno
                                    $next_year = $current_year + 1;
                                    foreach ($months as $num => $name) {
                                        echo '<option value="' . $next_year . '-' . $num . '">' . $name . ' ' . $next_year . '</option>';
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
                                <input type="number" id="activity_budget" name="activity_budget" min="0" required placeholder="500">
                            </div>

                            <div class="form-group">
                                <label for="activity_max_participants">Max Partecipanti <span class="required">*</span></label>
                                <input type="number" id="activity_max_participants" name="activity_max_participants" min="2" max="50" value="5" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Tipo di Attività</h3>
                        <div class="checkbox-group">
                            <?php
                            $activity_types = get_terms(array(
                                'taxonomy' => 'tipo_sport',
                                'hide_empty' => false,
                            ));
                            if (!empty($activity_types) && !is_wp_error($activity_types)) :
                                foreach ($activity_types as $type) :
                            ?>
                                <label>
                                    <input type="checkbox" name="activity_types[]" value="<?php echo esc_attr($type->term_id); ?>">
                                    <?php echo esc_html($type->name); ?>
                                </label>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Dettagli Attività Sportiva <span class="required">*</span></h3>
                        <p style="color: var(--text-medium); margin-bottom: calc(var(--spacing-unit) * 3);">Questi dettagli aiutano i partecipanti a capire meglio l'attività sportiva</p>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="activity_time">⏰ Orario Inizio <span class="required">*</span></label>
                                <input type="time" id="activity_time" name="activity_time" required>
                            </div>

                            <div class="form-group">
                                <label for="activity_duration">⏱️ Durata <span class="required">*</span></label>
                                <select id="activity_duration" name="activity_duration" required>
                                    <option value="">Seleziona durata</option>
                                    <option value="30">30 minuti</option>
                                    <option value="60">1 ora</option>
                                    <option value="90">1.5 ore</option>
                                    <option value="120" selected>2 ore</option>
                                    <option value="180">3 ore</option>
                                    <option value="240">4 ore</option>
                                    <option value="480">Mezza giornata (8 ore)</option>
                                    <option value="720">Giornata intera (12+ ore)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="activity_level">📊 Livello Esperienza Richiesto <span class="required">*</span></label>
                            <select id="activity_level" name="activity_level" required>
                                <option value="">Seleziona livello</option>
                                <option value="principiante">🟢 Principiante - Tutti possono partecipare</option>
                                <option value="intermedio">🟡 Intermedio - Serve esperienza base</option>
                                <option value="avanzato">🟠 Avanzato - Serve buona preparazione</option>
                                <option value="esperto">🔴 Esperto - Solo atleti esperti</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>🎒 Attrezzatura Richiesta</label>
                            <div class="checkbox-group" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                                <label>
                                    <input type="checkbox" name="equipment[]" value="scarpe">
                                    👟 Scarpe sportive
                                </label>
                                <label>
                                    <input type="checkbox" name="equipment[]" value="racchetta">
                                    🎾 Racchetta
                                </label>
                                <label>
                                    <input type="checkbox" name="equipment[]" value="bici">
                                    🚴 Bicicletta + casco
                                </label>
                                <label>
                                    <input type="checkbox" name="equipment[]" value="abbigliamento">
                                    👕 Abbigliamento tecnico
                                </label>
                                <label>
                                    <input type="checkbox" name="equipment[]" value="borraccia">
                                    💧 Borraccia
                                </label>
                                <label>
                                    <input type="checkbox" name="equipment[]" value="pallone">
                                    ⚽ Pallone
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>🏟️ Servizi Disponibili</label>
                            <div class="checkbox-group" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                                <label>
                                    <input type="checkbox" name="facilities[]" value="spogliatoi">
                                    🚪 Spogliatoi
                                </label>
                                <label>
                                    <input type="checkbox" name="facilities[]" value="docce">
                                    🚿 Docce
                                </label>
                                <label>
                                    <input type="checkbox" name="facilities[]" value="parcheggio">
                                    🅿️ Parcheggio
                                </label>
                                <label>
                                    <input type="checkbox" name="facilities[]" value="bar">
                                    ☕ Bar/Ristoro
                                </label>
                                <label>
                                    <input type="checkbox" name="facilities[]" value="wifi">
                                    📶 WiFi
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="activity_requirements">📝 Note e Requisiti Particolari</label>
                            <textarea id="activity_requirements" name="activity_requirements" rows="4" placeholder="Es: Certificato medico necessario, età minima, particolari condizioni fisiche richieste, documenti da portare..."></textarea>
                            <small style="display: block; margin-top: 8px; color: #666;">Inserisci qui eventuali requisiti particolari o informazioni importanti per i partecipanti</small>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="disclaimer-box" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                            <h4 style="margin-top: 0; color: #856404;">⚠️ Informativa Importante</h4>
                            <p style="margin-bottom: 15px;">Pubblicando questo annuncio, dichiari di comprendere e accettare che:</p>
                            <ul style="margin: 0; padding-left: 20px;">
                                <li style="margin-bottom: 8px;">La piattaforma <strong>facilita l'incontro tra sportivi</strong> ma non organizza materialmente le attività</li>
                                <li style="margin-bottom: 8px;">Sei <strong>l'unico responsabile</strong> per l'organizzazione, la sicurezza e la gestione dell'attività</li>
                                <li style="margin-bottom: 8px;">Devi verificare <strong>personalmente</strong> l'identità e l'affidabilità dei partecipanti</li>
                                <li style="margin-bottom: 8px;">La piattaforma <strong>non è responsabile</strong> per comportamenti, danni, cancellazioni o disservizi</li>
                                <li style="margin-bottom: 8px;">Tutte le <strong>questioni economiche e logistiche</strong> sono gestite direttamente tra te e i partecipanti</li>
                                <li style="margin-bottom: 8px;">Devi rispettare tutte le <strong>leggi locali e internazionali</strong> applicabili all'attività</li>
                            </ul>
                            <div style="margin-top: 15px;">
                                <label style="display: flex; align-items: start; gap: 10px; cursor: pointer;">
                                    <input type="checkbox" id="accept_activity_disclaimer" name="accept_activity_disclaimer" required style="margin-top: 4px;">
                                    <span>Ho letto e accetto l'informativa. Comprendo che sono l'unico responsabile per questa attività e sollevo la piattaforma da ogni responsabilità.</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="<?php echo esc_url(home_url('/dashboard')); ?>" class="btn-secondary">Annulla</a>
                        <button type="submit" class="btn-primary btn-large">Crea Annuncio 🚀</button>
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

    $('#create-travel-form').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        const $messages = $('#form-messages');

        const dateType = $('input[name="date_type"]:checked').val();
        let dataToSend = {
            action: 'cdv_create_travel',
            nonce: cdvAjax.nonce,
            title: $('#activity_title').val(),
            description: $('#activity_description').val(),
            destination: $('#activity_destination').val(),
            country: $('#activity_country').val(),
            budget: $('#activity_budget').val(),
            max_participants: $('#activity_max_participants').val(),
            activity_types: [],
            activity_time: $('#activity_time').val(),
            activity_duration: $('#activity_duration').val(),
            activity_level: $('#activity_level').val(),
            equipment: [],
            facilities: [],
            activity_requirements: $('#activity_requirements').val()
        };

        // Get activity types
        $('input[name="activity_types[]"]:checked').each(function() {
            dataToSend.activity_types.push($(this).val());
        });

        // Get equipment
        $('input[name="equipment[]"]:checked').each(function() {
            dataToSend.equipment.push($(this).val());
        });

        // Get facilities
        $('input[name="facilities[]"]:checked').each(function() {
            dataToSend.facilities.push($(this).val());
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

        // Disable submit button and show validation message
        $submitBtn.prop('disabled', true).text('Validazione indirizzo...');
        $messages.html('<div class="info-message">🔍 Verifica che l\'indirizzo esista...</div>');

        // First, validate the address with geocoding
        $.ajax({
            url: cdvAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'cdv_validate_address',
                nonce: cdvAjax.nonce,
                destination: dataToSend.destination,
                country: dataToSend.country
            },
            success: function(validationResponse) {
                if (validationResponse.success) {
                    // Address is valid, proceed with travel creation
                    $submitBtn.text('Creazione in corso...');
                    $messages.html('<div class="success-message">✅ Indirizzo valido! Creazione attività in corso...</div>');

                    $.ajax({
                        url: cdvAjax.ajaxurl,
                        type: 'POST',
                        data: dataToSend,
                        success: function(response) {
                            if (response.success) {
                                $messages.html('<div class="success-message">' + response.data.message + '</div>');

                                // Redirect to the travel page after 1.5 seconds
                                setTimeout(function() {
                                    window.location.href = response.data.redirect_url;
                                }, 1500);
                            } else {
                                $messages.html('<div class="error-message">' + response.data.message + '</div>');
                                $submitBtn.prop('disabled', false).text('Crea Annuncio 🚀');
                            }
                        },
                        error: function() {
                            $messages.html('<div class="error-message">Si è verificato un errore. Riprova più tardi.</div>');
                            $submitBtn.prop('disabled', false).text('Crea Annuncio 🚀');
                        }
                    });
                } else {
                    // Address validation failed
                    $messages.html('<div class="error-message">❌ ' + validationResponse.data.message + '</div>');
                    $submitBtn.prop('disabled', false).text('Crea Annuncio 🚀');
                }
            },
            error: function() {
                // Validation request failed, but allow creation anyway (fallback)
                console.warn('Address validation failed, proceeding anyway');
                $submitBtn.text('Creazione in corso...');
                $messages.html('<div class="warning-message">⚠️ Impossibile validare l\'indirizzo, ma procedo comunque...</div>');

                $.ajax({
                    url: cdvAjax.ajaxurl,
                    type: 'POST',
                    data: dataToSend,
                    success: function(response) {
                        if (response.success) {
                            $messages.html('<div class="success-message">' + response.data.message + '</div>');

                            // Redirect to the travel page after 1.5 seconds
                            setTimeout(function() {
                                window.location.href = response.data.redirect_url;
                            }, 1500);
                        } else {
                            $messages.html('<div class="error-message">' + response.data.message + '</div>');
                            $submitBtn.prop('disabled', false).text('Crea Annuncio 🚀');
                        }
                    },
                    error: function() {
                        $messages.html('<div class="error-message">Si è verificato un errore. Riprova più tardi.</div>');
                        $submitBtn.prop('disabled', false).text('Crea Annuncio 🚀');
                    }
                });
            }
        });
    });
});
</script>

<?php
get_footer();
