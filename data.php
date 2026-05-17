<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "ticket_system";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $name = $_POST['name'];
    $phone_number = $_POST['IC_number'];
    $address = $_POST['address'];
    $card_number = $_POST['card_number'];
    $ticket_date = $_POST['ticket_date'];
    $ticket_time = $_POST['ticket_time'];
    $travelorigin = $_POST['travelorigin'];
    $destination = $_POST['destination'];

    $sql = "INSERT INTO tickets (name, phone_number, card_number, ticket_date, ticket_time) 
    VALUES ('$name', '$phone_number', '$card_number', '$ticket_date', '$ticket_time')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
?>