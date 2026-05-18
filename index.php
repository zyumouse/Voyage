<?php
session_start();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : null;
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = !empty($_SESSION['is_admin']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem("voyage-theme")||"dark";document.documentElement.classList.add(t+"-mode");if(document.body)document.body.classList.add(t+"-mode");else document.addEventListener("DOMContentLoaded",function(){document.body.classList.add(t+"-mode")});}catch(e){}})();</script>
    <script src="theme.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Voyage - Home</title>
    <link rel="icon" type="image/x-icon" href="./pics/Icon/voyage1.ico">
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
        <div class="siteHeader-actions">
            <div class="search" role="search">
                <input type="text" placeholder="Search..." class="searchInput" aria-label="Search">
                <button class="searchButton" aria-label="Search Button"><img src="./pics/search.png" width="20" height="20" alt="Search" class="searchIcon"></button>
            </div>
            <?php if ($isLoggedIn): ?>
                <div class="profile-menu">
                    <button class="headerProfileButton" aria-expanded="false" tabindex="-1"><?php echo $username; ?></button>
                    <div class="profile-dropdown">
                        <a href="profile.php">Account</a>
                        <a href="settings.php">Settings</a>
                        <?php if ($isAdmin): ?>
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
    
    <div class = "introduction">
        <div class = "contents">
            <h1 class = "introTitle">Welcome to Voyage.</h1>
            <p>The future of LRT travel at your fingertips.</p>
            <div class = "introActions">
                <div class = "introActionscontainer">
                    <div class="introActionsitem">
                        <h3>Check our coverage!</h3>
                        <button class = "aboutv2" onclick="location.href='maps.php';" style="cursor:pointer;">Maps</button>
                    </div>
                    <div class="introActionsitem">
                        <h3>Haven't made an account?</h3>
                        <button class="aboutv2" onclick="location.href='signupredir.html';" style="cursor:pointer;">Sign Up!</button>
                    </div>
                    <div class="introActionsitem">
                        <h3>Need a ride?</h3>
                        <button class="aboutv2" onclick="location.href='booking.php';" style="cursor:pointer;">Book Here!</button>
                    </div>
                </div>
            </div>
            <div class = "introImage">
                <img src = "./pics/placeholder1.png" alt = "Image of the website booking page">
            </div>
        </div>      
    </div>
    <div class = "missionandvision">
        <div class = "thing1">
            <div class = "thing1Image">
                <img src = "./pics/placeholder2.png" alt = "gif navigating the map">
            </div>
            <div class = "thing1Text">
                <h2>Ordering rail tickets has never been more convenient.</h2>
                <p>Voyage provides the user with a seamless experience for booking and managing their trips. All it takes is a few buttons, and you're set for your destination.</p>
            </div>
        </div>
        <div class = "divide"></div>
        <div class = "thing2">
            <div class = "thing2Image">
                <img src = "./pics/placeholder3.png" alt = "gif of the price of the tickets">
            </div>
            <div class = "thing2Text">
                <h2>Let us handle the calculations.</h2>
                <p>Voyage takes the complexity out of pricing, ensuring you always know the price of your trip.</p>
            </div>
        </div>
        <div class = "divide"></div>
        <div class = "thing3">
            <div class = "thing3Image">
                <img src = "./pics/placeholder4.png" alt = "gif of removing the tickets">
            </div>
            <div class = "thing3Text">
                <h2>Change of plans? No problem.</h2>
                <p>With Voyage, you can easily modify or cancel your bookings in just a few clicks, allowing for flexibility to adapt to your changing schedule.</p> 
            </div>
        </div>
    </div>
    <div class = "mainbottomtext">
        <h1 class = "gradientbottomtext">Can't wait to get started?</h1>
        <button class="aboutv3" onclick="location.href='signupredir.html';" style="cursor:pointer;">Begin by signing up here.</button>
    </div>
    <div class = "mainbottomimage">
        <img src = "./pics/placeholder4.png" alt = "bottom image of the website, mascot of the mouse with a train hat or whatever">
    </div>
    <div class = "footer">
        <div class = "footerLeft">
            <h5>Useful Links</h5>
            <ul>
                <li><a href="notbuilt.html">FAQ</a></li>
            </ul>
        </div>
        <div class = "footerRight">
            <h4 class = "footerRighttext">Follow Us</h4>
            <div class ="footerIcons">  
                <img src = "./pics/Icon/discord.png" alt = "discord" onclick="location.href='https://discord.gg/BSxnSfRn';" style="cursor:pointer;">
                <img src = "./pics/Icon/instagram.webp" alt = "instagram" onclick="location.href='https://www.instagram.com/voyagingmouse?igsh==MXAxazQxb3Zxdnk3NQ==';" style="cursor:pointer;">
            </div>
            <div class = "footerCopyright">
                <p>&copy; 2026 MOUSE. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>