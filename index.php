<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Soutěže</title>
</head>
<body>
    <?php $db = new PDO("sqlite:database.db"); ?>
    <?php include_once("header.php"); ?>
    <main>
        <?php

        $q = $db->query("SELECT * FROM competition WHERE date_created > date()");
        $st = $q->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($st)) {
            echo "<div class='separator-div'><p class='separator-text'>Nadcházející</p>";
            echo "<hr></div>";
            foreach($st() as $i){
                echo "<article>";
                echo "<h3><a href='/competition.php?id=$i[id]'> $i[title] </a></h3>";
                echo "<div class='date_event'>".date_format(date_create($i["date_event"]), "d.  m. Y")."</div>";
                echo "<div> $i[description] </div>";
                echo "</article>";
            }
        }

        $q = $db->query("SELECT * FROM competition WHERE date_created < date()");
        $st = $q->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($st)){
            echo "<div class='separator-div'><p class='separator-text'>Proběhlé</p>";
            echo "<hr></div>";
            foreach($st as $i){
                echo "<article>";
                echo "<h3><a href='/competition.php?id=$i[id]'> $i[title] </a></h3>";
                echo "<div class='date_event'>".date_format(date_create($i["date_event"]), "d.  m. Y")."</div>";
                echo "<div> $i[description] </div>";
                echo "</article>";
            }
        }
    ?>
    </main>
</body>
</html>