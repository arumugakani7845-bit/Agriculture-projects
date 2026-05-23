<?php
require_once __DIR__ . '/../config.php';

function connect_db($useDatabase = true) {
    if ($useDatabase) {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    } else {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);
    }
    if ($mysqli->connect_error) {
        die('Database connection failed: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}
