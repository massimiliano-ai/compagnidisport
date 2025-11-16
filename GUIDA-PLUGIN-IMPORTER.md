# 🚀 Guida Rapida: Plugin Importatore JSON

## Plugin Creato: CDV JSON Activity Importer

Un plugin WordPress completo per importare annunci di attività sportive tramite interfaccia admin.

---

## 📦 Installazione

### Opzione 1: Il Plugin è Già nella Cartella Plugins

Il plugin si trova in: `plugins/cdv-json-importer/`

**Attivalo dal pannello WordPress:**

1. Vai su **Plugin → Plugin Installati**
2. Cerca **CDV JSON Activity Importer**
3. Clicca **Attiva**

### Opzione 2: Upload Manuale (se necessario)

Se il plugin non appare nella lista:

1. Comprimi la cartella `cdv-json-importer` in un file ZIP
2. Vai su **Plugin → Aggiungi nuovo → Carica Plugin**
3. Carica il file ZIP
4. Attiva il plugin

---

## 🎯 Come Usare il Plugin

### Passo 1: Accedi all'Interfaccia

Dopo aver attivato il plugin:

1. Vai nel menu WordPress Admin
2. Clicca su **Strumenti → Importa Attività**
3. Si aprirà l'interfaccia di importazione

### Passo 2: Prepara il File JSON

Hai due opzioni:

**A) Usa l'Esempio Fornito:**
- Clicca **"📥 Scarica Esempio JSON"** nell'interfaccia
- Scarica `esempio-annunci-attivita.json` (15 annunci pronti)

**B) Genera il Tuo JSON:**
- Usa ChatGPT o Claude con il prompt fornito
- Copia il prompt da `ISTRUZIONI-GENERAZIONE-ANNUNCI.md`
- Salva l'output come file `.json`

### Passo 3: Carica il File

**Metodo Drag & Drop:**
- Trascina il file JSON nell'area di upload

**Metodo Click:**
- Clicca **"Scegli File"**
- Seleziona il tuo file JSON

### Passo 4: Validazione Automatica

Il plugin automaticamente:
- ✅ Valida la sintassi JSON
- ✅ Controlla tutti i campi obbligatori
- ✅ Verifica formati date e orari
- ✅ Mostra anteprima di ogni annuncio

Vedrai:
- **Statistiche**: Totali, Validi, Con Errori
- **Anteprima**: Lista di tutti gli annunci con dettagli
- **Errori**: Se presenti, vengono evidenziati in rosso

### Passo 5: Importa

1. Rivedi l'anteprima
2. Clicca **"✓ Importa Annunci"**
3. Osserva il progresso in tempo reale:
   - Barra di avanzamento
   - Log dettagliato di ogni annuncio importato
   - Messaggi di successo/errore

### Passo 6: Verifica Risultati

Al termine vedrai:
- **Riepilogo**: Quanti importati, quanti errori
- **Log completo**: Dettagli di ogni operazione

Clicca **"📋 Visualizza Annunci"** per vedere gli annunci pubblicati!

---

## 🎨 Interfaccia Utente

Il plugin offre un'interfaccia moderna e intuitiva:

### Step 1: Upload
```
┌─────────────────────────────────────┐
│  📁 Trascina il file JSON qui       │
│                                     │
│      oppure                         │
│                                     │
│    [Scegli File]                    │
└─────────────────────────────────────┘

ℹ️ Informazioni
- Formato: JSON
- Max: 10MB
- Campi obbligatori verificati
```

### Step 2: Anteprima
```
┌─────────────────────────────────────┐
│  Statistiche                        │
│  [15] Totali  [14] Validi  [1] Err │
│                                     │
│  Anteprima Annunci:                 │
│  ┌─────────────────────────────┐   │
│  │ ✓ #1 Partita di Calcetto    │   │
│  │   📍 Milano 📊 intermedio   │   │
│  └─────────────────────────────┘   │
│  ┌─────────────────────────────┐   │
│  │ ✗ #2 Escursione Trekking    │   │
│  │   ⚠️ Data fine mancante      │   │
│  └─────────────────────────────┘   │
│                                     │
│  [← Indietro] [✓ Importa]          │
└─────────────────────────────────────┘
```

### Step 3: Importazione
```
┌─────────────────────────────────────┐
│  Importazione in Corso              │
│  ████████████░░░░░░░░ 60%          │
│  9 di 15 annunci importati          │
│                                     │
│  Log:                               │
│  ✓ Importato: Partita Calcetto     │
│  ✓ Importato: Corso Yoga           │
│  ✓ Importato: Running Group        │
│  ...                                │
└─────────────────────────────────────┘
```

### Step 4: Completamento
```
┌─────────────────────────────────────┐
│  ✅ Importazione Completata!        │
│                                     │
│  [14] Importati  [1] Errori         │
│                                     │
│  [📋 Visualizza]  [🔄 Altri]        │
└─────────────────────────────────────┘
```

---

## 📋 Esempio di Utilizzo Completo

### Scenario: Importare 15 Annunci di Esempio

1. **Attiva il plugin** da WordPress Admin
2. **Vai su Strumenti → Importa Attività**
3. **Scarica l'esempio JSON** dal pulsante nell'interfaccia
4. **Trascina il file** nell'area di upload
5. **Clicca "Valida e Visualizza Anteprima"**
6. **Controlla l'anteprima** (dovrebbero essere tutti validi)
7. **Clicca "Importa Annunci"**
8. **Attendi il completamento** (circa 10-15 secondi)
9. **Clicca "Visualizza Annunci"**
10. **Vedi i tuoi 15 annunci pubblicati!** 🎉

