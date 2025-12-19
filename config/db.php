<?php
$host = 'localhost';
$db = 'thesis_system';
$user = 'root';
$pass = '';
$options = [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, $options);
} catch (Exception $e) {
die('DB Connection Failed');
}