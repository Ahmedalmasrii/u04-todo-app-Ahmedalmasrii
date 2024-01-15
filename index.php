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

// Kontrollerar om förfrågningsmetoden är POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kontrollerar om formuläret 'register' har skickats
    if (isset($_POST['register'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Anropar funktionen för att registrera en ny användare
        registerUser($conn, $name, $email, $password);
        echo "Användaren registrerad framgångsrikt!";
    }

    // Kontrollerar om formuläret 'login' har skickats
    if (isset($_POST['login'])) {
        $email = $_POST['loginEmail'];
        $password = $_POST['loginPassword'];

        // Anropar funktionen för att kontrollera och logga in användaren i hemsidan
        $userId = loginUser($conn, $email, $password);

        if ($userId) {
            // Sätter användar-ID i sessionen
            $_SESSION['userId'] = $userId;

            // Omdirigerar till welcome.php vid en lyckad inloggning
            header("Location: welcome.php");
            exit();
        } else {
            echo "Inloggningen misslyckades. Var god kontrollera din e-post och ditt lösenord.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style.css">
    <title>Dokument</title>
</head>

<body>
    <div class="container">
        <div class="todo-app">
            <h2>Att göra-lista <img src="/images/clipboard-list-solid.svg" alt="ikon"></h2>

            <!-- Registreringsformulär  för att skapa konto -->
            <form action="" method="post">
                <h3>Registrera</h3>
                Namn: <input type="text" name="name"><br>
                E-post: <input type="text" name="email"><br>
                Lösenord: <input type="password" name="password"><br>
                <input type="submit" name="register" value="Registrera">
            </form>

            <hr>

            <!-- Inloggningsformulär -->
            <form action="" method="post">
                <h3>Logga in</h3>
                E-post: <input type="text" name="loginEmail"><br>
                Lösenord: <input type="password" name="loginPassword"><br>
                <input type="submit" name="login" value="Logga in">
            </form>
        </div>
    </div>
</body>

</html>
