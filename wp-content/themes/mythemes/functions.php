<?php

function save_data_urls()
{
    $original_url = sanitize_text_field($_POST['original_url']);
    $short_url = generate_short_url();
    $post_id = create_new_post_shortened_url();

    update_post_meta($post_id, '_original_url', $original_url);
    update_post_meta($post_id, '_short_url', $short_url);

    wp_send_json_success(array('message' => 'Дані успішно збережено.'));
    wp_die();
}
add_action('wp_ajax_nopriv_save_data_urls', 'save_data_urls');
add_action("wp_ajax_save_data_urls", "save_data_urls");

function create_new_post_shortened_url()
{
    $args = array(
        'post_title' => 'Url',
        'post_status' => 'draft',
        'post_type' => 'shortened_url'
    );
    $post_id = wp_insert_post($args);
    return $post_id;
}

function show_data()
{
    $data = get_all_saved_urls();
    echo json_encode($data);
    wp_die();
}
add_action('wp_ajax_nopriv_show_data', 'show_data');
add_action("wp_ajax_show_data", "show_data");

function del_data_url()
{
    $post_id = $_POST['post_id'];
    wp_delete_post($post_id, true);
    wp_die();
}
add_action('wp_ajax_nopriv_del_data_url', 'del_data_url');
add_action("wp_ajax_del_data_url", "del_data_url");

function update_url()
{
    $post_id = intval($_POST['post_id']);
    $new_original_url = sanitize_text_field($_POST['new_original_url']);
    $new_short_url = sanitize_text_field($_POST['new_short_url']);
    $all_urls = get_all_saved_urls();

    $errors = new WP_Error();

    if (!$post_id) {
        $errors->add('1', 'Post not found !');
    }
    if (!$new_original_url) {
        $errors->add('2', 'Enter the url !');
    }
    if (!$new_short_url) {
        $errors->add('3', 'Enter the short url !');
    }
    if (isset($all_urls[$new_short_url])) {
        if (!($all_urls[$new_short_url]['post_id'] === $post_id)) {
            $errors->add('4', 'Such a short url already exists !');
        }
    }
    if (!preg_match('/^http:\/\/startprogectwp\/redirect-page\//', $new_short_url)) {
        $errors->add('5', 'The short URL should be http://startprojectwp/redirect-page/your_continue');
    }

    if ($errors->get_error_messages() == NULL) {
        update_post_meta($post_id, '_original_url', $new_original_url);
        update_post_meta($post_id, '_short_url', $new_short_url);
        wp_send_json_success();
    } else {
        wp_send_json_error($errors->get_error_messages());
    }
    wp_die();
}

add_action('wp_ajax_nopriv_update_url', 'update_url');
add_action("wp_ajax_update_url", "update_url");

function load_scripts()
{
    wp_enqueue_script(
        'jquery-3.7.1',
        'https://code.jquery.com/jquery-3.7.1.min.js');

    wp_enqueue_script(
        'ajax-form-url',
        get_template_directory_uri() . '/js/ajax_fotm_url.js');

    wp_enqueue_style(
        'bootstrap-css',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');

    wp_enqueue_style(
        'bootstrap-icons',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css');

    wp_enqueue_script(
        'bootstrap-js',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js');

    wp_enqueue_style('mystyle', get_template_directory_uri(). '/css/main.css');
    
}
add_action('wp_enqueue_scripts', 'load_scripts');
