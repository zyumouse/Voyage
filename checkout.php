<?php
session_start();
require_once __DIR__ . '/schema.php';
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
voyage_migrate_legacy_tables($conn);

$trip = null;
if ($trip_id > 0) {
    $tripStmt = $conn->prepare('SELECT id, origin, destination, ticket_date, ticket_time, estimated_arrival_time FROM available_trips WHERE id = ?');
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
$customer = [
    'username' => '',
    'phone' => ''
];
$customerId = (int)$_SESSION['user_id'];
$customerStatement = $conn->prepare('SELECT username, phone FROM accounts WHERE id = ?');
if ($customerStatement) {
    $customerStatement->bind_param('i', $customerId);
    if ($customerStatement->execute()) {
        $customerResult = $customerStatement->get_result();
        if ($customerResult && $customerRow = $customerResult->fetch_assoc()) {
            $customer = $customerRow;
        }
    }
    $customerStatement->close();
}
$estimatedExpiryTimestamp = time() + 86400;
$escape = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem("voyage-theme")||"dark";document.documentElement.classList.add(t+"-mode");if(document.body)document.body.classList.add(t+"-mode");else document.addEventListener("DOMContentLoaded",function(){document.body.classList.add(t+"-mode")});}catch(e){}})();</script>
    <script src="theme.js" defer></script>
    <script src="form-validation.js" defer></script>
    <link rel="icon" type="image/x-icon" href="./pics/Icon/voyage1.ico">
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
    <div class="checkout-page-content">
        <main class="checkout-main">
            <header class="checkout-header">
                <p class="checkout-eyebrow">Ticket checkout</p>
                <h1>Confirm your trip</h1>
                <p>Review your journey and enter the information for this ticket.</p>
            </header>

            <div class="checkout-layout">
                <section class="checkout-journey" aria-label="Journey summary">
                    <p class="checkout-section-label">Your journey</p>
                    <h2 class="checkout-route">
                        <span><?php echo $escape($trip['origin']); ?></span>
                        <span class="checkout-route-arrow" aria-hidden="true">&rarr;</span>
                        <span><?php echo $escape($trip['destination']); ?></span>
                    </h2>
                    <dl class="checkout-trip-details">
                        <div>
                            <dt>Expires</dt>
                            <dd><time datetime="<?php echo $escape(date('c', $estimatedExpiryTimestamp)); ?>"><?php echo $escape(date('M j, Y \a\t H:i', $estimatedExpiryTimestamp)); ?></time></dd>
                        </div>
                        <?php if (!empty($trip['ticket_time']) && $trip['ticket_time'] !== '00:00:00'): ?>
                            <div>
                                <dt>Departure</dt>
                                <dd><?php echo $escape(date('H:i', strtotime((string)$trip['ticket_time']))); ?></dd>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($trip['estimated_arrival_time']) && $trip['estimated_arrival_time'] !== '00:00:00'): ?>
                            <div>
                                <dt>Estimated arrival</dt>
                                <dd><?php echo $escape(date('H:i', strtotime((string)$trip['estimated_arrival_time']))); ?></dd>
                            </div>
                        <?php endif; ?>
                    </dl>
                    <p class="checkout-validity">Ticket valid for 24 hours from purchase.</p>
                </section>

                <section class="checkout-form-panel" aria-labelledby="checkout-form-title">
                    <div class="checkout-form-heading">
                        <p class="checkout-section-label">Passenger details</p>
                        <h2 id="checkout-form-title">Complete booking</h2>
                        <p>Enter the related information for this ticket.</p>
                    </div>
                    <form action="data.php" method="POST" class="checkout-form">
                        <?php if ($trip['id'] > 0): ?>
                            <input type="hidden" name="trip_id" value="<?php echo (int)$trip['id']; ?>">
                        <?php else: ?>
                            <input type="hidden" name="origin" value="<?php echo $escape($trip['origin']); ?>">
                            <input type="hidden" name="destination" value="<?php echo $escape($trip['destination']); ?>">
                        <?php endif; ?>

                        <div class="checkout-fields">
                            <div class="checkout-field">
                                <label for="name">Name</label>
                                <input id="name" type="text" name="name" autocomplete="name" maxlength="100" pattern="[\p{L}\p{M}][\p{L}\p{M} .'\-]{0,99}" title="Use letters, spaces, apostrophes, periods, or hyphens." value="<?php echo $escape($customer['username']); ?>" required>
                            </div>
                            <div class="checkout-field">
                                <label for="IC_number">Phone Number</label>
                                <input id="IC_number" type="tel" name="IC_number" autocomplete="tel" pattern="\+[0-9]{7,15}" maxlength="16" title="Start with + followed by 7 to 15 digits. Do not use spaces or punctuation." data-voyage-phone value="<?php echo $escape($customer['phone']); ?>" required>
                            </div>
                            <div class="checkout-field checkout-field-full">
                                <label for="card_number">Credit Card Number</label>
                                <input id="card_number" type="text" name="card_number" inputmode="numeric" autocomplete="cc-number" pattern="[0-9]{1,50}" maxlength="50" title="Enter 1 to 50 digits with no spaces or punctuation." data-digits-only required>
                            </div>
                        </div>

                        <button type="submit" class="checkout-submit">Confirm & Pay</button>
                    </form>
                </section>
            </div>
        </main>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
