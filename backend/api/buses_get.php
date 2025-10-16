<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/response.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) jsonResponse(['error'=>'id required'], 400);
$pdo = getPDO();
$stmt = $pdo->prepare('SELECT * FROM buses WHERE id = ?');
$stmt->execute([$id]);
$bus = $stmt->fetch();
if (!$bus) jsonResponse(['error'=>'Not found'], 404);
jsonResponse(['bus'=>$bus]);
