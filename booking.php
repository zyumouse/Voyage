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
    'S20: Komtar'
];
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
        .trip-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin: 8px 0 18px;
        }
        .trip-card {
            display: block;
            width: calc(50% - 8px);
            box-sizing: border-box;
            background: #ffffff;
            border: 1px solid #e6e7fb;
            border-radius: 12px;
            padding: 14px;
            cursor: pointer;
            transition: box-shadow 0.18s ease, border-color 0.18s ease;
        }
        .trip-card input[type="radio"] {
            display: none;
        }
        .trip-card-content {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .trip-card .trip-route {
            font-weight: 700;
            color: #1f2360;
        }
        .trip-card .trip-datetime,
        .trip-card .trip-eta {
            color: #4b4f7d;
            font-size: 0.95rem;
        }
        .trip-card input[type="radio"]:checked + .trip-card-content {
            border-radius: 10px;
            box-shadow: 0 12px 30px rgba(46,37,119,0.12);
            border-color: #6356ff;
        }
        @media (max-width: 720px) {
            .trip-card { width: 100%; }
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
            <?php if ($isLoggedIn): ?>
                <div class="profile-menu">
                    <button class="headerProfileButton" aria-expanded="false" tabindex="-1"><?php echo $username; ?></button>
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
    

    <div class="auth-page booking-page">
        <div class="auth-card">
            <h1 class="auth-title">Your Trips</h1>
            <p class="auth-subtitle">Review your current bookings and select an available trip to reserve.</p>
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
                    ticket_time TIME NOT NULL,
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
                    ticket_time TIME NOT NULL,
                    estimated_arrival_time TIME NOT NULL,
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
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS ticket_time TIME NOT NULL DEFAULT '00:00:00'");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS estimated_arrival_time TIME NOT NULL DEFAULT '00:00:00'");

                $availableTrips = [];
                $tripResult = $conn->query("SELECT id, origin, destination, ticket_date, ticket_time, estimated_arrival_time FROM available_tickets ORDER BY ticket_date, ticket_time");
                if ($tripResult) {
                    while ($tripRow = $tripResult->fetch_assoc()) {
                        $availableTrips[] = $tripRow;
                    }
                }

                $stmt = $conn->prepare('SELECT origin, destination, ticket_date, ticket_time, estimated_arrival_time, created_at FROM tickets WHERE user_id = ? ORDER BY created_at DESC');
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
            <div class="bookingLog">
                <?php if (!empty($result) && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            $departureDateTime = date('d/m/Y @ H:i', strtotime($row['ticket_date'] . ' ' . $row['ticket_time']));
                            $etaTime = date('H:i', strtotime($row['estimated_arrival_time']));
                            $createdDateTime = date('d/m/Y H:i', strtotime($row['created_at']));
                            $departure = new DateTime($row['ticket_date'] . ' ' . $row['ticket_time']);
                            $arrival = new DateTime($row['ticket_date'] . ' ' . $row['estimated_arrival_time']);
                            if ($arrival < $departure) {
                                $arrival->modify('+1 day');
                            }
                            $durationText = $departure->diff($arrival)->format('%h hr %i min');
                        ?>
                        <div class="bookingLogItem">
                            <div>
                                <h2><?php echo htmlspecialchars($row['origin']); ?> &rarr; <?php echo htmlspecialchars($row['destination']); ?></h2>
                                <h4>Booked on <?php echo htmlspecialchars($createdDateTime); ?></h4>
                                <p class="bookingLogMeta">Trip date: <?php echo htmlspecialchars(date('d/m/Y', strtotime($row['ticket_date']))); ?> | Carrier: Voyage</p>
                                <p class="bookingLogMeta">Route: <?php echo htmlspecialchars($row['origin']); ?> → <?php echo htmlspecialchars($row['destination']); ?></p>
                            </div>
                            <div>
                                <h3><?php echo htmlspecialchars($departureDateTime); ?></h3>
                                <h4>ETA <?php echo htmlspecialchars($etaTime); ?> • Duration <?php echo htmlspecialchars($durationText); ?></h4>
                                <h4>Ticket status: Confirmed</h4>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="bookingLogItem empty">
                        <p>No bookings yet. Use the form below to book your first trip.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="addBooking">
                <h1 class="payTitle">Choose Your Trip</h1>
                <?php if (empty($availableTrips)): ?>
                    <div class="bookingNotice">
                        <p>No available trips have been provided by the admin yet.</p>
                        <p>Please check back later or contact support.</p>
                    </div>
                <?php else: ?>
                    <div class="trip-cards">
                        <?php foreach ($availableTrips as $trip): ?>
                            <a class="trip-card" href="checkout.php?trip_id=<?php echo urlencode($trip['id']); ?>">
                                <div class="trip-card-content">
                                    <div class="trip-route"><?php echo htmlspecialchars($trip['origin'] . ' → ' . $trip['destination']); ?></div>
                                    <div class="trip-datetime"><?php echo htmlspecialchars(date('d/m/Y', strtotime($trip['ticket_date'])) . ' @ ' . date('H:i', strtotime($trip['ticket_time']))); ?></div>
                                    <div class="trip-eta">ETA <?php echo htmlspecialchars(date('H:i', strtotime($trip['estimated_arrival_time']))); ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        </div>
    </div>
</body>
</html>
