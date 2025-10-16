<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

$pdo = getPDO();
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    jsonResponse(['success' => false, 'error' => 'User not authenticated'], 401);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            r.id AS reservation_id,
            r.status,
            r.seat_number,
            r.reserved_at,
            b.id AS bus_id,
            b.bus_number,
            b.bus_name,
            b.bus_type,
            b.total_seats,
            b.available_seats,
            b.fare,
            b.departure_datetime,
            b.arrival_datetime,
            rt.id AS route_id,
            rt.route_from,
            rt.route_to,
            rt.stops
        FROM reservations r
        INNER JOIN buses b ON r.bus_id = b.id
        INNER JOIN routes rt ON b.route_id = rt.id
        WHERE r.user_id = ?
        ORDER BY b.departure_datetime DESC
    ");
    $stmt->execute([$user_id]);
    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $grouped = [];

    foreach ($all as $row) {
        // ✅ Group by bus_id AND departure_datetime
        $key = $row['bus_id'] . '_' . $row['departure_datetime'];

        if (!isset($grouped[$key])) {
            $grouped[$key] = [
                'bus_id' => $row['bus_id'],
                'bus_number' => $row['bus_number'],
                'bus_name' => $row['bus_name'],
                'bus_type' => $row['bus_type'],
                'total_seats' => $row['total_seats'],
                'available_seats' => $row['available_seats'],
                'fare' => $row['fare'],
                'departure_datetime' => $row['departure_datetime'],
                'arrival_datetime' => $row['arrival_datetime'],
                'route_id' => $row['route_id'],
                'route_from' => $row['route_from'],
                'route_to' => $row['route_to'],
                'stops' => json_decode($row['stops'], true) ?: [],
                'status' => $row['status'],
                'reserved_at' => $row['reserved_at'],
                'seats' => [],
            ];
        }

        // Add seat number to that trip
        $grouped[$key]['seats'][] = $row['seat_number'];
    }

    $upcoming = [];
    $past = [];
    $now = new DateTime();

    foreach ($grouped as $journey) {
        $departure = new DateTime($journey['departure_datetime']);
        if ($departure >= $now) {
            $upcoming[] = $journey;
        } else {
            $past[] = $journey;
        }
    }

    jsonResponse([
        'success' => true,
        'upcoming' => array_values($upcoming),
        'past' => array_values($past)
    ]);
} catch (Exception $e) {
    error_log("Reservation list error: " . $e->getMessage());
    jsonResponse(['success' => false, 'error' => 'Server error occurred'], 500);
}
