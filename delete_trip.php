<?php
session_start();
require_once __DIR__ . '/schema.php';
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
voyage_migrate_legacy_tables($conn);

$stmt = $conn->prepare('DELETE FROM available_trips WHERE id = ?');
$stmt->bind_param('i', $ticketId);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: admin.php');
exit;
?>