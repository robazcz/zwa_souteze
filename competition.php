<?php session_start();?>
<?php
    $db = new PDO("sqlite:database.db");
    $comp = $db->query("SELECT * FROM competition WHERE id = $_GET[id]")->fetchAll();
    $comp = $comp[0]
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title><?php echo $comp["title"]; ?></title>
</head>
<body>
    <?php include_once("header.php"); ?>
    <main>
        <article>
            <h2><?php echo $comp["title"] ?></h2>
            <p>čas konání: <em><?php echo $comp["date_event"] ?></em></p>
            <p>místo konání: <em><?php echo $comp["town"] ?></em></p>
            <p>propozice: <em><?php echo $comp["proposition"] ?></em></p>
            <p><?php echo $comp["description"] ?></p>
            <div>
                <p>Výsledky</p>
                <?php
                    $categories = $db->query("SELECT * FROM category")->fetchAll();
                    //$results = $db->query("SELECT * FROM result WHERE id_results = $comp[id_results]"); // všechny výsledky pro soutěž
                    foreach ($categories as $category) {
                        $results = $db->query("SELECT * FROM result r, team t where r.id_team = t.id and t.id_category = $category[id] ORDER BY time_run ASC");
                        $results = $results->fetchAll();
                        if($results){
                            echo "<p>$category[name]</p>";
                            echo "<ol>";
                            foreach( $results as $result ) {
                                echo "<li>$result[name] : $result[time_run]</li>";
                            }
                            echo "</ol>";
                        }
                    }

                ?>
            </div>
        </article>
    </main>
</body>
</html>