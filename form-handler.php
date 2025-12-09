<?php
// form-handler.php

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Simple sanitiser
function clean_input($value)
{
    return htmlspecialchars(trim(stripslashes($value)), ENT_QUOTES, 'UTF-8');
}

// Collect fields from the form
$full_name = clean_input($_POST['full_name'] ?? '');
$phone     = clean_input($_POST['phone'] ?? '');
$email     = clean_input($_POST['email'] ?? '');
$location  = clean_input($_POST['location'] ?? '');
$equipment = clean_input($_POST['equipment'] ?? '');
$message   = clean_input($_POST['message'] ?? '');

// Basic validation
$errors = [];

if (empty($full_name)) {
    $errors[] = 'Full Name is required.';
}

if (empty($phone)) {
    $errors[] = 'Contact Number is required.';
}

// Optional: email format check if provided
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

// If there are validation errors, show a simple message and stop
if (!empty($errors)) {
    echo "<h2>There were some issues with your submission:</h2>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>" . $error . "</li>";
    }
    echo "</ul>";
    echo '<p><a href="javascript:history.back()">Go back to the form</a></p>';
    exit;
}

// Email settings
$to      = 'info@manliftbangalore.com'; // Destination email
$subject = 'New Enquiry - Manlift Bangalore';

// Build email body
$body  = "You have received a new enquiry from the Manlift Bangalore website form.\n\n";
$body .= "Full Name: {$full_name}\n";
$body .= "Contact Number: {$phone}\n";
$body .= "Email Address: " . ($email ?: 'Not provided') . "\n";
$body .= "Area / Location: " . ($location ?: 'Not provided') . "\n";
$body .= "Equipment Selected: " . ($equipment ?: 'Not specified') . "\n\n";
$body .= "Message / Requirement Details:\n";
$body .= ($message ?: 'No additional details provided.') . "\n";

// Headers
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/plain; charset=UTF-8\r\n";

// From / Reply-To
$from_email = !empty($email) ? $email : 'no-reply@manliftbangalore.com';
$headers   .= "From: Manlift Bangalore Enquiry <{$from_email}>\r\n";
if (!empty($email)) {
    $headers .= "Reply-To: {$email}\r\n";
}

// Send the email
$sent = mail($to, $subject, $body, $headers);

// Redirect based on result
if ($sent) {
    header('Location: thankyou.html');
    exit;
} else {
    echo "<h2>Oops, something went wrong while sending your enquiry.</h2>";
    echo "<p>Please try again in a few minutes, or contact us directly:</p>";
    echo "<p>Phone: +91 72000 45180<br>Email: info@manliftbangalore.com</p>";
    echo '<p><a href="form.html">Back to the enquiry form</a></p>';
    exit;
}
