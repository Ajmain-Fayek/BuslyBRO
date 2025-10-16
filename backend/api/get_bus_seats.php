<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

$bus_id = $_GET['bus_id'] ?? null;
if (!$bus_id) jsonResponse(['error' => 'Bus ID is required'], 400);

$pdo = getPDO();

// Fetch bus info
$stmt = $pdo->prepare('SELECT * FROM buses WHERE id = ?');
$stmt->execute([$bus_id]);
$bus = $stmt->fetch();
if (!$bus) jsonResponse(['error' => 'Bus not found'], 404);

// Fetch reserved seats
$stmt2 = $pdo->prepare('SELECT seat_number FROM reservations WHERE bus_id = ? AND status="confirmed"');
$stmt2->execute([$bus_id]);
$reservedSeats = $stmt2->fetchAll(PDO::FETCH_COLUMN);

jsonResponse([
  'bus' => $bus,
  'reserved_seats' => $reservedSeats
]);
