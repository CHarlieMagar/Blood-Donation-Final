<?php
// Include database connection
include 'db_connection.php';

// Start session
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize user input
    $inputUsername = mysqli_real_escape_string($conn, $_POST['username']);
    $inputPassword = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check credentials
    $sql = "SELECT * FROM admin_credentials WHERE username = '$inputUsername'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the user data
        $row = $result->fetch_assoc();

        // Verify password
        if ($row['password'] === $inputPassword) { // Replace with password_verify() for hashed passwords
            // Set session variables for logged-in user
            $_SESSION['admin'] = $row['username'];
            header("Location: Admin.html"); // Redirect to admin dashboard
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid username.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Error</title>
</head>

<body>
    <div class="container">
        <h2>Login Error</h2>
        <p><?php echo isset($error) ? $error : ''; ?></p>
        <a href="login.html">Back to Login</a>
    </div>
</body>

</html>
