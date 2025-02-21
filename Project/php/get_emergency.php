<?php
if (!isset($_SESSION)) {
    session_start();
}

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
        // Get emergency request statistics
        $query = "SELECT 
            blood_type,
            blood_required,
            created_at
            FROM emergency";
        
        $result = $conn->query($query);
        
        if (!$result) {
            throw new Exception($conn->error);
        }
        
        $stats = [
            'blood_types' => [],
            'response_time' => [
                '0-2 hours' => 0,
                '2-6 hours' => 0,
                '6-12 hours' => 0,
                '12-24 hours' => 0,
                '24+ hours' => 0
            ],
            'status' => [
                'Pending' => 0,
                'Fulfilled' => 0
            ]
        ];
        
        while ($row = $result->fetch_assoc()) {
            // Count blood types
            if (!empty($row['blood_type'])) {
                if (!isset($stats['blood_types'][$row['blood_type']])) {
                    $stats['blood_types'][$row['blood_type']] = 0;
                }
                $stats['blood_types'][$row['blood_type']]++;
            }
            
            // Calculate hours since creation
            $created = strtotime($row['created_at']);
            $now = time();
            $hours = round(($now - $created) / 3600);
            
            // Count response time
            if ($hours <= 2) {
                $stats['response_time']['0-2 hours']++;
            } elseif ($hours <= 6) {
                $stats['response_time']['2-6 hours']++;
            } elseif ($hours <= 12) {
                $stats['response_time']['6-12 hours']++;
            } elseif ($hours <= 24) {
                $stats['response_time']['12-24 hours']++;
            } else {
                $stats['response_time']['24+ hours']++;
            }
            
            // Count status (all current emergencies are considered pending)
            $stats['status']['Pending']++;
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $stats
        ]);
    } else {
        // Get emergency request list
        $query = "SELECT * FROM emergency ORDER BY created_at DESC";
        $result = $conn->query($query);
        
        if (!$result) {
            throw new Exception($conn->error);
        }
        
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'blood_required' => $row['blood_required'],
                'blood_type' => $row['blood_type'],
                'hospital' => $row['hospital'],
                'message' => $row['message'],
                'status' => $row['status'],
                'created_at' => $row['created_at']
            ];
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $requests
        ]);
    }
} catch (Exception $e) {
    error_log("Error in get_emergency.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch emergency requests'
    ]);
}
?>
