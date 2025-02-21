<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['status' => 'error', 'message' => 'Not authorized']);
    exit;
}

if (!isset($_POST['type']) || !isset($_POST['id']) || !isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    exit;
}

$type = $_POST['type'];
$id = (int)$_POST['id'];
$action = $_POST['action'];
$reason = $_POST['reason'] ?? '';
$admin = $_SESSION['admin_username'] ?? 'admin';

// Validate reason for rejection
if ($action === 'reject' && empty(trim($reason))) {
    echo json_encode(['status' => 'error', 'message' => 'Reason is required for rejection']);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Update the request status
    $table = ($type === 'emergency') ? 'emergency' : 'blood_requests';
    $status = ($action === 'accept') ? 'Accepted' : 'Rejected';
    
    $updateQuery = "UPDATE {$table} SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('si', $status, $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update request status");
    }

    // Add to history
    $historyQuery = "INSERT INTO request_history (request_id, request_type, name, blood_type, status, action_date, action_by, reason) 
                    SELECT id, ?, name, blood_type, ?, NOW(), ?, ? 
                    FROM {$table} WHERE id = ?";
    $stmt = $conn->prepare($historyQuery);
    $stmt->bind_param('ssssi', $type, $status, $admin, $reason, $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to add to history");
    }

    // Commit transaction
    $conn->commit();
    
    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>
