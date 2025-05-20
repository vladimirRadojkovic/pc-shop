<?php
// Start the session if it hasn't been started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include configuration
require_once 'config/config.php';

// Run database setup if needed
require_once 'setup.php';

// Define utility functions

/**
 * Redirect to another page
 * @param string $page The page to redirect to (default: home)
 */
function redirect($page = 'home') {
    header("Location: index.php?page=$page");
    exit();
}

/**
 * Set an alert message in the session
 * @param string $type The type of alert (success, danger, warning, info)
 * @param string $message The message to display
 */
function setAlert($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
} 