<?php
// DELETE THIS FILE after confirming the database works
require_once 'config.php';

// Override content type for plain text output
header('Content-Type: text/plain');

echo "PHP: " . phpversion() . "\n";
echo "mysqli: " . (extension_loaded('mysqli') ? 'YES' : 'NO') . "\n\n";
echo "Host: " . DB_HOST . "\n";
echo "DB:   " . DB_NAME . "\n\n";

$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    echo "FAILED: " . $conn->connect_error . "\n";
} else {
    echo "SUCCESS - Connected!\n";
    $r = $conn->query("SHOW TABLES");
    echo "Tables: " . $r->num_rows . "\n";
    while ($row = $r->fetch_row()) { echo " - " . $row[0] . "\n"; }
    $conn->close();
}