---

## 💡 Generare Nuovi Annunci con AI

### Prompt per ChatGPT/Claude

Copia questo prompt:

```
Genera 20 annunci di attività sportive vari e realistici per una piattaforma italiana.

REQUISITI:
- Sport vari: calcio, tennis, yoga, running, MTB, padel, surf, trekking, arrampicata, nuoto, etc.
- Livelli: 40% principiante, 40% intermedio, 15% avanzato, 5% esperto
- Località italiane (Nord, Centro, Sud, Isole)
- Date: da oggi a 6 mesi nel futuro
- Budget: 20% gratis, 50% economici (10-30€), 25% medi (30-80€), 5% alti (80€+)
- 60% date precise, 40% solo mese flessibile
- Descrizioni 100-250 parole, tono amichevole e motivante

Usa ESATTAMENTE la struttura del file esempio-annunci-attivita.json
Output: SOLO array JSON valido, senza markdown o commenti.
Inizia con [ e finisci con ]
```

### Dopo la Generazione

1. **Copia l'output JSON** dall'AI
2. **Incolla in un editor** (VS Code, Notepad++)
3. **Salva come** `nuovi-annunci.json`
4. **Importa nel plugin!**

---

## ⚙️ Funzionalità Avanzate

### Validazione Intelligente

Il plugin controlla:
- ✅ Sintassi JSON corretta
- ✅ Tutti i campi obbligatori presenti
- ✅ Formati date (YYYY-MM-DD)
- ✅ Formati orari (HH:MM)
- ✅ Livelli validi (principiante/intermedio/avanzato/esperto)
- ✅ Tipo data coerente (precise richiede start/end, month richiede mese)
- ✅ Budget e partecipanti numerici

### Import Batch Progressivo

- Importa **5 annunci alla volta**
- Evita timeout del server
- Feedback in tempo reale
- Gestisce file JSON di grandi dimensioni

### Sicurezza

- ✅ Verifica permessi amministratore
- ✅ Nonce per richieste AJAX
- ✅ Sanitizzazione di tutti i dati
- ✅ Limite 10MB per file
- ✅ Validazione lato server

---

## 🐛 Risoluzione Problemi

### Problema: Plugin non visibile nel menu

**Soluzione:**
1. Verifica che sia attivato in **Plugin → Plugin Installati**
2. Controlla di essere admin (serve permesso `manage_options`)
3. Prova disattiva e riattiva

### Problema: Errore "JSON non valido"

**Soluzione:**
1. Copia il contenuto del file
2. Vai su https://jsonlint.com
3. Incolla e clicca "Validate JSON"
4. Correggi eventuali errori di sintassi
5. Riprova l'upload

### Problema: Molti annunci hanno errori

**Soluzione:**
1. Controlla il preview dettagliato
2. Leggi gli errori specifici per ogni annuncio
3. Verifica campi obbligatori:
   - `activity_title`
   - `activity_description`
   - `activity_destination`
   - Date (start/end O month)
   - `activity_time`, `activity_duration`
   - `activity_budget`, `activity_level`

### Problema: Import si blocca

**Soluzione:**
1. Ricarica la pagina
2. Riduci il numero di annunci nel JSON
3. Verifica connessione internet
4. Controlla log errori PHP del server

---

## 📊 Limiti e Raccomandazioni

### Limiti Tecnici
- **File size**: Max 10MB
- **Batch size**: 5 annunci per volta
- **Formato**: Solo JSON
- **Permessi**: Solo amministratori

### Raccomandazioni
- ✅ **Testa con pochi annunci** prima (5-10)
- ✅ **Valida sempre** prima di importare
- ✅ **Fai backup** del database
- ✅ **Importa in orari di basso traffico**
- ✅ **Controlla il log** per errori

---

## 🎓 Best Practices

### Prima dell'Import
1. Fai **backup del database**
2. **Valida il JSON** con jsonlint.com
3. **Testa con 2-3 annunci** prima
4. Controlla che le **date siano future**

### Durante l'Import
1. **Non chiudere** la finestra
2. **Non ricaricare** la pagina
3. **Aspetta il completamento**
4. **Leggi il log** per errori

### Dopo l'Import
1. **Verifica gli annunci** pubblicati
2. **Controlla i metadati** (date, budget, etc.)
3. **Vai su Impostazioni → Permalink** → Salva
4. **Svuota la cache** se presente

---

## 📚 Risorse

### File Inclusi
- `cdv-json-importer.php` - Plugin principale
- `assets/style.css` - Stili interfaccia
- `assets/script.js` - Logica JavaScript
- `esempio-annunci-attivita.json` - 15 annunci di esempio
- `ISTRUZIONI-GENERAZIONE-ANNUNCI.md` - Guida completa
- `README.md` - Documentazione plugin

### Link Utili
- Validatore JSON: https://jsonlint.com
- Generatore Date: https://www.timestamp-converter.com
- WordPress Codex: https://codex.wordpress.org

---

## ✅ Checklist Importazione

Prima di importare, assicurati di:

- [ ] Plugin attivato
- [ ] File JSON valido (testato su jsonlint.com)
- [ ] Backup database fatto
- [ ] Annunci con date future
- [ ] Tutti i campi obbligatori presenti
- [ ] Formati corretti (date, orari)
- [ ] Anteprima controllata
- [ ] Errori risolti

---

## 🎉 Conclusione

Hai ora un plugin completo e professionale per importare annunci di attività sportive!

**Prossimi passi:**
1. Attiva il plugin
2. Prova con l'esempio fornito
3. Genera i tuoi annunci con AI
4. Importa e pubblica!

Buon lavoro! 🚀
