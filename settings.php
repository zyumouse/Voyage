<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <title>Settings</title>
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
    <script>(function(){try{var t=localStorage.getItem("voyage-theme")||"dark";document.documentElement.classList.add(t+"-mode");if(document.body)document.body.classList.add(t+"-mode");else document.addEventListener("DOMContentLoaded",function(){document.body.classList.add(t+"-mode")});}catch(e){}})();</script>
    <script src="theme.js" defer></script>
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="auth-page">
        <div class="auth-card">
            <h1 class="auth-title">Settings</h1>
            <p class="auth-subtitle">Choose your preferred display mode for Voyage.</p>
            <div class="theme-card">
                <p>Current theme: <strong id="themeStatus">Loading...</strong></p>
                <button id="themeToggle" type="button" class="theme-button">Switch theme</button>
            </div>
        </div>
    </div>
    <script src="theme.js"></script>
    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
