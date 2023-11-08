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
    <main>
        <?php
            if(isset($_SESSION["username"])){
                header("Location: /profile.php");
            }
            $db = new PDO("sqlite:database.db");
            if(isset($_POST["username"], $_POST["password"])){
                $q = $db->query("SELECT id, password FROM user WHERE username = '$_POST[username]'");
                $user = $q->fetch();
                if(!$user){
                    $error["login"] = "Špatné přihlašovací údaje";                
                }
                else{
                    if (password_verify($_POST["password"], $user["password"])){
                        $_SESSION["username"] = $_POST["username"];
                        header("Location: /");
                    }
                    else{
                        $error["login"] = "Špatné přihlašovací údaje"; 
                    }
                }
            }
            ?>
        <form action="/login.php" method="post" id="login_form">
            <div>
                <label for="username">Uživatelské jméno: </label>
                <input type="text" name="username" id="login_username" autofocus value="<?php echo isset($_POST["username"])?htmlspecialchars($_POST["username"]):"";?>">
                <p id="login_username_error"></p>
            </div>
            <div>
                <label for="password">Heslo: </label>
                <input type="password" name="password" id="login_password">
                <p id="login_password_error"><?php echo isset($error["login"])?$error["login"]:"";?></p>
            </div>
            <input type="submit" value="Přihlásit">
        </form>
        <p><a href="/register.php">registrace</a></p>
        <?php //echo "<p>".$_SESSION["username"]."</p>"; ?>
    </main>
    <script src="script.js"></script>
</body>
</html>