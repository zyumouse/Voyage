<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

// Accept POST from booking page
$trip_id = isset($_POST['trip_id']) ? (int)$_POST['trip_id'] : 0;

if ($trip_id <= 0) {
    header('Location: booking.php?error=' . urlencode('Please select a trip first.'));
    exit;
}

$servername = 'localhost';
$username = 'root';
$password = '';
$database = 'ticket_system';

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$tripStmt = $conn->prepare('SELECT id, origin, destination, ticket_date, ticket_time, estimated_arrival_time FROM available_tickets WHERE id = ?');
$tripStmt->bind_param('i', $trip_id);
$tripStmt->execute();
$tripResult = $tripStmt->get_result();
$trip = $tripResult ? $tripResult->fetch_assoc() : null;
if (!$trip) {
    header('Location: booking.php?error=' . urlencode('Selected trip not found.'));
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
    <link rel="stylesheet" href="style.css">
    <title>Checkout</title>
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="profile-menu">
                    <button class="headerProfileButton" aria-expanded="false"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></button>
                    <div class="profile-dropdown">
                        <a href="profile.php">Account</a>
                        <a href="settings.php">Settings</a>
                        <?php if (!empty($_SESSION['is_admin'])): ?>
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
    <div class="bookingContainer">
        <h1 class="bookingTitle">Checkout</h1>
        <div class="bookingLogItem">
            <div>
                <h2><?php echo htmlspecialchars($trip['origin']); ?> &rarr; <?php echo htmlspecialchars($trip['destination']); ?></h2>
                <h4>Trip on <?php echo date('d/m/Y', strtotime($trip['ticket_date'])); ?> at <?php echo date('H:i', strtotime($trip['ticket_time'])); ?></h4>
            </div>
        </div>

        <form action="data.php" method="POST" class="addBooking">
            <input type="hidden" name="trip_id" value="<?php echo (int)$trip['id']; ?>">

            <label for="name">Name</label>
            <input id="name" type="text" name="name" placeholder="Socrates" class="payInput" required>

            <label for="IC_number">IC number</label>
            <input id="IC_number" type="text" name="IC_number" placeholder="+6012 345 6781" class="payInput" maxlength="13" required>

            <label for="address">Address</label>
            <input id="address" type="text" name="address" placeholder="Diddy Av. 45 20 1785" class="payInput" required>

            <label for="card_number">Card Number</label>
            <input id="card_number" type="text" name="card_number" class="payInput" placeholder="Card Number" maxlength="19" required>

            <button type="submit" class="payButton">Confirm & Pay</button>
        </form>

    </div>
</body>
</html>
