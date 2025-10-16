<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../middleware/auth_middleware.php'; // optional: protect for admin
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
  jsonResponse(['success' => false, 'error' => 'Route ID required'], 400);
  exit;
}

try {
  $pdo = getPDO();

  // Check if route exists
  $check = $pdo->prepare("SELECT id FROM routes WHERE id = ?");
  $check->execute([$id]);
  if (!$check->fetch()) {
    jsonResponse(['success' => false, 'error' => 'Route not found'], 404);
    exit;
  }

  // Delete route
  $stmt = $pdo->prepare("DELETE FROM routes WHERE id = ?");
  $stmt->execute([$id]);

  jsonResponse(['success' => true, 'message' => 'Route deleted successfully']);
} catch (Exception $e) {
  error_log('Delete route error: ' . $e->getMessage());
  jsonResponse(['success' => false, 'error' => 'Database error occurred'], 500);
}
