<?php
session_start();
require_once __DIR__ . '/schema.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'ticket_system');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
voyage_migrate_legacy_tables($conn);

$userId = (int)$_SESSION['user_id'];
$stmt = $conn->prepare('SELECT username, email, phone, password, created_at, COALESCE(is_admin, 0) AS is_admin FROM accounts WHERE id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$user) {
    session_unset();
    session_destroy();
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem("voyage-theme")||"dark";document.documentElement.classList.add(t+"-mode");if(document.body)document.body.classList.add(t+"-mode");else document.addEventListener("DOMContentLoaded",function(){document.body.classList.add(t+"-mode")});}catch(e){}})();</script>
    <script src="theme.js" defer></script>
    <title>My Profile</title>
    <link rel="icon" type="image/x-icon" href="./pics/Icon/voyage1.ico">
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-page {
            min-height: calc(100vh - 84px);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 70px 20px 32px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="auth-page">
            <div class="auth-card profile-card">
        <h1 class="auth-title">My Profile</h1>
        <div class="profile-info">
                    <div class="profile-row"><span class="profile-row-label">Name:</span><span class="profile-row-value"><?php echo htmlspecialchars($user['username']); ?></span></div>
                    <div class="profile-row"><span class="profile-row-label">Email:</span><span class="profile-row-value"><?php echo htmlspecialchars($user['email']); ?></span></div>
                    <div class="profile-row"><span class="profile-row-label">Phone:</span><span class="profile-row-value"><?php echo htmlspecialchars($user['phone']); ?></span></div>
                    <div class="profile-row"><span class="profile-row-label">Password:</span><span class="profile-row-value"><?php echo htmlspecialchars($user['password'], ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <div class="profile-row"><span class="profile-row-label">Created:</span><span class="profile-row-value"><?php echo htmlspecialchars($user['created_at']); ?></span></div>
                    <div class="profile-row"><span class="profile-row-label">Role:</span><span class="profile-row-value"><?php echo $user['is_admin'] ? 'Administrator' : 'User'; ?></span></div>
        </div>
                <div class="auth-actions">
                    <a class="auth-link" href="logout.php">Log Out</a>
                </div>
      </div>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
