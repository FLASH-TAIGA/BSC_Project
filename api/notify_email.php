<?php
// Flash Learning — Email Notification Helper
// Uses PHP's built-in mail() function (works on AwardSpace)
// Called internally by other API files — not a public endpoint

function sendEmail($to, $subject, $body) {
    $from    = 'noreply@flashlearning.atwebpages.com';
    $headers = "From: Flash Learning <$from>\r\n";
    $headers .= "Reply-To: support@flashlearn.edu\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "MIME-Version: 1.0\r\n";

    $html = '<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>
  body{font-family:Arial,sans-serif;background:#f4f6f9;margin:0;padding:20px;}
  .box{background:#fff;border-radius:12px;padding:30px;max-width:560px;margin:0 auto;box-shadow:0 4px 15px rgba(0,0,0,0.08);}
  .header{background:linear-gradient(135deg,#1a2a4b,#2d6a4f);color:#fff;border-radius:8px;padding:20px;text-align:center;margin-bottom:24px;}
  .header h2{margin:0;font-size:1.4rem;}
  .content{color:#333;font-size:0.95rem;line-height:1.6;}
  .footer{text-align:center;color:#aaa;font-size:0.8rem;margin-top:24px;border-top:1px solid #eee;padding-top:16px;}
  .btn{display:inline-block;background:#4CAF50;color:#fff;padding:10px 24px;border-radius:50px;text-decoration:none;font-weight:bold;margin-top:16px;}
</style></head>
<body><div class="box">
  <div class="header"><h2>~FLASH LEARNING~</h2></div>
  <div class="content">' . $body . '</div>
  <div class="footer">Flash Learning Platform &mdash; <a href="https://flashlearning.atwebpages.com">flashlearning.atwebpages.com</a><br>
  This is an automated notification. Do not reply to this email.</div>
</div></body></html>';

    return @mail($to, $subject, $html, $headers);
}

// ── Notification templates ────────────────────────────────

function emailSessionBooked($tutorEmail, $tutorName, $studentName, $subject, $date, $time) {
    $body = "<p>Hi <strong>$tutorName</strong>,</p>
             <p>A new tutoring session has been booked with you:</p>
             <ul>
               <li><strong>Student:</strong> $studentName</li>
               <li><strong>Subject:</strong> $subject</li>
               <li><strong>Date:</strong> $date at $time</li>
             </ul>
             <p>Please log in to confirm or update the session status.</p>
             <a href='https://flashlearning.atwebpages.com/dashboard-tutor.html' class='btn'>View Dashboard</a>";
    return sendEmail($tutorEmail, "New Session Booked — $subject", $body);
}

function emailSessionUpdated($studentEmail, $studentName, $status, $subject, $date) {
    $statusText = strtoupper($status);
    $body = "<p>Hi <strong>$studentName</strong>,</p>
             <p>Your tutoring session status has been updated:</p>
             <ul>
               <li><strong>Subject:</strong> $subject</li>
               <li><strong>Date:</strong> $date</li>
               <li><strong>New Status:</strong> <strong>$statusText</strong></li>
             </ul>
             <a href='https://flashlearning.atwebpages.com/dashboard-student.html' class='btn'>View My Sessions</a>";
    return sendEmail($studentEmail, "Session $statusText — $subject", $body);
}

function emailBroadcast($toEmail, $toName, $title, $message) {
    $body = "<p>Hi <strong>$toName</strong>,</p>
             <p>You have a new announcement from Flash Learning:</p>
             <div style='background:#f8f9fa;border-left:4px solid #4CAF50;padding:16px;border-radius:4px;margin:16px 0;'>
               <strong>$title</strong><br><br>$message
             </div>
             <a href='https://flashlearning.atwebpages.com' class='btn'>Visit Platform</a>";
    return sendEmail($toEmail, "Announcement: $title", $body);
}

function emailContactReceived($adminEmail, $senderName, $senderEmail, $subject, $message) {
    $body = "<p>A new contact message has been received:</p>
             <ul>
               <li><strong>From:</strong> $senderName ($senderEmail)</li>
               <li><strong>Subject:</strong> $subject</li>
             </ul>
             <div style='background:#f8f9fa;border-left:4px solid #e94560;padding:16px;border-radius:4px;margin:16px 0;'>
               $message
             </div>
             <a href='https://flashlearning.atwebpages.com/dashboard-admin.html' class='btn'>View in Dashboard</a>";
    return sendEmail($adminEmail, "New Contact Message: $subject", $body);
}

function emailNewMessage($receiverEmail, $receiverName, $senderName, $preview) {
    $body = "<p>Hi <strong>$receiverName</strong>,</p>
             <p>You have a new message from <strong>$senderName</strong>:</p>
             <div style='background:#f8f9fa;border-left:4px solid #4CAF50;padding:16px;border-radius:4px;margin:16px 0;'>
               " . htmlspecialchars(substr($preview, 0, 200)) . "...
             </div>
             <a href='https://flashlearning.atwebpages.com' class='btn'>Reply Now</a>";
    return sendEmail($receiverEmail, "New message from $senderName", $body);
}
