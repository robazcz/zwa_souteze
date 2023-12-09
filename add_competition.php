<?php
    session_start();
    if(!isset($_SESSION["username"])){
        header("Location: login?next=add_competition");
    }
    
    $db = new PDO("sqlite:" . __DIR__ . "/database.db");
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

        if(!$error){
            $id_comp = $db->query("SELECT max(id) FROM competition");
            $id_comp = $id_comp->fetchColumn();
            $id_comp = $id_comp?$id_comp+1:0;
            if(!file_exists(__DIR__."/uploads/$id_comp")){
                mkdir(__DIR__."/uploads/$id_comp", 0777, true);
            }
            $prop_file = NULL;
            if(isset($_FILES["proposition"]) && is_uploaded_file($_FILES["proposition"]["tmp_name"])){
                $prop_file = basename($_FILES["proposition"]["name"]);
                $file_target = __DIR__."/uploads/$id_comp/$prop_file";
                if(!move_uploaded_file($_FILES["proposition"]["tmp_name"], $file_target)){
                    $error["proposition"] = "Soubor se nepodařilo uložit";
                }
            }
            if(!isset($error)){
                $new_comp = $db->prepare("INSERT INTO competition (id_user, title, description, date_event, town, proposition) VALUES (?, ?, ?, ?, ?, ?)");
                //echo "ukládám $_SESSION[user_id], $_POST[title], $_POST[description], $_POST[date_event], $_POST[town], $prop_file";
                $new_comp->execute([$_SESSION["user_id"], $_POST["title"], $_POST["description"], strtotime($_POST["date_event"]), $_POST["town"], $prop_file]);
                Header("Location: home");
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
    <title>Přidat soutěž</title>
</head>
<body>
<?php include_once("header.php"); ?>
    <main class="add-comp">
        <form action="" method="post" class="login-box" id="add_comp_form" enctype='multipart/form-data'>
            <div>
                <label for="comp_title">Název<div class="tooltip">*<span class="tooltiptext">Povinné pole</span></div></label>
                <input type="text" name="title" id="comp_title" value="<?php echo isset($_POST["title"])?htmlspecialchars($_POST["title"]):""?>">
                <p id="comp_title_error"><?php echo isset($error["title"])?$error["title"]:""?></p>
            </div>
            <div>
                <label for="comp_description">Popis</label>
                <textarea name="description" id="comp_description"><?php echo isset($_POST["description"])?htmlspecialchars($_POST["description"]):""?></textarea>
            </div>
            <div>
                <label for="comp_proposition">Propozice</label>
                <input type="file" name="proposition" id="comp_proposition">
                <p id="comp_proposition_error"><?php echo isset($error["proposition"])?$error["proposition"]:""?></p>
            </div>
            <div>
                <label for="comp_date_event">Datum a čas soutěže<div class="tooltip">*<span class="tooltiptext">Povinné pole</span></div></label>
                <input type="datetime-local" name="date_event" id="comp_date_event" value="<?php echo isset($_POST["date_event"])?htmlspecialchars($_POST["date_event"]):""?>">
                <p id="comp_date_event_error"><?php echo isset($error["date_event"])?$error["date_event"]:""?></p>
            </div>
            <div>
                <label for="comp_town">Obec konání<div class="tooltip">*<span class="tooltiptext">Povinné pole</span></div></label>
                <input type="text" name="town" id="comp_town" value="<?php echo isset($_POST["town"])?htmlspecialchars($_POST["town"]):""?>">
                <p id="comp_town_error"><?php echo isset($error["town"])?$error["town"]:""?></p>
            </div>
            <input type="submit" value="Přidat">
        </form>
    </main>
    <script src="zwa/static/script.js" type="text/javascript"></script>
</body>
</html>