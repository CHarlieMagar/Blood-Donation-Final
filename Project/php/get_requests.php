<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once 'admin_functions.php';
require_once 'db_connect.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit;
}

try {
    $stats = isset($_GET['stats']) && $_GET['stats'] === 'true';

    if ($stats) {
        // Get request statistics
        $query = "SELECT 
            blood_type,
            blood_required as status,
            COUNT(*) as count
            FROM blood_requests 
            GROUP BY blood_type, blood_required";
        
        $result = $conn->query($query);
        
        if (!$result) {
            throw new Exception($conn->error);
        }
        
        $stats = [
            'blood_types' => [],
            'status' => [],
            'timeline' => []
        ];
        
        while ($row = $result->fetch_assoc()) {
            // Count blood types
            if (!empty($row['blood_type'])) {
                if (!isset($stats['blood_types'][$row['blood_type']])) {
                    $stats['blood_types'][$row['blood_type']] = 0;
                }
                $stats['blood_types'][$row['blood_type']] += $row['count'];
            }
            
            // Count status
            if (!empty($row['status'])) {
                if (!isset($stats['status'][$row['status']])) {
                    $stats['status'][$row['status']] = 0;
                }
                $stats['status'][$row['status']] += $row['count'];
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $stats
        ]);
    } else {
        // Get request list
        $query = "SELECT * FROM blood_requests ORDER BY id DESC LIMIT 10";
        $result = $conn->query($query);
        
        if (!$result) {
            throw new Exception($conn->error);
        }
        
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'blood_type' => $row['blood_type'],
                'status' => $row['blood_required'],
                'created_at' => date('Y-m-d H:i:s'), // Using current date as created_at is not in table
                'message' => $row['message']
            ];
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $requests
        ]);
    }
} catch (Exception $e) {
    error_log("Error in get_requests.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch blood requests'
    ]);
}
?>
