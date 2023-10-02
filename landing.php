<?php
session_start();

// Check if the user is logged in and has a valid session
if (isset($_SESSION['uid'])) {
    $userId = $_SESSION['uid'];

    // Query the database to get the user's name
    require_once("connect.php"); // Include your database connection file
    $sql = "SELECT `Full name` FROM registration WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userFullName = $row['Full name'];
    } else {
        // Handle the case where the user is not found in the database
        $userFullName = "User";
    }

    $stmt->close();
    mysqli_close($conn);
} else {
    // Redirect to the login page if the user is not logged in
    header("Location: login");
    exit;
}

// Check if a greeting message is set in the session
$greetingMessage = isset($_SESSION['greeting']) ? $_SESSION['greeting'] : "You are logged out.";

// Unset the greeting session variable to avoid displaying it multiple times
unset($_SESSION['greeting']);

// Handle the logout request
if (isset($_GET['logout'])) {
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to the login page after logout
    header("Location: login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">Your Logo</div>
            <ul class="nav-links">
                <li><a href="#">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <aside class="sidebar">
            <ul class="sidebar-links">
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Profile</a></li>
                <li><a href="#">Settings</a></li>
                <li><a href="?logout">Logout</a></li>
            </ul>
        </aside>

        <!-- Display the greeting or "You are logged out" message -->
        <main class="content">
            <h1>Welcome to <?php echo $userFullName; ?> to our Website</h1>
            <p>This is the main content of your homepage.</p>
            <?php if (empty($_SESSION['uid'])) : ?>
                <p>You can <a href="login">sign in again</a>.</p>
            <?php endif; ?>
        </main>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Your Website</p>
    </footer>
</body>
</html>
