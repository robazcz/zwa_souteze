<?php 
session_start(); 
if(!isset($_SESSION["username"])){
    header("Location: login");
}
if(isset($_POST["logout"])){
    unset($_SESSION["username"]);
    header("Location: login");
}
?>
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
        echo $_SESSION["username"];
        ?>
        <form action="profile" method="post">
            <input type="hidden" name="logout">
            <input type="submit" value="Odhlásit se">
        </form>
    </main>
</body>
</html>