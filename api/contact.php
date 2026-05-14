<?php
// POST /api/contact.php  — save contact form message
// GET  /api/contact.php  — list all messages (admin)
// POST action=mark_read  — mark a message as read
// POST action=delete     — delete a message
require_once 'config.php';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $db->query('SELECT * FROM contact_messages ORDER BY created_at DESC');
    $rows   = $result->fetch_all(MYSQLI_ASSOC);
    $unread = $db->query('SELECT COUNT(*) FROM contact_messages WHERE is_read=0')->fetch_row()[0];
    $db->close();
    echo json_encode(['success' => true, 'messages' => $rows, 'unread' => (int)$unread]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? 'send';

    if ($action === 'send') {
        $name    = trim($data['name']    ?? '');
        $email   = trim($data['email']   ?? '');
        $subject = trim($data['subject'] ?? '');
        $message = trim($data['message'] ?? '');

        if (!$name || !$email || !$message) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            $db->close(); exit();
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
            $db->close(); exit();
        }

        $stmt = $db->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?,?,?,?)');
        $stmt->bind_param('ssss', $name, $email, $subject, $message);
        $ok = $stmt->execute();
        $stmt->close();
        $db->close();

        // Email admin
        if ($ok) {
            require_once 'notify_email.php';
            emailContactReceived(ADMIN_EMAIL, $name, $email, $subject, $message);
        }

        echo json_encode(['success' => $ok, 'message' => $ok ? 'Message sent successfully.' : 'Failed to send.']);

    } elseif ($action === 'mark_read') {
        $id   = intval($data['id'] ?? 0);
        $stmt = $db->prepare('UPDATE contact_messages SET is_read=1 WHERE id=?');
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        $db->close();
        echo json_encode(['success' => $ok]);

    } elseif ($action === 'delete') {
        $id   = intval($data['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM contact_messages WHERE id=?');
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        $db->close();
        echo json_encode(['success' => $ok]);

    } elseif ($action === 'mark_all_read') {
        $db->query('UPDATE contact_messages SET is_read=1');
        $db->close();
        echo json_encode(['success' => true]);
    }
}
