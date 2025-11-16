# 📝 Istruzioni Generazione Annunci Attività Sportive

## 🎯 Obiettivo
Generare annunci di attività sportive realistici e vari in formato JSON valido per popolare il database della piattaforma Compagni di Sport.

---

## 📋 Prompt per LLM

Usa questo prompt con ChatGPT, Claude, o qualsiasi LLM:

```
Genera 15 annunci di attività sportive vari e realistici per una piattaforma italiana di sport.
Ogni annuncio deve essere in formato JSON e seguire questa struttura:

REQUISITI OBBLIGATORI:
1. Varietà di sport: calcio, tennis, ciclismo, running, nuoto, padel, yoga, trekking, arrampicata, basket, pallavolo, surf, sci, kayak, golf, etc.
2. Mix di livelli: principiante (40%), intermedio (40%), avanzato (15%), esperto (5%)
3. Località italiane diverse coprendo Nord, Centro, Sud e Isole
4. Date realistiche: da oggi fino a 6 mesi nel futuro
5. Budget variabili: gratuiti (20%), economici 10-30€ (50%), medio 30-80€ (25%), alto 80€+ (5%)
6. Durate varie: brevi 30-90min (30%), medie 2-4h (50%), lunghe 4h+ (20%)
7. Mix date: 60% con date precise, 40% con solo mese flessibile
8. Descrizioni coinvolgenti e realistiche (100-250 parole)
9. Attrezzatura e servizi realistici per ogni sport

CARATTERISTICHE DESCRIZIONI:
- Tono amichevole e inclusivo
- Dettagli pratici (cosa si farà, cosa aspettarsi)
- Elementi che creano community (aperitivo dopo, gruppo WhatsApp, etc.)
- Informazioni sulla location e sul percorso
- Aspetti motivazionali per invogliare l'iscrizione

FORMATO OUTPUT:
Restituisci SOLO un array JSON valido. NO markdown, NO testo aggiuntivo, NO commenti.
L'output deve iniziare con [ e finire con ]

VALIDAZIONE:
- Tutti i campi obbligatori presenti
- Date nel formato YYYY-MM-DD
- Orari nel formato HH:MM (es: "09:30")
- Budget come numero intero (senza €)
- Durata in minuti come stringa numerica (es: "120")
- equipment e facilities come array di stringhe
- activity_level: solo "principiante", "intermedio", "avanzato", "esperto"
- date_type: solo "precise" o "month"
```

---

## 📊 Struttura Campi JSON

### Campi Obbligatori

| Campo | Tipo | Valori Possibili | Esempio |
|-------|------|------------------|---------|
| `activity_title` | string | Titolo accattivante | "Partita di Calcetto Serale" |
| `activity_description` | string | 100-250 parole | "Ciao a tutti! Siamo un gruppo..." |
| `activity_destination` | string | Città/località | "Milano", "Val di Fassa" |
| `activity_country` | string | Nome paese | "Italia" |
| `date_type` | string | "precise" o "month" | "precise" |
| `activity_start_date` | string/null | YYYY-MM-DD o null | "2025-11-20" |
| `activity_end_date` | string/null | YYYY-MM-DD o null | "2025-11-20" |
| `activity_month` | string/null | YYYY-MM o null | "2025-12" |
| `activity_time` | string | HH:MM | "20:30" |
| `activity_duration` | string | Minuti | "90", "120", "480" |
| `activity_budget` | string | Numero intero | "15", "0", "120" |
| `activity_max_participants` | string | 1-50 | "10", "15" |
| `activity_level` | string | Vedi sotto | "intermedio" |
| `equipment` | array | Vedi sotto | ["scarpe", "abbigliamento"] |
| `facilities` | array | Vedi sotto | ["spogliatoi", "docce"] |
| `activity_requirements` | string | Note pratiche | "Portare scarpe da calcetto..." |

### Valori Specifici

**activity_level:**
- `"principiante"` - Adatto a tutti, nessuna esperienza richiesta
- `"intermedio"` - Esperienza base richiesta
- `"avanzato"` - Buona preparazione necessaria
- `"esperto"` - Solo atleti esperti

**equipment:** (array di stringhe)
- `"scarpe"` - Scarpe sportive specifiche
- `"racchetta"` - Racchetta (tennis, padel)
- `"bici"` - Bicicletta + casco
- `"abbigliamento"` - Abbigliamento tecnico
- `"borraccia"` - Borraccia/idratazione
- `"pallone"` - Pallone

**facilities:** (array di stringhe)
- `"spogliatoi"` - Spogliatoi disponibili
- `"docce"` - Docce disponibili
- `"parcheggio"` - Parcheggio disponibile
- `"bar"` - Bar/Ristoro
- `"wifi"` - WiFi disponibile

### Regole Date

**Se date_type = "precise":**
- `activity_start_date`: OBBLIGATORIO (formato YYYY-MM-DD)
- `activity_end_date`: OBBLIGATORIO (formato YYYY-MM-DD)
- `activity_month`: null

