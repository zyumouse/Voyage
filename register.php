<?php
session_start();
require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/schema.php';
$conn = new mysqli("localhost", "root", "", "ticket_system");
if ($conn->connect_error) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}
voyage_migrate_legacy_tables($conn);

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$name = isset($_POST['name']) && is_string($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) && is_string($_POST['phone']) ? trim($_POST['phone']) : '';
$password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

$validationErrors = [];
if (!preg_match('/\A[\p{L}\p{M}][\p{L}\p{M} .\'-]{0,99}\z/u', $name)) {
    $validationErrors[] = "Name must be 1 to 100 characters and use letters, spaces, apostrophes, periods, or hyphens.";
}
if (!voyage_is_valid_email($email)) {
    $validationErrors[] = "Enter a valid email address no longer than 255 characters.";
}
if (!voyage_is_valid_phone($phone)) {
    $validationErrors[] = "Phone number must start with + followed by 7 to 15 digits, with no spaces or punctuation.";
}
if (!voyage_is_valid_password($password)) {
    $validationErrors[] = "Enter a password that is no longer than 255 characters.";
}

if ($validationErrors) {
    $message = implode(' ', $validationErrors);
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => $message]);
        exit;
    }
    http_response_code(422);
    echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    exit;
}

$usernameIndex = $conn->query("SHOW INDEX FROM accounts WHERE Key_name = 'username'");
if (!$usernameIndex) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Unable to check existing account constraints."]);
    exit;
}
if ($usernameIndex->num_rows > 0 && !$conn->query("ALTER TABLE accounts DROP INDEX username")) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Unable to update the account name constraint."]);
    exit;
}

$checkStmt = $conn->prepare("SELECT email, phone FROM accounts WHERE email = ? OR phone = ?");
$checkStmt->bind_param("ss", $email, $phone);
$checkStmt->execute();
$result = $checkStmt->get_result();
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $message = "Email or phone number already in use.";
    $taken = [];
    if ($row['email'] === $email) {
        $taken[] = "email";
    }
    if ($row['phone'] === $phone) {
        $taken[] = "phone number";
    }
    if (!empty($taken)) {
        if (count($taken) === 1) {
            $message = "That " . $taken[0] . " is already taken.";
        } else {
            $message = "That email and phone number are already taken.";
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

$stmt = $conn->prepare("INSERT INTO accounts (username, email, phone, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $password);

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