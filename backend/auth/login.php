<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

session_start();

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if (!$email || !$password) jsonResponse(['error' => 'Missing credentials'], 400);

$pdo = getPDO();
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();
if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse(['error' => 'Invalid credentials'], 401);
}

// login success
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['role'] = $user['role'];

$token = bin2hex(random_bytes(32));
$pdo->prepare('UPDATE users SET api_token = ? WHERE id = ?')->execute([$token, $user['id']]);

jsonResponse([
    'message' => 'Logged in',
    'token' => $token,
    'user' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'role' => $user['role']
    ]
]);
