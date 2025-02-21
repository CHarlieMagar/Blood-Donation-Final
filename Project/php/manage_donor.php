<?php
require_once 'admin_functions.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';
$response = ['success' => false];

switch($action) {
    case 'add':
        $response['success'] = addDonor($_POST);
        break;
    case 'remove':
        $response['success'] = removeDonor($_POST['id']);
        break;
    default:
        $response['error'] = 'Invalid action';
}

echo json_encode($response);
?>
