<?php
/**
 * Icon Computers - Direct Email Handler
 * This handles the contact form without opening an email client.
 */

// Set the response type to JSON for the JavaScript 'fetch' call
header('Content-Type: application/json');

// Prevent direct browser access to this script
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Direct access not allowed."]);
    exit;
}

// 1. Destination Address
$to = "chris@theicon.ca";

// 2. Capture, Sanitize and Validate Data
$name = isset($_POST['name']) ? strip_tags(trim($_POST["name"])) : "Anonymous";
$email = isset($_POST['email']) ? filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL) : "";
$message = isset($_POST['message']) ? trim($_POST["message"]) : "";

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($message)) {
    echo json_encode(["success" => false, "message" => "Please provide a valid email and message."]);
    exit;
}

// 3. Email Headers
$subject = "New Bench Inquiry from $name";

// Important: 'From' should be an address at your domain to avoid being flagged as spam.
// 'Reply-To' ensures that when Chris hits reply, it goes to the customer.
$headers = "From: shop-contact@theicon.ca\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// 4. Construct the Email Body
$body = "New message received from Icon Computers website:\n\n";
$body .= "Name: $name\n";
$body .= "Email: $email\n\n";
$body .= "Message Details:\n$message\n";

// 5. Send the email using the server's mail system
if (mail($to, $subject, $body, $headers)) {
    echo json_encode(["success" => true]);
} else {
    // If this fails, the server's PHP mail function is likely not configured
    echo json_encode(["success" => false, "message" => "Server error. Please call (905) 467-2249 directly."]);
}
?>
