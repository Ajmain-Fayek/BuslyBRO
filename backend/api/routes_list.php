<?php
// backend/api/routes_list.php

require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

try {
  $pdo = getPDO();

  // Fetch all routes
  $stmt = $pdo->query("SELECT id AS route_id, route_from, route_to, stops FROM routes ORDER BY route_from, route_to");
  $routes = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Decode stops JSON for each route
  foreach ($routes as &$route) {
    $route['stops'] = json_decode($route['stops'], true) ?: [];
  }

  jsonResponse([
    'success' => true,
    'routes' => $routes
  ]);
} catch (Exception $e) {
  error_log("Routes list error: " . $e->getMessage());
  jsonResponse([
    'success' => false,
    'error' => 'Server error occurred'
  ], 500);
}
