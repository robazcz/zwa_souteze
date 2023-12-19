<?php 
/** Hlavní stránka pro zobrazení soutěží */
session_start();
include("functions.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="zwa/static/style.css">
    <link rel="icon" type="image/x-icon" href="zwa/static/favicon.ico">
    <title>Soutěže</title>
</head>
<body>
    <?php $db = new PDO("sqlite:" . __DIR__ . "/database.db"); ?>
    <?php include_once("header.php"); ?>
    <main>
    <?php
        $limit = 5;
        $site = 1;
        if(isset($_GET["limit"])){
            if(is_numeric($_GET["limit"]) && $_GET["limit"] > 0){
                $limit = intval($_GET["limit"]);
            }
        }
        if(isset($_GET["site"])){
            if(is_numeric($_GET["site"])){
                $site = intval($_GET["site"]);
            }
        }

        $q_count = $db->query("SELECT count(id) FROM competition");
        $comp_count = $q_count->fetchColumn();

        echo "<div class='page_nums'>";
        if($site > 1){
            echo "<a href=home?site=". $site - 1 ."&limit=$limit>&lt;</a>";
        }
        for($s = 0; $s*$limit < $comp_count; $s++){
            if($s+1 == $site){
                echo "<span>". $s+1 ."</span>";
            }
            else{
                echo "<a href=home?site=". $s+1 ."&limit=$limit>". $s+1 ."</a>";
            }
        }
        if($site < $s){
            echo "<a href=home?site=". $site + 1 ."&limit=$limit>&gt;</a>";
        }
        echo "</div>";

        $q = $db->query("SELECT * FROM competition ORDER BY date_event DESC LIMIT $limit OFFSET ".$limit*($site-1));
        $compets = $q->fetchAll(PDO::FETCH_ASSOC);
        if($compets){
            print_competitions($compets);
        }

    ?>
    </main>
</body>
</html>