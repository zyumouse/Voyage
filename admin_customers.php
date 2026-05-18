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
    <title>Admin - Customers</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        .auth-page {
            min-height: calc(100vh - 84px);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 50px 20px 40px;
        }
        .auth-card {
            width: min(1400px, 94%);
            max-width: 1400px;
            padding: 32px 28px;
        }
        .auth-subtitle {
            margin: 0 auto 24px;
            max-width: 760px;
            color: #4b4f7d;
        }
        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            margin-top: 18px;
            padding: 0 16px;
        }
        .admin-customers-table {
            width: 100%;
            max-width: calc(100% - 32px);
            border-collapse: collapse;
            margin: 0 auto;
            font-size: 0.97rem;
        }
        .admin-customers-table th,
        .admin-customers-table td {
            padding: 14px 16px;
        }
        .admin-customers-table th {
            font-size: 1rem;
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
            <h1 class="auth-title">Admin - Customers</h1>
            <p class="auth-subtitle">Review and manage customer bookings from the admin dashboard.</p>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "ticket_system";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, user_id, name, phone_number, card_number, ticket_date, ticket_time, estimated_arrival_time FROM tickets";
$result = $conn->query($sql);

if(!$result) {
    die("Query failed: " . $conn->error);
}
?>
            <div class="table-wrapper">
            <table class="admin-customers-table">
    <tr>
        <th>Booking ID</th>
        <th>Account ID</th>
        <th>Name</th>
        <th>Phone Number</th>
        <th>Card Number</th>
        <th>Ticket Date</th>
        <th>Departure</th>
        <th>Estimated Arrival</th>
        <th>Actions</th>
    </tr>
    <?php
    $serial_number = 1;
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $serial_number++ . "</td>";
            echo "<td>" . htmlspecialchars($row["user_id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["phone_number"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["card_number"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["ticket_date"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["ticket_time"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["estimated_arrival_time"]) . "</td>";
            echo "<td><a href='delete.php?id=" . $row["id"] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</a></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='9'>No records found</td></tr>";
    }
    $conn->close();
    ?>
            </table>
            </div>
        </div>
    </div>

</body>
</html>
