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
    <?php include __DIR__ . '/header.php'; ?>
    
    <main class="hero-section">
        <div class="hero-copy">
            <span class="hero-eyebrow">Light Rail Transit</span>
            <h1 class="hero-title">Voyage makes every LRT journey simple, fast, and dependable.</h1>
            <p>Find stations, book tickets, and plan your commute with a modern transit experience built for riders who want clarity and control.</p>
            <div class="hero-actions">
                <a class="primary-button" href="booking.php">Book a Ride</a>
                <a class="secondary-button" href="maps.php">View Route Map</a>
                <a class="secondary-button" href="signupredir.html">Create Account</a>
            </div>
            <div class="stats-grid">
                <div class="stat-card"><strong>21</strong><span>Stations</span></div>
                <div class="stat-card"><strong>7</strong><span>Lines</span></div>
                <div class="stat-card"><strong>99%</strong><span>On-time service</span></div>
                <div class="stat-card"><strong>Fast</strong><span>Mobile ticketing</span></div>
            </div>
        </div>
        <div class="hero-visual">
            <img src="./pics/bannerstuff2.png" alt="Banner for Voyage booking section">
        </div>
    </main>

    <section class="section-title">
        <h2>All the tools you need for a smart transit journey</h2>
        <p>Voyage blends station maps, ticket booking, and schedule tracking into a single experience designed for commuters, visitors, and travellers.</p>
    </section>

    <section class="feature-grid">
        <div class="feature-card">
            <h3>Route coverage</h3>
            <p>Explore the network map with connected stops, airport transfer points, and clear route paths for every journey.</p>
        </div>
        <div class="feature-card">
            <h3>Smart bookings</h3>
            <p>Book a ride in seconds, compare schedules, and confirm your trip with a few easy steps.</p>
        </div>
        <div class="feature-card">
            <h3>Reliable support</h3>
            <p>Stay informed with the latest service updates and trusted support for your daily commute.</p>
        </div>
    </section>

    <section class="work-grid">
        <div class="work-step">
            <h3>1. Pick your station</h3>
            <p>Check the route map and choose the closest stop with fast access to the city, airport, or transit hub.</p>
        </div>
        <div class="work-step">
            <h3>2. Book your ride</h3>
            <p>Select your departure and destination, choose the best fare, and confirm your trip in a few taps.</p>
        </div>
        <div class="work-step">
            <h3>3. Travel with confidence</h3>
            <p>Receive journey details, boarding notifications, and status updates so you always travel with clarity.</p>
        </div>
    </section>

    <section class="network-panel">
        <div class="network-copy">
            <h3>Connected service across the city</h3>
            <p>Voyage keeps the network transparent with route details, station amenities, and transfer connections for every leg of your trip.</p>
        </div>
        <div class="network-card">
            <h4>Airport access</h4>
            <p>Seamless transit to and from major terminals with dedicated airport transfer stops and easy planning.</p>
        </div>
        <div class="network-card">
            <h4>Peak-time schedules</h4>
            <p>Realistic service times and up-to-date arrival info help you travel when it matters most.</p>
        </div>
        <div class="network-card">
            <h4>Secure booking</h4>
            <p>Book with confidence using a clean interface and fast checkout for every commute.</p>
        </div>
    </section>

    <section class="section-title">
        <h2>Need help with booking?</h2>
        <p>Visit our dedicated <a href="faq.php">FAQ page</a> for answers about ticket expiry, checkout, and route planning.</p>
    </section>

    <section class="cta-panel">
        <div>
            <h2 class="gradientbottomtext">Start your first trip today</h2>
            <p>Enjoy the freedom of a transit plan built for modern passengers — all from the Voyage homepage.</p>
        </div>
        <a class="primary-button" href="booking.php">Book a Ride</a>
    </section>

    <section class="mainbottomimage">
        <img src="./pics/lrt.jpg" alt="Illustration of modern transit travel">
    </section>

    <footer class="footer">
        <div class="footerleft">
            <h5>Useful Links</h5>
            <ul>
                <li><a href="faq.php#faq-section">FAQ</a></li>
                <li><a href="maps.php">Route map</a></li>
            </ul>
        </div>
        <div class="footerright">
            <h4 class="footerRighttext">Follow Us</h4>
            <div class="footerIcons">
                <img src="./pics/Icon/discord.png" alt="discord" onclick="location.href='https://discord.gg/BSxnSfRn';" style="cursor:pointer;">
                <img src="./pics/Icon/instagram.webp" alt="instagram" onclick="location.href='https://www.instagram.com/voyagingmouse?igsh==MXAxazQxb3Zxdnk3NQ==';" style="cursor:pointer;">
            </div>
            <div class="footerCopyright">
                <p>&copy; 2026 MOUSE. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>