<?php
// middleware/auth_middleware.php
require_once __DIR__ . '/../helpers/response.php';
session_start();
if (empty($_SESSION['user_id'])) {
    jsonResponse(['error'=>'Unauthorized'], 401);
}
