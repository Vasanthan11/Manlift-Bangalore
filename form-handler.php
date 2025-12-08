<?php
// ---------------------------
// CONFIGURATION
// ---------------------------

// Where the enquiry should be sent
$toEmail   = "info@manliftbangalore.com";  // change to your real email
$siteName  = "Manlift Bangalore";          // used in email subject

// Optional: where to redirect after success
// Leave empty ("") to show the simple success message below.
$redirectURL = ""; // e.g. "thank-you.html"

// ---------------------------
// HELPER FUNCTION
// ---------------------------
function clean_input($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

// ---------------------------
// ONLY ALLOW POST
// ---------------------------
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

// ---------------------------
// COLLECT & CLEAN FIELDS
// ---------------------------
$full_name = isset($_POST['full_name']) ? clean_input($_POST['full_name']) : "";
$phone     = isset($_POST['phone'])     ? clean_input($_POST['phone'])     : "";
$email     = isset($_POST['email'])     ? clean_input($_POST['email'])     : "";
$location  = isset($_POST['location'])  ? clean_input($_POST['location'])  : "";
$equipment = isset($_POST['equipment']) ? clean_input($_POST['equipment']) : "";
$message   = isset($_POST['message'])   ? clean_input($_POST['message'])   : "";

// ---------------------------
// BASIC VALIDATION
// ---------------------------
$errors = [];

if ($full_name === "") {
    $errors[] = "Full Name is required.";
}
if ($phone === "") {
    $errors[] = "Contact Number is required.";
}

if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

// If errors, show them and stop
if (!empty($errors)) {
    http_response_code(400);
    echo "<h2>There was a problem with your submission:</h2>";
    echo "<ul>";
    foreach ($errors as $err) {
        echo "<li>" . $err . "</li>";
    }
    echo "</ul>";
    echo "<p><a href='javascript:history.back()'>Go back and fix the form</a></p>";
    exit;
}

// ---------------------------
// BUILD EMAIL CONTENT
// ---------------------------
$subject = "New lift enquiry from {$siteName} website";

$body  = "You have received a new enquiry from the website.\n\n";
$body .= "Full Name: {$full_name}\n";
$body .= "Contact Number: {$phone}\n";
$body .= "Email Address: " . ($email !== "" ? $email : "Not provided") . "\n";
$body .= "Area / Location: " . ($location !== "" ? $location : "Not provided") . "\n";
$body .= "Selected Equipment: " . ($equipment !== "" ? $equipment : "Not specified") . "\n\n";
$body .= "Request / Message:\n";
$body .= ($message !== "" ? $message : "No additional message provided.") . "\n\n";
$body .= "----\nSent from the enquiry form on {$siteName}.\n";

// ---------------------------
// EMAIL HEADERS
// ---------------------------
$headers   = "MIME-Version: 1.0\r\n";
$headers  .= "Content-type: text/plain; charset=utf-8\r\n";

// If user filled an email, use it as reply-to
if ($email !== "") {
    $headers .= "From: {$siteName} <{$toEmail}>\r\n";
    $headers .= "Reply-To: {$full_name} <{$email}>\r\n";
} else {
    $headers .= "From: {$siteName} <{$toEmail}>\r\n";
}

// ---------------------------
// SEND EMAIL
// ---------------------------
$sent = @mail($toEmail, $subject, $body, $headers);

if ($sent) {
    // If you set a redirect URL, send them there
    if (!empty($redirectURL)) {
        header("Location: " . $redirectURL);
        exit;
    }

    // Otherwise show a simple success message
    echo "<h2>Thank you, {$full_name}!</h2>";
    echo "<p>Your enquiry has been sent successfully. Our team will contact you soon.</p>";
    echo "<p><a href='index.html'>Click here to go back to the homepage</a></p>";
} else {
    http_response_code(500);
    echo "<h2>Sorry, something went wrong.</h2>";
    echo "<p>Your enquiry could not be sent. Please try again later or call us directly.</p>";
    echo "<p><a href='javascript:history.back()'>Click here to go back</a></p>";
}
