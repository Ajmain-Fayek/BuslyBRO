<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
  jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id = (int)($input['id'] ?? 0);

if (!$id) {
  jsonResponse(['success' => false, 'error' => 'Bus ID is required'], 400);
  exit;
}

try {
  $pdo = getPDO();

  // Optional: check if bus exists
  $check = $pdo->prepare('SELECT id FROM buses WHERE id = ?');
  $check->execute([$id]);
  if (!$check->fetch()) {
    jsonResponse(['success' => false, 'error' => 'Bus not found'], 404);
    exit;
  }

  // Delete bus
  $stmt = $pdo->prepare('DELETE FROM buses WHERE id = ?');
  $stmt->execute([$id]);

  jsonResponse(['success' => true, 'message' => 'Bus deleted successfully']);
} catch (Exception $e) {
  jsonResponse([
    'success' => false,
    'error' => 'Server error: ' . $e->getMessage()
  ], 500);
}
