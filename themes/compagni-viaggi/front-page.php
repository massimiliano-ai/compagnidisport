<?php
/**
 * Homepage Template
 */

get_header();
?>

<main class="site-main">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1><?php echo esc_html(get_theme_mod('cdv_hero_title', 'Trova i Tuoi Compagni di Sport')); ?></h1>
                <p><?php echo esc_html(get_theme_mod('cdv_hero_subtitle', 'Connettiti con sportivi che condividono le tue passioni. Organizza attività sportive insieme.')); ?></p>

                <!-- Search Box -->
                <div class="search-box">
                    <form class="search-form" action="<?php echo esc_url(home_url('/')); ?>" method="get">
                        <input type="hidden" name="post_type" value="attivita">

                        <div class="form-group">
                            <label for="destination">Luogo</label>
                            <input type="text" id="destination" name="s" placeholder="Dove vuoi praticare sport?">
                        </div>

                        <div class="form-group">
                            <label for="travel_type">Tipo di Sport</label>
                            <select id="travel_type" name="tipo_sport">
                                <option value="">Tutti i tipi</option>
                                <?php
                                $types = get_terms(array(
                                    'taxonomy' => 'tipo_sport',
                                    'hide_empty' => false,
                                ));
                                foreach ($types as $type) {
                                    echo '<option value="' . esc_attr($type->slug) . '">' . esc_html($type->name) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn-search">Cerca Attività</button>
                    </form>

                    <!-- CTA Button -->
                    <div class="hero-cta" style="text-align: center; margin-top: calc(var(--spacing-unit) * 4);">
                        <a href="<?php echo esc_url(get_theme_mod('cdv_hero_button_url', '/crea-attivita')); ?>" class="btn-primary btn-large" style="font-size: 1.1rem; padding: calc(var(--spacing-unit) * 2) calc(var(--spacing-unit) * 4); display: inline-flex; align-items: center; gap: calc(var(--spacing-unit) * 1); box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
                            <?php echo esc_html(get_theme_mod('cdv_hero_button_text', 'Inserisci il Tuo Annuncio')); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Activities -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2><?php echo esc_html(get_theme_mod('cdv_travels_title', 'Annunci di Attività Sportive')); ?></h2>
                <p><?php echo esc_html(get_theme_mod('cdv_travels_subtitle', 'Scopri le prossime attività e trova i tuoi compagni di sport')); ?></p>
            </div>

            <div class="grid">
                <?php
                $featured_travels = new WP_Query(array(
                    'post_type' => 'attivita',
                    'posts_per_page' => 6,
                    'meta_query' => array(
                        array(
                            'key' => 'cdv_activity_status',
                            'value' => 'open',
                            'compare' => '=',
                        ),
                        array(
                            'key' => 'cdv_end_date',
                            'value' => date('Y-m-d'),
                            'compare' => '>=',
                            'type' => 'DATE',
                        ),
                    ),
                ));

                if ($featured_travels->have_posts()) :
                    while ($featured_travels->have_posts()) : $featured_travels->the_post();
                        get_template_part('template-parts/content', 'activity-card');
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="no-travels">
                        <p>Nessuna attività disponibile al momento. <?php if (is_user_logged_in()) : ?><a href="<?php echo esc_url(home_url('/crea-attivita')); ?>">Crea il primo annuncio!</a><?php endif; ?></p>
                    </div>
                    <?php
                endif;
                ?>
            </div>

            <div class="text-center mt-3">
                <a href="<?php echo esc_url(get_post_type_archive_link('attivita')); ?>" class="btn-primary">
                    <?php echo esc_html(get_theme_mod('cdv_travels_button_text', 'Vedi Tutti gli Annunci')); ?> →
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="section how-it-works-section">
        <div class="container">
            <div class="section-title">
                <h2><?php echo esc_html(get_theme_mod('cdv_how_title', 'Come Funziona')); ?></h2>
                <p class="subtitle"><?php echo esc_html(get_theme_mod('cdv_how_subtitle', 'In pochi semplici passi puoi trovare i tuoi compagni di sport')); ?></p>
            </div>

            <div class="grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3><?php echo esc_html(get_theme_mod('cdv_step1_title', '1. Crea il Tuo Profilo')); ?></h3>
                    <p><?php echo esc_html(get_theme_mod('cdv_step1_text', 'Registrati e completa il tuo profilo con i tuoi sport preferiti e il livello di esperienza.')); ?></p>
                </div>

                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3><?php echo esc_html(get_theme_mod('cdv_step2_title', '2. Cerca o Pubblica un Annuncio')); ?></h3>
                    <p><?php echo esc_html(get_theme_mod('cdv_step2_text', 'Cerca tra gli annunci disponibili o pubblica il tuo e aspetta che altri sportivi si uniscano.')); ?></p>
                </div>

                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3><?php echo esc_html(get_theme_mod('cdv_step3_title', '3. Connettiti e Pratica Sport')); ?></h3>
                    <p><?php echo esc_html(get_theme_mod('cdv_step3_text', 'Usa la chat di gruppo per conoscere i compagni di sport e organizzare l\'attività insieme.')); ?></p>
                </div>
            </div>

            <style>
                .step-card {
                    text-align: center;
                    padding: calc(var(--spacing-unit) * 4);
                    border-radius: 12px;
                    transition: transform 0.3s ease;
                }
                .step-card:hover {
                    transform: translateY(-5px);
                }
                .step-number {
                    width: 60px;
                    height: 60px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.5rem;
                    font-weight: bold;
                    margin: 0 auto calc(var(--spacing-unit) * 2);
                }
                .step-card h3 {
                    margin-bottom: calc(var(--spacing-unit) * 2);
                }
            </style>
        </div>
    </section>

    <!-- Sport Stories Section -->
    <section class="section" style="background-color: white;">
        <div class="container">
            <div class="section-title">
                <h2>📖 <?php echo esc_html(get_theme_mod('cdv_stories_title', 'Storie Sportive')); ?></h2>
                <p><?php echo esc_html(get_theme_mod('cdv_stories_subtitle', 'Lasciati ispirare dalle esperienze dei nostri sportivi')); ?></p>
            </div>

            <div class="stories-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: calc(var(--spacing-unit) * 4);">
                <?php
                $recent_stories = new WP_Query(array(
                    'post_type' => 'storia-sport',
                    'posts_per_page' => 3,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC',
                ));

                if ($recent_stories->have_posts()) :
                    while ($recent_stories->have_posts()) : $recent_stories->the_post();
                        get_template_part('template-parts/content', 'story-card');
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="no-stories" style="grid-column: 1 / -1; text-align: center; padding: calc(var(--spacing-unit) * 4) 0;">
                        <p style="color: var(--text-medium);">Nessuna storia disponibile al momento.</p>
                    </div>
                    <?php
                endif;
                ?>
            </div>

            <?php if ($recent_stories->found_posts > 0) : ?>
                <div class="text-center mt-3">
                    <a href="<?php echo esc_url(home_url('/storie-sport')); ?>" class="btn-primary">
                        <?php echo esc_html(get_theme_mod('cdv_stories_button_text', 'Vedi Tutte le Storie')); ?> →
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="section">
        <div class="container">
            <div class="stats-grid">
                <?php
                global $wpdb;
                $total_travels = wp_count_posts('attivita')->publish;
                $total_users = count_users()['total_users'];
                $total_participants = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cdv_participants WHERE status = 'accepted'");
                ?>

                <div class="stat-item">
                    <div class="stat-number"><?php echo $total_travels; ?></div>
                    <div class="stat-label">Attività Pubblicate</div>
                </div>

                <div class="stat-item">
                    <div class="stat-number"><?php echo $total_users; ?></div>
                    <div class="stat-label">Sportivi Registrati</div>
                </div>

                <div class="stat-item">
                    <div class="stat-number"><?php echo $total_participants; ?></div>
                    <div class="stat-label">Partecipazioni</div>
                </div>

                <div class="stat-item">
                    <div class="stat-number">4.8</div>
                    <div class="stat-label">Rating Medio</div>
                </div>
            </div>

            <style>
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: calc(var(--spacing-unit) * 4);
                    text-align: center;
                }
                .stat-number {
                    font-size: 3rem;
                    font-weight: 700;
                    color: var(--primary-color);
                    margin-bottom: calc(var(--spacing-unit) * 1);
                }
                .stat-label {
                    font-size: 1.1rem;
                    color: var(--text-medium);
                }
            </style>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta-section" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
        <div class="container text-center">
            <h2 style="color: white;">Pronto per la Tua Prossima Attività Sportiva?</h2>
            <p style="font-size: 1.2rem; margin-bottom: calc(var(--spacing-unit) * 4); opacity: 0.95;">
                Unisciti a migliaia di sportivi che hanno già trovato i loro compagni di sport perfetti.
            </p>
            <?php if (is_user_logged_in()) : ?>
                <a href="<?php echo esc_url(home_url('/crea-attivita')); ?>" class="btn-primary">
                    Crea il Tuo Annuncio
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/registrazione')); ?>" class="btn-primary">
                    Registrati Gratis
                </a>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
get_footer();
