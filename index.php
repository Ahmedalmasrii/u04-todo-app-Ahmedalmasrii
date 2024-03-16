<?php
// Startar en ny session eller forsätter med den befintliga sessionen
session_start();

// Inkluderar filen med anslutningsdetaljer till  själva databasen
include "connect.php";


// Denna funktion är för att registrera en ny användare i databasen
function registerUser($pdo, $name, $email, $password)
{
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $email, $hashedPassword]);
}

function loginUser($pdo, $email, $password)
{
    $sql = "SELECT id, password FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        return $user['id']; // Returnerar användar-ID om inloggningen lyckas
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
        registerUser($pdo, $name, $email, $password); // Ändrat från $conn till $pdo
        echo "Användaren registrerad framgångsrikt!";
    }

    // Kontrollerar om formuläret 'login' har skickats
    if (isset($_POST['login'])) {
        $email = $_POST['loginEmail'];
        $password = $_POST['loginPassword'];

        // Anropar funktionen för att kontrollera och logga in användaren i hemsidan
        $userId = loginUser($pdo, $email, $password); // Ändrat från $conn till $pdo

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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style.css">
    <title>Din-Todo</title>
</head>

<body>
<header>
      
    </header>
    <div class="centered-container">
        <h2 class="page-title">Att göra-lista</h2>
        <div class="form-container">
            <form action="" method="post" class="form">
                <h3>Registrera</h3>
                Namn: <input type="text" name="name"><br>
                E-post: <input type="text" name="email"><br>
                Lösenord: <input type="password" name="password"><br>
                <input type="submit" name="register" value="Registrera" class="button">
            </form>
            <hr>
            <form action="" method="post" class="form">
                <h3>Logga in</h3>
                E-post: <input type="text" name="loginEmail"><br>
                Lösenord: <input type="password" name="loginPassword"><br>
                <input type="submit" name="login" value="Logga in" class="button">
            </form>
        </div>

    </div>
</body>

</html>