<?php
    session_start();
    if(isset($_FILES["image"])){

        $db = new PDO("sqlite:" . __DIR__ . "/database.db");
        $pict = $db->query("SELECT max(id) FROM photo");
        $id_pict = $pict->fetch()[0];

        if(!$id_pict){
            $id_pict = 0;
        }
        else{
            $id_pict++;
        }

        $file_name = $id_pict . "-" . basename($_FILES["image"]["name"]);
        $file_target = __DIR__."/uploads/$file_name";
        echo $file_target;
        print_r($_FILES);

        if(move_uploaded_file($_FILES["image"]["tmp_name"], $file_target)){
            $add_img = $db->prepare("INSERT INTO photo (id_user, id_competition, name) VALUES (?, ?, ?)");
            $add_img->execute([$_SESSION["user_id"], $_POST["competition_id"], $file_name]);
            header("Location: competition?id=$_POST[competition_id]");
        }
    }
    else{
        echo "no image";
    }

?>
<?php
// $target_dir = "uploads/";
// $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
// $uploadOk = 1;
// $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// // Check if image file is a actual image or fake image
// if(isset($_POST["submit"])) {
//   $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
//   if($check !== false) {
//     echo "File is an image - " . $check["mime"] . ".";
//     $uploadOk = 1;
//   } else {
//     echo "File is not an image.";
//     $uploadOk = 0;
//   }
// }

// // Check if file already exists
// if (file_exists($target_file)) {
//   echo "Sorry, file already exists.";
//   $uploadOk = 0;
// }

// // Check file size
// if ($_FILES["fileToUpload"]["size"] > 500000) {
//   echo "Sorry, your file is too large.";
//   $uploadOk = 0;
// }

// // Allow certain file formats
// if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
// && $imageFileType != "gif" ) {
//   echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
//   $uploadOk = 0;
// }

// // Check if $uploadOk is set to 0 by an error
// if ($uploadOk == 0) {
//   echo "Sorry, your file was not uploaded.";
// // if everything is ok, try to upload file
// } else {
//   if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
//     echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
//   } else {
//     echo "Sorry, there was an error uploading your file.";
//   }
// }
?>
