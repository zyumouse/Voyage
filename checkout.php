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

$origin = isset($_GET['origin']) ? trim($_GET['origin']) : '';
$destination = isset($_GET['destination']) ? trim($_GET['destination']) : '';
$ticket_date = isset($_GET['ticket_date']) ? trim($_GET['ticket_date']) : '';

if ($trip_id <= 0) {
    if ($origin === '' || $destination === '') {
        header('Location: booking.php?error=' . urlencode('Please choose an origin and destination for your trip.'));
        exit;
    }
    if ($origin === $destination) {
        header('Location: booking.php?error=' . urlencode('Origin and destination must be different.'));
        exit;
    }
    if ($ticket_date === '') {
        $ticket_date = (new DateTime('+1 day'))->format('Y-m-d');
    }
}

$servername = 'localhost';
$username = 'root';
$password = '';
$database = 'ticket_system';

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$trip = null;
if ($trip_id > 0) {
    $tripStmt = $conn->prepare('SELECT id, origin, destination, ticket_date, ticket_time, estimated_arrival_time FROM available_tickets WHERE id = ?');
    $tripStmt->bind_param('i', $trip_id);
    $tripStmt->execute();
    $tripResult = $tripStmt->get_result();
    $trip = $tripResult ? $tripResult->fetch_assoc() : null;
    if (!$trip) {
        header('Location: booking.php?error=' . urlencode('Selected trip not found.'));
        exit;
    }
} else {
    $trip = [
        'id' => 0,
        'origin' => $origin,
        'destination' => $destination,
        'ticket_date' => $ticket_date,
        'ticket_time' => '',
        'estimated_arrival_time' => ''
    ];
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
    <?php include __DIR__ . '/header.php'; ?>
    <div class="auth-page">
        <div class="auth-card">
            <h1 class="auth-title">Checkout</h1>
            <p class="checkout-subtitle">Complete your trip booking details and secure your ticket.</p>
            <div class="bookingContainer">
        <?php
            $duration = 'N/A';
        ?>
        <div class="bookingLogItem">
            <div>
                <h2><?php echo htmlspecialchars($trip['origin']); ?> &rarr; <?php echo htmlspecialchars($trip['destination']); ?></h2>
                <h4><?php echo htmlspecialchars(date('d/m/Y', strtotime($trip['ticket_date']))); ?></h4>
                <p class="bookingLogMeta">Ticket valid for 24 hours from booking time.</p>
            </div>
            <div>
                <h3>Route details</h3>
                <h4>Departure: <?php echo htmlspecialchars($trip['origin']); ?></h4>
                <h4>Destination: <?php echo htmlspecialchars($trip['destination']); ?></h4>
            </div>
        </div>

        <form action="data.php" method="POST" class="addBooking">
            <?php if ($trip['id'] > 0): ?>
                <input type="hidden" name="trip_id" value="<?php echo (int)$trip['id']; ?>">
            <?php else: ?>
                <input type="hidden" name="origin" value="<?php echo htmlspecialchars($trip['origin']); ?>">
                <input type="hidden" name="destination" value="<?php echo htmlspecialchars($trip['destination']); ?>">
            <?php endif; ?>

            <label for="name">Name</label>
            <input id="name" type="text" name="name" placeholder="" class="payInput" required>

            <label for="IC_number">Phone Number</label>
            <input id="IC_number" type="text" name="IC_number" placeholder="" class="payInput" maxlength="13" required>

            <label for="card_number">Credit Card Number</label>
            <input id="card_number" type="text" name="card_number" class="payInput" placeholder="" maxlength="19">

            <button type="submit" class="payButton">Confirm & Pay</button>
        </form>
            </div>
        </div>
    </div>
</body>
</html>
