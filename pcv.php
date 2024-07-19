<?php

// Function to establish database connection
function connectToDatabase() {
    // Modify the connection parameters as per your database configuration
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "db_bms";
}
    $conn = new mysqli_connect($host, $username, $password, $database)or die("Connection failed");
// Function to check if email exists in the database
function emailExists($email, $conn) {
    // Prepare and execute a SELECT query to check if the email exists in your database
    $selectemail = "SELECT * FROM users WHERE email = '$email'";
        $check_email = mysqli_query($conn, $selectemail);
    if(mysqli_num_rows($check_email) > 0) {
        return true;
    } else {
        return false;
    }
}

// Function to check if username exists in the database
function userExists($user, $conn) {
    // Prepare and execute a SELECT query to check if the username exists in your database
    $selectuser = "SELECT * FROM users WHERE username = '$user'";
    $check_user = mysqli_query($conn, $selectuser);
    if(mysqli_num_rows($check_user) > 0) {
        return true;
    } else {
        return false;
    }
}

// Function to generate a random authorization code
function generateAuthorizationCode() {
    return mt_rand(100000, 999999);
}

// Function to save the authorization code in the database
function saveAuthorizationCode($email, $code, $expirationTime, $conn) {
    // Prepare and execute an INSERT query to save the authorization code in your database
    $insertcode = "INSERT INTO authorization_codes (email, code, expiration_time) VALUES ('$email', '$code', '$expirationTime')";
    $result = mysqli_query($conn, $insertcode) or die("Insertion Error");
}

// Function to send the authorization code to the user's email
function sendAuthorizationCodeEmail($email, $code) {
    // Use PHP's mail function or a third-party library to send the email
    // Example using PHP's mail function (configure your SMTP settings accordingly)
    $subject = "Your Authorization Code";
    $message = "Your authorization code is: $code";
    $headers = "From: happinessangelic@gmail.com";

    // Send email
    mail($email, $subject, $message, $headers);
}

// Main code execution starts here
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish database connection
    $conn = connectToDatabase();

    // Validate and sanitize user inputs
    $username = $_POST["username"];
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

    // Check if email and username exist in the database
    if (emailExists($email, $conn) && userExists($username, $conn)) {
        // Generate a random authorization code
        $authorizationCode = generateAuthorizationCode();

        // Save the authorization code along with its expiration time in the database
        $expirationTime = time() + 60 * 15; // 15 minutes expiration time
        saveAuthorizationCode($email, $authorizationCode, $expirationTime, $conn);

        // Send the authorization code to the user's email
        sendAuthorizationCodeEmail($email, $authorizationCode);

        // Redirect to a page where the user can enter the code for further verification
        header("Location: ecv.html");
        exit();
    } else {
        // Email or username not found, handle accordingly (e.g., show an error message)
        echo "<script>alert('Email or username not found!');</script>";
		echo "<script>window.location.href = 'pvc.html';</script>";
    }

    // Close the database connection
    $conn->close();
}

?>
