<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../middleware/auth_middleware.php'; // optional: protect for admin
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

$input = json_decode(file_get_contents('php://input'), true);

$id = (int)($input['id'] ?? 0);
$from = trim($input['route_from'] ?? '');
$to = trim($input['route_to'] ?? '');
$stops = trim($input['stops'] ?? '');

if (!$id) {
  jsonResponse(['success' => false, 'error' => 'Route ID required'], 400);
  exit;
}

if (!$from || !$to) {
  jsonResponse(['success' => false, 'error' => 'Route From and To are required'], 400);
  exit;
}

try {
  $pdo = getPDO();

  // Ensure route exists
  $check = $pdo->prepare("SELECT id FROM routes WHERE id = ?");
  $check->execute([$id]);
  if (!$check->fetch()) {
    jsonResponse(['success' => false, 'error' => 'Route not found'], 404);
    exit;
  }

  // Update route
  $stmt = $pdo->prepare("
        UPDATE routes 
        SET route_from = ?, route_to = ?, stops = ?
        WHERE id = ?
    ");
  $stmt->execute([$from, $to, $stops, $id]);

  jsonResponse(['success' => true, 'message' => 'Route updated successfully']);
} catch (Exception $e) {
  error_log('Update route error: ' . $e->getMessage());
  jsonResponse(['success' => false, 'error' => 'Database error occurred'], 500);
}
