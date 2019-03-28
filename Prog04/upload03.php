<?php

// see HTML form (customers.html) for overview of this program

session_start();

//Controls the seesion.  If
if(!isset($_SESSION["tJHSQRuoNnWUwLRe"])) { // if "user" not set,
    session_destroy();
    header('Location: login.php');     // go to login page
    exit();
}
// include code for database access
require 'database.php';

// set PHP variables from data in HTML form
$fileName       = $_FILES['Filename']['name'];
$tempFileName   = $_FILES['Filename']['tmp_name'];
$fileSize       = $_FILES['Filename']['size'];
$fileType       = $_FILES['Filename']['type'];
$id             = $_GET['id'];
$fileLocation   = "uploads/";
$fileFullPath   = $fileLocation . $fileName;

if (!file_exists($fileLocation))
    mkdir ($fileLocation); // create subdirectory, if necessary

// abort if no filename
if (!$fileName) {
   die("No filename.");
}

//if all of above is okay, then upload the file
//from https://stackoverflow.com/questions/9858861/move-uploaded-file-wont-replace-existing-image
if(file_exists($fileFullPath)) {
    chmod($fileFullPath,0755); //Change the file permissions if allowed
    unlink($fileFullPath); //remove the file
}
$result = move_uploaded_file($tempFileName, $fileFullPath);
// abort if file is not an image
// never assume the upload succeeded
if ($_FILES['Filename']['error'] !== UPLOAD_ERR_OK) {
   die("Upload failed with error code " . $_FILES['file']['error']);
}
$info = getimagesize($fileFullPath);
if ($info === FALSE) {
   die("Error Unable to determine <i>image</i> type of uploaded file");
}
if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG)
        && ($info[2] !== IMAGETYPE_PNG)) {
   die("Not a gif/jpeg/png");
}

// abort if file is too big
if($fileSize > 2000000) { echo "Error: file exceeds 2MB."; exit(); }

// fix slashes in $fileType variable, if necessary
//$fileType=(get_magic_quotes_gpc()==0 ? mysqli_real_escape_string(
//$_FILES['Filename']['type']) : mysqli_real_escape_string(
//stripslashes ($_FILES['Filename'])));

// put the content of the file into a variable, $content
$fp      = fopen($fileFullPath, 'r');
$content = fread($fp, filesize($fileFullPath));
$content = addslashes($content);
fclose($fp);

// no longer needed - feature removed from php
// http://php.net/manual/en/function.get-magic-quotes-gpc.php
// restore slashes in $fileType variable, if necessary
if(!get_magic_quotes_gpc()) { $fileName = addslashes($fileName); }

// connect to database
$pdo = Database::connect();

// insert file info and content into table
$sql = "UPDATE customers
        SET filename = '$fileName', filesize = '$fileSize',
            filetype = '$fileType', filecontents = '$content',
            description = '$fileFullPath'
        WHERE id =$id";
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$q = $pdo->prepare($sql);
$q->bindValue("filename", $fileName);
$q->bindValue("filesize", $fileSize);
$q->bindValue("filetype", $fileType);
$q->bindValue("filecontests", $content);
$q->bindValue("description", $fileFullPath);
$q->execute();


// disconnect
Database::disconnect();
header("Location: customers.php");
