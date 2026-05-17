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
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <h1>Admin Panel</h1>
    <a href="index.php">Back</a>
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