<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) jsonResponse(['error' => 'Invalid JSON body'], 400);

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if (!$name || !$email || !$password || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['error' => 'Invalid input'], 400);
}

$pdo = getPDO();

// check email exists
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) jsonResponse(['error' => 'Email already registered'], 409);

// create user
$hash = password_hash($password, PASSWORD_DEFAULT);
$insert = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
$insert->execute([$name, $email, $hash, "user"]);

jsonResponse(['message' => 'User registered'], 201);
