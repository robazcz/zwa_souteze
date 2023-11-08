<?php session_start();?>
<?php
    $db = new PDO("sqlite:database.db");
    $comp = $db->query("SELECT * FROM competition WHERE id = $_GET[id]")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title><?php echo $comp[0]["title"]; ?></title>
</head>
<body>
    <?php include_once("header.php"); ?>
    <main>
        <article>
            <h2><?php echo "eho" ?></h2>
            <p>čas konání: <em>"datum"</em></p>
            <p>místo konání: <em>"místo"</em></p>
            <p>propozice: <em>"propozice.docx"</em></p>
            <p>loram ipsum</p>
            <div>
                výsledky
            </div>
        </article>
    </main>
</body>
</html>