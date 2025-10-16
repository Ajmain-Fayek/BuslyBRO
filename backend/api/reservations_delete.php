<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

$input = json_decode(file_get_contents('php://input'), true);
$bus_id = (int)($input['bus_id'] ?? 0);
$departure = trim($input['departure_datetime'] ?? '');

if (!$bus_id || !$departure) {
    jsonResponse(['error' => 'bus_id and departure_datetime are required'], 400);
}

$pdo = getPDO();

try {
    $pdo->beginTransaction();

    // find all reservations for this user, bus, and departure time
    $stmt = $pdo->prepare("
        SELECT r.id, r.seat_number 
        FROM reservations r
        JOIN buses b ON r.bus_id = b.id
        WHERE r.user_id = ? 
          AND r.bus_id = ? 
          AND DATE_FORMAT(b.departure_datetime, '%Y-%m-%d %H:%i:%s') = ?
          AND r.status = 'confirmed'
        FOR UPDATE
    ");
    $stmt->execute([$_SESSION['user_id'], $bus_id, $departure]);
    $reservations = $stmt->fetchAll();

    if (!$reservations) {
        $pdo->rollBack();
        jsonResponse(['error' => 'No active reservations found'], 404);
    }

    // cancel all reservations
    $update = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?");
    foreach ($reservations as $r) {
        $update->execute([$r['id']]);
    }

    // increase available seats count
    $updateSeats = $pdo->prepare("
        UPDATE buses 
        SET available_seats = available_seats + ? 
        WHERE id = ?
    ");
    $updateSeats->execute([count($reservations), $bus_id]);

    $pdo->commit();
    jsonResponse(['message' => 'Your ticket (all seats) has been cancelled successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    jsonResponse(['error' => 'Server error', 'details' => $e->getMessage()], 500);
}
