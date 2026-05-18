<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "ticket_system";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("CREATE TABLE IF NOT EXISTS available_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    ticket_date DATE NOT NULL,
    ticket_time TIME NOT NULL,
    estimated_arrival_time TIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
$conn->query("CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL DEFAULT 0,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(30) NOT NULL,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    card_number VARCHAR(32) NOT NULL,
    ticket_date DATE NOT NULL,
    ticket_time TIME NOT NULL,
    estimated_arrival_time TIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
$conn->query("ALTER TABLE available_tickets ADD COLUMN IF NOT EXISTS estimated_arrival_time TIME NOT NULL DEFAULT '00:00:00'");
$conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS trip_id INT NOT NULL DEFAULT 0");
$conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS user_id INT NOT NULL DEFAULT 0");
$conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS name VARCHAR(100) NOT NULL DEFAULT ''");
$conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS phone_number VARCHAR(30) NOT NULL DEFAULT ''");
$conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS card_number VARCHAR(32) NOT NULL DEFAULT ''");
$conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS origin VARCHAR(100) NOT NULL DEFAULT ''");
$conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS destination VARCHAR(100) NOT NULL DEFAULT ''");
$conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS ticket_date DATE NOT NULL DEFAULT '1970-01-01'");
$conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS ticket_time TIME NOT NULL DEFAULT '00:00:00'");
$conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS estimated_arrival_time TIME NOT NULL DEFAULT '00:00:00'");

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$phone_number = isset($_POST['IC_number']) ? trim($_POST['IC_number']) : '';
$trip_id = isset($_POST['trip_id']) ? (int)$_POST['trip_id'] : 0;
$card_number = isset($_POST['card_number']) ? trim($_POST['card_number']) : '';

$errors = [];
if ($name === '') {
    $errors[] = 'Name is required.';
}
if ($phone_number === '') {
    $errors[] = 'IC number is required.';
}
if ($trip_id <= 0) {
    $errors[] = 'Please select a valid available trip.';
}

if (!empty($errors)) {
    $errorMessage = urlencode(implode(' ', $errors));
    header("Location: booking.php?error=$errorMessage");
    exit;
}

$tripStmt = $conn->prepare('SELECT origin, destination, ticket_date, ticket_time, estimated_arrival_time FROM available_tickets WHERE id = ?');
$tripStmt->bind_param('i', $trip_id);
$tripStmt->execute();
$tripResult = $tripStmt->get_result();
$trip = $tripResult ? $tripResult->fetch_assoc() : null;
if (!$trip) {
    $errorMessage = urlencode('Selected trip is no longer available.');
    header("Location: booking.php?error=$errorMessage");
    exit;
}

$userId = (int)$_SESSION['user_id'];
$stmt = $conn->prepare('INSERT INTO tickets (trip_id, user_id, name, phone_number, origin, destination, card_number, ticket_date, ticket_time, estimated_arrival_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->bind_param('iissssssss', $trip_id, $userId, $name, $phone_number, $trip['origin'], $trip['destination'], $card_number, $trip['ticket_date'], $trip['ticket_time'], $trip['estimated_arrival_time']);

if ($stmt->execute()) {
    header('Location: booking.php?success=1');
    exit;
}

$errorMessage = urlencode('Error saving booking: ' . $stmt->error);
header("Location: booking.php?error=$errorMessage");
exit;
?>