<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ticket_system");
if ($conn->connect_error) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$username = isset($_POST['username']) ? $conn->real_escape_string($_POST['username']) : '';
$email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
$phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';
$password = isset($_POST['password']) ? $conn->real_escape_string($_POST['password']) : '';

$checkStmt = $conn->prepare("SELECT username, email, phone FROM users WHERE username = ? OR email = ? OR phone = ?");
$checkStmt->bind_param("sss", $username, $email, $phone);
$checkStmt->execute();
$result = $checkStmt->get_result();
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $message = "Username, email, or phone number already in use.";
    $taken = [];
    if ($row['username'] === $username) {
        $taken[] = "username";
    }
    if ($row['email'] === $email) {
        $taken[] = "email";
    }
    if ($row['phone'] === $phone) {
        $taken[] = "phone number";
    }
    if (!empty($taken)) {
        if (count($taken) === 1) {
            $message = "That " . $taken[0] . " is already taken.";
        } elseif (count($taken) === 2) {
            $message = "That " . $taken[0] . " and " . $taken[1] . " are already taken.";
        } else {
            $message = "That username, email, and phone number are already taken.";
        }
    }
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => $message]);
        exit;
    }
    echo $message;
    exit;
}

$stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $phone, $password);

if ($stmt->execute()) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(["success" => true, "message" => "Your account was created successfully."]);
        exit;
    }
    header("Location: login.html");
    exit;
} else {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Registration failed: " . $stmt->error]);
        exit;
    }
    echo "Error: " . $stmt->error;
}
?>