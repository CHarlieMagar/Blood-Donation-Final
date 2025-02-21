<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once 'db_connect.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

try {
    // Get total donors count
    $donors_query = "SELECT COUNT(*) as count FROM donors";
    $donors_result = $conn->query($donors_query);
    $donors_count = $donors_result->fetch_assoc()['count'];

    // Get total blood requests count
    $requests_query = "SELECT COUNT(*) as count FROM blood_requests";
    $requests_result = $conn->query($requests_query);
    $requests_count = $requests_result->fetch_assoc()['count'];

    // Get total emergency requests count
    $emergency_query = "SELECT COUNT(*) as count FROM emergency";
    $emergency_result = $conn->query($emergency_query);
    $emergency_count = $emergency_result->fetch_assoc()['count'];

    // Get blood group distribution
    $blood_groups_query = "SELECT blood_group, COUNT(*) as count FROM donors GROUP BY blood_group";
    $blood_groups_result = $conn->query($blood_groups_query);
    $blood_groups = array();
    while ($row = $blood_groups_result->fetch_assoc()) {
        $blood_groups[$row['blood_group']] = $row['count'];
    }

    // Get recent donors
    $recent_donors_query = "SELECT * FROM donors ORDER BY id DESC LIMIT 5";
    $recent_donors_result = $conn->query($recent_donors_query);
    $recent_donors = array();
    while ($row = $recent_donors_result->fetch_assoc()) {
        $recent_donors[] = $row;
    }

    // Get recent requests
    $recent_requests_query = "SELECT * FROM blood_requests ORDER BY id DESC LIMIT 5";
    $recent_requests_result = $conn->query($recent_requests_query);
    $recent_requests = array();
    while ($row = $recent_requests_result->fetch_assoc()) {
        $recent_requests[] = $row;
    }

    // Get recent emergency requests
    $recent_emergency_query = "SELECT * FROM emergency ORDER BY id DESC LIMIT 5";
    $recent_emergency_result = $conn->query($recent_emergency_query);
    $recent_emergency = array();
    while ($row = $recent_emergency_result->fetch_assoc()) {
        $recent_emergency[] = $row;
    }

    // Prepare data array
    $data = array(
        'counts' => [
            'total_donors' => $donors_count,
            'total_requests' => $requests_count,
            'total_emergency' => $emergency_count
        ],
        'blood_groups' => $blood_groups,
        'recent_donors' => $recent_donors,
        'recent_requests' => $recent_requests,
        'recent_emergency' => $recent_emergency
    );

    echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);

} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to load dashboard data: ' . $e->getMessage()
    ]);
}
?>
