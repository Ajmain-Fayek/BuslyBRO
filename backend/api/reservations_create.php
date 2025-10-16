<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = getPDO();

    // Read request body
    $raw = file_get_contents('php://input');
    if (!$raw) {
        jsonResponse(['success' => false, 'error' => 'Empty request body'], 400);
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        jsonResponse(['success' => false, 'error' => 'Invalid JSON format'], 400);
    }

    // Extract and validate
    $user_id = $data['user_id'] ?? null;
    $bus_id = $data['bus_id'] ?? null;
    $seats = $data['seats'] ?? [];

    if (!$user_id || !$bus_id || empty($seats) || !is_array($seats)) {
        jsonResponse(['success' => false, 'error' => 'Missing or invalid parameters'], 400);
    }

    // Verify bus exists
    $stmt = $pdo->prepare("SELECT id, departure_datetime FROM buses WHERE id = ?");
    $stmt->execute([$bus_id]);
    $bus = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bus) {
        jsonResponse(['success' => false, 'error' => 'Bus not found'], 404);
    }

    // Check datetime safety
    if (empty($bus['departure_datetime'])) {
        jsonResponse(['success' => false, 'error' => 'Invalid bus departure time'], 400);
    }

    $now = new DateTime();
    $departure = new DateTime($bus['departure_datetime']);
    if ($departure <= $now) {
        jsonResponse(['success' => false, 'error' => 'Cannot book seats for a departed bus'], 400);
    }

    // Check if seats already booked
    $placeholders = implode(',', array_fill(0, count($seats), '?'));
    $checkQuery = "
        SELECT seat_number FROM reservations
        WHERE bus_id = ? AND seat_number IN ($placeholders) AND status != 'cancelled'
    ";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(array_merge([$bus_id], $seats));
    $bookedSeats = $checkStmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($bookedSeats)) {
        jsonResponse([
            'success' => false,
            'error' => 'Some seats are already booked',
            'bookedSeats' => $bookedSeats
        ], 409);
    }

    // Insert reservations
    $pdo->beginTransaction();
    $insertStmt = $pdo->prepare("
        INSERT INTO reservations (user_id, bus_id, seat_number, status, reserved_at)
        VALUES (?, ?, ?, 'confirmed', NOW())
    ");

    foreach ($seats as $seat) {
        $insertStmt->execute([$user_id, $bus_id, $seat]);
    }

    // increase available seats count
    $updateSeats = $pdo->prepare("
        UPDATE buses 
        SET available_seats = available_seats - ? 
        WHERE id = ?
    ");
    $updateSeats->execute([count($seats), $bus_id]);

    $pdo->commit();

    jsonResponse([
        'success' => true,
        'message' => 'Reservation created successfully',
        'bus_id' => $bus_id,
        'seats' => $seats
    ]);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Log and return readable error
    error_log('Reservation error: ' . $e->getMessage());
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
