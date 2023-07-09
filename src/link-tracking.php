<?php
class LinkClickTracker {
    function __construct() {
        add_action('wp_ajax_link_click', array($this, 'handle_link_click'));
        add_action('wp_ajax_nopriv_link_click', array($this, 'handle_link_click'));
        add_action('wp_ajax_page_loaded', array($this, 'handle_page_loaded'));
        add_action('wp_ajax_nopriv_page_loaded', array($this, 'handle_page_loaded'));
    }

    function enqueue_link_tracker($main_js_handle) {
        error_log('Enqueuing link tracker');
        wp_enqueue_script('link-tracker', plugins_url('/js/link-tracker.js', __FILE__), ['jquery', $main_js_handle], '1.0', true);
        wp_localize_script('link-tracker', 'link_tracker', ['ajax_url' => admin_url('admin-ajax.php')]);
    }

    function handle_page_loaded() {
        $this->handle_action("page_loaded");
    }

    function handle_link_click() {
        $this->handle_action("link_click");
    }

    private function handle_action($action) {
        // Get link from AJAX request
        $data = array(
            'link' => $_POST['link'],
            'originalText' => $_POST['originalText'],
            'modifiedText' => $_POST['modifiedText'],
            'isPersonalized' => $_POST['isPersonalized'],
            'personalInterests' => $_POST['personalInterests'],
            'linkText' => $_POST['linkText'],
            'sourceUrl' => $_POST['sourceUrl'],
            'timestamp' => $_POST['timestamp'],
            'userAgent' => $_POST['userAgent'],
            'postId' => $_POST['postId'],
            'postTitle' => $_POST['postTitle'],
            'postCategory' => $_POST['postCategory'],
            'action' => $action
        );

        // Log the data
        error_log(json_encode($data));

        // Write the data to a text file
        $filename = plugin_dir_path(__FILE__) . "data.txt";
        error_log($filename);
        $toWrite = json_encode($data) . PHP_EOL;

        if(!file_exists($filename)){
            touch($filename);
            chmod($filename, 0777);
        }

        $newFile= fopen($filename, 'a');
        fwrite($newFile, $toWrite);
        fclose($newFile);

        // Send back a JSON response
        $response = array('status' => 'success', 'message' => ucfirst($action) . ' tracked successfully.');
        echo json_encode($response);

        wp_die(); // This is required to terminate immediately and return a proper response
    }
}
