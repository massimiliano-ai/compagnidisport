<?php
/**
 * Template Name: Archivio Attività
 * Description: Template per visualizzare l'archivio delle attività sportive con filtri
 */

get_header();

// Build query args based on filters
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$args = array(
    'post_type' => 'attivita',
    'post_status' => 'publish',
    'posts_per_page' => 12,
    'paged' => $paged,
);

// Search query
if (!empty($_GET['s'])) {
    $args['s'] = sanitize_text_field($_GET['s']);
}

// Tipo Sport filter
if (!empty($_GET['tipo_sport'])) {
    $args['tax_query'][] = array(
        'taxonomy' => 'tipo_sport',
        'field' => 'slug',
        'terms' => sanitize_text_field($_GET['tipo_sport']),
    );
}

// Date from filter
if (!empty($_GET['date_from'])) {
    $date_from = sanitize_text_field($_GET['date_from']);
    $args['meta_query'][] = array(
        'key' => 'cdv_start_date',
        'value' => $date_from . '-01',
        'compare' => '>=',
        'type' => 'DATE',
    );
}

// Budget filters
if (!empty($_GET['budget_min']) || !empty($_GET['budget_max'])) {
    $budget_query = array('key' => 'cdv_budget', 'type' => 'NUMERIC');
    if (!empty($_GET['budget_min'])) {
        $budget_query['compare'] = '>=';
        $budget_query['value'] = intval($_GET['budget_min']);
    }
    if (!empty($_GET['budget_max'])) {
        $budget_query['compare'] = isset($budget_query['compare']) ? 'BETWEEN' : '<=';
        $budget_query['value'] = isset($budget_query['value']) ? array(intval($_GET['budget_min']), intval($_GET['budget_max'])) : intval($_GET['budget_max']);
    }
    $args['meta_query'][] = $budget_query;
}

// Max participants filter
if (!empty($_GET['max_participants'])) {
    $args['meta_query'][] = array(
        'key' => 'cdv_max_participants',
        'value' => intval($_GET['max_participants']),
        'compare' => '<=',
        'type' => 'NUMERIC',
    );
}

// Activity status filter
if (!empty($_GET['activity_status'])) {
    $args['meta_query'][] = array(
        'key' => 'cdv_activity_status',
        'value' => sanitize_text_field($_GET['activity_status']),
        'compare' => '=',
    );
}

// Difficulty filter
if (!empty($_GET['difficulty'])) {
    $args['meta_query'][] = array(
        'key' => 'cdv_activity_level',
        'value' => sanitize_text_field($_GET['difficulty']),
        'compare' => '=',
    );
}

// Duration filter
if (!empty($_GET['duration'])) {
    $duration = sanitize_text_field($_GET['duration']);
    if ($duration === 'short') {
        $args['meta_query'][] = array(
            'key' => 'cdv_activity_duration',
            'value' => 180,
            'compare' => '<=',
            'type' => 'NUMERIC',
        );
    } elseif ($duration === 'medium') {
        $args['meta_query'][] = array(
            'key' => 'cdv_activity_duration',
            'value' => array(181, 480),
            'compare' => 'BETWEEN',
            'type' => 'NUMERIC',
        );
    } elseif ($duration === 'long') {
        $args['meta_query'][] = array(
            'key' => 'cdv_activity_duration',
            'value' => 480,
            'compare' => '>',
            'type' => 'NUMERIC',
        );
    }
}

// Orderby
if (!empty($_GET['orderby'])) {
    $orderby = sanitize_text_field($_GET['orderby']);
    switch ($orderby) {
        case 'start_date':
            $args['meta_key'] = 'cdv_start_date';
            $args['orderby'] = 'meta_value';
            $args['order'] = 'ASC';
            break;
        case 'budget_asc':
            $args['meta_key'] = 'cdv_budget';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
            break;
        case 'budget_desc':
            $args['meta_key'] = 'cdv_budget';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        default:
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
    }
} else {
    $args['orderby'] = 'date';
    $args['order'] = 'DESC';
}

// Meta query relation
if (!empty($args['meta_query']) && count($args['meta_query']) > 1) {
    $args['meta_query']['relation'] = 'AND';
}

// Execute query
$activities_query = new WP_Query($args);
?>

