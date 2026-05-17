<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ticket_system");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$username = $conn->real_escape_string($_POST['username']);
$email = $conn->real_escape_string($_POST['email']);
$password = $_POST['password'];

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hashed);

if ($stmt->execute()) {
    header("Location: login.html");
    exit;
} else {
    echo "Error: " . $stmt->error;
}
?>