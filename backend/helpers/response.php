<?php
// helpers/response.php

function jsonResponse($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');

    // Ensure encoding errors are caught
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($json === false) {
        $error = json_last_error_msg();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => "JSON encoding error: $error"]);
        exit;
    }

    echo $json;
    exit;
}
