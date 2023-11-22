<?php
    if(!isset($_POST["title"])) {
        $error["title"] = "Pole nemůže být prázdné";
    }
    if(!isset($_POST["date_event"])) {
        $error["date_event"] = "Pole nemůže být prázdné";
    }
    if(!isset($_POST["town"])) {
        $error["town"] = "Pole nemůže být prázdné";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="zwa/static/style.css">
    <title>Přidat soutěž</title>
</head>
<body>
<?php include_once("header.php"); ?>
    <main class="add-comp">
        <form action="" method="post" class="login-box">
            <div>
                <label for="comp_title">Název<div class="tooltip">*<span class="tooltiptext">Povinné pole</span></div></label>
                <input type="text" name="title" id="comp_title">
                <p id="comp_title_error"></p>
            </div>
            <div>
                <label for="comp_description">Popis</label>
                <textarea name="description" id="comp_description"></textarea>
            </div>
            <div>
                <label for="comp_proposition">Propozice</label>
                <input type="file" name="proposition" id="comp_proposition">
            </div>
            <div>
                <label for="comp_date_event">Datum a čas soutěže<div class="tooltip">*<span class="tooltiptext">Povinné pole</span></div></label>
                <input type="datetime-local" name="date_event" id="comp_date_event">
                <p id="comp_date_event_error"></p>
            </div>
            <div>
                <label for="comp_town">Obec konání<div class="tooltip">*<span class="tooltiptext">Povinné pole</span></div></label>
                <input type="text" name="town" id="comp_town">
                <p id="comp_town_error"></p>
            </div>
            <input type="submit" value="Přidat">
        </form>
    </main>
</body>
</html>