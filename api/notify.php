<?php
// Check if this file is being included (not accessed directly)
$is_included = (basename($_SERVER['PHP_SELF']) !== 'notify.php');

if (!$is_included) {
    // Only set headers and start buffering if accessed directly as API
    // Define API context to prevent session start in config
    if (!defined('API_CONTEXT')) {
        define('API_CONTEXT', true);
    }
    
    // Disable error display for clean JSON output
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    
    // Start output buffering
    ob_start();
    
    // Suppress any warnings/notices
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        error_log("PHP Error: $errstr in $errfile on line $errline");
        return true;
    }, E_WARNING | E_NOTICE | E_DEPRECATED);
    
    // Require config but suppress any output
    ob_start();
    require_once '../config.php';
    $config_output = ob_get_clean();
    if (!empty($config_output)) {
        error_log("Config output in notify.php: " . substr($config_output, 0, 200));
    }
    
    header('Content-Type: application/json; charset=utf-8');
} else {
    // If included, just require config without headers
    if (!defined('API_CONTEXT')) {
        define('API_CONTEXT', true);
    }
    require_once '../config.php';
}

// Function to send email notification
function sendEmailNotification($to, $subject, $message) {
    global $koneksi;
    
    // Check if email notification is enabled
    $result = $koneksi->query("SELECT setting_value FROM settings WHERE setting_key = 'email_notification'");
    if (!$result) {
        return false;
    }
    $setting = $result->fetch_assoc();
    
    if ($setting && $setting['setting_value'] != '1') {
        return false; // Email notification disabled
    }
    
    // Simple mail function (for production, use PHPMailer or similar)
    $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Function to create notification
function createNotification($host_id, $visitor_id, $type, $title, $message) {
    global $koneksi;
    
    $host_id_sql = $host_id ? intval($host_id) : 'NULL';
    $visitor_id_sql = $visitor_id ? intval($visitor_id) : 'NULL';
    $type_esc = esc($type);
    $title_esc = esc($title);
    $message_esc = esc($message);
    
    $koneksi->query("INSERT INTO notifications (host_id, visitor_id, type, title, message) 
                     VALUES ($host_id_sql, $visitor_id_sql, '$type_esc', '$title_esc', '$message_esc')");
    
    $notification_id = $koneksi->insert_id;
    
    // Send email if host_id is provided
    if ($host_id) {
        $host = $koneksi->query("SELECT email FROM hosts WHERE id = $host_id")->fetch_assoc();
        if ($host && $host['email']) {
            $email_sent = sendEmailNotification($host['email'], $title, $message);
            if ($email_sent) {
                $koneksi->query("UPDATE notifications SET email_sent = 1 WHERE id = $notification_id");
            }
        }
    }
    
    return $notification_id;
}

// API endpoint to get notifications (only if accessed directly, not included)
if (!$is_included && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $host_id = intval($_GET['host_id'] ?? 0);
    $status = $_GET['status'] ?? 'unread';
    
    $query = "SELECT * FROM notifications WHERE 1=1";
    
    if ($host_id > 0) {
        $query .= " AND host_id = $host_id";
    } else {
        $query .= " AND host_id IS NULL"; // Admin notifications
    }
    
    if ($status != 'all') {
        $status_esc = esc($status);
        $query .= " AND status = '$status_esc'";
    }
    
    $query .= " ORDER BY created_at DESC LIMIT 50";
    
    $result = $koneksi->query($query);
    $notifications = [];
    
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'data' => $notifications,
        'count' => count($notifications)
    ], JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}

// API endpoint to mark notification as read (only if accessed directly, not included)
if (!$is_included && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $id = intval($_POST['id'] ?? 0);
    
    if ($id > 0) {
        $koneksi->query("UPDATE notifications SET status = 'read' WHERE id = $id");
        ob_clean();
        echo json_encode(['success' => true, 'message' => 'Notification marked as read'], JSON_UNESCAPED_UNICODE);
        ob_end_flush();
        exit;
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid notification ID'], JSON_UNESCAPED_UNICODE);
        ob_end_flush();
        exit;
    }
}

// Only output if accessed directly as API endpoint
if (!$is_included) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request'], JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}
// If included, just return (don't output anything)
