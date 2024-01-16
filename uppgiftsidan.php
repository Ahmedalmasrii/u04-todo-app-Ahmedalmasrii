<?php
//  anslutningsfilen för att koppla upp mot databasen
include "connect.php";

// Funktion för att lägga till en ny uppgift
function addTask($conn, $task)
{
    $stmt = $conn->prepare("INSERT INTO todo (attgora) VALUES (?)");
    $stmt->bind_param("s", $task);

    // Kolla om uppgiften har lagts till framgångsrikt, annars visa ett felmeddelande
    if ($stmt->execute()) {
        $message = "Du har nu lagt till en ny uppgift att göra!";
        echo "<div class='success-message'>$message</div>";
    } else {
        $errorMessage = "Oops! Något gick fel när du försökte lägga till uppgiften: " . $stmt->error;
        echo "<div class='error-message'>$errorMessage</div>";
    }
}