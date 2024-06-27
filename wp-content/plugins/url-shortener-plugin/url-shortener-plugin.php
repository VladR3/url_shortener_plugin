<?php
/*
Plugin Name: URL Shortener Plugin
Description: A URL shortener plugin.
Version: 1.0
Author: Vlad Raikovskyi
*/

function url_shortener_register_post_type() {
    $labels = array(
        'name'               => 'Shortened URLs',
        'singular_name'      => 'Shortened URL',
        'menu_name'          => 'URL Shortener',
        'name_admin_bar'     => 'Shortened URL',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Shortened URL',
        'new_item'           => 'New Shortened URL',
        'edit_item'          => 'Edit Shortened URL',
        'view_item'          => 'View Shortened URL',
        'all_items'          => 'All Shortened URLs',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title')
    );

    register_post_type('shortened_url', $args);
}
add_action('init', 'url_shortener_register_post_type');

function url_shortener_add_meta_boxes() {
    add_meta_box('original_url', 'Original URL', 'url_shortener_meta_box_callback', 'shortened_url', 'normal', 'high');
}
add_action('add_meta_boxes', 'url_shortener_add_meta_boxes');

function url_shortener_meta_box_callback($post) {
    wp_nonce_field('save_original_url', 'original_url_nonce');
    $value = get_post_meta($post->ID, '_original_url', true);
    echo '<label for="original_url">Original URL: </label>';
    echo '<input type="url" id="original_url" name="original_url" value="' . esc_attr($value) . '" size="25" />';
}

/*function get_all_saved_urls() {
    $args = array(
        'post_type' => 'shortened_url',
        'posts_per_page' => -1,
    );
    global $urls;
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $original_url = get_post_meta(get_the_ID(), '_original_url', true);
            $short_url = get_post_meta(get_the_ID(), '_short_url', true);

            $urls[$short_url] = $original_url;
        }
    } else {
        echo '<p>No URLs found.</p>';
    }
    //wp_reset_postdata();
    return $urls;
}*/

function get_all_saved_urls(){
    global $wpdb;

    $query = "SELECT pm1.post_id, pm1.meta_value AS short_url, pm2.meta_value AS original_url
              FROM {$wpdb->prefix}postmeta AS pm1
              INNER JOIN {$wpdb->prefix}postmeta AS pm2 ON pm1.post_id = pm2.post_id
              WHERE pm1.meta_key = '_short_url' AND pm2.meta_key = '_original_url'";

    $results = $wpdb->get_results($query);

    $urls = array();

    if ($results) {
        foreach ($results as $row) {
            $short_url = $row->short_url;
            $original_url = $row->original_url;
            $urls[$short_url] = $original_url;
        }
    } 
    return $urls;
}

add_action('wp_loaded', 'get_all_saved_urls');

function url_shortener_save_post($post_id) {
    if (!isset($_POST['original_url_nonce'])) {
        return $post_id;
    }

    if (!wp_verify_nonce($_POST['original_url_nonce'], 'save_original_url')) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    }

    if (!isset($_POST['original_url'])) {
        return $post_id;
    }

    $original_url = sanitize_text_field($_POST['original_url']);
    $short_url = get_short_url($original_url);

    update_post_meta($post_id, '_original_url', $original_url);
    update_post_meta($post_id, '_short_url', $short_url);
}
add_action('save_post', 'url_shortener_save_post');

function get_short_url($url){
    $short_url = 'http://startprogectwp/' . url_shortener_generate_code() . '/';
    return $short_url;
}

function url_shortener_generate_code($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $random_string = '';
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, $characters_length - 1)];
    }
    return $random_string;
}

function set_custom_edit_url_columns($columns) {
    $columns['original_url'] = 'Повна';
    $columns['short_url'] = 'Скорочена';
    return $columns;
}
add_filter('manage_shortened_url_posts_columns', 'set_custom_edit_url_columns');



function custom_shortened_url_column($column, $post_id) {
    switch ($column) 
    {
        case 'original_url' :
            $original_url = get_post_meta($post_id, '_original_url', true);
            echo $original_url;
            break;

        case 'short_url' :
            $short_url = get_post_meta($post_id, '_short_url', true);
            echo $short_url;
            break;
    }
}
add_action('manage_shortened_url_posts_custom_column', 'custom_shortened_url_column', 10, 2);

add_action('template_redirect', 'url_shortener_redirect_to_original_url');

function url_shortener_redirect_to_original_url() {
    $urls = get_all_saved_urls();
    $requested_url = 'http://startprogectwp'.$_SERVER['REQUEST_URI'];
    var_dump(isset($urls[$requested_url]));
    if (isset($urls[$requested_url])) {
        $original_url = $urls[$requested_url];
        wp_redirect($original_url); 
        exit;
    }
}
add_action('template_redirect', 'url_shortener_redirect_to_original_url');