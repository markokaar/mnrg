<!DOCTYPE HTML>
<html>
	<head>
		<title>M.NRG Admin paneel</title>
		<link rel="icon" type="image/png" href="../img/favicon.ico">

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<!--<link rel='stylesheet' type='text/css' href='bootstrap/css/bootstrap.min.css'>
		<link rel='stylesheet' type='text/css' href='bootstrap/css/bootstrap.css'>-->
		<link rel='stylesheet' type='text/css' href='../css/webstyle.css'>
	</head>

	<body>
		<?php
		require_once("../connect.php");

		//$not_logged = false;
		$user_rights = 3;
		// 1 - opetajad
		// 2 - ?
		// 3 - admin
			if(!isset($_SESSION["username"]))
			{ 
				require("loginform.php");	
			}
			else
			{
				?>
				<div id='wrap'>
					<div id='menu'>
						<a href='index.php'><div class='menublock'>Avaleht</div></a>
						<a href='index.php?tunniplaan'><div class='menublock'>Tunniplaan</div></a>
						<a href='index.php?menyy'><div class='menublock'>Menüü</div></a>
						<a href='index.php?teated'><div class='menublock'>Teated</div></a>
							<?php if($user_rights == 3){ echo "<a href='index.php?new_user'><div class='menublock'>Lisa kasutaja</div></a>"; } ?>
						<a href='index.php?seaded'><div class='menublock'>Seaded</div></a>
						<a href='authenticate.php?destroy'><div class='menublock'>LOGI VÄLJA</div></a>
						<?php echo "&nbsp;&nbsp;<span style='font-size:16px; color:white'>Sisse logitud, kui <b>" . $_SESSION['username'] . "</b>.</span>"; ?>
					</div>

					
				
				<?php
			
				if(isset($_GET['tunniplaan'])){
					echo "<div id='content'>
						<h1>Tunniplaan</h1>
					</div>";
				}
				elseif(isset($_GET['menyy'])){
					echo "<div id='content'>
						<h1>Söökla Menüü</h1>
					</div>";
				}
				elseif(isset($_GET['teated'])){
					echo "<div id='content'>
						<h1>Teated</h1>
					</div>";
				}
				elseif(isset($_GET['new_user'])){
					$randompw = rand(100000,999999);
					echo "<div id='content'>
							<div class='contentleft'>
								<h1>Uue kasutaja lisamine admin paneelile</h1>
									<h2>Uus kasutaja</h2>
									<form method='POST' action='newuser.php'>
										<table border='0' align='left'>
											<tr><td>Kasutajanimi</td><td><input type='text' name='name'></td></tr>
											<tr><td>E-Mail</td><td><input type='text' name='email'></td></tr>
											<tr><td>Kasutaja Parool</td><td>" . $randompw . "<input type='hidden' value='" . $randompw . "' name='password'></td></tr>
											<tr><td>Sinu parool</td><td><input type='password' name='yourpassword'></td></tr>
											<tr><td colspan='2'><center><button type='submit'>Lisa kasutaja</button></center></td></tr>
										</table>
									</form>
							</div>
							<div class='contentright'>
							<h2>Kasutajad</h2>
							<table border='0'>
							<tr><td>ID</td><td>Nimi</td><td>E-Mail</td></tr>							";
							$stmt = $db->query('SELECT * FROM users');
							while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							    echo '<tr><td>'.$row['id'].'.</td><td>'.$row['name'].'</td><td>'.$row['email'].'</td></tr>'; //etc...
							}
				 	 echo "</table>
				 	 	</div>
						</div>";
				}
				elseif(isset($_GET['seaded'])){
					echo "<div id='content'>
						<h1>Kasutaja seaded</h1><br>
							<h2>Muuda parooli</h2>
							<form method='POST' action='newpw.php'>
								<table border='0' align='left'>
									<tr><td>Uus parool</td><td><input type='password' name='password'></td></tr>
									<tr><td>Uus Parool uuesti</td><td><input type='password' name='retypepassword'></td></tr>
									<tr><td>Vana parool</td><td><input type='password' name='oldpassword'></td></tr>
									<tr><td colspan='2'><center><button type='submit'>Muuda parooli</button></center></td></tr>
								</table>
							</form>
					</div>";
				}
				else{
					echo "<div id='content'>
						<h1>Avaleht</h1>
					</div>";
				}




				echo "</div>"; // Wrap
			}
				
		?>
	</body>
</html>