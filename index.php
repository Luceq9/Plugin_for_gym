<?php
/*
Plugin Name: Gym Plugin
Description: Plugin for gym website, custom post type, shortcode, template.
Version: 1.0
Author: Luceq
Author URI: https://luckadomena.pl
*/

function my_custom_plugin_enqueue_scripts() {
    wp_enqueue_style('my-custom-plugin-style', plugins_url('style.css', __FILE__));
    wp_enqueue_script('my-custom-plugin-script', plugins_url('script.js', __FILE__), array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'my_custom_plugin_enqueue_scripts');

function my_custom_plugin_function() {
    echo '<div class="custom-posts-container">';
    $args = array(
        'post_type' => 'exercises',
        'posts_per_page' => -1
    );
    $custom_posts = new WP_Query($args);

    if ($custom_posts->have_posts()) {
        while ($custom_posts->have_posts()) {
            $custom_posts->the_post();
            $post_permalink = get_permalink();
            $thumbnail = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'medium') : 'path/to/placeholder.jpg';
            echo '<div class="custom-post-item" style="background-image: url(' . esc_url($thumbnail) . ');">';
            echo '<a href="' . esc_url($post_permalink) . '" class="custom-post-link">';
           // echo '<img src="' . esc_url($thumbnail) . '" alt="' . esc_attr(get_the_title()) . '" class="custom-post-thumbnail">';
            echo '<h2 class="custom-post-title">' . get_the_title() . '</h2>';
            echo '</a>';
            echo '</div>';
        }
    } else {
        echo 'Nie znaleźliśmy ćwiczeń.';
    }
    wp_reset_postdata();
    echo '</div>';
}

add_shortcode('my_custom_posts', 'my_custom_plugin_function');

function my_exercises_plugin_register_templates( $templates ) {
    $templates['single-exercises.php'] = 'Custom Post Template for Exercises';
    return $templates;
}
add_filter( 'theme_page_templates', 'my_exercises_plugin_register_templates' );

function my_exercises_plugin_load_template( $template ) {
    if ( get_post_type() === 'exercises' ) {
        $plugin_template = plugin_dir_path( __FILE__ ) . 'single-exercises.php';
        if ( file_exists( $plugin_template ) ) {
            // Debugowanie
            error_log('Szablon single-exercises.php został załadowany.');
            return $plugin_template;
        } else {
            // Debugowanie ścieżki pliku
            error_log('Ścieżka ' . $plugin_template . ' nie istnieje.');
        }
    }
    return $template;
}
add_filter( 'template_include', 'my_exercises_plugin_load_template' );


function my_exercises_post_type() {
    $labels = array(
        'name'          => _x('Exercises', 'Post Type General Name', 'text_domain'),
        'singular_name' => _x('Exercise', 'Post Type Singular Name', 'text_domain'),
        'menu_name'     => __('Exercises', 'text_domain'),
        'add_new'       => __('Add New', 'text_domain'),
        'add_new_item'  => __('Add New Exercise', 'text_domain'),
        'edit_item'     => __('Edit Exercise', 'text_domain'),
        'new_item'      => __('New Exercise', 'text_domain'),
        'view_item'     => __('View Exercise', 'text_domain'),
        'all_items'     => __('All Exercises', 'text_domain'),
        'search_items'  => __('Search Exercises', 'text_domain'),
        'not_found'     => __('No exercises found.', 'text_domain'),
        'not_found_in_trash' => __('No exercises found in Trash', 'text_domain'),
    );

    $args = array(
        'label'               => __('Exercises', 'text_domain'),
        'labels'              => $labels,
        'supports'            => array('title', 'editor', 'excerpt', 'thumbnail'),
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-admin-post',
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'rewrite'             => array('slug' => 'exercises'),
    );

    register_post_type('exercises', $args);
}
add_action('init', 'my_exercises_post_type');



function my_custom_add_metabox() {
    add_meta_box(
        'my_custom_metabox',        // ID metaboxa
        'Partie ciała',             // Tytuł metaboxa
        'my_custom_metabox_callback', // Callback funkcji
        'exercises',                // <-- Zmienione z 'custom_post' na 'exercises'
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'my_custom_add_metabox');



// Callback funkcji do wyświetlania metaboxa
function my_custom_metabox_callback($post) {
    // Pobierz zapisane wartości
    $selected_parts = get_post_meta($post->ID, '_my_custom_parts', true);
    $parts = array('Nogi', 'Plecy', 'Klatka piersiowa', 'Ramiona', 'Brzuch');

    // Wyświetl checkboxy
    echo '<div>';
    foreach ($parts as $part) {
        $checked = is_array($selected_parts) && in_array($part, $selected_parts) ? 'checked' : '';
        echo '<label>';
        echo '<input type="checkbox" name="my_custom_parts[]" value="' . esc_attr($part) . '" ' . $checked . '> ' . esc_html($part);
        echo '</label><br>';
    }
    echo '</div>';
}

// Zapisz metadane
function my_custom_save_metabox($post_id) {
    // Sprawdź, czy dane zostały przesłane
    if (isset($_POST['my_custom_parts'])) {
        $parts = array_map('sanitize_text_field', $_POST['my_custom_parts']);
        update_post_meta($post_id, '_my_custom_parts', $parts);
    } else {
        delete_post_meta($post_id, '_my_custom_parts');
    }
}

add_action('save_post', 'my_custom_save_metabox');