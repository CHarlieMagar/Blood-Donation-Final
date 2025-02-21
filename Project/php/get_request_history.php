<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: ../Html/Login.html");
    exit;
}

try {
    $query = "SELECT 
                id,
                request_id,
                request_type,
                name,
                blood_type,
                status,
                action_date,
                action_by,
                reason
            FROM request_history 
            ORDER BY action_date DESC";

    $result = $conn->query($query);
    
    echo "<table class='table'>
            <thead>
                <tr>
                    <th>Request Type</th>
                    <th>Name</th>
                    <th>Blood Type</th>
                    <th>Status</th>
                    <th>Action Date</th>
                    <th>Action By</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>";
    
    while ($row = $result->fetch_assoc()) {
        $status = ucfirst($row['status']);
        $statusClass = strtolower($status);
        
        echo "<tr>
                <td>" . htmlspecialchars(ucfirst($row['request_type'])) . "</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
                <td>" . htmlspecialchars($row['blood_type']) . "</td>
                <td><span class='status-badge {$statusClass}'>" . htmlspecialchars($status) . "</span></td>
                <td>" . htmlspecialchars(date('Y-m-d H:i:s', strtotime($row['action_date']))) . "</td>
                <td>" . htmlspecialchars($row['action_by']) . "</td>
                <td>" . htmlspecialchars($row['reason'] ?? '-') . "</td>
            </tr>";
    }
    
    echo "</tbody></table>";

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

$conn->close();
?>
