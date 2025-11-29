<?php

/**
 * Database Connection Configuration
 * Security: Errors are logged, not displayed to users in production
 */

// Production mode - set to false during development for debugging
$PRODUCTION_MODE = true;

// Configure error handling based on environment
if ($PRODUCTION_MODE) {
    // Production: Log errors, don't display them
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../logs/error.log');
} else {
    // Development: Display errors for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Create logs directory if it doesn't exist
$logsDir = __DIR__ . '/../logs';
if (!is_dir($logsDir)) {
    @mkdir($logsDir, 0750, true);
}

$configPath = __DIR__ . '/config.ini';

// Check if config file exists
if (!file_exists($configPath)) {
    error_log("Database configuration file not found: $configPath");
    die("Error: System configuration issue. Please contact administrator.");
}

$ConfigFile = parse_ini_file($configPath, true);

// Validate config file structure
if (!isset($ConfigFile['Database'])) {
    error_log("Database configuration section not found in config.ini");
    die("Error: System configuration issue. Please contact administrator.");
}

$server = $ConfigFile['Database']['server'] ?? 'localhost';
$user_db = $ConfigFile['Database']['user_db'] ?? '';
$password_db = $ConfigFile['Database']['password_db'] ?? '';
$db = $ConfigFile['Database']['db'] ?? '';

$connection = mysqli_connect($server, $user_db, $password_db, $db);

// Check connection
if (!$connection) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Error: Unable to connect to the database. Please try again later.");
}

// Set charset to UTF-8
mysqli_set_charset($connection, "utf8mb4");

// Set mysqli to throw exceptions for errors (better error handling)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
