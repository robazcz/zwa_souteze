<?php session_start();
    if(!is_numeric($_GET["id"])){
        header("Location: home");
    }
    $db = new PDO("sqlite:" . __DIR__ . "/database.db");
    $comp = $db->query("SELECT * FROM competition WHERE id = $_GET[id]")->fetchAll();
    $comp = $comp[0];
    
    if(isset($_FILES["image"]) && !empty(isset($_FILES["image"]))){
        foreach($_FILES["image"]["tmp_name"] as $key=>$value){
            $file_name = basename($_FILES["image"]["name"][$key]);
            if(file_exists(__DIR__."/uploads/$comp[id]/$file_name")){
                $error["image"] = "Obrázek existuje";
            }
        
            if(is_uploaded_file($value)){
                if($_FILES["image"]["size"][$key] > 8000000){ // 8MB
                    $error["image"] = "Velikost souboru je moc velká";
                }
                switch(strtolower(pathinfo($_FILES["image"]["name"][$key], PATHINFO_EXTENSION))){
                    case "png":
                    case "jpg":
                    case "jpeg":
                    case "gif":
                        break;
                    default:
                        $error["image"] = "Podporované formáty jsou pouze .png, .jpg, .jpeg a .gif";
                        break;
                }
                if(!$error){
                    $file_target = __DIR__."/uploads/$comp[id]/$file_name";
                    
                    if(move_uploaded_file($value, $file_target)){
                        $add_img = $db->prepare("INSERT INTO photo (id_user, id_competition, name) VALUES (?, ?, ?)");
                        $add_img->execute([$_SESSION["user_id"], $_POST["competition_id"], $file_name]);
                        header("Location: competition?id=$_POST[competition_id]");
                    }
                }
            }
        }
        
    }
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
            <div class="comp-name-line"><h2><?php echo htmlspecialchars($comp["title"]) ?></h2> <em><?php echo date_format(date_create($comp["date_event"]), 'j. n. Y G:i') ?></em></div>
            <hr>
            <p><strong>místo konání:</strong><em class="comp-info"><?php echo htmlspecialchars($comp["town"]) ?></em></p>
            <p><strong>propozice:</strong><a href="<?php echo "zwa/uploads/$comp[id]/$comp[proposition]" ?>"><em class="comp-info"><?php echo $comp["proposition"] ?></em></a></p>
            <p><?php echo htmlspecialchars($comp["description"]) ?></p>
            <div>
                <strong>Výsledky</strong>
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
                                echo "<table class='results'>";
                                echo "<tr><th colspan=3>".mb_strtoupper($categories[$key-1]["name"])."</th></tr>";
                                echo "<tr><th>Pořadí</th><th>Družstvo</th><th>Čas</th>";
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
                        if(isset($_SESSION["username"])){
                            echo "<a>přidat výsledky</a>";
                        }
                        else{
                            echo "<p><em>Pro přidávání výsledků se přihlaste</em></p>";
                        }
                    }
                ?>
            </div>
            <?php
            if(isset($_SESSION["username"])){
                echo "<form enctype='multipart/form-data' method='POST' action=''>";
                echo "<label for='image_upload'>Nahrát obrázek: </label>";
                echo "<input type='file' name='image[]' id='image_upload' multiple required>";
                echo "<p>".$error?$error["image"]:''."</p>";
                echo "<input type='hidden' name='competition_id' value=$comp[id]>";
                echo "<input type='submit' value='Nahrát'></form>";
            }
            else{
                echo "<p><em>Pro nahrávání obrázků se přihlaste</em></p>";
            }
            
            $pictures = $db->query("SELECT name FROM photo WHERE id_competition == $comp[id]")->fetchAll();
            //$count = count($pictures);

            echo "<div class='row'>";
            for($c = 0; $c < 4; $c++){ // sloupečky
                echo "<div class='col'>";
                $pic_pos = $c;
                while($pic_pos < count($pictures)){
                    echo "<img src='zwa/uploads/$comp[id]/".$pictures[$pic_pos][0]."' alt='".htmlspecialchars($pictures[$pic_pos][0])."'>";
                    $pic_pos += 4; 
                }
                echo "</div>";
            }
            // echo "</div>";

            //     echo "<div class='row'>";
            //     for($c = 0; $c < 4; $c++){
            //         echo "<div class='col'>";
            //         $divident = 4;
            //         $exp = count($pictures)%$divident > 1%$divident?ceil(count($pictures)/$divident):count($pictures)/$divident;
            //         for($i = 1; $i <= $exp; $i++){// vyřešit líp!
            //             echo "<img src='zwa/uploads/$comp[id]/".$pictures[$pic_pos][0]."' alt='$pic[0]'>";
            //             $pic_pos++;
            //         }
            //         echo "</div>";
            //     }
            ?>
        </article>
    </main>
</body>
</html>