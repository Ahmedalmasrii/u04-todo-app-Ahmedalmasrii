<?php
session_start();

if (isset($_POST['logout'])) {
    // Förstör sessionen för att logga ut användaren
    session_unset(); // Tar bort alla sessionvariabler
    session_destroy(); // Förstör sessionen

    // Omdirigera användaren till inloggningssidan eller hemsidan
    header("Location: index.php"); // Ändra 'login.php' till rätt sökväg för din inloggningssida
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style.css">
    <title>Din Webbplats</title>
</head>
<body>
    <header class="hdclass">
        <!-- Logga ut-knapp -->
        <form  class="frhd" action="" method="POST">
            <button  class="lgbtn" type="submit" name="logout">Logga ut</button>
        </form>
    </header>

  
</body>
</html>
