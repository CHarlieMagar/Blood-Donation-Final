<?php
require_once 'db_connect.php';

function adminLogout() {
    if (!isset($_SESSION)) {
        session_start();
    }

    // Clear all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    return ['success' => true];
}

header('Content-Type: application/json');

echo json_encode(adminLogout());
?>
