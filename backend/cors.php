<?php
// cors.php
require_once __DIR__ . '/config.php';

if (defined('ALLOWED_ORIGINS')) {
    if (ALLOWED_ORIGINS === '*') {
        header("Access-Control-Allow-Origin: *");
    } else {
        $allowedOrigins = explode(',', ALLOWED_ORIGINS);
        if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], array_map('trim', $allowedOrigins))) {
            header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
        }
    }
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400'); // cache preflight response for 24h 

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}
