<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'ticket_system');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$userId = (int)$_SESSION['user_id'];
$stmt = $conn->prepare('SELECT username, email, phone, created_at, COALESCE(is_admin, 0) AS is_admin FROM users WHERE id = ?');
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
      <div class="auth-card">
        <h1 class="auth-title">My Profile</h1>
        <div class="profile-info">
          <div class="profile-row"><span>Username:</span> <?php echo htmlspecialchars($user['username']); ?></div>
          <div class="profile-row"><span>Email:</span> <?php echo htmlspecialchars($user['email']); ?></div>
          <div class="profile-row"><span>Phone:</span> <?php echo htmlspecialchars($user['phone']); ?></div>
          <div class="profile-row"><span>Created:</span> <?php echo htmlspecialchars($user['created_at']); ?></div>
          <div class="profile-row"><span>Role:</span> <?php echo $user['is_admin'] ? 'Administrator' : 'Regular user'; ?></div>
        </div>
                <div class="auth-actions">
                    <a class="auth-link" href="logout.php">Logout</a>
                </div>
      </div>
    </div>
</body>
</html>
