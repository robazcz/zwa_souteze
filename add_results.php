<?php
/** Stránka k přidání výsledku */
session_start();
include("functions.php");

login_check("home");
// Když není uvedená soutěž v uri
if(!isset($_GET["competition"])){ 
    header("Location: home");
    exit;
}

$db = db_connect();
$competition = $db->prepare("SELECT id_results FROM competition WHERE id = ?");
if(!$competition->execute([$_GET["competition"]])){ // Když neexistuje competition id
    header("Location: home");
    exit;
}

$c = $competition->fetchColumn();
//$creator = false;
if(!is_null($c)){ // Když už nějaký výsledky má
    
    // Upravování výsledků
    // $made_by = $db->query("SELECT id_user FROM results WHERE id == $c");
    // if($_SESSION["user"]["id"] == $made_by->fetchColumn()){
    //     $creator = true;
    // }
    // else{
    //     header("Location: competition?id=$_GET[competition]");
    // }

    header("Location: competition?id=$_GET[competition]");
    exit;
}


if(isset($_POST["selected-categories"])){
    // Vytvoř záznam výsledků
    $db->exec("INSERT INTO results (id_user) VALUES ({$_SESSION['user']['id']})");
    $results_id = $db->lastInsertId();

    // Přiřaď id novýho záznamu k soutěži
    $add_result_id = $db->prepare("UPDATE competition SET id_results = ? WHERE id = ?");
    $add_result_id->execute([$results_id, $_GET["competition"]]);

    // Pro každou vybranou kategorii
    foreach($_POST["selected-categories"] as $category){
        // Pro každej výsledek ve vybraný kategorii
        for($i = 0; $i < count($_POST["results-name-$category"]); $i++){    
            $valid = 0;
            
            if($_POST["results-np-$category"][$i] == "false"){
                $valid = 1;
            }
            else{
                $valid = 0;
            }
            
            // Vyhledej tým
            $team = $db->prepare("SELECT * FROM team WHERE id_category = ? and lower(name) = ?");
            $team->execute([$category, strtolower(trim($_POST["results-name-$category"][$i]))]);
            $s_team = $team->fetch();
            
            // Když tým neexistuje, vytvoř ho
            if(!$s_team){
                $new_team = $db->prepare("INSERT INTO team (name, id_category) VALUES (?, ?)");
                $new_team->execute([$_POST["results-name-$category"][$i], $category]);
                $team_id = $db->lastInsertId();
            }
            else{
                $team_id = $s_team["id"];
            }
            
            $time1 = NULL;
            $time2 = NULL;
            
            // Vybere pomalejší čas
            if($_POST["results-time1-$category"][$i] != ""){
                if($_POST["results-time2-$category"][$i] != ""){
                    if($_POST["results-time1-$category"][$i] < $_POST["results-time2-$category"][$i]){
                        $time1 = $_POST["results-time1-$category"][$i];
                        $time2 = $_POST["results-time2-$category"][$i];
                    }
                    else{
                        $time1 = $_POST["results-time2-$category"][$i];
                        $time2 = $_POST["results-time1-$category"][$i];
                    }
                }
                else{
                    $time1 = $_POST["results-time1-$category"][$i];
                }
            }
            else{
                $valid = 0;
            }

            // Zapiš výsledek do databáze
            $one_result = $db->prepare("INSERT INTO result (id_team, id_results, time_run, time_run_2, valid_run) VALUES (?, ?, ?, ?, ?)");
            $one_result->execute([$team_id, $results_id, $time1, $time2, $valid]);
        }
    }
    header("Location: competition?id=$_GET[competition]");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="zwa/static/style.css">
    <link rel="icon" type="image/x-icon" href="zwa/static/favicon.ico">
    <title>Přidat výsledky</title>
</head>
<body>
<?php include_once("header.php"); ?>
    <main class="add-comp">
        <form action="<?php echo "add_results?competition=$_GET[competition]" ?>" method="POST" class="login-box">
            <?php
            $cats = $db->query("SELECT * FROM category");
            $cats = $cats->fetchAll();

            echo "<div class='labels'>"; // Výběr kategorií
            foreach($cats as $cat){
                echo "<label> $cat[name]";
                echo "<input type='checkbox' id='$cat[id]' value='$cat[id]' name='selected-categories[]'></label>";
            }
            echo "</div>";

            foreach($cats as $cat){ // Vytvoří 1 řádek inputů pro zapsání výsledku (js přidává další podle šablony níže)
                echo "<div class='category-list-hid'>";
                echo "<h3>$cat[name]</h3>";
                echo "<ol id='cat-$cat[id]'>";
                echo "<li id='item-$cat[id]-0'><div class='liflex'><label>Tým<span class='tooltip'>*<span class='tooltiptext'>Povinné pole</span></span>: <input type='text' list='list-$cat[id]-0' name='results-name-$cat[id][]' autocomplete='off'>";
                echo "<datalist id='list-$cat[id]-0'></datalist></label>";
                echo "<label>Čas: <input type='number' step='.01' class='time' id='time-np-$cat[id]-0' name='results-time1-$cat[id][]' placeholder='sekund'></label>"; 
                echo "<label>Čas 2: <input type='number' step='.01' class='time' name='results-time2-$cat[id][]' placeholder='sekund'></label>";
                echo "<label>NP: <input type='checkbox' id='np-$cat[id]-0'></label>"; 
                echo "<input type='hidden' name='results-np-$cat[id][]' id='helper-np-$cat[id]-0' value='false'></div></li></ol>";
                echo "<button id='button-$cat[id]-0'>Přidat tým</button>";   
                echo "</div>";
            }
            ?>

            <input type="submit" value="Odeslat výsledky">
        </form>
        <ol>
        <li id='item-template-id' class='disnone'><div class='liflex'>
            <label>Tým<span class="tooltip">*<span class="tooltiptext">Povinné pole</span></span>: <input type='text' list='list-template-id' name='results-name-template' required autocomplete='off'>
                <datalist id='list-template-id'></datalist>    
            </label>
            <label>Čas: <input type='number' step='.01' class='time' id='time-np-template-id' name='results-time1-template' required placeholder="sekund"></label>
            <label>Čas 2: <input type='number' step='.01' class='time' name='results-time2-template' placeholder="sekund"></label>
            <label>NP: <input type='checkbox' id='np-template-id'></label> 
            <input type='hidden' name='results-np-template' id='helper-np-template-id' value='false'>
            </div>
            <!-- datalist ke každýmu list inputu -->
        </li>
        </ol>
    </main>
    <script src="zwa/static/add_results_script.js"></script>
</body>
</html>