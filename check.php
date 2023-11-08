<?php
    if(isset($_POST["username"])){
        $db = new PDO("sqlite:database.db");
        $q = $db->query("SELECT count(id) FROM user WHERE username = '$_POST[username]'");
        if($q->fetchColumn() > 0){
            $found = ["exist" => "true"];
        }
        else{
            $found = ["exist" => "false"];
        }
        header('Content-Type: application/json');
        echo json_encode($found);
    }
    else{
        http_response_code(404);
    }
?>