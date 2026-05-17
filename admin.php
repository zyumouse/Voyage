<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: login.html');
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
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
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
    <h1>Admin Panel</h1>
    <a href="index.php">Back</a>

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
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $addError = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_trip') {
            $origin = isset($_POST['origin']) ? trim($_POST['origin']) : '';
            $destination = isset($_POST['destination']) ? trim($_POST['destination']) : '';
            $ticket_date = isset($_POST['ticket_date']) ? trim($_POST['ticket_date']) : '';
            $ticket_time = isset($_POST['ticket_time']) ? trim($_POST['ticket_time']) : '';

            if ($origin === '' || $destination === '' || $ticket_date === '' || $ticket_time === '') {
                $addError = 'All fields are required to add an available trip.';
            } elseif ($origin === $destination) {
                $addError = 'Origin and destination must be different.';
            } else {
                $stmt = $conn->prepare('INSERT INTO available_tickets (origin, destination, ticket_date, ticket_time) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('ssss', $origin, $destination, $ticket_date, $ticket_time);
                $stmt->execute();
                header('Location: admin.php?added=1');
                exit;
            }
        }

        $availableTrips = [];
        $tripResult = $conn->query("SELECT id, origin, destination, ticket_date, ticket_time FROM available_tickets ORDER BY ticket_date, ticket_time");
        if ($tripResult) {
            while ($tripRow = $tripResult->fetch_assoc()) {
                $availableTrips[] = $tripRow;
            }
        }
    ?>

    <section class="available-trips">
        <h2>Available Trips</h2>
        <?php if (!empty($addError)): ?>
            <div class="errorMessage"><?php echo htmlspecialchars($addError); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['added'])): ?>
            <div class="successMessage">Available trip added successfully.</div>
        <?php endif; ?>

        <form action="admin.php" method="POST" class="add-trip-form">
            <input type="hidden" name="action" value="add_trip">
            <label for="origin">Origin</label>
            <input id="origin" name="origin" type="text" required>
            <label for="destination">Destination</label>
            <input id="destination" name="destination" type="text" required>
            <label for="ticket_date">Date</label>
            <input id="ticket_date" name="ticket_date" type="date" required>
            <label for="ticket_time">Time</label>
            <input id="ticket_time" name="ticket_time" type="time" required>
            <button type="submit">Add Available Trip</button>
        </form>

        <table border="1">
            <tr>
                <th>ID</th>
                <th>Origin</th>
                <th>Destination</th>
                <th>Trip Date</th>
                <th>Trip Time</th>
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
                        <td><a href="delete_trip.php?id=<?php echo htmlspecialchars($trip['id']); ?>" onclick="return confirm('Delete this available trip?');">Delete</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No available trips have been added yet.</td></tr>
            <?php endif; ?>
        </table>
    </section>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone Number</th>
            <th>Card Number</th>
            <th>Ticket Date</th>
            <th>Ticket Time</th>
        </tr>
        <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $database = "ticket_system";

            $conn = new mysqli($servername, $username, $password, $database);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $sql = "SELECT id, name, phone_number, card_number, ticket_date, ticket_time FROM tickets";
            $result = $conn->query($sql);

            if(!$result) {
                die("Query failed: " . $conn->error);
            }
            $serial_number = 1;

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $serial_number++ . "</td>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td>" . $row["phone_number"] . "</td>";
                    echo "<td>" . $row["card_number"] . "</td>";
                    echo "<td>" . $row["ticket_date"] . "</td>";
                    echo "<td>" . $row["ticket_time"] . "</td>";
                    echo "<td><a href='delete.php?id=" . $row["id"] . "'class='delete-btn'
                    onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No records found</td></tr>";
            }
            $conn->close();
        ?>
        </table>
</body>
</html>