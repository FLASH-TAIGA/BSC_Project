<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST required']);
    exit();
}

$data     = json_decode(file_get_contents('php://input'), true);
$email    = trim($data['email']    ?? '');
$password = trim($data['password'] ?? '');

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit();
}

// Check hardcoded admin
if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
    echo json_encode([
        'success' => true,
        'user'    => ['id' => 0, 'name' => 'Administrator', 'email' => $email, 'role' => 'admin']
    ]);
    exit();
}

// Check database users
$db   = getDB();
$stmt = $db->prepare('SELECT id, name, email, password, role FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();
$db->close();

if ($user && password_verify($password, $user['password'])) {
    unset($user['password']); // never send hash to client
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
}
