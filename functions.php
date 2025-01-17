<?php
// Register Custom Post Type
function my_custom_post_type() {
    $args = array(
        'public'    => true,
        'label'     => 'Custom Posts',
        'menu_icon' => 'dashicons-admin-post', // Ikona w menu
    );
    register_post_type('custom_post', $args);
}

add_action('init', 'my_custom_post_type');