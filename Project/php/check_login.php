<?php
require_once 'db_connect.php';

if (!isset($_SESSION)) {
    session_start();
}

header('Content-Type: application/json');

$response = [
    'status' => 'success',
    'logged_in' => isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true
];

echo json_encode($response);
?>
