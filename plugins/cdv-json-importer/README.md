# CDV JSON Activity Importer

Plugin WordPress per importare annunci di attività sportive da file JSON tramite interfaccia admin.

## 🎯 Caratteristiche

- ✅ **Interfaccia drag & drop** per caricare file JSON
- ✅ **Validazione automatica** dei dati prima dell'importazione
- ✅ **Anteprima dettagliata** di tutti gli annunci
- ✅ **Import batch progressivo** con feedback in tempo reale
- ✅ **Gestione errori intelligente** con log dettagliato
- ✅ **100% sicuro** - validazione lato server e sanitizzazione
- ✅ **Responsive** - funziona su desktop, tablet e mobile

## 📦 Installazione

### Metodo 1: Upload via WordPress Admin

1. Comprimi la cartella `cdv-json-importer` in un file ZIP
2. Vai su **Plugin → Aggiungi nuovo → Carica Plugin**
3. Carica il file ZIP
4. Clicca **Attiva Plugin**

### Metodo 2: Installazione Manuale

1. Carica la cartella `cdv-json-importer` in `/wp-content/plugins/`
2. Vai su **Plugin** nel pannello WordPress
3. Attiva **CDV JSON Activity Importer**

## 🚀 Utilizzo

### Step 1: Accedi al Plugin

Vai su **Strumenti → Importa Attività** nel menu admin di WordPress.

### Step 2: Carica il JSON

- **Trascina il file JSON** nell'area di upload, oppure
- **Clicca "Scegli File"** per selezionarlo dal tuo computer

### Step 3: Validazione e Anteprima

Il plugin:
- Valida automaticamente il JSON
- Mostra statistiche (totali, validi, errori)
- Visualizza anteprima di ogni annuncio
- Evidenzia errori se presenti

### Step 4: Importazione

- Clicca **"Importa Annunci"**
- Monitora il progresso in tempo reale
- Visualizza log dettagliato
- Ricevi riepilogo finale

### Step 5: Completamento

- Visualizza statistiche finali
- Clicca **"Visualizza Annunci"** per vedere i risultati
- Oppure **"Importa Altri Annunci"** per continuare

## 📄 Formato JSON

Il file JSON deve contenere un array di oggetti con questa struttura:

```json
[
  {
    "activity_title": "Titolo Attività",
    "activity_description": "Descrizione dettagliata...",
    "activity_destination": "Milano",
    "activity_country": "Italia",
    "date_type": "precise",
    "activity_start_date": "2025-11-20",
    "activity_end_date": "2025-11-20",
    "activity_month": null,
    "activity_time": "20:30",
    "activity_duration": "90",
    "activity_budget": "15",
    "activity_max_participants": "2",
    "activity_level": "intermedio",
    "equipment": ["scarpe", "abbigliamento"],
    "facilities": ["spogliatoi", "docce"],
    "activity_requirements": "Note particolari..."
  }
]
```

### Campi Obbligatori

| Campo | Tipo | Descrizione |
|-------|------|-------------|
| `activity_title` | string | Titolo dell'annuncio |
| `activity_description` | string | Descrizione (100-250 parole) |
| `activity_destination` | string | Città/località |
| `activity_country` | string | Paese |
| `date_type` | string | "precise" o "month" |
| `activity_time` | string | Orario formato HH:MM |
| `activity_duration` | string | Durata in minuti |
| `activity_budget` | string | Budget in € (numero) |
| `activity_max_participants` | string | Max partecipanti |
| `activity_level` | string | Livello (vedi sotto) |

### Campi Opzionali

- `activity_start_date` - Obbligatorio se `date_type = "precise"`
- `activity_end_date` - Obbligatorio se `date_type = "precise"`
- `activity_month` - Obbligatorio se `date_type = "month"`
- `equipment` - Array di stringhe (opzionale)
- `facilities` - Array di stringhe (opzionale)
- `activity_requirements` - Note aggiuntive (opzionale)

### Valori Validi

**activity_level:**
- `principiante`
- `intermedio`
- `avanzato`
- `esperto`

**date_type:**
- `precise` - Richiede start_date e end_date
- `month` - Richiede activity_month (formato YYYY-MM)

**equipment e facilities:**
- Array di stringhe: `["scarpe", "racchetta", "bici", "abbigliamento", "borraccia", "pallone"]`
- `["spogliatoi", "docce", "parcheggio", "bar", "wifi"]`

## 📚 File di Esempio

Il plugin include:

1. **`esempio-annunci-attivita.json`** - 15 annunci completi di esempio
2. **`ISTRUZIONI-GENERAZIONE-ANNUNCI.md`** - Guida dettagliata

Puoi scaricarli direttamente dall'interfaccia del plugin.

## 🔒 Sicurezza

- ✅ Verifica permessi utente (`manage_options`)
- ✅ Nonce verification per tutte le richieste AJAX
- ✅ Sanitizzazione di tutti i dati prima del salvataggio
- ✅ Validazione lato server e client
- ✅ Limite dimensione file (10MB)
- ✅ Protezione contro JSON injection

## ⚡ Performance

- Import batch progressivo (5 annunci alla volta)
- Feedback in tempo reale senza bloccare il browser
- Ottimizzato per file JSON di grandi dimensioni
- Gestione memoria efficiente

## 🐛 Troubleshooting

### Il file non viene caricato
- Verifica che sia un file `.json` valido
- Controlla che non superi 10MB
- Usa un validatore JSON online per verificare la sintassi

### Errori durante l'importazione
- Controlla il log dettagliato
- Verifica che tutti i campi obbligatori siano presenti
- Assicurati che i formati date siano corretti (YYYY-MM-DD)

### Plugin non visibile nel menu
- Verifica di aver attivato il plugin
- Controlla di avere i permessi `manage_options`
- Prova a disattivare e riattivare il plugin

## 🔄 Aggiornamenti

**Versione 1.0.0**
- Prima release
- Interfaccia drag & drop
- Validazione completa
- Import batch progressivo
- Log dettagliato

## 📞 Supporto

Per problemi o domande:
- Controlla la documentazione in `ISTRUZIONI-GENERAZIONE-ANNUNCI.md`
- Verifica la struttura JSON con l'esempio fornito
- Consulta il log degli errori per dettagli specifici

## 📝 License

GPL v2 or later

## 👨‍💻 Author

Compagni di Sport Team
