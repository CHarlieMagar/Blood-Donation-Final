<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once 'db_connect.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo '<p class="error">Unauthorized access</p>';
    exit;
}

try {
    // Check if a search term is provided
    $bloodType = isset($_GET['blood_type']) ? $_GET['blood_type'] : '';

    // Modify the query to filter by blood group if a search term is provided
    if (!empty($bloodType)) {
        $sql = "SELECT * FROM donors WHERE blood_group LIKE ? AND status = 'Available' ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%{$bloodType}%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT * FROM donors WHERE status = 'Available' ORDER BY id DESC";
        $result = $conn->query($sql);
    }

    if (!$result) {
        throw new Exception($conn->error);
    }

    // Check if any donors were found
    if ($result->num_rows === 0) {
        echo '<p class="no-results">No donors found' . (!empty($bloodType) ? ' for blood group: ' . htmlspecialchars($bloodType) : '') . '</p>';
    } else {
        // Loop through the donors and display their information
        while ($row = $result->fetch_assoc()) {
            echo '<div class="donor-card">';
            echo '<p>Name: ' . htmlspecialchars($row["name"]) . '</p>';
            echo '<p>Gender: ' . htmlspecialchars($row["gender"]) . '</p>';
            echo '<p>Blood Group: ' . htmlspecialchars($row["blood_group"]) . '</p>';
            echo '<p>Mobile No: ' . htmlspecialchars($row["mobile_no"]) . '</p>';
            echo '<p>Age: ' . htmlspecialchars($row["age"]) . '</p>';
            echo '<p>Email: ' . htmlspecialchars($row["email"]) . '</p>';
            echo '<p>Address: ' . htmlspecialchars($row["address"] ?? '-') . '</p>';
            echo '<button onclick="location.href=\'../Html/RequestBlood.html?donor_id=' . $row["id"] . '\'">Request</button>';
            echo '</div>';
        }
    }

} catch (Exception $e) {
    error_log("Error in get_donors.php: " . $e->getMessage());
    http_response_code(500);
    echo '<p class="error">Failed to fetch donors list</p>';
}

// Close the statement if it was used
if (isset($stmt)) {
    $stmt->close();
}

$conn->close();
?>
