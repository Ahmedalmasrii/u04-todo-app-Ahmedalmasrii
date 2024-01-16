<?php
// Startar  en sessionshantering för att spåra användarsessioner som är aktiva
session_start();

//  kopplar till connect.php fil som innehåller anslutningsformulär till själva databasen 
include "connect.php";

//Detta är default värden för name och email 
$name = "Guest";
$email = "N/A";

//  denna kod kontrollerar om användar-ID är satt i sessionen som är aktiv 
if (isset($_SESSION['userId'])) {
    // Hämtar användar-ID från sessionen
    $userId = $_SESSION['userId'];

    //  förbereda sql fråga för att hämta användarinformation från databasen baserat på användar-id 
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId); // Binda användar-ID till förberedd fråga
    $stmt->execute(); // Utför SQL-frågan
    $result = $stmt->get_result(); // Hämta resultatet av frågan

    // Kontrollerar om det finns några rader i resultatet
    if ($result->num_rows > 0) {
        // Hämta användarinformation från resultatet
        $user = $result->fetch_assoc();
        $name = $user['name'];
        $email = $user['email'];
    } else {
        // Om ingen användare hittades i databasen, ska  användaren meddelas och avsluta sessionen
        echo "Användaren hittades inte i databasen.";
        unset($_SESSION['userId']);
    }

    // Stänger förberedda fråga
    $stmt->close();
} else {
    // Om användar-ID inte är satt i sessionen, meddela användaren att idet inte är satt i session
    echo "Användar-ID är inte satt i sessionen.";
}