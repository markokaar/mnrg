<center>
	<div id='loginbox'>
		<h2>Logi sisse</h2>
		<form method='POST' action='authenticate.php'>
			<table border='0' align='center'>
				<tr><td>Kasutajanimi</td><td><input type='text' name='username'></td></tr>
				<tr><td>Parool</td><td><input type='password' name='password'></td></tr>
				<tr><td colspan='2'><center><button type='submit'>Logi sisse</button></center></td></tr>
			</table>
		</form>

		<?php
		if(isset($_GET['err'])){
			if($_GET['err'] == 1){
				echo "<span style='color:red'>Kasutajanimi on sisestamata!</span>";
				}
				if($_GET['err'] == 2){
					echo "<span style='color:red'>Parool on sisestamata!</span>";
				}
				if($_GET['err'] == 3){
					echo "<span style='color:red'>Vale kasutajanimi!</span>";
				}
				if($_GET['err'] == 4){
					echo "<span style='color:red'>Vale parool!</span>";
				}
		}
		?>
	</div>
</center>