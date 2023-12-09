<?php 
session_start();
$db = new PDO("sqlite:" . __DIR__ . "/database.db");
if(isset($_POST["username"], $_POST["password"], $_POST["password_2"])){
    if(empty($_POST["username"])){
        $error["username"] = "Pole nemůže být prázdné";
    }
    else{
        $q = $db->prepare("SELECT count(id) FROM user WHERE username = '$_POST[username]'");
        $q->execute();
        if ($q->fetchColumn() > 0){
            $error["username"] = "Jméno již existuje";
        }
        if (preg_match("/^[a-zA-Z0-9-_.]+$/", $_POST["username"]) == 0){
            $error["username"] = "Jméno obsahuje zakázané znaky";
        }
    }
    if(empty($_POST["password"])){
        $error["password"] = "Pole nemůže být prázdné";
    }
    else{
        if($_POST["password"] != $_POST["password_2"]){
            $error["password_2"] = "Hesla se neshodují";
        }
    }

    if(!(isset($error["username"]) | isset($error["password"]) | isset($error["password_2"]))){
        $pswd = password_hash($_POST["password"], PASSWORD_BCRYPT);
        $usr = strtolower($_POST["username"]);
        $db->exec("INSERT INTO user (username, password) VALUES ('$usr', '$pswd')");
        header("Location: login");
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
    <title>Registrace</title>
</head>
<body>
    <?php include_once("header.php"); ?>  
    <main class="login">
        <form action="register" method="post" id="register_form" class="login-box">
            <h3>Registrovat</h3>
            <div>
                <label for="username">Uživatelské jméno<div class="tooltip">*<span class="tooltiptext">Povinné pole</span></div></label>
                <input type="text" name="username" id="register_username" autofocus value="<?php echo isset($_POST["username"])?htmlspecialchars($_POST["username"]):"";?>">
                <p id="register_username_error"><?php echo isset($error["username"])?$error["username"]:"";?></p>
            </div>
            <div>
                <label for="password">Heslo<div class="tooltip">*<span class="tooltiptext">Povinné pole</span></div></label>
                <input type="password" name="password" id="register_password">
                <p id="register_password_error"><?php echo isset($error["password"])?$error["password"]:"";?></p>
            </div>
            <div>
                <label for="password_2">Heslo znovu<div class="tooltip">*<span class="tooltiptext">Povinné pole</span></div></label>
                <input type="password" name="password_2" id="register_password_2">
                <p id="register_password_2_error"><?php echo isset($error["password_2"])?$error["password_2"]:"";?></p>
            </div>
            <input type="submit" value="Registrovat">
        </form>
    </main>
    <script src="zwa/static/script.js" type="text/javascript"></script>
</body>
</html>