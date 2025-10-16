<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../middleware/auth_middleware.php'; // ensure only admin can create
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
  jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$route_from = trim(strtolower($input['route_from'] ?? ''));
$route_to   = trim(strtolower($input['route_to'] ?? ''));
$stops      = $input['stops'] ?? [];


if (!$route_from || !$route_to) {
  jsonResponse(['success' => false, 'error' => 'Route from and to are required'], 400);
  exit;
}

if (!is_array($stops) || empty($stops)) {
  // If no stops provided, auto-generate simple route path
  $stops = [$route_from, $route_to];
}

try {
  $pdo = getPDO();

  // Check if this route already exists
  // $check = $pdo->prepare("SELECT id FROM routes WHERE route_from = ? AND route_to = ?");
  // $check->execute([$route_from, $route_to]);
  // if ($check->fetch()) {
  //   jsonResponse(['success' => false, 'error' => 'Route already exists'], 409);
  //   exit;
  // }

  // Insert new route
  $stmt = $pdo->prepare("
        INSERT INTO routes (route_from, route_to, stops)
        VALUES (:from, :to, :stops)
    ");
  $stmt->execute([
    ':from'  => $route_from,
    ':to'    => $route_to,
    ':stops' => json_encode($stops, JSON_UNESCAPED_UNICODE)
  ]);

  jsonResponse([
    'success' => true,
    'message' => 'Route created successfully',
    'route_id' => $pdo->lastInsertId()
  ]);
} catch (Exception $e) {
  error_log('Create route error: ' . $e->getMessage());
  jsonResponse(['success' => false, 'error' => 'Database error occurred'], 500);
}
