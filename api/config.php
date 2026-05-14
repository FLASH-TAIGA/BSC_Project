<?php
// ============================================================
// Flash Learning — Database Configuration
// ============================================================
// Fill in your AwardSpace MySQL credentials below.
// Get these from: AwardSpace Control Panel → MySQL Manager
// ============================================================

define('DB_HOST', 'fdb1034.awardspace.net'); // AwardSpace MySQL host
define('DB_USER', '4758498_flashlearn');      // e.g. 1234567_flashlearn
define('DB_PASS', 'McjRocks02');      // password you set
define('DB_NAME', '4758498_flashlearn');          // e.g. 1234567_flashlearn
define('DB_PORT', 3306);

define('ADMIN_EMAIL',    'admin@flashlearn.edu');
define('ADMIN_PASSWORD', 'Admin@FL2026');

// ── CORS headers (must be before any output) ──────────────
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ── Database connection ───────────────────────────────────
function getDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode(['success' => false, 'message' => 'DB connection failed: ' . $conn->connect_error]));
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
