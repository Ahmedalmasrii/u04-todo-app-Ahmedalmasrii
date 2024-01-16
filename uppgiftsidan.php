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
