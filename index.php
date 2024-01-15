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

// Funktion för att kontrollera och logga in en användare
function loginUser($conn, $email, $password)
{
    // Förbereder ett SQL-uttalande för att hämta användar-ID och krypterat lösenord baserat på e-post som har matats in vid skapandet
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Kontrollerar om  användare med den angivna e-postadressen finns i databasen
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $hashedPassword);
        $stmt->fetch();

        // Verifierar det angivna lösenordet mot det krypterade lösenordet
        if (password_verify($password, $hashedPassword)) {
            return $userId; // Returnerar användar-ID om inloggningen lyckas
        }
    }

    return false; // Returnerar false om inloggningen misslyckas
}
