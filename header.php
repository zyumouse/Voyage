<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : null;
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = !empty($_SESSION['is_admin']);
?>
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
        <a href="booking.php">Booking</a>
        <a href="maps.php">Maps</a>
        <a href="faq.php">FAQ</a>
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
