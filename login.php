<?php 
session_start(); 
if(isset($_SESSION["username"])){
    header("Location: profile");
}
$db = new PDO("sqlite:" . __DIR__ . "/database.db");
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
            $_SESSION["username"] = $usr;
            $_SESSION["user_id"] = $user["id"];
            if(isset($_POST["next"])){
                header("Location: $_POST[next]");
            }
            else{
                header("Location: home"); //vyzkoušet
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
                <label for="username">Uživatelské jméno<div class="tooltip">*<span class="tooltiptext">Povinné pole</span></div></label>
                <input type="text" name="username" id="login_username" autofocus value="<?php echo isset($_POST["username"])?htmlspecialchars($_POST["username"]):"";?>">
                <p id="login_username_error"></p>
            </div>
            <div>
                <label for="password">Heslo<div class="tooltip">*<span class="tooltiptext">Povinné pole</span></div></label>
                <input type="password" name="password" id="login_password">
                <p id="login_password_error"><?php echo isset($error["login"])?$error["login"]:"";?></p>
            </div>
            <input type="submit" value="Přihlásit">
            <div><a href="register">registrace</a></div>
        </form>
        <?php //echo "<p>".$_SESSION["username"]."</p>"; ?>
    </main>
    <script src="zwa/static/script.js" type="text/javascript"></script>
</body>
</html>