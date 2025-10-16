<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

session_start();

// Get the current user’s token from the request headers
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
$token = '';

if (preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

// If token found, clear it from the DB
if (!empty($token)) {
    $pdo = getPDO();
    $stmt = $pdo->prepare("UPDATE users SET api_token = NULL WHERE api_token = ?");
    $stmt->execute([$token]);
}

// Clear PHP session if used
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();

// Send final JSON response
jsonResponse(['message' => 'Logged out successfully']);
