<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: login.html');
    exit;
}

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
    <title>Admin - Schedule</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        html, body {
            min-height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .auth-page {
            min-height: auto;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 50px 20px 40px;
        }
        .auth-card {
            width: min(1400px, 94%);
            max-width: 1400px;
            padding: 40px 32px;
        }
        .auth-subtitle {
            margin: 0 auto 24px;
            max-width: 760px;
            color: #4b4f7d;
        }
        .available-trips {
            width: 100%;
        }
        .available-trips table {
            width: 100%;
            max-width: 1250px;
            margin: 22px auto 0;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            font-size: 0.88rem;
        }
        .available-trips th,
        .available-trips td {
            padding: 10px 12px;
            text-align: center;
        }
        .available-trips th {
            background-image: linear-gradient(to bottom, black, #2c3550);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.88rem;
        }
        .available-trips tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .available-trips tr:hover {
            background-color: #f1f1f1;
        }
        .available-trips td {
            border-bottom: 1px solid #ddd;
        }
        .add-trip-form {
            width: 100%;
            max-width: 100%;
            background: rgba(255,255,255,0.95);
            padding: 22px;
            border-radius: 18px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px 18px;
            align-items: center;
        }
        .add-trip-form button {
            grid-column: 2 / 3;
            justify-self: end;
            align-self: center;
        }
        .available-trips .errorMessage,
        .available-trips .successMessage {
            margin: 0 0 16px;
            padding: 14px 16px;
            border-radius: 12px;
        }
        .available-trips .errorMessage {
            background: rgba(235, 87, 87, 0.12);
            color: #b03030;
        }
        .available-trips .successMessage {
            background: rgba(76, 61, 245, 0.12);
            color: #2d2ba8;
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
        <div class="siteHeader-search">
            <div class="search" role="search">
                <input type="text" placeholder="Search..." class="searchInput" aria-label="Search">
                <button class="searchButton" aria-label="Search Button"><img src="./pics/search.png" width="20" height="20" alt="Search" class="searchIcon"></button>
            </div>
        </div>
        <div class="siteHeader-actions">
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
            <h1 class="auth-title">Admin - Schedule</h1>
            <p class="auth-subtitle">Manage available trips for the Voyage booking system.</p>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "ticket_system";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("CREATE TABLE IF NOT EXISTS available_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    ticket_date DATE NOT NULL,
    ticket_time TIME NOT NULL,
    estimated_arrival_time TIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
$conn->query("ALTER TABLE available_tickets ADD COLUMN IF NOT EXISTS estimated_arrival_time TIME NOT NULL DEFAULT '00:00:00'");

$addError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_trip') {
    $origin = isset($_POST['origin']) ? trim($_POST['origin']) : '';
    $destination = isset($_POST['destination']) ? trim($_POST['destination']) : '';
    $ticket_date = isset($_POST['ticket_date']) ? trim($_POST['ticket_date']) : '';
    $ticket_time = isset($_POST['ticket_time']) ? trim($_POST['ticket_time']) : '';
    $estimated_arrival_time = isset($_POST['estimated_arrival_time']) ? trim($_POST['estimated_arrival_time']) : '';

    if ($origin === '' || $destination === '' || $ticket_date === '' || $ticket_time === '' || $estimated_arrival_time === '') {
        $addError = 'All fields are required to add an available trip.';
    } elseif ($origin === $destination) {
        $addError = 'Origin and destination must be different.';
    } else {
        $stmt = $conn->prepare('INSERT INTO available_tickets (origin, destination, ticket_date, ticket_time, estimated_arrival_time) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $origin, $destination, $ticket_date, $ticket_time, $estimated_arrival_time);
        $stmt->execute();
        header('Location: admin_schedule.php?added=1');
        exit;
    }
}

$availableTrips = [];
$tripResult = $conn->query("SELECT id, origin, destination, ticket_date, ticket_time, estimated_arrival_time FROM available_tickets ORDER BY ticket_date, ticket_time");
if ($tripResult) {
    while ($tripRow = $tripResult->fetch_assoc()) {
        $availableTrips[] = $tripRow;
    }
}
?>

<section class="available-trips">
    <?php if (!empty($addError)): ?>
        <div class="errorMessage"><?php echo htmlspecialchars($addError); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['added'])): ?>
        <div class="successMessage">Available trip added successfully.</div>
    <?php endif; ?>

    <form action="admin_schedule.php" method="POST" class="add-trip-form">
        <input type="hidden" name="action" value="add_trip">
        <label for="origin">Origin</label>
        <select id="origin" name="origin" required>
            <option value="">Select origin</option>
            <?php foreach ($stops as $stop): ?>
                <option value="<?php echo htmlspecialchars($stop); ?>"><?php echo htmlspecialchars($stop); ?></option>
            <?php endforeach; ?>
        </select>
        <label for="destination">Destination</label>
        <select id="destination" name="destination" required>
            <option value="">Select destination</option>
            <?php foreach ($stops as $stop): ?>
                <option value="<?php echo htmlspecialchars($stop); ?>"><?php echo htmlspecialchars($stop); ?></option>
            <?php endforeach; ?>
        </select>
        <label for="ticket_date">Date</label>
        <input id="ticket_date" name="ticket_date" type="date" required>
        <label for="ticket_time">Departure Time</label>
        <input id="ticket_time" name="ticket_time" type="time" required>
        <label for="estimated_arrival_time">Estimated Arrival Time</label>
        <input id="estimated_arrival_time" name="estimated_arrival_time" type="time" required>
        <button type="submit">Add Available Trip</button>
    </form>

    <table class="admin-customers-table">
        <tr>
            <th>ID</th>
            <th>Origin</th>
            <th>Destination</th>
            <th>Trip Date</th>
            <th>Departure</th>
            <th>Estimated Arrival</th>
            <th>Actions</th>
        </tr>
        <?php if (!empty($availableTrips)): ?>
            <?php foreach ($availableTrips as $trip): ?>
                <tr>
                    <td><?php echo htmlspecialchars($trip['id']); ?></td>
                    <td><?php echo htmlspecialchars($trip['origin']); ?></td>
                    <td><?php echo htmlspecialchars($trip['destination']); ?></td>
                    <td><?php echo htmlspecialchars($trip['ticket_date']); ?></td>
                    <td><?php echo htmlspecialchars($trip['ticket_time']); ?></td>
                    <td><?php echo htmlspecialchars($trip['estimated_arrival_time']); ?></td>
                    <td><a href="delete_trip.php?id=<?php echo htmlspecialchars($trip['id']); ?>" onclick="return confirm('Delete this available trip?');">Delete</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No available trips have been added yet.</td></tr>
        <?php endif; ?>
    </table>
    </section>
        </div>
    </div>

</body>
</html>
