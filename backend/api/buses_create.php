<?php
// backend/api/buses_create.php

require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../middleware/auth_middleware.php'; // Only admin can create
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

// Only allow admin
if ($_SESSION['role'] !== 'admin') {
    jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
}

$input = json_decode(file_get_contents('php://input'), true);

$route_id = (int)($input['route_id'] ?? 0);
$bus_number = trim($input['bus_number'] ?? '');
$bus_name = trim($input['bus_name'] ?? '');
$bus_type = trim($input['bus_type'] ?? '');
$total_seats = (int)($input['total_seats'] ?? 0);
$fare = (float)($input['fare'] ?? 0);
$departure_datetime = trim($input['departure_datetime'] ?? '');
$arrival_datetime = trim($input['arrival_datetime'] ?? '');

if (!$route_id || !$bus_number || !$bus_name || !$bus_type || $total_seats <= 0 || $fare <= 0 || !$departure_datetime || !$arrival_datetime) {
    jsonResponse(['success' => false, 'error' => 'All fields are required'], 400);
}

try {
    $pdo = getPDO();

    $stmt = $pdo->prepare(
        "INSERT INTO buses 
        (route_id, bus_number, bus_name, bus_type, total_seats, available_seats, fare, departure_datetime, arrival_datetime)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $route_id,
        $bus_number,
        $bus_name,
        $bus_type,
        $total_seats,
        $total_seats, // initially available seats = total seats
        $fare,
        $departure_datetime,
        $arrival_datetime
    ]);

    jsonResponse([
        'success' => true,
        'message' => 'Bus created successfully',
        'bus_id' => $pdo->lastInsertId()
    ]);
} catch (Exception $e) {
    error_log("Bus create error: " . $e->getMessage());
    jsonResponse(['success' => false, 'error' => 'Server error occurred'], 500);
}
