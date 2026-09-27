<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$fullName = trim((string)($_SESSION['username'] ?? ''));
$nameParts = preg_split('/\s+/u', $fullName, 2) ?: [];
$firstName = $nameParts[0] ?? '';
$firstNameCharacters = preg_split('//u', $firstName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
$profileButtonName = count($firstNameCharacters) > 8
    ? implode('', array_slice($firstNameCharacters, 0, 8)) . '...'
    : $firstName;
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
            <a href="support.php">Support</a>
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
                    <button class="headerProfileButton" type="button" aria-expanded="false" tabindex="0" title="<?php echo htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($profileButtonName, ENT_QUOTES, 'UTF-8'); ?>
                    </button>
                    <div class="profile-dropdown">
                        <a href="profile.php">Profile</a>
                        <a href="settings.php">Settings</a>
                        <?php if ($isAdmin): ?>
                            <a href="admin_accounts.php">Customers</a>
                            <a href="admin_customers.php">Ticket Management</a>
                        <?php endif; ?>
                        <a href="logout.php">Log Out</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>
