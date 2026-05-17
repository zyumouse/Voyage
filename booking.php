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
                    <button class="headerProfileButton" aria-expanded="false"><?php echo $username; ?></button>
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
    <nav id="nav">
        <div class="navTop">
            <div class="navItem">
                <a href="index.php">
                    <img class="navItemimg" src="./pics/Icon/voyage1.png" alt="Voyage logo">
                </a>
            </div>
            
            <div class="navItem"></div>
        </div>
        <div class="navBottom">
            <div class="navItem">
                <button class="about" onclick="location.href='index.php';" style="cursor:pointer;">Homepage</button>
                <button class="about" onclick="location.href='maps.php';" style="cursor:pointer;">Maps</button>
            </div>
            <div class="navItem">
                <button class="aboutSpecialCase" onclick="location.href='signupredir.html';" style="cursor:pointer;">Sign Up!</button>
            </div>
        </div>
    </nav>

    <div class="bookingContainer">
        <h1 class="bookingTitle">Your Trips</h1>

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
                    address VARCHAR(255) NOT NULL,
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
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS address VARCHAR(255) NOT NULL DEFAULT ''");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS card_number VARCHAR(32) NOT NULL DEFAULT ''");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS origin VARCHAR(100) NOT NULL DEFAULT ''");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS destination VARCHAR(100) NOT NULL DEFAULT ''");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS ticket_date DATE NOT NULL DEFAULT '1970-01-01'");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS ticket_time TIME NOT NULL DEFAULT '00:00:00'");
                $conn->query("ALTER TABLE tickets ADD COLUMN IF NOT EXISTS estimated_arrival_time TIME NOT NULL DEFAULT '00:00:00'");

                $availableTrips = [];
                $tripResult = $conn->query("SELECT id, origin, destination, ticket_date, ticket_time FROM available_tickets ORDER BY ticket_date, ticket_time");
                if ($tripResult) {
                    while ($tripRow = $tripResult->fetch_assoc()) {
                        $availableTrips[] = $tripRow;
                    }
                }

                $stmt = $conn->prepare('SELECT origin, destination, ticket_date, ticket_time, created_at FROM tickets WHERE user_id = ? ORDER BY created_at DESC');
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
                        <div class="bookingLogItem">
                            <div>
                                <h2><?php echo htmlspecialchars($row['origin']); ?> &rarr; <?php echo htmlspecialchars($row['destination']); ?></h2>
                                <h4>Booked on <?php echo date('d/m/Y', strtotime($row['created_at'])); ?></h4>
                            </div>
                            <div>
                                <h3><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['ticket_date'])) . ' @ ' . date('H:i', strtotime($row['ticket_time']))); ?></h3>
                                <h4>ETA <?php echo htmlspecialchars(date('H:i', strtotime($row['estimated_arrival_time']))); ?></h4>
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
                <form action="checkout.php" method="POST">
                    <?php if (empty($availableTrips)): ?>
                        <div class="bookingNotice">
                            <p>No available trips have been provided by the admin yet.</p>
                            <p>Please check back later or contact support.</p>
                        </div>
                    <?php else: ?>
                        <label for="trip_id">Choose available trip</label>
                        <select id="trip_id" name="trip_id" class="payInput" required>
                            <option value="">Select available trip</option>
                            <?php foreach ($availableTrips as $trip): ?>
                                <option value="<?php echo htmlspecialchars($trip['id']); ?>"><?php echo htmlspecialchars($trip['origin'] . ' → ' . $trip['destination'] . ' — ' . date('d/m/Y', strtotime($trip['ticket_date'])) . ' @ ' . date('H:i', strtotime($trip['ticket_time'])) . ' (ETA ' . date('H:i', strtotime($trip['estimated_arrival_time'])) . ')'); ?></option>
                            <?php endforeach; ?>
                        </select>

                        <button id="checkoutButton" type="submit" class="payButton" style="display: none;">Proceed to checkout</button>
                    <?php endif; ?>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tripSelect = document.getElementById('trip_id');
            var checkoutButton = document.getElementById('checkoutButton');
            if (tripSelect && checkoutButton) {
                tripSelect.addEventListener('change', function() {
                    if (tripSelect.value) {
                        checkoutButton.style.display = 'inline-flex';
                    } else {
                        checkoutButton.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>
