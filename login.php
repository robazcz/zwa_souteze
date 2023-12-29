<?php 
/** Stránka pro příhlášení */
session_start(); 
include("functions.php");

if(isset($_SESSION["user"])){
    header("Location: profile");
}

$db = db_connect();

if(isset($_POST["username"], $_POST["password"])){
    $usr = strtolower($_POST["username"]);
    $q = $db->prepare("SELECT id, password FROM user WHERE username = ?");
    $q->execute([$usr]);
    $user = $q->fetch();
    
    if(!$user){
        $error["login"] = "Špatné přihlašovací údaje";                
    }
    else{
        if (password_verify($_POST["password"], $user["password"])){
            $_SESSION["user"]["username"] = $usr;
            $_SESSION["user"]["id"] = $user["id"];

            if(isset($_POST["next"])){
                header("Location: $_POST[next]");
                exit;
            }
            else{
                header("Location: home");
                exit;
            }
        }
        else{
            $error["login"] = "Špatné přihlašovací údaje"; 
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
    <title>Přihlášení</title>
</head>
<body>
    <?php include_once("header.php"); ?>
    <main class="login">
        <form action="login" method="post" id="login_form" class="login-box">
            <h3>Přihásit</h3>
            <?php echo isset($_GET["next"])? "<input type='hidden' name='next' value='$_GET[next]'>": "" ?>
            <div>
                <label for="login_username">Uživatelské jméno<span class="tooltip">*<span class="tooltiptext">Povinné pole</span></span></label>
                <input type="text" name="username" id="login_username" required autofocus value="<?php echo isset($_POST["username"])?htmlspecialchars($_POST["username"]):"";?>">
                <p id="login_username_error"></p>
            </div>
            <div>
                <label for="login_password">Heslo<span class="tooltip">*<span class="tooltiptext">Povinné pole</span></span></label>
                <input type="password" name="password" id="login_password" required>
                <p id="login_password_error"><?php echo isset($error["login"])?$error["login"]:"";?></p>
            </div>
            <input type="submit" value="Přihlásit">
            <div><a href="register">registrace</a></div>
        </form>
    </main>
    <script src="zwa/static/script.js"></script>
</body>
</html>