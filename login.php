<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ticket_system");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Ensure admin flag exists in users table
$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) NOT NULL DEFAULT 0");

$email = $conn->real_escape_string($_POST['email']);
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password, username, COALESCE(is_admin, 0) AS is_admin FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if ($password === $row['password']) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['is_admin'] = (int)$row['is_admin'];
        header("Location: profile.php");
        exit;
    }
}
echo "Invalid email or password.";
?>