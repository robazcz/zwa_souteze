<?php 
session_start(); 
if(isset($_POST["logout"])){
    unset($_SESSION["username"]);
    header("Location: login");
}
if(!isset($_SESSION["username"])){
    header("Location: login");
}
include("functions.php");
$db = new PDO("sqlite:" . __DIR__ . "/database.db");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="zwa/static/style.css">
    <link rel="icon" type="image/x-icon" href="zwa/static/favicon.ico">
    <title>Uživatelský profil</title>
</head>
<body>
    <?php include_once("header.php"); ?>  
    <main class="profile">
        <?php
        if(isset($_SESSION["username"])){
            echo "<h2>Uživatel <em>$_SESSION[username]</em></h2>";

            echo "<form action='' method='post'><input type='hidden' name='logout'>";
            echo "<input type='submit' value='Odhlásit se'></form>";
        
            $q = $db->query("SELECT * FROM competition WHERE id_user = $_SESSION[user_id] ORDER BY date_event DESC");
            
            echo "<div>Mnou přidané soutěže";
            print_competitions($q->fetchAll(PDO::FETCH_ASSOC));
            echo "</div>";
        }
        ?>
        
    </main>
</body>
</html>