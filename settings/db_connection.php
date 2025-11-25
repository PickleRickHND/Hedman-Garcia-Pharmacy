<?php

$configPath = __DIR__ . '/config.ini';

// Check if config file exists
if (!file_exists($configPath)) {
    die("Error: Configuration file not found. Please create settings/config.ini");
}

$ConfigFile = parse_ini_file($configPath, true);

// Validate config file structure
if (!isset($ConfigFile['Database'])) {
    die("Error: Database configuration section not found in config.ini");
}

$server = $ConfigFile['Database']['server'] ?? 'localhost';
$user_db = $ConfigFile['Database']['user_db'] ?? '';
$password_db = $ConfigFile['Database']['password_db'] ?? '';
$db = $ConfigFile['Database']['db'] ?? '';

$connection = mysqli_connect($server, $user_db, $password_db, $db);

// Check connection
if (!$connection) {
    die("Error: Database connection failed. Please check your configuration. " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($connection, "utf8mb4");
