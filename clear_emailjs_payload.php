<?php
require_once 'includes/auth.php';
// Simple endpoint to clear pending emailjs payload from session
if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();
if (isset($_SESSION['emailjs_payload']))
    unset($_SESSION['emailjs_payload']);
if (isset($_SESSION['booking_notification']))
    unset($_SESSION['booking_notification']);
http_response_code(204);
exit();
