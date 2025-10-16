<?php
// backend/api/admin_dashboard.php

require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

// Ensure user is logged in
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? null;

if (!$user_id || $user_role !== 'admin') {
  jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
  exit;
}

try {
  $pdo = getPDO();

  // Total users
  $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
  $totalUsers = (int)$stmt->fetchColumn();

  // Total buses
  $stmt = $pdo->query("SELECT COUNT(*) as total_buses FROM buses");
  $totalBuses = (int)$stmt->fetchColumn();

  // Total bookings (confirmed)
  $stmt = $pdo->query("SELECT COUNT(*) as total_bookings FROM reservations WHERE status = 'confirmed'");
  $totalBookings = (int)$stmt->fetchColumn();

  // Total earnings (sum of fare * number of confirmed reservations)
  $stmt = $pdo->query("
        SELECT SUM(b.fare) as total_earnings
        FROM reservations r
        JOIN buses b ON r.bus_id = b.id
        WHERE r.status = 'confirmed'
    ");
  $totalEarnings = (float)($stmt->fetchColumn() ?? 0);

  jsonResponse([
    'success' => true,
    'data' => [
      'total_users' => $totalUsers,
      'total_buses' => $totalBuses,
      'total_bookings' => $totalBookings,
      'total_earnings' => $totalEarnings
    ]
  ]);
} catch (Exception $e) {
  error_log("Admin dashboard error: " . $e->getMessage());
  jsonResponse(['success' => false, 'error' => 'Server error'], 500);
}
