<?php

include 'db_connect.php';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $blood_type = $_POST['blood-type'];
    $blood_required = $_POST['blood-require'];
    $message = $_POST["message"];

    // Check if email already exists
    $checkEmail = "SELECT * FROM emergency WHERE email='$email'";
    $result = $conn->query($checkEmail);
    
    if ($result->num_rows > 0) {
        echo "<script>
                alert('Email Address Already Exists!');
                window.location.href = 'emergency_request_form.html'; // Replace with your form page
              </script>";
    } else {
        // Insert data into the database
        $insertQuery = "INSERT INTO emergency (name, phone, email, blood_type, blood_required, message)
                        VALUES ('$name', '$phone', '$email', '$blood_type', '$blood_required', '$message')";
        if ($conn->query($insertQuery) === TRUE) {
            echo "<script>
                    alert('Request Sent Successfully');
                    window.location.href = 'Homepage.html';
                  </script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

?>
