<?php
session_start();
require_once("connect.php");

$message = '';

if (isset($_POST['full-name']) && isset($_POST['new-username']) && isset($_POST['new-password'])) {
    $fullName = $_POST['full-name'];
    $newUsername = $_POST['new-username'];
    $newPassword = $_POST['new-password'];

    if (!empty($fullName) && !empty($newUsername) && !empty($newPassword)) {
        $fullName = filter_var($fullName, FILTER_SANITIZE_STRING);
        $newUsername = filter_var($newUsername, FILTER_SANITIZE_EMAIL);
        $newPassword = filter_var($newPassword, FILTER_SANITIZE_STRING);

        // Check if the email is already in use
        $sql = "SELECT * FROM registration WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $newUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = 'Email is already in use. Please choose another email.';
        } else {
            // Generate a unique 6-digit verification code
            $verificationCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Send the verification email using PHP mail function
            $to = $newUsername;
            $subject = 'Email Verification';
            $messageBody = 'Your 6-digit verification code is: ' . $verificationCode;
            $headers = 'From: kushagra260205@gmail.com'; // Replace with your email address

            if (mail($to, $subject, $messageBody, $headers)) {
                // Hash the password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Insert the new user into the database
                $stmt = $conn->prepare("INSERT INTO registration (full_name, username, password, verification_code) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $fullName, $newUsername, $hashedPassword, $verificationCode);

                if ($stmt->execute()) {
                    $message = 'Registration Successful. Please check your email for a verification code.';
                } else {
                    $message = 'Registration Failed. Please try again.';
                }
            } else {
                $message = 'Email could not be sent. Please try again.';
            }
        }
    } else {
        $message = 'Required fields missing.';
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="wrapper">
        <div class="heading">
            <h1>Signup Form</h1>
        </div>
        <div class="form">
            <form method="POST">
                <span>
                    <i class="fa fa-user"></i>
                    <input type="text" placeholder="Full Name" name="full-name" required>
                </span><br>
                <span>
                    <i class="fa fa-envelope"></i>
                    <input type="email" placeholder="Email" name="new-username" required>
                </span><br>
                <span>
                    <i class="fa fa-lock"></i>
                    <input type="password" placeholder="Password" name="new-password" required>
                </span><br>
                <button type="submit">Sign Up</button>
            </form>
        </div>
        <div class="signin-button">
            <p><font color = white>Already a Member?</font> <button onclick="window.location.href='login'">Sign In</button></p>
        </div>
        <?php
        if (!empty($message)) {
            echo "<script>alert('$message');</script>";
        }
        ?>
    </div>
</body>
</html>
