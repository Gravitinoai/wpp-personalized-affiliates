<?php
/*
Plugin Name: Content personalization
Description: A plugin to log the category of a post, its content blocks, and send it to a specific API.
Version: 1.0
Author: Your Name
Author URI: Your Website
*/

require 'vendor/autoload.php';

class PAFF_Plugin {
    private $debug_panel;

    public function __construct() {
        // Include the necessary files.
        require_once plugin_dir_path(__FILE__) . 'api-proxy.php';
        require_once plugin_dir_path(__FILE__) . 'settings.php';
        require_once plugin_dir_path(__FILE__) . 'utils/is-bot.php';
        require_once plugin_dir_path(__FILE__) . 'debug-panel.php';
        require_once plugin_dir_path(__FILE__) . 'link-tracking.php';

        // Init debug panel
        $this->debug_panel = new DebugPanel();
        $this->link_click_tracker = new LinkClickTracker();

        // Register WordPress actions.
        add_action('wp_logout', array($this, 'end_session'));
        add_action('shutdown', array($this, 'end_session'));
        add_action('wp_enqueue_scripts', array($this, 'run'));
    }

    public function generate_uuid() {
        $uuid4 = \Ramsey\Uuid\Uuid::uuid4();
        return $uuid4->toString();
    }

    public function run() {
        if (!is_single() || is_bot()) {
            return;
        }

        if (!session_id()) {
            session_start();
        }

        $this->process_post();
    }

    public function process_post() {
        // ==================== SESSION ====================
        // ==================== SESSION ====================
        $post_id = get_the_ID();
        $categories = get_the_category($post_id);
        $post_title = get_the_title($post_id);
    
        // Iterate through each category and increment the view count.
        foreach ($categories as $category) {
            error_log('Category: ' . $category->cat_name);
            $count = $_SESSION['category_views'][$category->cat_name] ?? 0;
            $_SESSION['category_views'][$category->cat_name] = $count + 1;
        }
    
        // Extract relevant data from the post.
        $category_name = $categories[0]->cat_name;

        // view id
        $uuid = $this->generate_uuid();
    
        // Enqueue the necessary JavaScript files.
        wp_enqueue_script('taxonomy', plugins_url('/js/taxonomy.js', __FILE__), [], '1.0', true);
        wp_enqueue_script('google_topics', plugins_url('/js/google_topics.js', __FILE__), ['taxonomy'], '1.0', true);
        wp_enqueue_script('model_prompting', plugins_url('/js/model_prompting.js', __FILE__), ['jquery'], '1.0', true);
        wp_enqueue_script('main-js', plugins_url('/js/main.js', __FILE__), ['model_prompting', 'google_topics'], '1.0', true);
    
        // Pass data to the logger script.
        wp_localize_script('model_prompting', 'my_ajax_object', ['ajax_url' => admin_url('admin-ajax.php')]);
        wp_localize_script('main-js', 'postCategory', ['category_name' => $category_name]);
        wp_localize_script('main-js', 'postID', ['post_id' => $post_id]);
        wp_localize_script('main-js', 'postTitle', ['post_title' => $post_title]);
        wp_localize_script('main-js', 'viewID', ['view_id' => $uuid]);
        wp_localize_script('main-js', 'cat_views', ['views' => $_SESSION['category_views']]);


        // ==================== DEBUG PANEL ====================
        $this->debug_panel->gen_debug_panel('main-js');

        // ==================== LINK TRACKING ====================
        $this->link_click_tracker->enqueue_link_tracker('main-js');
    }

    public function end_session() {
        session_write_close();
    }
}

$paffPlugin = new PAFF_Plugin();
