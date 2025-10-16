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
$bus_id = (int)($input['bus_id'] ?? 0);
if (!$bus_id) jsonResponse(['error' => 'bus_id is required'], 400);

$pdo = getPDO();

// Fields allowed to update
$fields = [
    'bus_number',
    'bus_name',
    'bus_type',
    'total_seats',
    'available_seats',
    'fare',
    'departure_datetime',
    'arrival_datetime',
    'route_id'
];

$setParts = [];
$params = [];

// Collect fields to update
foreach ($fields as $f) {
    if (isset($input[$f])) {
        $setParts[] = "$f = ?";
        $params[] = $input[$f];
    }
}

if (empty($setParts)) jsonResponse(['error' => 'No fields to update'], 400);

// Add bus_id to params for WHERE clause
$params[] = $bus_id;

// Update query
$sql = 'UPDATE buses SET ' . implode(', ', $setParts) . ' WHERE id = ?';
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute($params);
    jsonResponse(['success' => true, 'message' => 'Bus updated successfully']);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => 'Database error', 'details' => $e->getMessage()], 500);
}
