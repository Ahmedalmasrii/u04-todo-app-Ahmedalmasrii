<?php
// Startar en ny session eller forsätter med den befintliga sessionen
session_start();

// Inkluderar filen med anslutningsdetaljer till  själva databasen
include "connect.php";

// Denna funktion är för att registrera en ny användare i databasen
function registerUser($conn, $name, $email, $password)
{
    // Krypterar användarens lösenordet med bcrypt
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Förbereder ett SQL-uttalande för att infoga användardata i tabellen 'users'
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashedPassword);
    $stmt->execute();
}
