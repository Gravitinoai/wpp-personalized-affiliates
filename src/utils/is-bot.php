<?php

function is_bot() {
    static $patterns = null;

    if ($patterns === null) {
        // Load data from the JSON file
        $data = json_decode(file_get_contents(__DIR__ . '/crawler-user-agents.json'), true);
        $patterns = array();

        foreach ($data as $entry) {
            $patterns[] = preg_quote($entry['pattern'], '/');
        }
    }

    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        // Testing with a giant regular expression
        $regexp = implode('|', $patterns);
        if (preg_match('/' . $regexp . '/', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
    }

    return false;
}

?>
