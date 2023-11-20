<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="zwa/static/style.css">
    <title>Uživatelský profil</title>
</head>
<body>
    <?php include_once("header.php"); ?>  
    <main>
        <?php
            if (isset($_SESSION["username"])) {
                echo $_SESSION["username"];
            }
            else{
                header("Location: login");
            }

            //odhlásit
            if(isset($_POST["logout"])){
                unset($_SESSION["username"]);
                header("Location: login");
            }
        ?>
        <form action="profile" method="post">
            <input type="hidden" name="logout">
            <input type="submit" value="Odhlásit se">
        </form>
    </main>
</body>
</html>