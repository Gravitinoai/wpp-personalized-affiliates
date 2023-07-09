<?php
// api-proxy.php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Firebase\JWT\JWT;

// Check if this file is called by WordPress. If not, exit.
if (!defined('ABSPATH')) {
    exit;
}

// Add a new AJAX action
add_action('wp_ajax_proxy_request', 'handle_proxy_request');
add_action('wp_ajax_nopriv_proxy_request', 'handle_proxy_request');

function handle_proxy_request() {
    
    // get local path to google cloud service account credentials json file 
    // See https://developers.google.com/workspace/guides/create-credentials
    $path_to_gcloud_credential_json = $_ENV['PATH_TO_GCLOUD_CREDENTIAL_JSON'];
    // // get google cloud project id
    $gcloud_project_id = $_ENV['GCLOUD_PROJECT_ID'];
    $keyFile = file_get_contents($path_to_gcloud_credential_json);


    // ==================== LIST OF AFFILIATES ====================
    $partners = get_option('paff_partners');
    $partners_string = "";
    foreach ($partners as $index => $partner) {
        if ($partner!=""){
            $partners_string .= "===Partner" . ($index + 1) . ":===\n" . $partner . "\n\n";
        }
    }
    error_log('partners: ' . print_r($partners_string, true));

    // ==================== GET THE REQUEST DATA ====================
    // Check if the session data is set
    if (!isset($_POST['prompt'])) {
        echo json_encode(array('error' => 'No session data provided.'));
        wp_die();
    }

    // Get the session data from the client request
    $client_data = $_POST['prompt'];

    // The API URL
    $API_ENDPOINT = "us-central1-aiplatform.googleapis.com";
    $api_url = "https://{$API_ENDPOINT}/v1/projects/{$gcloud_project_id}/locations/us-central1/publishers/google/models/chat-bison@001:predict";

    // Array of data for the request
    $data = [
        'instances' => [
            [
                'context' => '',
                'examples' => [],
                'messages' => [
                    [
                        'author' => 'user',
                        'content' => $client_data,
                    ],
                ],
            ],
        ],
        'parameters' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 1024,
            'topP' => 0.8,
            'topK' => 40,
        ],
    ];

    // Get ID token for authentication
    $id_token = getIdToken($keyFile);

    // Array of headers
    $headers = [
        'Authorization' => 'Bearer ' . $id_token,
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
        if(isset($response_body['predictions'][0]['candidates'][0]['content'])) {
            $content = $response_body['predictions'][0]['candidates'][0]['content'];
            echo json_encode(['content' => $content]);
        } else {
            echo json_encode(['error' => 'The key does not exist in the response.']);
        }
    }

    wp_die();
}

function getIdToken($keyFile)
{
    $keyData = json_decode($keyFile, true);
    $payload = array(
        "iss" => $keyData['client_email'],
        "scope" => "https://www.googleapis.com/auth/cloud-platform",
        "aud" => "https://www.googleapis.com/oauth2/v4/token",
        "exp" => time() + 3600,
        "iat" => time()
    );

    $jwt = JWT::encode($payload, $keyData['private_key'], 'RS256');

    $client = new Client();
    $response = $client->post('https://www.googleapis.com/oauth2/v4/token', [
        'form_params' => [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]
    ]);

    $data = json_decode($response->getBody(), true);

    return $data['access_token'];
}

?>
