<?php

$ConfigFile = parse_ini_file(__DIR__ . '/Config.ini', true);

$server = $ConfigFile['Database']['server'];
$userdb = $ConfigFile['Database']['userdb'];
$passdb = $ConfigFile['Database']['passdb'];
$db = $ConfigFile['Database']['db'];

$conexion = mysqli_connect($server,$userdb,$passdb,$db);
