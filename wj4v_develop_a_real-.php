<?php
// wj4v_develop_a_real-.php

// Configuration
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'real_time_tracker';

// Connect to database
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists
$sql = "CREATE TABLE IF NOT EXISTS tracker (
    id INT AUTO_INCREMENT,
    url VARCHAR(255) NOT NULL,
    visitor_ip VARCHAR(50) NOT NULL,
    visitor_country VARCHAR(100) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
)";
$conn->query($sql);

// Function to get visitor's country
function get_visitor_country($ip) {
    $ch = curl_init('http://ip-api.com/json/' . $ip);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $response['country'];
}

// Track visitor
if (isset($_GET['url'])) {
    $url = $_GET['url'];
    $visitor_ip = $_SERVER['REMOTE_ADDR'];
    $visitor_country = get_visitor_country($visitor_ip);
    $sql = "INSERT INTO tracker (url, visitor_ip, visitor_country) VALUES ('$url', '$visitor_ip', '$visitor_country')";
    $conn->query($sql);
}

// Real-time tracking
if (isset($_GET['track'])) {
    $sql = "SELECT * FROM tracker ORDER BY timestamp DESC";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        echo $row['url'] . ' - ' . $row['visitor_ip'] . ' - ' . $row['visitor_country'] . ' - ' . $row['timestamp'] . '<br>';
    }
}

// Close database connection
$conn->close();
?>