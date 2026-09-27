<?php
session_start();
require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/schema.php';
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
voyage_migrate_legacy_tables($conn);

$conn->query("CREATE TABLE IF NOT EXISTS available_trips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    ticket_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
$conn->query("CREATE TABLE IF NOT EXISTS ticket_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL DEFAULT 0,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(30) NOT NULL,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    card_number VARCHAR(50) NOT NULL,
    qr_token CHAR(64) NULL,
    ticket_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
$conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS trip_id INT NOT NULL DEFAULT 0");
$conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS user_id INT NOT NULL DEFAULT 0");
$conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS name VARCHAR(100) NOT NULL DEFAULT ''");
$conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS phone_number VARCHAR(30) NOT NULL DEFAULT ''");
$conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS card_number VARCHAR(50) NOT NULL DEFAULT ''");
$conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS origin VARCHAR(100) NOT NULL DEFAULT ''");
$conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS destination VARCHAR(100) NOT NULL DEFAULT ''");
$conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS ticket_date DATE NOT NULL DEFAULT '1970-01-01'");

$name = isset($_POST['name']) && is_string($_POST['name']) ? trim($_POST['name']) : '';
$phone_number = isset($_POST['IC_number']) && is_string($_POST['IC_number']) ? trim($_POST['IC_number']) : '';
$trip_id = isset($_POST['trip_id']) ? (int)$_POST['trip_id'] : 0;
$origin = isset($_POST['origin']) && is_string($_POST['origin']) ? trim($_POST['origin']) : '';
$destination = isset($_POST['destination']) && is_string($_POST['destination']) ? trim($_POST['destination']) : '';
$card_number = isset($_POST['card_number']) && is_string($_POST['card_number']) ? trim($_POST['card_number']) : '';

$errors = [];
if (!voyage_is_valid_name($name)) {
    $errors[] = 'Enter a valid name using letters, spaces, apostrophes, periods, or hyphens.';
}
if (!voyage_is_valid_phone($phone_number)) {
    $errors[] = 'Phone number must start with + followed by 7 to 15 digits, with no spaces or punctuation.';
}
if (preg_match('/\A[0-9]{1,50}\z/', $card_number) !== 1) {
    $errors[] = 'Credit card number must contain 1 to 50 digits with no spaces or punctuation.';
}
if ($trip_id <= 0) {
    if ($origin === '' || $destination === '') {
        $errors[] = 'Please select a valid origin and destination.';
    }
    if ($origin === $destination) {
        $errors[] = 'Origin and destination must be different.';
    }
}

if (!empty($errors)) {
    $errorMessage = urlencode(implode(' ', $errors));
    header("Location: maps.php?error=$errorMessage");
    exit;
}

$expiryDate = date('Y-m-d', strtotime('+1 day'));
if ($trip_id > 0) {
    $tripStmt = $conn->prepare('SELECT origin, destination FROM available_trips WHERE id = ?');
    $tripStmt->bind_param('i', $trip_id);
    $tripStmt->execute();
    $tripResult = $tripStmt->get_result();
    $trip = $tripResult ? $tripResult->fetch_assoc() : null;
    if (!$trip) {
        $errorMessage = urlencode('Selected trip is no longer available.');
        header("Location: booking.php?error=$errorMessage");
        exit;
    }
} else {
    $trip = [
        'origin' => $origin,
        'destination' => $destination
    ];
}

$userId = (int)$_SESSION['user_id'];
$qrToken = bin2hex(random_bytes(32));
$stmt = $conn->prepare('INSERT INTO ticket_records (trip_id, user_id, name, phone_number, origin, destination, card_number, qr_token, ticket_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->bind_param('iisssssss', $trip_id, $userId, $name, $phone_number, $trip['origin'], $trip['destination'], $card_number, $qrToken, $expiryDate);

if ($stmt->execute()) {
    header('Location: booking.php?success=1');
    exit;
}

$errorMessage = urlencode('Error saving booking: ' . $stmt->error);
header("Location: maps.php?error=$errorMessage");
exit;
?>