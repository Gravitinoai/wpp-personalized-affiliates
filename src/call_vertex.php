<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('./');
$dotenv->load();

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Firebase\JWT\JWT;

// get local path to google cloud service account credentials json file see https://developers.google.com/workspace/guides/create-credentials
$path_to_gcloud_credential_json = $_ENV['PATH_TO_GCLOUD_CREDENTIAL_JSON'];
// get google cloud project id
$gcloud_project_id = $_ENV['GCLOUD_PROJECT_ID'];


$API_ENDPOINT = "us-central1-aiplatform.googleapis.com";
$URL = "https://{$API_ENDPOINT}/v1/projects/{$gcloud_project_id}/locations/us-central1/publishers/google/models/chat-bison@001:predict";
$keyFile = file_get_contents($path_to_gcloud_credential_json);

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

function getTextPalm($prompt, $temperature = 0.5)
{
    global $URL, $keyFile;

    $headers = [
        'Authorization' => 'Bearer ' . getIdToken($keyFile),
        'Content-Type' => 'application/json',
    ];

    $data = [
        'instances' => [
            [
                'context' => '',
                'examples' => [],
                'messages' => [
                    [
                        'author' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ],
        ],
        'parameters' => [
            'temperature' => $temperature,
            'maxOutputTokens' => 1024,
            'topP' => 0.8,
            'topK' => 40,
        ],
    ];

    $client = new Client(['headers' => $headers]);

    try {
        $response = $client->post($URL, ['body' => json_encode($data)]);

        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody(), true);
            return $result['predictions'][0]['candidates'][0]['content'];
        } else {
            echo $response->getReasonPhrase();
            throw new Exception("Request failed " . $response->getReasonPhrase());
        }
    } catch (RequestException $e) {
        echo $e->getResponse()->getBody(true);
    }
}
$prompt = "Give me ten interview questions for the role of program manager.";
$response = getTextPalm($prompt);
echo $response;
?>