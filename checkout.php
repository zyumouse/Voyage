<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

// Accept trip_id from POST or GET from booking page
$trip_id = 0;
if (isset($_POST['trip_id'])) {
    $trip_id = (int)$_POST['trip_id'];
} elseif (isset($_GET['trip_id'])) {
    $trip_id = (int)$_GET['trip_id'];
}

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
    <style>
        .auth-page {
            min-height: calc(100vh - 84px);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 70px 20px 32px;
        }
        .auth-card {
            width: min(1250px, 100%);
            padding: 36px 32px;
            text-align: left;
        }
        .checkout-subtitle {
            margin: 0 0 20px;
            color: #4b4f7d;
            font-size: 1rem;
        }
        .bookingContainer {
            padding: 0;
        }
        .bookingLogItem {
            margin-bottom: 22px;
        }
        .addBooking {
            display: grid;
            gap: 18px;
            max-width: 540px;
        }
        .addBooking label {
            display: block;
            margin-bottom: 4px;
            font-weight: 600;
            color: #2b2f58;
        }
        .payButton {
            width: 100%;
            justify-content: center;
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
        <div class="siteHeader-actions">
            <div class="search" role="search">
                <input type="text" placeholder="Search..." class="searchInput" aria-label="Search">
                <button class="searchButton" aria-label="Search Button"><img src="./pics/search.png" width="20" height="20" alt="Search" class="searchIcon"></button>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="profile-menu">
                    <button class="headerProfileButton" aria-expanded="false" tabindex="-1"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></button>
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
    <div class="auth-page">
        <div class="auth-card">
            <h1 class="auth-title">Checkout</h1>
            <p class="checkout-subtitle">Complete your trip booking details and secure your ticket.</p>
            <div class="bookingContainer">
        <?php
            $departure = new DateTime($trip['ticket_date'] . ' ' . $trip['ticket_time']);
            $arrival = new DateTime($trip['ticket_date'] . ' ' . $trip['estimated_arrival_time']);
            if ($arrival < $departure) {
                $arrival->modify('+1 day');
            }
            $duration = $departure->diff($arrival)->format('%h hr %i min');
        ?>
        <div class="bookingLogItem">
            <div>
                <h2><?php echo htmlspecialchars($trip['origin']); ?> &rarr; <?php echo htmlspecialchars($trip['destination']); ?></h2>
                <h4><?php echo htmlspecialchars(date('d/m/Y', strtotime($trip['ticket_date']))); ?> at <?php echo htmlspecialchars(date('H:i', strtotime($trip['ticket_time']))); ?></h4>
                <p class="bookingLogMeta">Estimated arrival: <?php echo htmlspecialchars(date('H:i', strtotime($trip['estimated_arrival_time']))); ?> &bull; Duration <?php echo htmlspecialchars($duration); ?></p>
            </div>
            <div>
                <h3>Route details</h3>
                <h4>Departure: <?php echo htmlspecialchars($trip['origin']); ?></h4>
                <h4>Destination: <?php echo htmlspecialchars($trip['destination']); ?></h4>
            </div>
        </div>

        <form action="data.php" method="POST" class="addBooking">
            <input type="hidden" name="trip_id" value="<?php echo (int)$trip['id']; ?>">

            <label for="name">Name</label>
            <input id="name" type="text" name="name" placeholder="" class="payInput" required>

            <label for="IC_number">Phone Number</label>
            <input id="IC_number" type="text" name="IC_number" placeholder="" class="payInput" maxlength="13" required>

            <label for="card_number">Card Number</label>
            <input id="card_number" type="text" name="card_number" class="payInput" placeholder="" maxlength="19">

            <button type="submit" class="payButton">Confirm & Pay</button>
        </form>
            </div>
        </div>
    </div>
</body>
</html>
