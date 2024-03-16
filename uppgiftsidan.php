<?php include 'header.php';
// Inkluderar filen för anslutning till databasen som använder PDO
include "connect.php";

// Loggaut-logik
if (isset($_POST['logout'])) {
    // Förstör sessionen för att logga ut användaren
    session_unset(); // Tar bort alla sessionvariabler
    session_destroy(); // Förstör sessionen

    // Omdirigera användaren till inloggningssidan eller hemsidan
    header("Location: login.php"); // Ändra 'login.php' till rätt sökväg för din inloggningssida
    exit();
}
// Funktion för att lägga till en ny uppgift
function addTask($pdo, $task) {
    try {
        $stmt = $pdo->prepare("INSERT INTO todo (attgora) VALUES (:task)");
        $stmt->execute([':task' => $task]);
        echo "<div class='success-message'>Du har nu lagt till en ny uppgift att göra!</div>";
    } catch (PDOException $e) {
        echo "<div class='error-message'>Oops! Något gick fel när du försökte lägga till uppgiften: " . $e->getMessage() . "</div>";
    }
}

// Funktion för att markera en uppgift som klar
function completeTask($pdo, $id)
{
    try {
        $stmt = $pdo->prepare("UPDATE todo SET klar = 1 WHERE ID = :id");
        $stmt->execute([':id' => $id]);
        echo "<div class='success-message'>Bra jobbat! Uppgiften är nu klar!</div>";
    } catch (PDOException $e) {
        echo "<div class='error-message'>Hoppsan! Något gick fel när du försökte markera uppgiften som klar: " . $e->getMessage() . "</div>";
    }
}

// Funktion för att uppdatera en befintlig uppgift
function updateTask($pdo, $id, $newTask)
{
    try {
        // Använd placeholders i din SQL-fråga
        $stmt = $pdo->prepare("UPDATE todo SET attgora = :newTask WHERE ID = :id");
        
        // Bind parametrar med associerade nycklar och värden
        $stmt->bindParam(':newTask', $newTask);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Exekvera den förberedda frågan
        $stmt->execute();

        echo "<div class='update-message'>Uppgiften är nu uppdaterad!</div>";
    } catch (PDOException $e) {
        echo "<div class='error-message'>Oj då! Något gick fel när du försökte uppdatera uppgiften: " . $e->getMessage() . "</div>";
    }
}
// Funktion för att ta bort en uppgift
function deleteTask($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM todo WHERE ID = :id");
        $stmt->execute([':id' => $id]);
        echo "<div class='success-message'>Borttagning lyckad! Uppgiften är nu borttagen!</div>";
    } catch (PDOException $e) {
        echo "<div class='error-message'>Oops! Något gick fel när du försökte ta bort uppgiften: " . $e->getMessage() . "</div>";
    }
}
// Funktion för att hämta alla uppgifter från databasen
function getTasks($pdo)
{
    $stmt = $pdo->query("SELECT * FROM todo");
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

// Hantera formulärinskick
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lägga till en ny uppgift
    if (isset($_POST['addTask'])) {
        $newTask = $_POST['newTask'];
        addTask($pdo, $newTask);
    }

    // **Visa och hantera alla uppgifter:**
    // Om användaren har skickat in formuläret för att visa alla uppgifter (`showAllTasks`-knappen har tryckts),
    // hämtas alla uppgifter från databasen. Varje uppgift presenteras med dess namn, status, datum och en bild
    // som visar om den är klar eller ej. För varje uppgift finns även möjlighet att markera som slutförd,
    // uppdatera eller radera den genom ett formulär. All information visas i en HTML-container med klassen `task-container`.
    if (isset($_POST['showAllTasks'])) {
        $tasks = getTasks($pdo);
        foreach ($tasks as $task) {
            echo "<div class='task-container custom-task-container'>
            <!-- <p class='task-id'>ID: " . $task['ID'] . "</p> -->
            <p class='task-name'>Uppgift: " . $task['attgora'] . "</p>
            <p class='task-status'>Klar: " . ($task['klar'] ? 'Ja' : 'Nej') . "</p>
            <p class='task-date'>Datum: " . $task['datum'] . "</p>
            <img src='" . ($task['klar'] ? './images/doneicon.png' : './images/notdone.png') . "' alt='Status Image'>
            <form method='post'>
                <input type='hidden' name='taskId' value='" . $task['ID'] . "'>
                <button type='submit' name='completeTask'>Markera som slutförd</button>
                <input type='text' name='updatedTask' placeholder='Redigera uppgift'>
                <button type='submit' name='updateTask'>Updatera Uppgift</button>
                <button type='submit' name='deleteTask'>Radera</button>
            </form>
        </div>
        ";
        }
    }


    // Markerar en uppgift som klar
    if (isset($_POST['completeTask'])) {
        $taskId = $_POST['taskId'];
        completeTask($pdo, $taskId);
    }

    // Uppdaterar en uppgift
    if (isset($_POST['updateTask'])) {
        $taskId = $_POST['taskId'];
        $updatedTask = $_POST['updatedTask'];
        updateTask($pdo, $taskId, $updatedTask);
    }

    // Tar bort en uppgift
    if (isset($_POST['deleteTask'])) {
        $taskId = $_POST['taskId'];
        deleteTask($pdo, $taskId);
    }
}

// Stänger anslutningen till databasen
$pdo = null;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style.css">
    <title>Att göra!</title>
</head>

<body>
    <form method="post" class="form uppgiftsidan">
        <label for="newTask">Ny uppgift:</label>
        <input type="text" name="newTask" required>
        <button type="submit" name="addTask" class="button">Lägg till uppgift</button>
    </form>
    <hr>
    <form method="post" class="form uppgiftsidan">
        <button type="submit" name="showAllTasks" class="button">Visa alla uppgifter</button>
    </form>
    
</body>

</html>