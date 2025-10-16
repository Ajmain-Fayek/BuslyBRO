<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../helpers/response.php';
session_start();

if (!empty($_SESSION['user_id'])) {
    jsonResponse(['user'=>[
        'id'=>$_SESSION['user_id'],
        'name'=>$_SESSION['user_name'],
        'role'=>$_SESSION['role']
    ]]);
}
jsonResponse(['user'=>null], 200);
