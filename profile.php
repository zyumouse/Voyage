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
    <header class="siteHeader">
        <div class="siteHeader-brand">
            <a href="index.php"><img class="siteHeader-logo" src="./pics/Icon/voyage1.png" alt="Voyage logo"></a>
            <div class="siteHeader-title">
                <h1>Voyage</h1>
                <p>A better LRT booking experience</p>
            </div>
        </div>
        <nav class="siteHeader-nav">
            <a href="index.php">Home</a>
            <a href="maps.php">Maps</a>
            <a href="booking.php">Book</a>
        </nav>
        <div class="siteHeader-search">
            <div class="search" role="search">
                <input type="text" placeholder="Search..." class="searchInput" aria-label="Search">
                <button class="searchButton" aria-label="Search Button"><img src="./pics/search.png" width="20" height="20" alt="Search" class="searchIcon"></button>
            </div>
        </div>
        <div class="siteHeader-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="profile-menu">
                    <button class="headerProfileButton" aria-expanded="false" tabindex="-1"><?php echo htmlspecialchars($user['username']); ?></button>
                    <div class="profile-dropdown">
                        <a href="profile.php">Account</a>
                        <a href="settings.php">Settings</a>
                        <?php if (!empty($user['is_admin'])): ?>
                            <a href="admin_schedule.php">Schedule</a>
                            <a href="admin_customers.php">Customers</a>
                        <?php endif; ?>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a class="headerProfileButton" href="login.html">Login</a>
            <?php endif; ?>
        </div>
    </header>
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
