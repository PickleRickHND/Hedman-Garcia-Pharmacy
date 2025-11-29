<?php
/**
 * Secure Session Configuration
 * Include this file at the very beginning of any file that needs sessions
 * This MUST be called BEFORE session_start()
 */

// Only configure if session hasn't started yet
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session cookie parameters
    $cookieParams = [
        'lifetime' => 0,           // Session cookie (expires when browser closes)
        'path' => '/',             // Available across entire domain
        'domain' => '',            // Current domain only
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // Only over HTTPS if available
        'httponly' => true,        // Prevent JavaScript access (XSS protection)
        'samesite' => 'Strict'     // CSRF protection - strict same-site policy
    ];

    session_set_cookie_params($cookieParams);

    // Use secure session name
    session_name('HGPHARM_SESSION');

    // Start the session
    session_start();

    // Regenerate session ID periodically to prevent session fixation
    if (!isset($_SESSION['_session_created'])) {
        $_SESSION['_session_created'] = time();
    } elseif (time() - $_SESSION['_session_created'] > 1800) {
        // Regenerate session ID every 30 minutes
        session_regenerate_id(true);
        $_SESSION['_session_created'] = time();
    }
}
