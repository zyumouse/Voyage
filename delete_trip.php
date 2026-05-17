<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: login.html');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$ticketId = (int)$_GET['id'];
$servername = 'localhost';
$username = 'root';
$password = '';
$database = 'ticket_system';

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$stmt = $conn->prepare('DELETE FROM available_tickets WHERE id = ?');
$stmt->bind_param('i', $ticketId);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: admin.php');
exit;
?>