<?php
date_default_timezone_set('America/New_York');

$host = "192.168.1.160";
$dbname = "lajna_db";
$username = "root";
$password = "casaos";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      // Set MySQL session timezone after connection is created
    $db->exec("SET time_zone = '-04:00'");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}