<?php
/*
Plugin Name: Content personalization
Description: A plugin to log the category of a post, its content blocks, and send it to a specific API.
Version: 1.0
Author: Your Name
Author URI: Your Website
*/
// Include the API proxy.
include plugin_dir_path( __FILE__ ) . 'api-proxy.php';
include plugin_dir_path( __FILE__ ) . 'settings.php';

function custom_category_logger() {
    // Check if the current request is for a single post.
    if (!is_single()) {
        return;
    }

    // Start the session if it hasn't been started yet.
    if (!session_id()) {
        session_start();
    }

    $post_id = get_the_ID();
    $categories = get_the_category($post_id);

    // Iterate through each category and increment the view count.
    foreach ($categories as $category) {
        $count = $_SESSION['category_views'][$category->cat_name] ?? 0;
        $_SESSION['category_views'][$category->cat_name] = $count + 1;
    }

    // Only proceed if there is at least one category.
    if (count($categories) <= 0) {
        return;
    }

    // Extract relevant data from the post.
    $category_name = $categories[0]->cat_name;
    $post = get_post($post_id);
    $blocks = parse_blocks($post->post_content);
    $block_data = array_map(function($block) {
        return [
            'type' => $block['blockName'],
            'content' => $block['innerHTML'],
            'id' => md5($block['innerHTML']),  // Use a hash of the content as the unique identifier
        ];
    }, $blocks);

    // Enqueue the necessary JavaScript files.
    wp_enqueue_script('taxonomy', plugins_url('/js/taxonomy.js', __FILE__), [], '1.0', true);
    wp_enqueue_script('google_topics', plugins_url('/js/google_topics.js', __FILE__), ['taxonomy'], '1.0', true);
    wp_enqueue_script('model_prompting', plugins_url('/js/model_prompting.js', __FILE__), ['jquery'], '1.0', true);
    wp_enqueue_script('main-js', plugins_url('/js/main.js', __FILE__), ['model_prompting', 'google_topics'], '1.0', true);

    // Pass data to the logger script.
    wp_localize_script('model_prompting', 'my_ajax_object', ['ajax_url' => admin_url( 'admin-ajax.php' )]);
    wp_localize_script('main-js', 'postCategory', $category_name);
    wp_localize_script('main-js', 'blockData', $block_data);
    wp_localize_script('main-js', 'postID', $post_id);
    wp_localize_script('main-js', 'cat_views', $_SESSION['category_views']);
}

// Define session end handlers.
function end_session() {
    session_write_close();
}


// Register WordPress actions.
add_action('wp_logout', 'end_session');
add_action('shutdown', 'end_session');
add_action('wp_enqueue_scripts', 'custom_category_logger');
