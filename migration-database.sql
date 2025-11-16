-- =====================================================
-- SCRIPT MIGRAZIONE DATABASE
-- Da "Compagni di Viaggio" a "Compagni di Sport"
-- =====================================================
-- IMPORTANTE: Esegui un BACKUP completo prima di procedere!
-- =====================================================

SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

-- =====================================================
-- STEP 1: RINOMINA TABELLE ESISTENTI
-- =====================================================

-- Rinomina tabella partecipanti
RENAME TABLE
  wp_cdv_travel_participants TO wp_cdv_activity_participants;

-- Rinomina tabella messaggi di gruppo
RENAME TABLE
  wp_cdv_travel_group_messages TO wp_cdv_activity_group_messages;

-- =====================================================
-- STEP 2: AGGIORNA COLONNE NELLE TABELLE
-- =====================================================

-- Aggiorna colonna nella tabella partecipanti
ALTER TABLE wp_cdv_activity_participants
  CHANGE COLUMN travel_id activity_id bigint(20) UNSIGNED NOT NULL;

-- Aggiorna colonna nella tabella messaggi di gruppo
ALTER TABLE wp_cdv_activity_group_messages
  CHANGE COLUMN travel_id activity_id bigint(20) UNSIGNED NOT NULL;

-- Aggiorna colonna nella tabella reviews
ALTER TABLE wp_cdv_reviews
  CHANGE COLUMN travel_id activity_id bigint(20) UNSIGNED NOT NULL;

-- =====================================================
-- STEP 3: AGGIORNA POST TYPES
-- =====================================================

-- Cambia post type da 'viaggio' a 'attivita'
UPDATE wp_posts
SET post_type = 'attivita'
WHERE post_type = 'viaggio';

-- Cambia post type da 'racconto' a 'storia_sport'
UPDATE wp_posts
SET post_type = 'storia_sport'
WHERE post_type = 'racconto';

-- =====================================================
-- STEP 4: AGGIORNA TAXONOMIES
-- =====================================================

-- Cambia taxonomy da 'tipo_viaggio' a 'tipo_sport'
UPDATE wp_term_taxonomy
SET taxonomy = 'tipo_sport'
WHERE taxonomy = 'tipo_viaggio';

-- Cambia taxonomy da 'destinazione' a 'luogo'
UPDATE wp_term_taxonomy
SET taxonomy = 'luogo'
WHERE taxonomy = 'destinazione';

-- =====================================================
-- STEP 5: AGGIORNA META KEYS
-- =====================================================

-- Aggiorna meta keys per status
UPDATE wp_postmeta
SET meta_key = 'cdv_activity_status'
WHERE meta_key = 'cdv_travel_status';

-- Aggiorna meta keys per livello/difficoltà
UPDATE wp_postmeta
SET meta_key = 'cdv_activity_level'
WHERE meta_key = 'cdv_travel_difficulty';

-- Aggiorna meta keys per mese
UPDATE wp_postmeta
SET meta_key = 'cdv_activity_month'
WHERE meta_key = 'cdv_travel_month';

-- Aggiorna meta keys per trasporto (opzionale, poi rimuovibile)
UPDATE wp_postmeta
SET meta_key = 'cdv_activity_transport'
WHERE meta_key = 'cdv_travel_transport';

-- Aggiorna meta keys per alloggio (opzionale, poi rimuovibile)
UPDATE wp_postmeta
SET meta_key = 'cdv_activity_accommodation'
WHERE meta_key = 'cdv_travel_accommodation';

-- Aggiorna meta keys per destinazione a location
UPDATE wp_postmeta
SET meta_key = 'cdv_location'
WHERE meta_key = 'cdv_destination';

-- =====================================================
-- STEP 6: AGGIORNA OPTIONS
-- =====================================================

-- Aggiorna versione database
UPDATE wp_options
SET option_value = '2.0.0'
WHERE option_name = 'cdv_db_version';

-- Inserisci timestamp migrazione
INSERT INTO wp_options (option_name, option_value, autoload)
VALUES ('cdv_migration_to_sport_completed', NOW(), 'yes')
ON DUPLICATE KEY UPDATE option_value = NOW();

-- =====================================================
-- STEP 7: PULIZIA E OTTIMIZZAZIONE
-- =====================================================

-- Ottimizza le tabelle modificate
OPTIMIZE TABLE wp_cdv_activity_participants;
OPTIMIZE TABLE wp_cdv_activity_group_messages;
OPTIMIZE TABLE wp_cdv_reviews;
OPTIMIZE TABLE wp_posts;
OPTIMIZE TABLE wp_postmeta;
OPTIMIZE TABLE wp_term_taxonomy;

-- =====================================================
-- FINE MIGRAZIONE
-- =====================================================

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

-- =====================================================
-- VERIFICA POST-MIGRAZIONE
-- =====================================================

-- Conta annunci sportivi
SELECT COUNT(*) as 'Annunci Sportivi' FROM wp_posts WHERE post_type = 'attivita';

-- Conta partecipanti
SELECT COUNT(*) as 'Totale Partecipazioni' FROM wp_cdv_activity_participants;

-- Conta taxonomies sport
SELECT COUNT(*) as 'Sport Types' FROM wp_term_taxonomy WHERE taxonomy = 'tipo_sport';

-- Conta luoghi
SELECT COUNT(*) as 'Luoghi' FROM wp_term_taxonomy WHERE taxonomy = 'luogo';

-- Mostra stato migrazione
SELECT option_value as 'Migrazione Completata il' FROM wp_options WHERE option_name = 'cdv_migration_to_sport_completed';

-- =====================================================
-- ISTRUZIONI POST-MIGRAZIONE:
-- =====================================================
-- 1. Vai su WordPress Admin → Impostazioni → Permalink
-- 2. Clicca "Salva Modifiche" (senza cambiare nulla)
-- 3. Svuota tutte le cache (se presenti)
-- 4. Ricarica la homepage
-- =====================================================
