<?php 
/** Profil uživatele */

session_start(); 
include("functions.php");

login_check("login");

if(isset($_POST["logout"])){
    unset($_SESSION["user"]);
    header("Location: login");
    exit;
}

$db = db_connect();
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
        // odhlašovací formulář
        if(isset($_SESSION["user"])){
            echo "<h2>Uživatel <em>{$_SESSION['user']['username']}</em></h2>";

            echo "<form action='profile' method='post' class='print-hide'><input type='hidden' name='logout'>";
            echo "<input type='submit' value='Odhlásit se'></form>";

            // výpis přidaných soutěží
            $q = $db->query("SELECT * FROM competition WHERE id_user = ".$_SESSION["user"]["id"]." ORDER BY date_event DESC");
            
            echo "<div><h3>Mnou přidané soutěže</h3>";
            print_competitions($q->fetchAll(PDO::FETCH_ASSOC));
            echo "</div>";
        }
        ?>
        
    </main>
</body>
</html>