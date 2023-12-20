<?php 
/** Stránka výpisu detailu soutěže
 * - vypisuje veškeré detaily zadané při vytvoření soutěže
 * - umožńuje zobrazení nebo přídání výsledků
 * - umožňuje zobrazení a nahrávání fotografií
 */
session_start();
if(!is_numeric($_GET["id"])){
    header("Location: home");
}
$db = new PDO("sqlite:" . __DIR__ . "/database.db");
$comp = $db->prepare("SELECT * FROM competition WHERE id = ?");
$comp->execute([$_GET["id"]]);
$comp = $comp->fetch();

if(!$comp){
    header("Location: home");
}

if(isset($_FILES["image"]) && !empty(isset($_FILES["image"]))){
    if(!isset($_SESSION["user"])){
        header("Location: competition?id=$_GET[id]");
    }
    else{
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
                if(!isset($error)){
                    $file_target = __DIR__."/uploads/$comp[id]/$file_name";
                    
                    if(move_uploaded_file($value, $file_target)){
                        $add_img = $db->prepare("INSERT INTO photo (id_user, id_competition, name) VALUES (?, ?, ?)");
                        $add_img->execute([$_SESSION["user"]["id"], $_POST["competition_id"], $file_name]);
                        header("Location: competition?id=$_POST[competition_id]");
                    }
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
    <link rel="icon" type="image/x-icon" href="zwa/static/favicon.ico">
    <title><?php echo $comp["title"]; ?></title>
</head>
<body>
    <?php include_once("header.php"); ?>
    <main class="comp">
        <article>
            <div class="comp-name-line"><h2><?php echo htmlspecialchars($comp["title"]) ?></h2> <em><?php echo date_format(date_create($comp["date_event"]), 'j. n. Y G:i') ?></em></div>
            <hr>
            <?php 
            if(isset($_SESSION["user"])){
                if($_SESSION["user"]["id"] == $comp["id_user"]){
                    echo "<a href='add_competition?id=$comp[id]'>Upravit</a>";
                }
            }
            ?>
            <p><strong>Místo konání:</strong><em class="comp-info"><?php echo htmlspecialchars($comp["town"]) ?></em></p>
            <p><strong>Propozice:</strong><a target="_blank" href="<?php echo "zwa/uploads/$comp[id]/$comp[proposition]" ?>"><em class="comp-info"><?php echo $comp["proposition"] ?></em></a></p>
            <p><?php echo htmlspecialchars($comp["description"]) ?></p>
            <p>
                <strong>Výsledky</strong>
                <?php
                    if(!is_null($comp["id_results"])){
                        // Upravování výsledků
                        // if(isset($_SESSION["user"])){
                        //     if($db->query("SELECT id_user FROM results WHERE id = $comp[id_results]")->fetchColumn() == $_SESSION["user"]["id"]){
                        //         echo "<a class='block' href='add_results?competition=$comp[id]'>Upravit výsledky</a>";
                        //     }
                        // }
                        $categories = $db->query("SELECT * FROM category")->fetchAll();
                        //$results = $db->query("SELECT * FROM result WHERE id_results = $comp[id_results]"); // všechny výsledky pro soutěž
                        $results = [];
                        foreach ($categories as $category) {
                            $results_db = $db->query("SELECT * FROM result r, team t where r.id_team = t.id and t.id_category = $category[id] and r.id_results = $comp[id_results] ORDER BY r.valid_run DESC, r.time_run ASC");
                            $results_db = $results_db->fetchAll();
                            $results[$category["id"]] = $results_db;
                        }
                        echo "<div class='results'>";
                        
                        foreach($results as $key => $cat_result){
                            if($cat_result){
                                $rowcount = 1;
                                echo "<table class='results'>";
                                echo "<tr><th colspan=3 class='uppercase'>".$categories[$key-1]["name"]."</th></tr>";
                                echo "<tr><th>Pořadí</th><th>Družstvo</th><th>Čas</th>";
                                foreach( $cat_result as $result ) {
                                    if($result["valid_run"] == 1){
                                        echo "<tr><td>$rowcount</td><td>$result[name]</td><td>".number_format($result["time_run"],2,",")."</td></tr>";
                                    }
                                    else{
                                        echo "<tr><td>$rowcount</td><td>$result[name]</td><td>NP</td></tr>";
                                    }
                                    $rowcount++;
                                }
                                echo "</table>";
                            }
                        }
                        echo "</div>";
                    }
                    else{
                        if(isset($_SESSION["user"])){
                            echo "<a class='block' href='add_results?competition=$comp[id]'>Přidat výsledky</a>";
                        }
                        else{
                            echo "<em class='block'>Pro přidávání výsledků se přihlaste</em>";
                        }
                    }
                ?>
            </p>
            <p>
            <?php
            if(isset($_SESSION["user"])){
                echo "<form enctype='multipart/form-data' method='POST' action=''>";
                echo "<label for='image_upload'>Nahrát obrázek: </label>";
                echo "<input type='file' name='image[]' accept='.jpg, .jpeg, .png, .gif' id='image_upload' required>";
                if(isset($error["image"])){
                    echo "<p class='error-text'>$error[image]</p>";
                }
                echo "<input type='hidden' name='competition_id' value=$comp[id]>";
                echo "<input type='submit' value='Nahrát' class='block'></form>";
            }
            else{
                echo "<em>Pro nahrávání obrázků se přihlaste</em>";
            }
            ?>
            </p>
            <?php
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
            echo "</div>";
            ?>
        </article>
    </main>
</body>
</html>