<?php
// GET  /api/users.php         — list all users (admin only)
// POST /api/users.php         — delete a user  (admin only)
require_once 'config.php';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $db->query(
        'SELECT u.id, u.name, u.email, u.role, u.created_at,
                sp.photo_filename AS student_photo,
                tp.photo_filename AS tutor_photo
         FROM users u
         LEFT JOIN student_profiles sp ON u.id = sp.user_id AND u.role = "student"
         LEFT JOIN tutor_profiles   tp ON u.id = tp.user_id AND u.role = "tutor"
         ORDER BY u.created_at DESC'
    );
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $row['photo_filename'] = $row['tutor_photo'] ?: $row['student_photo'] ?: null;
        unset($row['student_photo'], $row['tutor_photo']);
        $users[] = $row;
    }
    $db->close();
    echo json_encode(['success' => true, 'users' => $users]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data  = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';

    if ($action === 'delete') {
        $id   = intval($data['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
        $stmt->bind_param('i', $id);
        $ok   = $stmt->execute();
        $stmt->close();
        $db->close();
        echo json_encode(['success' => $ok]);
    } else {
        $db->close();
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }
}
