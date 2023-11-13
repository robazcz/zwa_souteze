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
                    if(!is_null($comp["id_results"])){
                        $categories = $db->query("SELECT * FROM category")->fetchAll();
                        //$results = $db->query("SELECT * FROM result WHERE id_results = $comp[id_results]"); // všechny výsledky pro soutěž
                        $vysledky = [];
                        foreach ($categories as $category) {
                            $results = $db->query("SELECT * FROM result r, team t where r.id_team = t.id and t.id_category = $category[id] and r.id_results = $comp[id_results] ORDER BY time_run ASC");
                            $results = $results->fetchAll();
                            $vysledky[$category["id"]] = $results;
                        }

                        foreach($vysledky as $key => $vysledek){
                            if($vysledek){
                                $rowcount = 1;
                                echo "<table>";
                                echo "<tr><th colspan=3>".$categories[$key-1]["name"]."</th></tr>";
                                foreach( $vysledek as $result ) {
                                    echo "<tr><td>$rowcount</td><td>$result[name]</td><td>".number_format($result["time_run"],2,",")."</td></tr>";
                                    $rowcount++;
                                }
                                echo "</table>";
                            }
                        }
                    }
                    else{
                        echo "<a>přidat výsledky</a>";
                    }
                ?>
            </div>
            <?php
            if(isset($_SESSION["username"])){
                echo "<form method='POST' action='/upload.php'>";
                echo "<label for='image_upload'>Nahrát obrázek: </label>";
                echo "<input type='file' name='image' id='image_upload' multiple></form>";
            }
            else{
                echo "<p>Pro nahrávání obrázků se přihlašte</p>";
            }
            ?>
        </article>
    </main>
</body>
</html>