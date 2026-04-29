<?php
// Temporary diagnostic file - DELETE after fixing
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain');

echo "PHP version: " . phpversion() . "\n";
echo "mysqli available: " . (extension_loaded('mysqli') ? 'YES' : 'NO') . "\n";
echo "PDO available: " . (extension_loaded('pdo_mysql') ? 'YES' : 'NO') . "\n\n";

// Use Railway environment variables
$host = getenv('MYSQLHOST')     ?: 'localhost';
$user = getenv('MYSQLUSER')     ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: '';
$db   = getenv('MYSQLDATABASE') ?: 'flashlearn';
$port = (int)(getenv('MYSQLPORT') ?: 3306);

echo "Host: $host\n";
echo "Port: $port\n";
echo "User: $user\n";
echo "DB:   $db\n\n";

echo "Attempting connection...\n";
$conn = @new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    echo "FAILED: " . $conn->connect_error . "\n";
    echo "Error code: " . $conn->connect_errno . "\n";
} else {
    echo "SUCCESS - Connected to database!\n";
    $r = $conn->query("SHOW TABLES");
    echo "Tables found: " . $r->num_rows . "\n";
    while ($row = $r->fetch_row()) {
        echo " - " . $row[0] . "\n";
    }
    $conn->close();
}
