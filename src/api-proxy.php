<?php
// api-proxy.php

// Check if this file is called by WordPress. If not, exit.
if (!defined('ABSPATH')) {
    exit; 
}

// Add a new AJAX action
add_action('wp_ajax_proxy_request', 'handle_proxy_request');
add_action('wp_ajax_nopriv_proxy_request', 'handle_proxy_request');

function handle_proxy_request() {
    // Check if the session data is set
    if (!isset($_POST['prompt'])) {
        echo json_encode(array('error' => 'No session data provided.'));
        wp_die();
    }

    // Get the session data from the client request
    $client_data = $_POST['prompt'];

    // Your private API key
    $api_key = get_option('paff_google_api_key');
    if (!$api_key) {
        echo json_encode(array('error' => 'No API key provided.'));
        wp_die();
    }

    // The API URL
    $api_url = 'https://api.openai.com/v1/chat/completions';

    // Array of data for the request
    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [["role" => "user", "content" => $client_data]],
        "temperature" => 0.7,
    ];

    // Array of headers
    $headers = [
        'Authorization' => 'Bearer ' . $api_key,
        'Content-Type' => 'application/json',
    ];

    // Prepare the arguments for the request
    $args = [
        'method' => 'POST',
        'headers' => $headers,
        'body' => json_encode($data),
        'data_format' => 'body',
    ];

    // Use WordPress' built-in HTTP API to make the request
    $response = wp_remote_post($api_url, $args);

    // Check for errors
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        // Format the error message as JSON and echo it
        echo json_encode(['error' => $error_message]);
    } else {
        // Decode the JSON response
        $response_body = json_decode(wp_remote_retrieve_body($response), true);

        // Access the content
        if(isset($response_body['choices'][0]['message']['content'])) {
            $content = $response_body['choices'][0]['message']['content'];
            echo json_encode(['content' => $content]);
        } else {
            echo json_encode(['error' => 'The key does not exist in the response.']);
        }
    }

    // Always end your AJAX functions with wp_die()
    wp_die();
}
