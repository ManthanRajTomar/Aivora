<?php
/**
 * AIVORA - AJAX Newsletter Subscription Endpoint
 * Course: Advanced Web Development in PHP (Code 2010043347)
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Support both JSON payload and standard form POST
$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true);

if (is_array($jsonData)) {
    $email = strtolower(trim($jsonData['email'] ?? ''));
} else {
    $email = strtolower(trim($_POST['email'] ?? ''));
}

// Server-side validation
if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email address is required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide a valid email address.']);
    exit;
}

try {
    $db = getDB();

    // Check if email already exists
    $checkStmt = $db->prepare("SELECT id FROM subscribers WHERE email = :email LIMIT 1");
    $checkStmt->execute([':email' => $email]);

    if ($checkStmt->fetch()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'You are already subscribed to our newsletter! Thank you.'
        ]);
        exit;
    }

    // Insert new subscriber
    $insertStmt = $db->prepare("INSERT INTO subscribers (email, subscribed_at) VALUES (:email, NOW())");
    $insertStmt->execute([':email' => $email]);

    echo json_encode([
        'status'  => 'success',
        'message' => 'Welcome to the Aivora community! You are now subscribed.'
    ]);
    exit;

} catch (PDOException $e) {
    error_log("Newsletter subscription error: " . $e->getMessage());
    echo json_encode([
        'status'  => 'error',
        'message' => 'An error occurred while subscribing. Please try again later.'
    ]);
    exit;
}
