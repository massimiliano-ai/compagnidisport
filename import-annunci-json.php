<?php
/**
 * Import Annunci da JSON
 *
 * Script per importare annunci di attività sportive da file JSON
 * Uso: php import-annunci-json.php [file.json] [user_id]
 */

// Carica WordPress
$wp_load_paths = [
    __DIR__ . '/wp-load.php',
    __DIR__ . '/../wp-load.php',
    __DIR__ . '/../../wp-load.php',
];

$wp_loaded = false;
foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $wp_loaded = true;
        break;
    }
}

if (!$wp_loaded) {
    die("❌ Errore: impossibile trovare wp-load.php\nEsegui lo script dalla root di WordPress.\n");
}

// Parametri
$json_file = $argv[1] ?? 'esempio-annunci-attivita.json';
$author_id = isset($argv[2]) ? intval($argv[2]) : 1;

echo "=== Import Annunci Attività Sportive ===\n\n";

// Verifica file
if (!file_exists($json_file)) {
    die("❌ File non trovato: $json_file\n");
}

// Leggi e decodifica JSON
$json_content = file_get_contents($json_file);
$annunci = json_decode($json_content, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("❌ Errore JSON: " . json_last_error_msg() . "\n");
}

if (!is_array($annunci) || empty($annunci)) {
    die("❌ Il JSON non contiene un array valido di annunci\n");
}

echo "📄 File: $json_file\n";
echo "📊 Annunci da importare: " . count($annunci) . "\n";
echo "👤 Autore: User ID $author_id\n\n";

// Verifica utente
$user = get_user_by('ID', $author_id);
if (!$user) {
    die("❌ Utente con ID $author_id non trovato\n");
}

echo "Procedo con l'importazione...\n\n";

$imported = 0;
$errors = 0;

foreach ($annunci as $index => $annuncio) {
    $num = $index + 1;

    echo "[$num/" . count($annunci) . "] {$annuncio['activity_title']}... ";

    // Validazione campi obbligatori
    $required_fields = [
        'activity_title', 'activity_description', 'activity_destination',
        'activity_country', 'date_type', 'activity_time', 'activity_duration',
        'activity_budget', 'activity_max_participants', 'activity_level'
    ];

    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (!isset($annuncio[$field]) || $annuncio[$field] === '') {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        echo "❌ ERRORE - Campi mancanti: " . implode(', ', $missing_fields) . "\n";
        $errors++;
        continue;
    }

    // Validazione date
    if ($annuncio['date_type'] === 'precise') {
        if (empty($annuncio['activity_start_date']) || empty($annuncio['activity_end_date'])) {
            echo "❌ ERRORE - Date precise richieste ma mancanti\n";
            $errors++;
            continue;
        }
    } elseif ($annuncio['date_type'] === 'month') {
        if (empty($annuncio['activity_month'])) {
            echo "❌ ERRORE - Mese richiesto ma mancante\n";
            $errors++;
            continue;
        }
    }

    try {
        // Crea il post
        $post_data = array(
            'post_type'    => 'attivita',
            'post_title'   => sanitize_text_field($annuncio['activity_title']),
            'post_content' => wp_kses_post($annuncio['activity_description']),
            'post_status'  => 'publish',
            'post_author'  => $author_id,
        );

        $post_id = wp_insert_post($post_data, true);

        if (is_wp_error($post_id)) {
            echo "❌ ERRORE - " . $post_id->get_error_message() . "\n";
            $errors++;
            continue;
        }

        // Aggiungi tutti i meta fields
        $meta_fields = [
            'cdv_destination' => $annuncio['activity_destination'],
            'cdv_country' => $annuncio['activity_country'],
            'cdv_date_type' => $annuncio['date_type'],
            'cdv_start_date' => $annuncio['activity_start_date'] ?? null,
            'cdv_end_date' => $annuncio['activity_end_date'] ?? null,
            'cdv_activity_month' => $annuncio['activity_month'] ?? null,
            'cdv_activity_time' => $annuncio['activity_time'],
            'cdv_activity_duration' => $annuncio['activity_duration'],
            'cdv_budget' => $annuncio['activity_budget'],
            'cdv_max_participants' => $annuncio['activity_max_participants'],
            'cdv_activity_level' => $annuncio['activity_level'],
            'cdv_equipment' => $annuncio['equipment'] ?? [],
            'cdv_facilities' => $annuncio['facilities'] ?? [],
            'cdv_activity_requirements' => $annuncio['activity_requirements'] ?? '',
            'cdv_activity_status' => 'open', // Default status
        ];

        foreach ($meta_fields as $meta_key => $meta_value) {
            if ($meta_value !== null && $meta_value !== '') {
                update_post_meta($post_id, $meta_key, $meta_value);
            }
        }

        echo "✅ OK (ID: $post_id)\n";
        $imported++;

    } catch (Exception $e) {
        echo "❌ ERRORE - " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=== Riepilogo Importazione ===\n";
echo "✅ Importati: $imported\n";
echo "❌ Errori: $errors\n";
echo "📊 Totale: " . count($annunci) . "\n\n";

if ($imported > 0) {
    echo "🎉 Importazione completata con successo!\n";
    echo "Visita il sito per vedere gli annunci pubblicati.\n\n";
    echo "IMPORTANTE:\n";
    echo "- Vai su Impostazioni → Permalink e clicca 'Salva Modifiche'\n";
    echo "- Svuota la cache se presente\n";
} else {
    echo "⚠️ Nessun annuncio importato. Controlla gli errori sopra.\n";
}
