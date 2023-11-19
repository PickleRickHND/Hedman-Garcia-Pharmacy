<?php

$ConfigFile = parse_ini_file(__DIR__ . '/config.ini', true);

$server = $ConfigFile['Database']['server'];
$user_db = $ConfigFile['Database']['user_db'];
$password_db = $ConfigFile['Database']['password_db'];
$db = $ConfigFile['Database']['db'];

$connection = mysqli_connect($server,$user_db,$password_db,$db);
