<?php

$host = "db";
$user = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$database = getenv("DB_NAME");

$conn = new mysqli(
    $host,
    $user,
    $password,
    $database
);

if ($conn->connect_error) {
    die("Erro na ligação: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>