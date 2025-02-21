<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_POST['request_id']) || !isset($_POST['action']) || !isset($_POST['type'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    exit;
}

$request_id = $_POST['request_id'];
$action = $_POST['action']; // 'accept' or 'reject'
$type = $_POST['type']; // 'normal' or 'emergency'
$reason = $_POST['reason'] ?? '';
$admin_name = $_SESSION['admin_name'] ?? 'Admin'; // Make sure to set this during login

try {
    // Start transaction
    $conn->begin_transaction();

    // Update the request status in the original table
    $table = ($type === 'emergency') ? 'emergency' : 'blood_requests';
    $status = ($action === 'accept') ? 'Accepted' : 'Rejected';
    
    $update_sql = "UPDATE $table SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('si', $status, $request_id);
    $stmt->execute();

    // Get request details for history
    $select_sql = "SELECT name, blood_type FROM $table WHERE id = ?";
    $stmt = $conn->prepare($select_sql);
    $stmt->bind_param('i', $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();

    // Insert into history table
    $history_sql = "INSERT INTO request_history (request_id, request_type, name, blood_type, status, action_by, reason) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($history_sql);
    $request_type = ($type === 'emergency') ? 'emergency' : 'normal';
    $stmt->bind_param('issssss', 
        $request_id,
        $request_type,
        $request['name'],
        $request['blood_type'],
        $action,
        $admin_name,
        $reason
    );
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => "Request " . ucfirst($action) . "ed successfully"
    ]);

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode([
        'status' => 'error',
        'message' => 'Error processing request: ' . $e->getMessage()
    ]);
}
?>
