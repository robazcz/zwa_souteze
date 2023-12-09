<?php
function print_competitions($competition_list){
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
        echo "<h3><a href='competition?id=$competition[id]'> $competition[title] </a></h3>";
        echo "<div class='date_event'>".date_format(date_create($competition["date_event"]), "j. n. Y G:i")."</div>";
        echo "<div>$competition[description]</div>";
        echo "</article>";
        if(strtotime($competition["date_event"]) > time() && isset($competition_list[$i+1])){
            if(strtotime($competition_list[$i+1]["date_event"]) < time()){
                echo "<div class='separator-div'><p class='separator-text'>Proběhlé</p>";
                echo "<hr></div>";
            }
        }
    }
}
?>