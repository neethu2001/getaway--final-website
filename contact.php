<?php

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Only POST requests are accepted.'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    $input = $_POST;
}

$name = trim(strip_tags((string)($input['name'] ?? '')));
$email = trim((string)($input['email'] ?? ''));
$phone = trim(strip_tags((string)($input['phone'] ?? '')));
$date = trim(strip_tags((string)($input['date'] ?? '')));
$message = trim(strip_tags((string)($input['message'] ?? '')));

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Please provide a valid name and email address.'
    ]);
    exit;
}

$recipient = 'reservations@getawaybeachresort.com';
$subject = 'New website enquiry from ' . preg_replace('/[\r\n]+/', ' ', $name);
$body = "A new enquiry was submitted through the Getaway Beach Resort website.\n\n"
    . "Name: {$name}\n"
    . "Email: {$email}\n"
    . "Phone: {$phone}\n"
    . "Preferred date: {$date}\n"
    . "Message: {$message}\n";

$headers = "From: Getaway Beach Resort Website <noreply@getawaybeachresort.com>\r\n"
    . "Reply-To: {$email}\r\n"
    . "X-Mailer: PHP/" . PHP_VERSION;

if (mail($recipient, $subject, $body, $headers)) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your enquiry. Our team will contact you shortly.'
    ]);
    exit;
}

http_response_code(500);
echo json_encode([
    'success' => false,
    'message' => 'Unable to send your enquiry right now. Please try again shortly.'
]);

?>
