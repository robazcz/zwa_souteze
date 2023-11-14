<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Přihlášení</title>
</head>
<body>
    <?php include_once("header.php"); ?>
    <main class="login">
        <?php
            if(isset($_SESSION["username"])){
                header("Location: /profile.php");
            }
            $db = new PDO("sqlite:database.db");
            if(isset($_POST["username"], $_POST["password"])){
                $usr = strtolower($_POST["username"]);
                $q = $db->query("SELECT id, password FROM user WHERE username = '$usr'");
                $user = $q->fetch();
                if(!$user){
                    $error["login"] = "Špatné přihlašovací údaje";                
                }
                else{
                    if (password_verify($_POST["password"], $user["password"])){
                        $_SESSION["username"] = $usr;
                        if(isset($_POST["next"])){
                            header("Location: $_POST[next]");
                        }
                        else{
                            header("Location: /");
                        }
                    }
                    else{
                        $error["login"] = "Špatné přihlašovací údaje"; 
                    }
                }
            }
            ?>
        <form action="/login.php" method="post" id="login_form" class="login-box">
            <?php echo isset($_GET["next"])? "<input type='hidden' name='next' value='$_GET[next]'>": "" ?>
            <div>
                <label for="username" class="block">Uživatelské jméno</label>
                <input type="text" name="username" id="login_username" class="block" autofocus value="<?php echo isset($_POST["username"])?htmlspecialchars($_POST["username"]):"";?>">
                <p id="login_username_error"></p>
            </div>
            <div>
                <label for="password" class="block">Heslo</label>
                <input type="password" name="password" id="login_password" class="block">
                <p id="login_password_error"><?php echo isset($error["login"])?$error["login"]:"";?></p>
            </div>
            <input type="submit" value="Přihlásit">
            <div><a href="/register.php">registrace</a></div>
        </form>
        <?php //echo "<p>".$_SESSION["username"]."</p>"; ?>
    </main>
    <script src="script.js"></script>
</body>
</html>