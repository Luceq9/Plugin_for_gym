<?php
/*
Plugin Name: My Custom Plugin
Description: A custom plugin for WordPress.
Version: 1.0
Author: Your Name
*/

function my_custom_plugin_enqueue_scripts() {
    wp_enqueue_style('my-custom-plugin-style', plugins_url('style.css', __FILE__));
    wp_enqueue_script('my-custom-plugin-script', plugins_url('script.js', __FILE__), array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'my_custom_plugin_enqueue_scripts');

function my_custom_plugin_function() {
    echo '<div class="custom-posts-container">';
    $args = array(
        'post_type' => 'custom_post',
        'posts_per_page' => -1
    );
    $custom_posts = new WP_Query($args);

    if ($custom_posts->have_posts()) {
        while ($custom_posts->have_posts()) {
            $custom_posts->the_post();
            $thumbnail = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'medium') : 'path/to/placeholder.jpg';
            echo '<div class="custom-post-item"';
            echo '<a href="' . get_permalink() . '" class="custom-post-link">';
            echo '<img src="' . esc_url($thumbnail) . '" alt="' . esc_attr(get_the_title()) . '" class="custom-post-thumbnail">';
            echo '<h2 class="custom-post-title">' . get_the_title() . '</h2>';
            echo '</a>';
            echo '</div>';
        }
    } else {
        echo 'No custom posts found.';
    }
    wp_reset_postdata();
    echo '</div>';
}

add_shortcode('my_custom_posts', 'my_custom_plugin_function');

function my_custom_shortcode() {
    return "Hello, this is my custom shortcode!";
}

add_shortcode('my_custom_shortcode', 'my_custom_shortcode');


function my_custom_plugin_register_templates( $templates ) {
    $templates['single-custom_post.php'] = 'Custom Post Template';
    return $templates;
}
add_filter( 'theme_page_templates', 'my_custom_plugin_register_templates' );

function my_custom_plugin_load_template( $template ) {
    if ( get_post_type() == 'custom_post' ) {
        $plugin_template = plugin_dir_path( __FILE__ ) . 'single-custom_post.php';
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'my_custom_plugin_load_template' );

function my_custom_post_type() {
    $labels = array(
        'name'                  => _x('Custom Posts', 'Post Type General Name', 'text_domain'),
        'singular_name'         => _x('Custom Post', 'Post Type Singular Name', 'text_domain'),
        'menu_name'             => __('Custom Posts', 'text_domain'),
        'name_admin_bar'        => __('Custom Post', 'text_domain'),
        'archives'              => __('Item Archives', 'text_domain'),
        'attributes'            => __('Item Attributes', 'text_domain'),
        'parent_item_colon'     => __('Parent Item:', 'text_domain'),
        'all_items'             => __('All Items', 'text_domain'),
        'add_new_item'          => __('Add New Item', 'text_domain'),
        'add_new'               => __('Add New', 'text_domain'),
        'new_item'              => __('New Item', 'text_domain'),
        'edit_item'             => __('Edit Item', 'text_domain'),
        'update_item'           => __('Update Item', 'text_domain'),
        'view_item'             => __('View Item', 'text_domain'),
        'view_items'            => __('View Items', 'text_domain'),
        'search_items'          => __('Search Item', 'text_domain'),
        'not_found'             => __('Not found', 'text_domain'),
        'not_found_in_trash'    => __('Not found in Trash', 'text_domain'),
        'featured_image'        => __('Featured Image', 'text_domain'),
        'set_featured_image'    => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image'    => __('Use as featured image', 'text_domain'),
        'insert_into_item'      => __('Insert into item', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'text_domain'),
        'items_list'            => __('Items list', 'text_domain'),
        'items_list_navigation' => __('Items list navigation', 'text_domain'),
        'filter_items_list'     => __('Filter items list', 'text_domain'),
    );
    $args = array(
        'label'                 => __('Custom Post', 'text_domain'),
        'description'           => __('Custom Post Type Description', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
        'taxonomies'            => array('category', 'post_tag'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'menu_icon'             => 'dashicons-admin-post',
    );
    register_post_type('custom_post', $args);
}

add_action('init', 'my_custom_post_type');


// Dodaj metabox
function my_custom_add_metabox() {
    add_meta_box(
        'my_custom_metabox', // ID metaboxa
        'Partie ciała', // Tytuł metaboxa
        'my_custom_metabox_callback', // Callback funkcji
        'custom_post', // Typ postu
        'side', // Kontekst (side, normal, advanced)
        'default' // Priorytet (default, low, high)
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