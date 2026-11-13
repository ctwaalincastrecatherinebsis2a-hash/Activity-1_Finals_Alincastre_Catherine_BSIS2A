
<?php

require_once 'includes/functions.php';

// clear session data
$_SESSION = [];

// destroy session
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// redirect to login page
header('Location: login.php');
exit;
