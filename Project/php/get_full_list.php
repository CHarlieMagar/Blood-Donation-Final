<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: ../Html/Login.html");
    exit;
}

$type = isset($_GET['type']) ? $_GET['type'] : '';

try {
    switch ($type) {
        case 'donors':
            $query = "SELECT * FROM donors ORDER BY id DESC";
            $result = $conn->query($query);
            echo "<table class='table'>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Blood Group</th>
                            <th>Age</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>";
            
            while ($row = $result->fetch_assoc()) {
                $status = $row['status'] ?? 'Available';
                $statusClass = $status == 'Available' ? 'available' : 'unavailable';
                
                echo "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['blood_group']) . "</td>
                        <td>" . htmlspecialchars($row['age']) . "</td>
                        <td>" . htmlspecialchars($row['mobile_no']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['address'] ?? '-') . "</td>
                        <td><span class='status-badge {$statusClass}'>" . htmlspecialchars($status) . "</span></td>
                    </tr>";
            }
            break;
            
        case 'requests':
            $query = "SELECT * FROM blood_requests ORDER BY id DESC";
            $result = $conn->query($query);
            echo "<table class='table'>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Blood Type</th>
                            <th>Required For</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>";
            
            while ($row = $result->fetch_assoc()) {
                $status = $row['status'] ?? 'Pending';
                $statusClass = $status == 'Pending' ? 'pending' : ($status == 'Accepted' ? 'accepted' : 'rejected');
                
                echo "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['blood_type']) . "</td>
                        <td>" . htmlspecialchars($row['blood_required']) . "</td>
                        <td>" . htmlspecialchars($row['phone'] ?? '-') . "</td>
                        <td><span class='status-badge {$statusClass}'>" . htmlspecialchars($status) . "</span></td>
                        <td class='action-buttons'>";
                if ($status == 'Pending') {
                    echo "<button class='btn btn-success btn-sm' onclick='handleRequestAction(\"normal\", {$row['id']}, \"accept\")'>
                            <i class='fas fa-check'></i> Accept
                        </button>
                        <button class='btn btn-danger btn-sm' onclick='handleRequestAction(\"normal\", {$row['id']}, \"reject\")'>
                            <i class='fas fa-times'></i> Reject
                        </button>";
                }
                echo "</td></tr>";
            }
            break;
            
        case 'emergency':
            $query = "SELECT * FROM emergency ORDER BY id DESC";
            $result = $conn->query($query);
            echo "<table class='table'>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Blood Type</th>
                            <th>Hospital</th>
                            <th>Contact</th>
                            <th>Required Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>";
            
            while ($row = $result->fetch_assoc()) {
                $status = $row['status'] ?? 'Pending';
                $statusClass = $status == 'Pending' ? 'pending' : ($status == 'Accepted' ? 'accepted' : 'rejected');
                
                echo "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['blood_type']) . "</td>
                        <td>" . htmlspecialchars($row['hospital'] ?? '-') . "</td>
                        <td>" . htmlspecialchars($row['contact'] ?? '-') . "</td>
                        <td>" . htmlspecialchars($row['required_date'] ?? '-') . "</td>
                        <td><span class='status-badge {$statusClass}'>" . htmlspecialchars($status) . "</span></td>
                        <td class='action-buttons'>";
                if ($status == 'Pending') {
                    echo "<button class='btn btn-success btn-sm' onclick='handleRequestAction(\"emergency\", {$row['id']}, \"accept\")'>
                            <i class='fas fa-check'></i> Accept
                        </button>
                        <button class='btn btn-danger btn-sm' onclick='handleRequestAction(\"emergency\", {$row['id']}, \"reject\")'>
                            <i class='fas fa-times'></i> Reject
                        </button>";
                }
                echo "</td></tr>";
            }
            break;
            
        default:
            echo "<div class='alert alert-danger'>Invalid type specified</div>";
            exit;
    }
    
    echo "</tbody></table>";

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

$conn->close();
?>
