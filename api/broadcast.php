<?php
// GET  /api/broadcast.php               — get all broadcasts (for users)
// GET  /api/broadcast.php?role=student  — get broadcasts for a specific role
// POST /api/broadcast.php               — send a broadcast (admin only)
// POST action=delete                    — delete a broadcast (admin only)
require_once 'config.php';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $role = trim($_GET['role'] ?? 'all');
    if ($role !== 'all') {
        $stmt = $db->prepare(
            "SELECT * FROM broadcasts WHERE target_role='all' OR target_role=?
             ORDER BY created_at DESC LIMIT 20"
        );
        $stmt->bind_param('s', $role);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $result = $db->query('SELECT * FROM broadcasts ORDER BY created_at DESC LIMIT 50');
        $rows   = $result->fetch_all(MYSQLI_ASSOC);
    }
    $db->close();
    echo json_encode(['success' => true, 'broadcasts' => $rows]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data   = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? 'send';

    if ($action === 'send') {
        $title   = trim($data['title']       ?? '');
        $message = trim($data['message']     ?? '');
        $target  = $data['target_role']      ?? 'all';
        $sentBy  = trim($data['sent_by']     ?? 'Administrator');

        if (!$title || !$message) {
            echo json_encode(['success' => false, 'message' => 'Title and message are required.']);
            $db->close(); exit();
        }
        if (!in_array($target, ['all','student','tutor'])) $target = 'all';

        $stmt = $db->prepare('INSERT INTO broadcasts (title, message, target_role, sent_by) VALUES (?,?,?,?)');
        $stmt->bind_param('ssss', $title, $message, $target, $sentBy);
        $ok = $stmt->execute();
        $stmt->close();

        // Email all matching users
        if ($ok) {
            require_once 'notify_email.php';
            if ($target === 'all') {
                $users = $db->query("SELECT name, email FROM users")->fetch_all(MYSQLI_ASSOC);
            } else {
                $stmt2 = $db->prepare("SELECT name, email FROM users WHERE role=?");
                $stmt2->bind_param('s', $target);
                $stmt2->execute();
                $users = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt2->close();
            }
            foreach ($users as $u) {
                emailBroadcast($u['email'], $u['name'], $title, $message);
            }
        }

        $db->close();
        echo json_encode(['success' => $ok]);

    } elseif ($action === 'delete') {
        $id   = intval($data['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM broadcasts WHERE id=?');
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        $db->close();
        echo json_encode(['success' => $ok]);
    }
}
