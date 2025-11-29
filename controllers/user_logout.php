<?php

require_once __DIR__ . "/../settings/session_config.php";
session_destroy();
header("Location: ../index.php");