<main class="site-main">
    <div class="page-header">
        <div class="container">
            <?php if (!empty($_GET['s'])) : ?>
                <h1>Risultati per: "<?php echo esc_html($_GET['s']); ?>"</h1>
                <p>Trovati <strong><?php echo $activities_query->found_posts; ?></strong> annunci</p>
            <?php else : ?>
                <h1><?php the_title(); ?></h1>
                <?php if (get_the_content()) : ?>
                    <p><?php echo wp_trim_words(get_the_content(), 20); ?></p>
                <?php else : ?>
                    <p>Esplora tutti gli annunci disponibili e trova la tua prossima avventura sportiva</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <div class="archive-layout">
            <!-- Filters Sidebar -->
            <aside class="filters-sidebar">
                <h3>Filtra Annunci</h3>

                <form method="get" action="" class="filters-form">
                    <div class="filters-form-scroll">
                        <div class="filter-group">
                            <label for="search">Cerca</label>
                            <input type="text" id="search" name="s" value="<?php echo isset($_GET['s']) ? esc_attr($_GET['s']) : ''; ?>" placeholder="Luogo, sport...">
                        </div>

                        <div class="filter-group">
                            <label for="tipo_sport">Tipo di Attività</label>
                            <select id="tipo_sport" name="tipo_sport">
                                <option value="">Tutti</option>
                                <?php
                                $types = get_terms(array(
                                    'taxonomy' => 'tipo_sport',
                                    'hide_empty' => false,
                                ));
                                if (!empty($types) && !is_wp_error($types)) {
                                    foreach ($types as $type) {
                                        $selected = isset($_GET['tipo_sport']) && $_GET['tipo_sport'] === $type->slug ? 'selected' : '';
                                        echo '<option value="' . esc_attr($type->slug) . '" ' . $selected . '>' . esc_html($type->name) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="date_from">A partire da</label>
                            <input type="month" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : ''; ?>">
                        </div>

                        <div class="filter-group">
                            <label for="activity_status">Stato</label>
                            <select id="activity_status" name="activity_status">
                                <option value="">Tutti</option>
                                <option value="open" <?php selected(isset($_GET['activity_status']) && $_GET['activity_status'] === 'open'); ?>>Aperto</option>
                                <option value="full" <?php selected(isset($_GET['activity_status']) && $_GET['activity_status'] === 'full'); ?>>Completo</option>
                                <option value="closed" <?php selected(isset($_GET['activity_status']) && $_GET['activity_status'] === 'closed'); ?>>Chiuso</option>
                            </select>
                        </div>

                        <!-- Advanced Filters -->
                        <div class="filter-group">
                            <button type="button" class="filter-toggle-btn" id="toggle-advanced-filters">
                                <span>🔧 Filtri Avanzati</span>
                                <span class="toggle-icon">▼</span>
                            </button>
                        </div>

                        <div class="advanced-filters" id="advanced-filters-section" style="display: none;">
                            <div class="filter-group">
                                <label for="budget_min">Budget Minimo (€)</label>
                                <input type="number" id="budget_min" name="budget_min" value="<?php echo isset($_GET['budget_min']) ? esc_attr($_GET['budget_min']) : ''; ?>" min="0" placeholder="0">
                            </div>

                            <div class="filter-group">
                                <label for="budget_max">Budget Massimo (€)</label>
                                <input type="number" id="budget_max" name="budget_max" value="<?php echo isset($_GET['budget_max']) ? esc_attr($_GET['budget_max']) : ''; ?>" min="0" placeholder="1000">
                            </div>

                            <div class="filter-group">
                                <label for="max_participants">Max Partecipanti</label>
                                <input type="number" id="max_participants" name="max_participants" value="<?php echo isset($_GET['max_participants']) ? esc_attr($_GET['max_participants']) : ''; ?>" min="1" placeholder="10">
                            </div>

                            <div class="filter-group">
                                <label for="difficulty">Livello Difficoltà</label>
                                <select id="difficulty" name="difficulty">
                                    <option value="">Tutti i livelli</option>
                                    <option value="principiante" <?php selected(isset($_GET['difficulty']) && $_GET['difficulty'] === 'principiante'); ?>>Principiante</option>
                                    <option value="intermedio" <?php selected(isset($_GET['difficulty']) && $_GET['difficulty'] === 'intermedio'); ?>>Intermedio</option>
                                    <option value="avanzato" <?php selected(isset($_GET['difficulty']) && $_GET['difficulty'] === 'avanzato'); ?>>Avanzato</option>
                                    <option value="esperto" <?php selected(isset($_GET['difficulty']) && $_GET['difficulty'] === 'esperto'); ?>>Esperto</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="duration">Durata</label>
                                <select id="duration" name="duration">
                                    <option value="">Tutte le durate</option>
                                    <option value="short" <?php selected(isset($_GET['duration']) && $_GET['duration'] === 'short'); ?>>Breve (fino 3h)</option>
                                    <option value="medium" <?php selected(isset($_GET['duration']) && $_GET['duration'] === 'medium'); ?>>Media (3-8h)</option>
                                    <option value="long" <?php selected(isset($_GET['duration']) && $_GET['duration'] === 'long'); ?>>Lunga (8h+)</option>
                                </select>
                            </div>
                        </div>

                        <div class="filter-group">
                            <label for="orderby">Ordina per</label>
                            <select id="orderby" name="orderby">
                                <option value="date" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'date'); ?>>Più Recenti</option>
                                <option value="start_date" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'start_date'); ?>>Data Partenza</option>
                                <option value="budget_asc" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'budget_asc'); ?>>Budget: Basso → Alto</option>
                                <option value="budget_desc" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'budget_desc'); ?>>Budget: Alto → Basso</option>
                            </select>
                        </div>
                    </div>

                    <div class="filters-form-actions">
                        <button type="submit" class="btn-primary" style="width: 100%;">Applica Filtri</button>

                        <?php if (!empty($_GET['s']) || !empty($_GET['tipo_sport']) || !empty($_GET['date_from']) ||
                                  !empty($_GET['budget_min']) || !empty($_GET['budget_max']) || !empty($_GET['max_participants']) ||
                                  !empty($_GET['activity_status']) || !empty($_GET['difficulty']) || !empty($_GET['duration']) ||
                                  (isset($_GET['orderby']) && $_GET['orderby'] !== 'date')) : ?>
                            <a href="<?php echo get_permalink(); ?>" class="btn-secondary" style="width: 100%; text-align: center;">
                                Reset Filtri
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </aside>

            <!-- Activities Grid -->
            <div class="travels-content">
                <?php
                // Separate active and expired activities
                $active_travels = array();
                $expired_travels = array();
                $today = date('Y-m-d');

                if ($activities_query->have_posts()) :
                    while ($activities_query->have_posts()) : $activities_query->the_post();
                        $end_date = get_post_meta(get_the_ID(), 'cdv_end_date', true);
                        if ($end_date && $end_date < $today) {
                            $expired_travels[] = $post;
                        } else {
                            $active_travels[] = $post;
                        }
                    endwhile;
                    wp_reset_postdata();

                    $total_travels = $activities_query->found_posts;
                    ?>
                    <div class="results-header">
                        <p>
                            <?php echo $total_travels . ' ' . ($total_travels === 1 ? 'annuncio trovato' : 'annunci trovati'); ?>
                        </p>
                    </div>

                    <div class="grid">
                        <?php
                        // Show active activities first
                        foreach ($active_travels as $post) :
                            setup_postdata($post);
                            get_template_part('template-parts/content', 'activity-card');
                        endforeach;

                        // Show expired activities with badge
                        foreach ($expired_travels as $post) :
                            setup_postdata($post);
                            set_query_var('is_expired', true);
                            get_template_part('template-parts/content', 'activity-card');
                            set_query_var('is_expired', false);
                        endforeach;
                        wp_reset_postdata();
                        ?>
                    </div>

                    <!-- Pagination -->
                    <?php
                    $big = 999999999;
                    echo '<div class="pagination">';
                    echo paginate_links(array(
                        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                        'format' => '?paged=%#%',
                        'current' => max(1, $paged),
                        'total' => $activities_query->max_num_pages,
                        'prev_text' => '← Precedente',
                        'next_text' => 'Successivo →',
                    ));
                    echo '</div>';
                    ?>

                <?php else : ?>
                    <div class="no-results">
                        <h2>Nessun annuncio trovato</h2>
                        <p>Prova a modificare i filtri di ricerca o <a href="<?php echo get_permalink(); ?>">visualizza tutti gli annunci</a>.</p>
                        <?php if (is_user_logged_in() && current_user_can('create_attivita')) : ?>
                            <a href="<?php echo esc_url(home_url('/crea-attivita')); ?>" class="btn-primary">
                                Crea il Primo Annuncio
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php
// Use same styles from archive-attivita.php
get_template_part('template-parts/archive', 'styles');
?>

<script>
jQuery(document).ready(function($) {
    // Toggle advanced filters
    $('#toggle-advanced-filters').on('click', function() {
        const $section = $('#advanced-filters-section');
        const $icon = $(this).find('.toggle-icon');

        $section.slideToggle(300);
        $icon.text($section.is(':visible') ? '▲' : '▼');
    });

    // Auto-open advanced filters if any advanced filter is set
    <?php if (!empty($_GET['budget_min']) || !empty($_GET['budget_max']) ||
              !empty($_GET['max_participants']) || !empty($_GET['difficulty']) ||
              !empty($_GET['duration'])) : ?>
        $('#advanced-filters-section').show();
        $('#toggle-advanced-filters .toggle-icon').text('▲');
    <?php endif; ?>
});
</script>

<?php
get_footer();
