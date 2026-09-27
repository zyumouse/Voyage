<?php
session_start();
require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/schema.php';
$conn = new mysqli("localhost", "root", "", "ticket_system");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
voyage_migrate_legacy_tables($conn);

// Ensure admin flag exists in accounts table
$conn->query("ALTER TABLE accounts ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) NOT NULL DEFAULT 0");

$email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if (!voyage_is_valid_email($email) || !voyage_is_valid_password($password)) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Invalid email or password."]);
    } else {
        echo "Invalid email or password.";
    }
    exit;
}

$stmt = $conn->prepare("SELECT id, password, username, COALESCE(is_admin, 0) AS is_admin FROM accounts WHERE email = ?");
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
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(["success" => true, "redirect" => "index.php"]);
            exit;
        }
        header("Location: index.php");
        exit;
    }
}
if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Invalid email or password."]);
} else {
    echo "Invalid email or password.";
}
?>