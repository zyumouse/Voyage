<?php
session_start();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : null;
$isLoggedIn = isset($_SESSION['user_id']);
$stops = [
    'A01: PSR-A',
    'S02: Permatang Damar Laut',
    'S03: Lapangan Terbang Antarabangsa Pulau Pinang',
    'S04: Sungai Tiram',
    'S05: FIZ South',
    'S06: FIZ North',
    'S07: Jalan Tengah',
    'S08: SPICE',
    'S09: Bukit Jambul',
    'S10: Sungai Nibong',
    'S11: Sungai Dua',
    'S12: Batu Uban',
    'S13: Jalan Universiti',
    'S14: Gelugor',
    'S15: Penang Waterfront',
    'S16: Jelutong East',
    'S17: Sungai Pinang',
    'S18: Bandar Sri Pinang',
    'S19: Macallum',
    'S20: Komtar',
    'S31: Penang Sentral'
];
$today = (new DateTime())->format('Y-m-d');
$maxDate = (new DateTime('+7 days'))->format('Y-m-d');
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
    <title>Voyage - Booking</title>
    <link rel="icon" type="image/x-icon" href="./pics/Icon/voyage1.ico">
    <style>
        .auth-page.booking-page {
            min-height: calc(100vh - 84px);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 70px 20px 32px;
        }
        .booking-page .auth-card {
            width: min(1200px, 100%);
            padding: 36px 32px;
            text-align: left;
        }
        .booking-page .bookingContainer {
            padding: 0;
        }
        .booking-page .bookingTitle {
            margin: 0 0 8px;
        }
        .booking-page .bookingLog {
            gap: 24px;
        }
        .booking-page .addBooking {
            margin-top: 32px;
        }
        .booking-page .payTitle {
            margin-bottom: 16px;
        }
        .route-search {
            margin: 26px 0 20px;
            padding: 24px;
            background: #ffffff;
            border: 1px solid rgba(77,64,255,0.16);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(92,88,173,0.08);
        }
        .route-search h2 {
            margin: 0 0 16px;
            font-size: 1.3rem;
            color: #161c3f;
        }
        .route-search-form {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 16px;
            align-items: end;
        }
        .route-search-form label {
            display: flex;
            flex-direction: column;
            gap: 8px;
            color: #1f2350;
            font-weight: 700;
        }
        .route-search-form select,
        .route-search-form input[type="date"],
        .route-search-form button {
            width: 100%;
            min-height: 46px;
            padding: 10px 12px;
            border-radius: 14px;
            border: 1px solid #c8cbe8;
            font-size: 0.95rem;
            color: #161c3f;
            background: #ffffff;
            box-sizing: border-box;
            font-family: inherit;
            line-height: 1.5;
        }
        .route-search-form input[type="date"] {
            appearance: textfield;
            -webkit-appearance: textfield;
            -moz-appearance: textfield;
        }
        .route-search-form label[for="custom_ticket_date"] {
            grid-column: 1;
        }
        .route-search-form button {
            grid-column: 1 / -1;
            background: #3039ff;
            border-color: transparent;
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .route-search-form button:hover {
            transform: translateY(-1px);
            background: #202dce;
        }
        .route-notes {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 8px;
        }
        .route-notes .route-note {
            margin: 0;
            font-size: 0.86rem;
            color: #5a5f7d;
            line-height: 1.4;
        }
        .route-search .message-box {
            display: none;
        }
        @media (max-width: 720px) {
            .route-search-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>

    <div class="auth-page booking-page">
        <div class="auth-card">
            <h1 class="auth-title">Your Trips</h1>
            <p class="auth-subtitle">Review your current bookings and book a custom route with your chosen origin and destination.</p>
            <div class="bookingContainer">

        <?php if (!$isLoggedIn): ?>
            <div class="bookingNotice">
                <p>Please <a href="login.html">log in</a> to create and view bookings.</p>
            </div>
        <?php else: ?>
            <?php
            $conn = new mysqli('localhost', 'root', '', 'ticket_system');
            if (!$conn->connect_error) {
                $conn->query("CREATE TABLE IF NOT EXISTS available_tickets (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    origin VARCHAR(100) NOT NULL,
                    destination VARCHAR(100) NOT NULL,
                    ticket_date DATE NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )");
                $conn->query("CREATE TABLE IF NOT EXISTS tickets (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    trip_id INT NOT NULL DEFAULT 0,
                    user_id INT NOT NULL,
                    name VARCHAR(100) NOT NULL,
                    phone_number VARCHAR(30) NOT NULL,
                    origin VARCHAR(100) NOT NULL,
                    destination VARCHAR(100) NOT NULL,
                    card_number VARCHAR(32) NOT NULL,
                    ticket_date DATE NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS trip_id INT NOT NULL DEFAULT 0");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS user_id INT NOT NULL DEFAULT 0");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS name VARCHAR(100) NOT NULL DEFAULT ''");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS phone_number VARCHAR(30) NOT NULL DEFAULT ''");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS card_number VARCHAR(32) NOT NULL DEFAULT ''");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS origin VARCHAR(100) NOT NULL DEFAULT ''");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS destination VARCHAR(100) NOT NULL DEFAULT ''");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS ticket_date DATE NOT NULL DEFAULT '1970-01-01'");

                $stmt = $conn->prepare('SELECT origin, destination, ticket_date, created_at FROM tickets WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) ORDER BY created_at DESC');
                $stmt->bind_param('i', $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
            }
            ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="successMessage">Booking created successfully!</div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="errorMessage"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></div>
            <?php endif; ?>

            <div class="route-search" style="margin-top: 0;">
                <h2>Book your own route</h2>
                <form class="route-search-form" method="GET" action="checkout.php">
                    <label for="custom_origin">Origin
                        <select id="custom_origin" name="origin" required>
                            <option value="" disabled selected>Select origin</option>
                            <?php foreach ($stops as $stop): ?>
                                <option value="<?php echo htmlspecialchars($stop); ?>"><?php echo htmlspecialchars($stop); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label for="custom_destination">Destination
                        <select id="custom_destination" name="destination" required>
                            <option value="" disabled selected>Select destination</option>
                            <?php foreach ($stops as $stop): ?>
                                <option value="<?php echo htmlspecialchars($stop); ?>"><?php echo htmlspecialchars($stop); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <div class="route-notes">
                        <p class="route-note">Ticket expiry date will be created automatically after purchase.</p>
                        <p class="route-note">Choose a route from our list of predefined stations. Tickets are valid for 24 hours from booking.</p>
                    </div>
                    <button type="submit">Book custom route</button>
                </form>
            </div>
            <div class="bookingLog">
                <?php if (!empty($result) && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            $createdDateTime = date('d/m/Y H:i', strtotime($row['created_at']));
                        ?>
                        <div class="bookingLogItem">
                            <div>
                                <h2><?php echo htmlspecialchars($row['origin']); ?> &rarr; <?php echo htmlspecialchars($row['destination']); ?></h2>
                                <h4>Booked on <?php echo htmlspecialchars($createdDateTime); ?></h4>
                                <p class="bookingLogMeta">Trip date: <?php echo htmlspecialchars(date('d/m/Y', strtotime($row['ticket_date']))); ?> | Carrier: Voyage</p>
                                <p class="bookingLogMeta">Route: <?php echo htmlspecialchars($row['origin']); ?> → <?php echo htmlspecialchars($row['destination']); ?></p>
                            </div>
                            <div>
                                <h3><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['ticket_date']))); ?></h3>
                                <h4>Ticket valid for 24 hours from booking time</h4>
                                <h4>Ticket status: Confirmed</h4>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="bookingLogItem empty">
                        <p>No active tickets yet. Book a custom route now — tickets expire 24 hours after purchase.</p>
                    </div>
                <?php endif; ?>
            </div>

        <?php endif; ?>
        </div>
    </div>
</body>
</html>
