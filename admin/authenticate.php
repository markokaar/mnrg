<?php
	require_once('../connect.php');
	if(isset($_POST['username']) && isset($_POST['password'])){
		$username = $_POST['username'];
		$password = $_POST['password'];

		if(empty($username)){ header("Location: index.php?err=1"); exit;}
		if(empty($password)){ header("Location: index.php?err=2"); exit;}

		//Andmebaasist id,name,passwordi ja salti votmine, USERNAME JÄRGI OTSIB!
		$sth = $db->prepare('SELECT id, name, password, salt FROM users WHERE name=:username LIMIT 1');
		$sth->execute(array(':username' => $username));
		$user = $sth->fetch();

		//Kui kasutajanime ei leitud andmebaasist
		if(!$user){	header("Location: index.php?err=3"); exit;}

		//Parooli andmebaasis ja kasutaja sisestatud parooli võrdlus
		$dbpassword = sha1($password + $user['salt']);
		if($user['password'] != $dbpassword){ header("Location: index.php?err=4"); exit;}

		$_SESSION["username"] = $username;
		header("Location: index.php");
	}

	//Logout
	if(isset($_GET['destroy'])){
		session_destroy();
		header("Location: index.php");
	}
	header("Location: index.php");
?>