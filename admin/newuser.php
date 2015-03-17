<?php
require_once("../connect.php");

$name = $_POST['name'];
$salt = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5); //Loob suvalise 5-margilise salti
$password = "1337";
$newpassword = sha1($password + $salt);
$email = $_POST['email'];


$stmt = $db->prepare("INSERT INTO users(name,password,salt,email) VALUES(:field1,:field2,:field3,:field4)");
$stmt->execute(array(':field1' => $name, ':field2' => $newpassword, ':field3' => $salt, ':field4' => $email));
//$affected_rows = $stmt->rowCount();

header("Location: index.php?new_user");

?>