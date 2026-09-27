<?php
session_start();
require_once __DIR__ . '/schema.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: login.html');
    exit;
}

$redirectWithNotice = static function (string $type, string $message): void {
    $_SESSION['account_action_notice'] = [
        'type' => $type,
        'message' => $message
    ];
    header('Location: admin_accounts.php');
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_accounts.php');
    exit;
}

$submittedToken = isset($_POST['csrf_token']) && is_string($_POST['csrf_token'])
    ? $_POST['csrf_token']
    : '';
if (empty($_SESSION['account_delete_token']) || !hash_equals($_SESSION['account_delete_token'], $submittedToken)) {
    $redirectWithNotice('error', 'Your request could not be verified. Please try again.');
}

$accountId = filter_input(INPUT_POST, 'account_id', FILTER_VALIDATE_INT);
if (!$accountId || $accountId < 1) {
    $redirectWithNotice('error', 'Select a valid customer to delete.');
}
if ($accountId === (int)$_SESSION['user_id']) {
    $redirectWithNotice('error', 'You cannot delete the administrator account you are currently using.');
}

$conn = new mysqli('localhost', 'root', '', 'ticket_system');
if ($conn->connect_error) {
    $redirectWithNotice('error', 'The customer could not be deleted because the database is unavailable.');
}

voyage_migrate_legacy_tables($conn);
$conn->begin_transaction();

$actorStatement = $conn->prepare('SELECT is_admin FROM accounts WHERE id = ? FOR UPDATE');
$actorId = (int)$_SESSION['user_id'];
$actorStatement->bind_param('i', $actorId);
$actorStatement->execute();
$actorResult = $actorStatement->get_result();
$actor = $actorResult ? $actorResult->fetch_assoc() : null;
$actorStatement->close();
if (!$actor || empty($actor['is_admin'])) {
    $conn->rollback();
    $conn->close();
    unset($_SESSION['is_admin']);
    header('Location: login.html');
    exit;
}

$accountStatement = $conn->prepare('SELECT id, is_admin FROM accounts WHERE id = ? FOR UPDATE');
$accountStatement->bind_param('i', $accountId);
$accountStatement->execute();
$accountResult = $accountStatement->get_result();
$account = $accountResult ? $accountResult->fetch_assoc() : null;
$accountStatement->close();
if (!$account) {
    $conn->rollback();
    $conn->close();
    $redirectWithNotice('error', 'That customer no longer exists.');
}

if (!empty($account['is_admin'])) {
    $adminResult = $conn->query('SELECT id FROM accounts WHERE is_admin = 1 FOR UPDATE');
    if (!$adminResult || $adminResult->num_rows <= 1) {
        $conn->rollback();
        $conn->close();
        $redirectWithNotice('error', 'The last administrator cannot be deleted.');
    }
}

$ticketStatement = $conn->prepare('DELETE FROM ticket_records WHERE user_id = ?');
$ticketStatement->bind_param('i', $accountId);
$ticketsDeleted = $ticketStatement->execute();
$ticketStatement->close();

$deleteStatement = $conn->prepare('DELETE FROM accounts WHERE id = ?');
$deleteStatement->bind_param('i', $accountId);
$accountDeleted = $deleteStatement->execute() && $deleteStatement->affected_rows === 1;
$deleteStatement->close();

if (!$ticketsDeleted || !$accountDeleted) {
    $conn->rollback();
    $conn->close();
    $redirectWithNotice('error', 'The customer could not be deleted. No records were removed.');
}

$conn->commit();
$conn->close();
$redirectWithNotice('success', 'Customer and associated ticket records deleted.');