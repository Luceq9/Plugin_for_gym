<?php
/*
Template Name: Single exercises Template
*/

get_header(); ?>

<div class="post-center-wrapper"> <!-- Dodany kontener -->
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) : the_post();
                    if ( has_post_thumbnail() ) {
                        echo '<div class="custom-post-thumbnail">';
                        the_post_thumbnail('full', array('class' => 'styled-thumbnail'));
                        echo '<div class="custom-post-details">';
                        the_title('<h2>', '</h2>');
                        the_content();

                        // Wyświetl wybrane partie ciała
                        $selected_parts = get_post_meta(get_the_ID(), '_my_custom_parts', true);
                        if (!empty($selected_parts)) {
                            echo '<h3>Partie ciała:</h3>';
                            echo '<ul>';
                            foreach ($selected_parts as $part) {
                                echo '<li>' . esc_html($part) . '</li>';
                            }
                            echo '</ul>';
                        }

                        echo '</div></div>';
                    }
                endwhile;
            endif;
            ?>
        </main><!-- #main -->
    </div><!-- #primary -->
</div><!-- .post-center-wrapper -->

<?php get_footer(); ?>