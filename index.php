<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
    <?php
    $db = new PDO("sqlite:database.db");
    ?>
    <header>
        <nav class="navbar">
            <ul>
                <li><a href="/">Domov</a></li>
                <li class='login'><a href="/login.php"><?php echo isset($_SESSION['username'])?$_SESSION['username']:"login"; ?></a></li>
            </ul>
        </nav>
    </header>
    <main>
    <?php
        $q = $db->prepare("SELECT * FROM competition"); // WHERE date_created < date()
        $q->execute();

        foreach($q->fetchAll() as $i){
            echo "<article>";
            echo "<h3> $i[title] </h3>";
            echo "<div class='date_event'>".date_format(date_create($i["date_event"]), "d.  m. Y")."</div>";
            echo "<div> $i[description] </div>";
            echo "</article>";
            //print_r($i);
            //echo("<p>".$i["title"]."</p>");
            //echo strtotime($i["date_created"]);
        }

        //echo $q;
        
    ?>
    </main>
</body>
</html>