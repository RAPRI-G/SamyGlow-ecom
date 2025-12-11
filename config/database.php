<?php
$host = 'localhost';
$dbname = 'vjetvzgr_samyglow';
$user = 'vjetvzgr_samyglow';
$pass = 'samyglow.qatunas.com/';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
