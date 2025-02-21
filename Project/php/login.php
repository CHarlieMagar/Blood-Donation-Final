<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    // Get JSON data from request body
    $json = file_get_contents('php://input');
    if (!$json) {
        throw new Exception('No data received');
    }

    $data = json_decode($json, true);
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    // Validate required fields
    if (empty($data['username']) || empty($data['password'])) {
        throw new Exception('Username and password are required');
    }

    $username = sanitizeInput($data['username']);
    $password = $data['password'];

    // Get admin user
    $query = "SELECT * FROM admin WHERE username = ?";
    $result = executeQuery($query, "s", [$username]);

    if ($result['status'] === 'success' && $result['result']->num_rows > 0) {
        $admin = $result['result']->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $admin['password'])) {
            // Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful'
            ]);
        } else {
            throw new Exception('Invalid username or password');
        }
    } else {
        throw new Exception('Invalid username or password');
    }

} catch (Exception $e) {
    error_log("Login Error: " . $e->getMessage());
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
