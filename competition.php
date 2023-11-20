<?php session_start();?>
<?php
    $db = new PDO("sqlite:" . __DIR__ . "/database.db");
    $comp = $db->query("SELECT * FROM competition WHERE id = $_GET[id]")->fetchAll();
    $comp = $comp[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="zwa/static/style.css">
    <title><?php echo $comp["title"]; ?></title>
</head>
<body>
    <?php include_once("header.php"); ?>
    <main class="comp">
        <article>
            <div class="comp-name-line"><h2><?php echo $comp["title"] ?></h2> <em><?php echo date_format(date_create($comp["date_event"]), 'j. n. Y') ?></em></div>
            <hr>
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
                        echo "<div class='results'>";
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
                        echo "</div>";
                    }
                    else{
                        echo "<a>přidat výsledky</a>";
                    }
                ?>
            </div>
            <?php
            if(isset($_SESSION["username"])){
                echo "<form enctype='multipart/form-data' method='POST' action='upload_image'>";
                echo "<label for='image_upload'>Nahrát obrázek: </label>";
                echo "<input type='file' name='image' id='image_upload' multiple required>";
                echo "<input type='hidden' name='competition_id' value=$comp[id]>";
                echo "<input type='submit' value='Nahrát'></form>";
            }
            else{
                echo "<p>Pro nahrávání obrázků se přihlaste</p>";
            }
            ?>
        </article>
    </main>
</body>
</html>