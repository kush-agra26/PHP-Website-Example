<?php
session_start();
require_once("connect.php"); // Include your database connection file

$message = ''; // Initialize a variable to store messages

if (isset($_POST['user']) && isset($_POST['pass'])) {
    // Sanitize the user input
    $user = mysqli_real_escape_string($conn, $_POST['user']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']);

    if (!empty($user) && !empty($pass)) {
        $sql = "SELECT * FROM registration WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($pass, $row['password'])) {
                // Regenerate the session id
                session_regenerate_id();
                $_SESSION['uid'] = $row['id'];

                // Set the greeting message in the session
                $_SESSION['greeting'] = "Hello ".htmlspecialchars($user).", You May Have a Good Day!";
                
                // Redirect to the landing page
                header("Location: landing");
                exit(); // Exit after redirecting
            } else {
                $message = 'Invalid Username or Password.';
            }
        } else {
            $message = 'Invalid Username or Password.';
        }

        $stmt->close();
    } else {
        $message = 'Required fields missing.';
    }
}

mysqli_close($conn); // Close the database connection when done
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="wrapper">
        <div class="heading">
            <h1>Login Form</h1>
        </div>
        <div class="form">
            <form method="POST">
                <span>
                    <i class="fa fa-user"></i>
                    <input type="text" placeholder="Username" name="user">
                </span><br>
                <span>
                    <i class="fa-solid fa-lock"></i>
             "
