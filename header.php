<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : null;
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = !empty($_SESSION['is_admin']);
?>
<header class="siteHeader">
    <div class="siteHeader-inner">
        <a class="siteHeader-brand" href="index.php" aria-label="Voyage home">
            <img class="siteHeader-logo" src="./pics/Icon/voyage1.png" alt="Voyage logo">
            <span class="siteHeader-brandName">Voyage</span>
        </a>

        <nav class="siteHeader-nav" aria-label="Main navigation">
            <a href="index.php">Home</a>
            <a href="booking.php">Booking</a>
            <a href="maps.php">Map</a>
            <a href="faq.php">FAQ</a>
        </nav>

        <div class="siteHeader-actions">
            <?php if (!$isLoggedIn): ?>
                <a class="headerLoginLink" href="login.html">Log in</a>
            <?php endif; ?>
            <?php if (!$isLoggedIn): ?>
                <a class="headerBookButton" href="signupredir.html">Sign Up</a>
            <?php endif; ?>

            <?php if ($isLoggedIn): ?>
                <div class="profile-menu">
                    <button class="headerProfileButton" type="button" aria-expanded="false" tabindex="0">
                        <?php echo $username; ?>
                    </button>
                    <div class="profile-dropdown">
                        <a href="profile.php">Account</a>
                        <a href="settings.php">Settings</a>
                        <?php if ($isAdmin): ?>
                            <a href="admin_customers.php">Customers</a>
                        <?php endif; ?>
                        <a href="logout.php">Log Out</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>
