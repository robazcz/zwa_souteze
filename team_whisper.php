<?php
/** Použití pro Ajax odpověď, vytváří datalist element obsahující jména existujících týmů při zadávání výsledků */
include("functions.php");
    if(isset($_GET["name"], $_GET["cat"])){
        $db = db_connect();
        
        $team_names = $db->prepare("SELECT name FROM team WHERE id_category = ? and lower(name) like ?");
        $team_names->execute([$_GET["cat"], "%$_GET[name]%"]);

        echo "<datalist>";
        foreach($team_names->fetchAll() as $team_name){
            echo "<option value='$team_name[name]'>";
        }
        echo "</datalist>";
    }
?>