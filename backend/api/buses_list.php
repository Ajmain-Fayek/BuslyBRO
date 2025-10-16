<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

$from = $_GET['from'] ?? null;
$to = $_GET['to'] ?? null;
$date = $_GET['date'] ?? date('Y-m-d');

// ✅ Validate and normalize date
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $date = date('Y-m-d');
}

$inputTimestamp = strtotime($date);
$todayTimestamp = strtotime(date('Y-m-d'));

// ✅ If user's date is in the past, reset to today
if ($inputTimestamp < $todayTimestamp) {
    $date = date('Y-m-d');
}

try {
    $pdo = getPDO();

    // ✅ Base query
    $query = "
        SELECT 
            b.id AS bus_id,
            b.bus_number,
            b.bus_name,
            b.bus_type,
            b.total_seats,
            b.available_seats,
            b.fare,
            b.departure_datetime,
            b.arrival_datetime,
            r.id AS route_id,
            r.route_from,
            r.route_to,
            r.stops
        FROM buses b
        JOIN routes r ON b.route_id = r.id
        WHERE 1
    ";

    $params = [];

    // ✅ Filter by date
    if ($inputTimestamp === $todayTimestamp) {
        $query .= " AND b.departure_datetime >= NOW()";
    } else {
        $query .= " AND DATE(b.departure_datetime) = :date";
        $params[':date'] = $date;
    }

    // ✅ Optional route filter
    if ($from && $to) {
        $query .= " AND r.route_from = :from AND r.route_to = :to";
        $params[':from'] = strtolower($from);
        $params[':to'] = strtolower($to);
    }

    $query .= " ORDER BY b.departure_datetime ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $buses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ✅ Decode JSON stops for each route
    foreach ($buses as &$bus) {
        $bus['stops'] = json_decode($bus['stops'], true) ?? [];
    }

    jsonResponse([
        'success' => true,
        'buses' => $buses
    ]);
} catch (Exception $e) {
    jsonResponse([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ], 500);
}
