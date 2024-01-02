<?php 
/** Stránka registrace */
session_start();
include("functions.php");

if(isset($_SESSION["user"])){
    header("Location: profile");
}

$db = db_connect();

if(isset($_POST["username"], $_POST["password"], $_POST["password_2"])){
    if(empty($_POST["username"])){
        $error["username"] = "Pole nemůže být prázdné";
    }
    else{
        $q = $db->prepare("SELECT count(id) FROM user WHERE username = ?");
        $q->execute([$_POST["username"]]);

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
        // Žádnej error
        $pswd = password_hash($_POST["password"], PASSWORD_BCRYPT);
        $usr = strtolower($_POST["username"]);
        $db->prepare("INSERT INTO user (username, password) VALUES (?, ?)")->execute([$usr, $pswd]);

        header("Location: login");
        exit;
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
                <label for="register_username">Uživatelské jméno<span class="tooltip">*<span class="tooltiptext">Povinné pole</span></span></label>
                <input type="text" name="username" id="register_username" required autofocus value="<?php echo isset($_POST["username"])?htmlspecialchars($_POST["username"]):"";?>">
                <p id="register_username_error"><?php echo isset($error["username"])?$error["username"]:"";?></p>
            </div>
            <div>
                <label for="register_password">Heslo<span class="tooltip">*<span class="tooltiptext">Povinné pole</span></span></label>
                <input type="password" name="password" id="register_password" required>
                <p id="register_password_error"><?php echo isset($error["password"])?$error["password"]:"";?></p>
            </div>
            <div>
                <label for="register_password_2">Heslo znovu<span class="tooltip">*<span class="tooltiptext">Povinné pole</span></span></label>
                <input type="password" name="password_2" id="register_password_2" required>
                <p id="register_password_2_error"><?php echo isset($error["password_2"])?$error["password_2"]:"";?></p>
            </div>
            <input type="submit" value="Registrovat">
        </form>
    </main>
    <script src="zwa/static/script.js"></script>
</body>
</html>