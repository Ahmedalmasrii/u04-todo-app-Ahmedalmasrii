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

// Funktion för att markera en uppgift som klar
function completeTask($conn, $id)
{
    $stmt = $conn->prepare("UPDATE todo SET klar = 1 WHERE ID = ?");
    $stmt->bind_param("i", $id);

    // Kolla om uppgiften har markerats som klar, annars visa ett felmeddelande
    if ($stmt->execute()) {
        $message = "Bra jobbat! Uppgiften är nu klar!";
        echo "<div class='success-message'>$message</div>";
    } else {
        $errorMessage = "Hoppsan! Något gick fel när du försökte markera uppgiften som klar: " . $stmt->error;
        echo "<div class='error-message'>$errorMessage</div>";
    }
}

// Funktion för att uppdatera en befintlig uppgift
function updateTask($conn, $id, $newTask)
{
    $stmt = $conn->prepare("UPDATE todo SET attgora = ? WHERE ID = ?");
    $stmt->bind_param("si", $newTask, $id);

    // Kolla om uppgiften har uppdaterats, annars visa ett felmeddelande
    if ($stmt->execute()) {
        $message = "Uppgiften är nu uppdaterad!";
        echo "<div class='update-message'>$message</div>";
    } else {
        $errorMessage = "Oj då! Något gick fel när du försökte uppdatera uppgiften: " . $stmt->error;
        echo "<div class='error-message'>$errorMessage</div>";
    }
}

// Funktion för att ta bort en uppgift
function deleteTask($conn, $id)
{
    $stmt = $conn->prepare("DELETE FROM todo WHERE ID = ?");
    $stmt->bind_param("i", $id);

    // Kolla om uppgiften har tagits bort, annars visa ett felmeddelande
    if ($stmt->execute()) {
        $message = "Borttagning lyckad! Uppgiften är nu borttagen!";
        echo "<div class='success-message'>$message</div>";
    } else {
        $errorMessage = "Oops! Något gick fel när du försökte ta bort uppgiften: " . $stmt->error;
        echo "<div class='error-message'>$errorMessage</div>";
    }
}

// Funktion för att hämta alla uppgifter från databasen
function getTasks($conn)
{
    $result = $conn->query("SELECT * FROM todo");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Hantera formulärinskick
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lägga till en ny uppgift
    if (isset($_POST['addTask'])) {
        $newTask = $_POST['newTask'];
        addTask($conn, $newTask);
    }

    // **Visa och hantera alla uppgifter:**
    // Om användaren har skickat in formuläret för att visa alla uppgifter (`showAllTasks`-knappen har tryckts),
    // hämtas alla uppgifter från databasen. Varje uppgift presenteras med dess namn, status, datum och en bild
    // som visar om den är klar eller ej. För varje uppgift finns även möjlighet att markera som slutförd,
    // uppdatera eller radera den genom ett formulär. All information visas i en HTML-container med klassen `task-container`.
    if (isset($_POST['showAllTasks'])) {
        $tasks = getTasks($conn);
        foreach ($tasks as $task) {
            echo "<div class='task-container'>
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
            </div>";
        }
    }


    // Markera en uppgift som klar
    if (isset($_POST['completeTask'])) {
        $taskId = $_POST['taskId'];
        completeTask($conn, $taskId);
    }

    // Uppdatera en uppgift
    if (isset($_POST['updateTask'])) {
        $taskId = $_POST['taskId'];
        $updatedTask = $_POST['updatedTask'];
        updateTask($conn, $taskId, $updatedTask);
    }

    // Ta bort en uppgift
    if (isset($_POST['deleteTask'])) {
        $taskId = $_POST['taskId'];
        deleteTask($conn, $taskId);
    }
}

// Stäng anslutningen till databasen
$conn->close();
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
    <!-- Formulär för att lägga till en ny uppgift -->
    <form method="post">
        <label for="newTask">Ny uppgift:</label>
        <input type="text" name="newTask" required>
        <button type="submit" name="addTask">Lägg till uppgift</button>
    </form>

    <hr>
    <!-- Formulär för att visa alla uppgifter och utföra åtgärder -->
    <form method="post">
        <button type="submit" name="showAllTasks">Visa alla uppgifter</button>
    </form>

</body>

</html>