<?php
/**
 * Register Custom Taxonomies
 */

if (!defined('ABSPATH')) {
    exit;
}

class CDV_Taxonomies {

    /**
     * Initialize
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_taxonomies'));
    }

    /**
     * Register custom taxonomies
     */
    public static function register_taxonomies() {
        self::register_tipo_sport();
        self::register_luogo();
    }

    /**
     * Register 'Tipo Sport' taxonomy
     */
    private static function register_tipo_sport() {
        $labels = array(
            'name'              => _x('Tipi di Sport', 'taxonomy general name', 'compagni-di-sport'),
            'singular_name'     => _x('Tipo di Sport', 'taxonomy singular name', 'compagni-di-sport'),
            'search_items'      => __('Cerca Sport', 'compagni-di-sport'),
            'all_items'         => __('Tutti gli Sport', 'compagni-di-sport'),
            'parent_item'       => __('Sport Genitore', 'compagni-di-sport'),
            'parent_item_colon' => __('Sport Genitore:', 'compagni-di-sport'),
            'edit_item'         => __('Modifica Sport', 'compagni-di-sport'),
            'update_item'       => __('Aggiorna Sport', 'compagni-di-sport'),
            'add_new_item'      => __('Aggiungi Nuovo Sport', 'compagni-di-sport'),
            'new_item_name'     => __('Nuovo Tipo di Sport', 'compagni-di-sport'),
            'menu_name'         => __('Tipi di Sport', 'compagni-di-sport'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'sport'),
        );

        register_taxonomy('tipo_sport', array('attivita'), $args);

        // Add default sport terms
        $sports = array(
            'Calcio' => 'calcio',
            'Calcetto' => 'calcetto',
            'Tennis' => 'tennis',
            'Padel' => 'padel',
            'Corsa' => 'corsa',
            'Running' => 'running',
            'Ciclismo' => 'ciclismo',
            'Mountain Bike' => 'mtb',
            'Trekking' => 'trekking',
            'Escursionismo' => 'escursionismo',
            'Nuoto' => 'nuoto',
            'Arrampicata' => 'arrampicata',
            'Fitness' => 'fitness',
            'Palestra' => 'palestra',
            'Sport Acquatici' => 'sport-acquatici',
            'Sport Invernali' => 'sport-invernali',
            'Yoga' => 'yoga',
            'Pilates' => 'pilates',
            'Badminton' => 'badminton',
            'Basket' => 'basket',
            'Pallavolo' => 'pallavolo',
            'Camminate' => 'camminate',
        );

        foreach ($sports as $name => $slug) {
            if (!term_exists($name, 'tipo_sport')) {
                wp_insert_term($name, 'tipo_sport', array('slug' => $slug));
            }
        }
    }

    /**
     * Register 'Luogo' taxonomy
     */
    private static function register_luogo() {
        $labels = array(
            'name'              => _x('Luoghi', 'taxonomy general name', 'compagni-di-sport'),
            'singular_name'     => _x('Luogo', 'taxonomy singular name', 'compagni-di-sport'),
            'search_items'      => __('Cerca Luoghi', 'compagni-di-sport'),
            'all_items'         => __('Tutti i Luoghi', 'compagni-di-sport'),
            'parent_item'       => __('Luogo Genitore', 'compagni-di-sport'),
            'parent_item_colon' => __('Luogo Genitore:', 'compagni-di-sport'),
            'edit_item'         => __('Modifica Luogo', 'compagni-di-sport'),
            'update_item'       => __('Aggiorna Luogo', 'compagni-di-sport'),
            'add_new_item'      => __('Aggiungi Nuovo Luogo', 'compagni-di-sport'),
            'new_item_name'     => __('Nuovo Luogo', 'compagni-di-sport'),
            'menu_name'         => __('Luoghi', 'compagni-di-sport'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'luogo'),
        );

        register_taxonomy('luogo', array('attivita'), $args);
    }
}
