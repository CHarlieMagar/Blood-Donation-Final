<?php

include 'db_connect.php';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $blood_required = $_POST['blood-require'];
    $blood_type = $_POST['blood-type'];
    $message = $_POST["message"];

    $checkEmail = "SELECT * FROM blood_requests WHERE email='$email'";
    $result = $conn->query($checkEmail);
    if ($result->num_rows > 0) {
        echo "<script>alert('Email Address Already Exists!');</script>";
    } else {
        $insertQuery = "INSERT INTO blood_requests (name, phone, email, blood_required, blood_type, message)
                        VALUES ('$name', '$phone', '$email', '$blood_required', '$blood_type', '$message')";
        if ($conn->query($insertQuery) === TRUE) {
            echo "<script>
                    alert('Request Sent Successfully');
                    window.location.href = 'RequestBlood.html';
                  </script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

?>
