<?php include 'header.php';
// Startar en sessionshantering för att spåra användarsessioner som är aktiva
session_start();

// Kopplar till connect.php fil som innehåller anslutningsinformation till själva databasen
include "connect.php"; // Denna fil ska definiera $pdo för PDO-anslutningen

// Loggaut-logik
if (isset($_POST['logout'])) {
    // Förstör sessionen för att logga ut användaren
    session_unset(); // Tar bort alla sessionvariabler
    session_destroy(); // Förstör sessionen

    // Omdirigera användaren till inloggningssidan eller hemsidan
    header("Location: login.php"); // Ändra 'login.php' till rätt sökväg för din inloggningssida
    exit();
}

// Detta är standardvärden för name och email
$name = "Guest";
$email = "N/A";

// Denna kod kontrollerar om användar-ID är satt i den aktiva sessionen
if (isset($_SESSION['userId'])) {
    // Hämtar användar-ID från sessionen
    $userId = $_SESSION['userId'];

    try {
        // Förbereder SQL-fråga för att hämta användarinformation från databasen baserat på användar-id
        $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        // Kontrollerar om det finns någon rad i resultatet
        if ($user) {
            // Hämta användarinformation från resultatet
            $name = $user['name'];
            $email = $user['email'];
        } else {
            // Om ingen användare hittades i databasen, ska användaren meddelas och avsluta sessionen
            echo "Användaren hittades inte i databasen.";
            unset($_SESSION['userId']);
        }
    } catch (PDOException $e) {
        echo "Ett fel uppstod vid databasförfrågan: " . $e->getMessage();
    }
} else {
    // Om användar-ID inte är satt i sessionen, meddela användaren att idet inte är satt i session
    echo "Användar-ID är inte satt i sessionen.";
}
$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style.css">
    <title>Välkomstsida</title>
</head>
<body>
    <h1 class="page-title">Välkommen <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></h1>
    <p class="epost">Din e-postadress är: <?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?></p>
    <a href="uppgiftsidan.php" class="button-link">
        <button class="button">Klicka här för att se alla uppgifter</button>
    </a>
</body>
</html>
