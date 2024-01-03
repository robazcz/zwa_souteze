<?php
/**
 * Vypíše soutěže ze seznamu soutěží z databáze
 *
 * @param array $competition_list Fetched seznam všech sloupečků z tabulky competition 
 * @return void
 */
function print_competitions(array $competition_list){
    if(strtotime($competition_list[0]["date_event"]) > time()){
        echo "<div class='separator-div'><p class='separator-text'>Nadcházející</p>";
        echo "<hr></div>";
    }
    else{
        if($competition_list){
            echo "<div class='separator-div'><p class='separator-text'>Proběhlé</p>";
            echo "<hr></div>";
        }
    }
    for($i = 0; $i < count($competition_list); $i++){
        $competition = $competition_list[$i];
        echo "<article>";
        echo "<h3><a href='competition?id=$competition[id]'> ".htmlspecialchars($competition["title"])." </a></h3>";
        echo "<div class='date_event'>".date_format(date_create($competition["date_event"]), "j. n. Y G:i")."</div>";
        echo "<div>".htmlspecialchars($competition["description"])."</div>";
        echo "</article>";
        if(strtotime($competition["date_event"]) > time() && isset($competition_list[$i+1])){
            if(strtotime($competition_list[$i+1]["date_event"]) < time()){
                echo "<div class='separator-div'><p class='separator-text'>Proběhlé</p>";
                echo "<hr></div>";
            }
        }
    }
}

/**
 * Funkce pro zamezení přístupu ke stránce při nepřihlášení
 *
 * @param string $redirect Adresa pro přesměrování
 * @return void
 */
function login_check(string $redirect){
    if(!isset($_SESSION["user"])){
        Header("Location: $redirect");
        exit;
    }
}

/**
 * Funkce k připojení k databázi
 *
 * @return PDO proměnná k práci s databázi
 */
function db_connect(){
    $db = new PDO("sqlite:" . __DIR__ . "/database.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}
?>