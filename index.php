<?php include "connect.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style.css">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <div class="todo-app">
            <h2>To-do list <img src="/images/clipboard-list-solid.svg" alt="icon"></h2>

            <form action="welcome.php" method="post">
                Name: <input type="text" name="name"><br>
                E-mail: <input type="text" name="email"><br>
                <input type="submit">
            </form>
        </div>

    </div>
</body>

</html>