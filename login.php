<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ticket_system");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$email = $conn->real_escape_string($_POST['email']);
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password, username FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if ($password === $row['password']) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        header("Location: index.html");
        exit;
    }
}
echo "Invalid email or password.";
?>