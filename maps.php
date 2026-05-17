<?php
session_start();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : null;
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Voyage - Maps</title>
    <link rel="icon" type="image/x-icon" href="./pics/Icon/voyage1.ico">
</head>
<body>
    <nav id = "nav">
        <div class = "navTop">
            <div class = "navItem">
                <a href="index.php">
                    <img class = "navItemimg" src = "./pics/Icon/voyage1.png" alt = ":3">
                </a>
            </div>
            <div class = "navItem">
                <div class = "search">
                    <input type = "text" placeholder = "Hi I'm useless :3" class="searchInput">
                    <img src = "./pics/search.png" width = "20" height = "20" alt = "N-nyaaaa~" class="searchIcon">
                </div>
            </div>
            <div class = "navItem">
                 <span class="profilebutton" onclick="location.href='<?php echo $isLoggedIn ? 'profile.php' : 'login.html'; ?>';" style="cursor:pointer;"><?php echo $isLoggedIn ? $username : 'Login'; ?></span>
            </div>
        </div>
        <div class = "navBottom">
            <div class = "navItem">
                <button class="about" onclick="location.href='index.php';" style="cursor:pointer;">Homepage</button>
                <button class="about" onclick="location.href='booking.php';" style="cursor:pointer;">Booking</button>
            </div>
            <div class = "navItem">
                <button class="aboutSpecialCase" onclick="location.href='signupredir.html';" style="cursor:pointer;">Sign Up!</button>
            </div>
        </div>
    </nav>