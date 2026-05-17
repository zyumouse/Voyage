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

$name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
$phone_number = isset($_POST['IC_number']) ? $conn->real_escape_string($_POST['IC_number']) : '';
$address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';
$card_number = isset($_POST['card_number']) ? $conn->real_escape_string($_POST['card_number']) : '';
$ticket_date = isset($_POST['ticket_date']) ? $conn->real_escape_string($_POST['ticket_date']) : '';
$ticket_time = isset($_POST['ticket_time']) ? $conn->real_escape_string($_POST['ticket_time']) : '';

$stmt = $conn->prepare('INSERT INTO tickets (name, phone_number, card_number, ticket_date, ticket_time) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('sssss', $name, $phone_number, $card_number, $ticket_date, $ticket_time);

if ($stmt->execute()) {
    echo "New record created successfully";
    header("Location: profile.php");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>