**Se date_type = "month":**
- `activity_start_date`: null
- `activity_end_date`: null
- `activity_month`: OBBLIGATORIO (formato YYYY-MM)

---

## 🔧 Validazione Output

Prima di usare il JSON generato, verifica:

1. **Sintassi JSON valida**: Usa jsonlint.com o `jq` command
   ```bash
   cat output.json | jq .
   ```

2. **Campi obbligatori**: Tutti presenti in ogni oggetto

3. **Formati date**:
   - Date: YYYY-MM-DD
   - Mese: YYYY-MM
   - Orario: HH:MM

4. **Tipi corretti**:
   - Numeri come stringhe dove richiesto
   - Array per equipment e facilities
   - Stringhe per descrizioni

5. **Coerenza logica**:
   - activity_end_date >= activity_start_date
   - activity_month nel futuro
   - Budget ragionevole per lo sport
   - Durata adeguata all'attività

---

## 📥 Import nel Database

Dopo aver generato e validato il JSON:

1. Salva il file come `annunci-generati.json`

2. Crea uno script PHP di import (esempio):

```php
<?php
// File: import-annunci.php
require_once('wp-load.php');

$json = file_get_contents('annunci-generati.json');
$annunci = json_decode($json, true);

if (!$annunci) {
    die('Errore JSON non valido');
}

foreach ($annunci as $annuncio) {
    // Crea il post
    $post_data = array(
        'post_type' => 'attivita',
        'post_title' => $annuncio['activity_title'],
        'post_content' => $annuncio['activity_description'],
        'post_status' => 'publish',
        'post_author' => 1, // Cambia con ID utente
    );

    $post_id = wp_insert_post($post_data);

    if ($post_id) {
        // Aggiungi meta fields
        update_post_meta($post_id, 'cdv_destination', $annuncio['activity_destination']);
        update_post_meta($post_id, 'cdv_country', $annuncio['activity_country']);
        update_post_meta($post_id, 'cdv_date_type', $annuncio['date_type']);
        update_post_meta($post_id, 'cdv_start_date', $annuncio['activity_start_date']);
        update_post_meta($post_id, 'cdv_end_date', $annuncio['activity_end_date']);
        update_post_meta($post_id, 'cdv_activity_month', $annuncio['activity_month']);
        update_post_meta($post_id, 'cdv_activity_time', $annuncio['activity_time']);
        update_post_meta($post_id, 'cdv_activity_duration', $annuncio['activity_duration']);
        update_post_meta($post_id, 'cdv_budget', $annuncio['activity_budget']);
        update_post_meta($post_id, 'cdv_max_participants', $annuncio['activity_max_participants']);
        update_post_meta($post_id, 'cdv_activity_level', $annuncio['activity_level']);
        update_post_meta($post_id, 'cdv_equipment', $annuncio['equipment']);
        update_post_meta($post_id, 'cdv_facilities', $annuncio['facilities']);
        update_post_meta($post_id, 'cdv_activity_requirements', $annuncio['activity_requirements']);

        echo "✓ Importato: {$annuncio['activity_title']}\n";
    }
}

echo "\nImportazione completata!\n";
```

3. Esegui lo script:
   ```bash
   php import-annunci.php
   ```

---

## ✅ Checklist Pre-Generazione

Prima di chiedere all'LLM di generare gli annunci:

- [ ] Hai il prompt completo pronto
- [ ] Hai specificato il numero di annunci desiderati
- [ ] Hai controllato l'esempio JSON di riferimento
- [ ] Hai preparato uno script di import
- [ ] Hai fatto backup del database

---

## 💡 Suggerimenti

1. **Varietà geografica**: Assicurati di coprire tutte le regioni italiane
2. **Stagionalità**: Sport invernali per date inverno, sport estivi per estate
3. **Realismo**: Budget e durate coerenti con lo sport (es: golf costa più di calcetto)
4. **Inclusività**: Mix di livelli con prevalenza principiante/intermedio
5. **Community**: Descrizioni che enfatizzano aspetto sociale
6. **Dettagli pratici**: Info su cosa portare, dove parcheggiare, etc.

---

## 🐛 Troubleshooting

**Problema**: JSON non valido
- **Soluzione**: Usa jsonlint.com per trovare errori sintassi

**Problema**: Campi mancanti
- **Soluzione**: Verifica che ogni oggetto abbia tutti i campi obbligatori

**Problema**: Date nel passato
- **Soluzione**: Rigenera specificando "da oggi" nel prompt

**Problema**: Budget irrealistici
- **Soluzione**: Specifica range nel prompt (es: "0-150€")

---

## 📚 Risorse

- Esempio completo: `esempio-annunci-attivita.json`
- Validatore JSON: https://jsonlint.com
- Template import: Vedi sezione "Import nel Database"
