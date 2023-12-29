<?php
/** Používání pro Ajax odpověď, kontrola existence jména u registrace */
include("functions.php");
    if(isset($_POST["username"])){
        $db = db_connect();

        // Získávání jména z databáze
        $q = $db->prepare("SELECT count(id) FROM user WHERE username = ?");
        $q->execute([$_POST["username"]]);

        // Když existuje 
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