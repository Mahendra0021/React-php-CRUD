<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

// Database connection
$conn = new mysqli("localhost", "root", "M@hendr@0021", "reactphp");

if (mysqli_connect_error()) {
    echo json_encode(array("result" => "Database connection failed"));
    exit();
} else {
    // Get the input data
    $eData = file_get_contents("php://input");

    // Decode the JSON data
    $dData = json_decode($eData, true);

    // Check if the input contains email and password
    if (isset($dData['email']) && isset($dData['password'])) {
        $email = $dData['email'];
        $password = $dData['password'];
        $result = "";

        // Check if email and password are not empty
        if (!empty($email) && !empty($password)) {
            // First, check if the user already exists in the database
            $checkSql = "SELECT * FROM login WHERE email='$email'";
            $checkRes = mysqli_query($conn, $checkSql);

            if (mysqli_num_rows($checkRes) > 0) {
                // Email already exists
                $result = "Email already exists, please use a different one.";
            } else {
                // Insert new user into the database
                $insertSql = "INSERT INTO login (email, password) VALUES ('$email', '$password')";
                if (mysqli_query($conn, $insertSql)) {
                    $result = "Registration successful!";
                } else {
                    $result = "Error saving credentials, please try again.";
                }
            }
        } else {
            $result = "Email and password cannot be empty";
        }
    } else {
        $result = "Invalid input data. Email and password are required";
    }

    // Close the connection
    $conn->close();

    // Send the response as JSON
    $response = array("result" => $result);
    echo json_encode($response);
}

?>
