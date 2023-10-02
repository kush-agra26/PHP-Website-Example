<?php
$conn = new mysqli('localhost', 'root', '', 'reg');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}



?>