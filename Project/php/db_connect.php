<?php
if (!isset($_SESSION)) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 0);

// Database configuration
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'mysql';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper function to execute queries with error handling
function executeQuery($query, $types = null, $params = []) {
    global $conn;
    try {
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $conn->error);
        }

        if ($types && $params) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $stmt->close();

        return [
            'status' => 'success',
            'result' => $result
        ];
    } catch (Exception $e) {
        error_log("Query Error: " . $e->getMessage() . " in query: " . $query);
        return [
            'status' => 'error',
            'error' => $e->getMessage()
        ];
    }
}

// Helper function to sanitize input
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Helper function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Helper function to validate phone number
function validatePhone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
}

// Function to get dashboard statistics
function getDashboardStats() {
    $stats = [
        'total_donors' => 0,
        'total_requests' => 0,
        'total_emergency' => 0,
        'blood_groups' => [],
        'recent_donors' => [],
        'recent_requests' => [],
        'recent_emergency' => []
    ];

    // Get total counts
    $queries = [
        'donors' => "SELECT COUNT(*) as count FROM donors",
        'requests' => "SELECT COUNT(*) as count FROM blood_requests",
        'emergency' => "SELECT COUNT(*) as count FROM emergency"
    ];

    foreach ($queries as $key => $query) {
        $result = executeQuery($query);
        if ($result['status'] === 'success') {
            $row = $result['result']->fetch_assoc();
            $stats['total_' . $key] = $row['count'];
        }
    }

    // Get blood group distribution
    $blood_query = "SELECT blood_group, COUNT(*) as count FROM donors WHERE blood_group != '' GROUP BY blood_group";
    $result = executeQuery($blood_query);
    if ($result['status'] === 'success') {
        while ($row = $result['result']->fetch_assoc()) {
            $stats['blood_groups'][$row['blood_group']] = $row['count'];
        }
    }

    // Get recent activities
    $recent_queries = [
        'donors' => "SELECT * FROM donors ORDER BY id DESC LIMIT 5",
        'requests' => "SELECT * FROM blood_requests ORDER BY id DESC LIMIT 5",
        'emergency' => "SELECT * FROM emergency ORDER BY id DESC LIMIT 5"
    ];

    foreach ($recent_queries as $key => $query) {
        $result = executeQuery($query);
        if ($result['status'] === 'success') {
            while ($row = $result['result']->fetch_assoc()) {
                $stats['recent_' . $key][] = $row;
            }
        }
    }

    return $stats;
}

// Function to check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Function to handle admin login
function adminLogin($username, $password) {
    try {
        $query = "SELECT * FROM admin WHERE username = ?";
        $result = executeQuery($query, "s", [$username]);
        
        if ($result['status'] === 'success' && $result['result']->num_rows > 0) {
            $admin = $result['result']->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                return [
                    'status' => 'success',
                    'message' => 'Login successful'
                ];
            }
        }
        
        return [
            'status' => 'error',
            'message' => 'Invalid username or password'
        ];
    } catch (Exception $e) {
        error_log("Login Error: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Login failed: ' . $e->getMessage()
        ];
    }
}

// Function to handle admin logout
function adminLogout() {
    $_SESSION = array();
    session_destroy();
    return [
        'status' => 'success',
        'message' => 'Logout successful'
    ];
}
?>
