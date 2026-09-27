<?php
session_start();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : null;
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem("voyage-theme")||"dark";document.documentElement.classList.add(t+"-mode");if(document.body)document.body.classList.add(t+"-mode");else document.addEventListener("DOMContentLoaded",function(){document.body.classList.add(t+"-mode")});}catch(e){}})();</script>
    <script src="theme.js" defer></script>
    <link rel="stylesheet" href="style.css">
    <title>Voyage - Credits &amp; Misc.</title>
    <link rel="icon" type="image/x-icon" href="./pics/Icon/voyage1.ico">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>

    <main class="credits-page-content">
        <section class="credits-card" aria-labelledby="credits-title">
            <span class="hero-eyebrow">Credits &amp; Misc.</span>
            <h1 id="credits-title">Voyage</h1>
            <p class="credits-intro">References and acknowledgements for the Voyage transit booking system.</p>

            <div class="credits-section">
                <h2>Presentation</h2>
                <a href="https://www.canva.com/design/DAHKBImUd2k/Ey9e3TJin4vHQLLksM0kpA/edit" target="_blank" rel="noopener noreferrer">View</a>
            </div>

            <div class="credits-section">
                <h2>Credits</h2>
                <ul class="credits-list">
                    <li>QR Code Generator: <a href="https://github.com/davidshimjs/qrcodejs" target="_blank" rel="noopener noreferrer">qrcodejs by davidshimjs</a></li>
                    <li>LRT Stations Map: <a href="https://gamuda.com/our-expertise/engineering-construction/penang-mutiara-line-mtl/" target="_blank" rel="noopener noreferrer">Gamuda Berhad</a></li>
                    <li>Voyage Logo &amp; Icon: <a href="https://github.com/pleaseplayetoh" target="_blank" rel="noopener noreferrer">pleaseplayetoh</a></li>
                    <li>Rise icons: <a href="https://www.flaticon.com/free-icons/rise" target="_blank" rel="noopener noreferrer">Magnific - Flaticon</a></li>
                    <li>Payment icons: <a href="https://www.flaticon.com/free-icons/payment" target="_blank" rel="noopener noreferrer">Pixel perfect - Flaticon</a></li>
                    <li>Airport icons: <a href="https://www.flaticon.com/free-icons/airport" target="_blank" rel="noopener noreferrer">Magnific - Flaticon</a></li>
                    <li>Railway icons: <a href="https://www.flaticon.com/free-icons/railway" target="_blank" rel="noopener noreferrer">Smashicons - Flaticon</a></li>
                    <li>Booking icons: <a href="https://www.flaticon.com/free-icons/booking" target="_blank" rel="noopener noreferrer">Magnific - Flaticon</a></li>
                    <li>Contact us icons: <a href="https://www.flaticon.com/free-icons/contact-us" target="_blank" rel="noopener noreferrer">alimasykurm - Flaticon</a></li>
                    <li>Pin icons: <a href="https://www.flaticon.com/free-icons/pin" target="_blank" rel="noopener noreferrer">Magnific - Flaticon</a></li>
                    <li>Search icons: <a href="https://www.flaticon.com/free-icons/search" target="_blank" rel="noopener noreferrer">Chanut - Flaticon</a></li>
                </ul>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
