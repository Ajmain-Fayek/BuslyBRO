<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

$from = $_GET['from'] ?? null;
$to = $_GET['to'] ?? null;

try {
  $pdo = getPDO();

  // Base query to get all buses with routes
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

  // Optional route filter
  if ($from && $to) {
    $query .= " AND r.route_from = :from AND r.route_to = :to";
    $params[':from'] = strtolower($from);
    $params[':to'] = strtolower($to);
  }

  $query .= " ORDER BY b.departure_datetime DESC";

  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
  $buses = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Decode JSON stops
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
