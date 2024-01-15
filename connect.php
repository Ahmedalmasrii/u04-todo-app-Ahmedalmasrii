<?php
$host = 'db';  // Detta bör matcha tjänstens namn som definierats i min Docker Compose-fil.
$database = 'mariadb';
$user = 'mariadb';
$password = 'mariadb';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
