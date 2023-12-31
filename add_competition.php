<?php
/** Stránka pro přidání a editaci soutěže */

    session_start();
    include("functions.php");

    // Když v uri je uvedeno id
    if(isset($_GET["id"])){
        login_check("login?next=add_competition?id=$_GET[id]");
    }
    else{
        login_check("login?next=add_competition");
    }

    $db = db_connect();

    $comp_form = [
        "id" => "",
        "title" => "",
        "description" => "",
        "proposition" => "",
        "date_event" => "",
        "town" => ""
    ];

    if(isset($_GET["id"])){
        // Získání dat soutěže
        $comp = $db->prepare("SELECT * FROM competition WHERE id = ?");
        $comp->execute([$_GET["id"]]);
        $competition = $comp->fetch();

        if($competition["id_user"] == $_SESSION["user"]["id"]){
            $comp_form = [
                "id" => $competition["id"],
                "title" => $competition["title"],
                "description" => $competition["description"],
                "proposition" => $competition["proposition"],
                "date_event" => $competition["date_event"],
                "town" => $competition["town"]
            ];
        }
        else{
            // Když uživatel nevytvořil soutěž, tak nemůže editovat
            header("Location: competition?id=$_GET[id]");
            exit;
        }
    }
    
    $error = array();

    if(isset($_POST["title"], $_POST["date_event"], $_POST["town"])){
        if(empty($_POST["title"])) {
            $error["title"] = "Pole nemůže být prázdné";
        }
        if(empty($_POST["date_event"])) {
            $error["date_event"] = "Pole nemůže být prázdné";
        }
        if(empty($_POST["town"])) {
            $error["town"] = "Pole nemůže být prázdné";
        }
        if(isset($_FILES["proposition"]) && is_uploaded_file($_FILES["proposition"]["tmp_name"])){
            if($_FILES["proposition"]["size"] > 20000000){ // 20MB
                $error["proposition"] = "Velikost souboru je moc velká";
            }
            switch(strtolower(pathinfo($_FILES["proposition"]["name"], PATHINFO_EXTENSION))){
                case "doc":
                case "docx":
                case "pdf":
                    break;
                default:
                    $error["proposition"] = "Podporované formáty jsou pouze .doc, .docx a .pdf";
                    break;
            }
        }

        // Když není žádnej error, připrav
        if(!$error){
            if($comp_form["id"] == ""){ // Nová soutěž
                $id_comp = $db->query("SELECT max(id) FROM competition");
                $id_comp = $id_comp->fetchColumn();
                $id_comp = $id_comp?$id_comp+1:1;
            }
            else{ // Editovaná soutěž
                $id_comp = $comp_form["id"];
            }

            // Vytvoření složky pro soutěž - pro propozice a fotky
            if(!file_exists(__DIR__."/uploads/$id_comp")){
                mkdir(__DIR__."/uploads/$id_comp", 0777, true);
            }

            $prop_file = NULL;
            if($comp_form["proposition"] != ""){
                $prop_file = $comp_form["proposition"];
            }

            if(isset($_FILES["proposition"]) && is_uploaded_file($_FILES["proposition"]["tmp_name"])){
                $prop_file = basename($_FILES["proposition"]["name"]);
                $file_target = __DIR__."/uploads/$id_comp/$prop_file";
                if(!move_uploaded_file($_FILES["proposition"]["tmp_name"], $file_target)){
                    $error["proposition"] = "Soubor se nepodařilo uložit";
                }
                else{
                    $existing_file = __DIR__."/uploads/$id_comp/$comp_form[proposition]";
                    if($comp_form["proposition"] != "" && file_exists($existing_file)){
                        unlink($existing_file);
                    }
                }
            }

            // Když pořád není žádnej error, ulož
            if(!$error){
                if($comp_form["id"] != ""){
                    $update_comp = $db->prepare("UPDATE competition SET title = ? , description = ? , date_event = ? , town = ? , proposition = ? WHERE id = $competition[id]");
                    $update_comp->execute([$_POST["title"], $_POST["description"], $_POST["date_event"], $_POST["town"], $prop_file]);
                }
                else{
                    $new_comp = $db->prepare("INSERT INTO competition (id_user, title, description, date_event, town, proposition) VALUES (?, ?, ?, ?, ?, ?)");
                    //echo "ukládám $_SESSION[user_id], $_POST[title], $_POST[description], $_POST[date_event], $_POST[town], $prop_file";
                    $new_comp->execute([$_SESSION["user"]["id"], $_POST["title"], $_POST["description"], $_POST["date_event"], $_POST["town"], $prop_file]);
                }
                Header("Location: competition?id=$id_comp");
                exit;
            }
        }
        else{
            // Když je error, tak připrav hodnoty zpět k zápisu do formuláře
            $comp_form = [
                "title" => isset($_POST["title"])?$_POST["title"]:"",
                "description" => isset($_POST["description"])?$_POST["description"]:"",
                "proposition" => isset($_POST["proposition"])?$_POST["proposition"]:"",
                "date_event" => isset($_POST["date_event"])?$_POST["date_event"]:"",
                "town" => isset($_POST["town"])?$_POST["town"]:""
            ];
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
    <title>Přidat soutěž</title>
</head>
<body>
<?php include_once("header.php"); ?>
    <main class="add-comp">
        <form action="<?php echo isset($_GET["id"])?"add_competition?id=$_GET[id]":"add_competition" ?>" method="post" class="login-box" id="add_comp_form" enctype='multipart/form-data'>
            <div>
                <label for="comp_title">Název<span class="tooltip">*<span class="tooltiptext">Povinné pole</span></span></label>
                <input type="text" name="title" id="comp_title" required value="<?php echo htmlspecialchars($comp_form["title"])?>">
                <p id="comp_title_error"><?php echo isset($error["title"])?$error["title"]:""?></p>
            </div>
            <div>
                <label for="comp_description">Popis</label>
                <textarea name="description" id="comp_description"><?php echo htmlspecialchars($comp_form["description"])?></textarea>
            </div>
            <div>
                <label for="comp_proposition">Propozice</label>
                <input type="file" name="proposition" accept=".doc, .docx, .pdf" id="comp_proposition">
                <?php echo isset($comp_form["id"])?"<a target='_blank' href='zwa/uploads/$comp_form[id]/$comp_form[proposition]'>".htmlspecialchars($comp_form["proposition"])."</a>":""; ?>
                <p id="comp_proposition_error"><?php echo isset($error["proposition"])?$error["proposition"]:""?></p>
            </div>
            <div>
                <label for="comp_date_event">Datum a čas soutěže<span class="tooltip">*<span class="tooltiptext">Povinné pole</span></span></label>
                <input type="datetime-local" name="date_event" id="comp_date_event" required value="<?php echo htmlspecialchars($comp_form["date_event"])?>">
                <p id="comp_date_event_error"><?php echo isset($error["date_event"])?$error["date_event"]:""?></p>
            </div>
            <div>
                <label for="comp_town">Obec konání<span class="tooltip">*<span class="tooltiptext">Povinné pole</span></span></label>
                <input type="text" name="town" id="comp_town" required value="<?php echo htmlspecialchars($comp_form["town"])?>">
                <p id="comp_town_error"><?php echo isset($error["town"])?$error["town"]:""?></p>
            </div>
            <?php echo isset($_GET["id"])?"<input type='hidden' value='".htmlspecialchars($_GET["id"])."'>":"" ?>
            <input type="submit" value="Uložit">
        </form>
    </main>
    <script src="zwa/static/script.js"></script>
</body>
</html>