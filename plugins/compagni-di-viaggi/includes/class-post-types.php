<?php
/**
 * Register Custom Post Types
 */

if (!defined('ABSPATH')) {
    exit;
}

class CDV_Post_Types {

    /**
     * Initialize
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_types'));
    }

    /**
     * Register custom post types
     */
    public static function register_post_types() {
        self::register_attivita();
    }

    /**
     * Register 'Attivita' post type
     */
    private static function register_attivita() {
        $labels = array(
            'name'                  => _x('Attività Sportive', 'Post Type General Name', 'compagni-di-sport'),
            'singular_name'         => _x('Attività Sportiva', 'Post Type Singular Name', 'compagni-di-sport'),
            'menu_name'            => __('Attività Sportive', 'compagni-di-sport'),
            'name_admin_bar'       => __('Attività Sportiva', 'compagni-di-sport'),
            'archives'             => __('Archivio Attività', 'compagni-di-sport'),
            'attributes'           => __('Attributi Attività', 'compagni-di-sport'),
            'parent_item_colon'    => __('Attività Genitore:', 'compagni-di-sport'),
            'all_items'            => __('Tutte le Attività', 'compagni-di-sport'),
            'add_new_item'         => __('Aggiungi Nuova Attività', 'compagni-di-sport'),
            'add_new'              => __('Aggiungi Nuova', 'compagni-di-sport'),
            'new_item'             => __('Nuova Attività', 'compagni-di-sport'),
            'edit_item'            => __('Modifica Attività', 'compagni-di-sport'),
            'update_item'          => __('Aggiorna Attività', 'compagni-di-sport'),
            'view_item'            => __('Visualizza Attività', 'compagni-di-sport'),
            'view_items'           => __('Visualizza Attività', 'compagni-di-sport'),
            'search_items'         => __('Cerca Attività', 'compagni-di-sport'),
            'not_found'            => __('Nessuna attività trovata', 'compagni-di-sport'),
            'not_found_in_trash'   => __('Nessuna attività trovata nel cestino', 'compagni-di-sport'),
            'featured_image'       => __('Immagine di Copertina', 'compagni-di-sport'),
            'set_featured_image'   => __('Imposta immagine di copertina', 'compagni-di-sport'),
            'remove_featured_image'=> __('Rimuovi immagine di copertina', 'compagni-di-sport'),
            'use_featured_image'   => __('Usa come immagine di copertina', 'compagni-di-sport'),
            'insert_into_item'     => __('Inserisci nell\'attività', 'compagni-di-sport'),
            'uploaded_to_this_item'=> __('Caricato in questa attività', 'compagni-di-sport'),
            'items_list'           => __('Lista attività', 'compagni-di-sport'),
            'items_list_navigation'=> __('Navigazione lista attività', 'compagni-di-sport'),
            'filter_items_list'    => __('Filtra lista attività', 'compagni-di-sport'),
        );

        $args = array(
            'label'               => __('Attività Sportiva', 'compagni-di-sport'),
            'description'         => __('Attività sportive della community', 'compagni-di-sport'),
            'labels'              => $labels,
            'supports'            => array('title', 'editor', 'thumbnail', 'author', 'comments', 'revisions'),
            'taxonomies'          => array('tipo_sport', 'luogo'),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-groups',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest'        => true,
            'rest_base'           => 'attivita',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'rewrite'             => array('slug' => 'attivita'),
        );

        register_post_type('attivita', $args);
    }
}
