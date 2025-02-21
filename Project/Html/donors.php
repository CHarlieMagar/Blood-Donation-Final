<?php

include 'db_connection.php';

if (isset($_POST['register'])) {
    // Retrieve the form data
    $name = $_POST['Name'];
    $phone = $_POST['number'];
    $email = $_POST['email'];
    $blood_required = $_POST['Blood_type'];
    $gender = $_POST['Gender'];
    $message = $_POST["location"];
    $age = $_POST['Age'];

    // Corrected SQL query with variable names
    $insertQuery = "INSERT INTO donors (name, gender, blood_group, mobile_no, age, email, address)
                    VALUES ('$name', '$gender', '$blood_required', '$phone', '$age', '$email', '$message')";

    // Execute the query
    if ($conn->query($insertQuery) === TRUE) {
        // Display JavaScript alert and redirect to Donorform.html
        echo "<script>alert('Request Successful!'); window.location.href = 'Donorform.html';</script>";
        exit();
    } else {
        // Display error message if the query fails
        echo "Error: " . $conn->error;
    }
}

?>