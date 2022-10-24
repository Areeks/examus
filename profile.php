<?php 
require 'assets/config/connectmysqlwsb.php';
require 'assets/config/configwsb.php';
require 'assets/config/functions.php';
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" href="assets/img/favicon.ico">
<title><?php echo $title ?> - Profil</title>

<link href="assets/css/bootstrap.min.css" rel="stylesheet">

<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Righteous" rel="stylesheet">

<link href="assets/css/style.css" rel="stylesheet">
<script src="assets/js/include.js"></script>
</head>
<?php
$get_uid = get_uniq_id($conn, $_SESSION["user_id"]);
if(isset($_SESSION["login"]))
{
	logout_time(); // funkcja logout po czasie
	check_uniqid($get_uid); // sprawdzenie uid

$userek = $_SESSION["user_id"];
?>
<nav class="navbar navbar-toggleable-md navbar-light bg-white fixed-top mediumnavigation">
<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
<span class="navbar-toggler-icon"></span>
</button>
<div class="container">

	<a class="navbar-brand" href="index.php">
	<img src="assets/img/logo.png" alt="logo">
	</a>

	<div class="collapse navbar-collapse" id="navbarsExampleDefault">

		<ul class="navbar-nav ml-auto">
			<li class="nav-item ">
			<a class="nav-link<?php echo (basename($_SERVER['PHP_SELF']) == "index.php") ? " active" : "";?>" href="index.php">Powrót</a>
			</li>
			<?php
				if($_SESSION['permission'] > 0) {?>
				<li class="nav-item">
				<a class="nav-link" href="godmode.php"><font color="red"><b>ADMIN</b></font></a>
				</li>
				<?php } ?>
			<li class="nav-item">
			<a class="nav-link" href="logout.php">Wyloguj</a>
			</li>
			<li>
			<div style="display: flex; padding-top: 5px; padding-left: 5px;">
			<p id="mins">59</p>
			<p id="dwukropek">:</p>
			<p id="secs">00</p>
			<h5 id="end" style="color: red;"></h5>
			</div>
			</li>
		</ul>

	</div>
</div>
</nav>

<div class="container">
	<div class="mainheading">
		<h1 class="sitetitle"><?php echo $title;?> - do usług :)</h1>
		<p class="lead">
			 <?php echo $description; ?>
		</p>
	</div>

	<section class="featured-posts">
	<div class="section-title">
		<h2><span>Profil</span></h2>
	</div>
	<?php
	if(isset($_POST['oldpassword']) && !empty($_POST['oldpassword'])){
		if(check_password($conn, $_SESSION["user_id"], $_POST['oldpassword']))
		{
			if(isset($_POST['password']) && !empty($_POST['password']))
			{
				$uid = $_SESSION["user_id"];
				$pass = $_POST['password'];
				$sql = "UPDATE users SET password=MD5('$pass') WHERE id='$uid'";
				$result = $conn->query($sql);
				echo '<center><font color="green"><b>Hasło zostało zmienione</b></font></center>';
			}
			if(isset($_POST['nick']) && !empty($_POST['nick']))
			{
				if(free_nick($conn, $_POST['nick'], $_SESSION["user_id"]))
				{
					$uid = $_SESSION["user_id"];
					$nick = $conn->real_escape_string($_POST['nick']);
					$sql = "UPDATE users SET nick='$nick' WHERE id='$uid'";
					$result = $conn->query($sql);
				} else echo '<center><font color="red"><b>Nick został juz wykorzystany!</b></font></center>';
			}
			if(isset($_POST['groups']) && !empty($_POST['groups']))
			{
				$uid = $_SESSION["user_id"];
				$group_id = $conn->real_escape_string($_POST['groups']);
				$sql = "UPDATE users SET group_id='$group_id' WHERE id='$uid'";
				$result = $conn->query($sql);
				$_SESSION['group_id'] = $group_id;
			}
		}
		else 
		{
			echo '<center><font color="red"><b>Wprowadzono złe hasło!</b></font></center>';
		}
	}
	if(isset($_POST['delpassword']) && !empty($_POST['delpassword']))
	{
		if(check_password($conn, $_SESSION["user_id"], $_POST['delpassword']))
		{
			del_user($conn, $_SESSION["user_id"]);
			save_log($conn, "Autoskasowane konto: ".$_SESSION['userlogin']." - ".$_SESSION['name']);
			header('Location: logout.php');
		}
		else echo '<center><font color="red"><b>Wprowadzono złe hasło!</b></font></center>';
	}
	?>
	<div class="loginpage settings">
	<form action="" method="POST" class="settings_form">
		<label for="groups">Grupa:</label>
		<?php
			if($_SESSION["group_id"] == 0)
			{
				?>
					<select name="groups" id="groups">
					<?php
					$resultek = get_all_groups($conn);
					while($row = $resultek->fetch_assoc())
					{
					?>
						<option value="<?php echo $row["id"]; ?>"><?php echo $row["name"]; ?></option>
					<?php
					}
					?>
					</select>
				<?php
			}
			else 
			{
		?>
				<input type="text" name="login" value="<?php echo get_group_name($conn, $_SESSION["group_id"]); ?>" disabled>
		<?php
			}
		?>
		<label for="login">Login:</label>
		<input type="text" name="login" value="<?php echo get_login($conn, $userek); ?>" disabled>
		<label for="login">Imię i Nazwisko:</label>
		<input type="text" name="namesurname" value="<?php echo get_name($conn, $userek); ?>" disabled>
		<label for="password">Zmień hasło:</label>
		<input type="password" name="password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-]).{8,20}$" placeholder="Nowe hasło" oninvalid="this.setCustomValidity('Hasło od 8 do 20 znaków. Mała litera, duża litera, cyfra i znak specjany')"
  oninput="this.setCustomValidity('')"/>
		<label for="nick">Nick:</label>
		<input type="text" name="nick" value="<?php echo get_nickname($conn, $userek); ?>">
		<label for="discordkey">Ważność konta</label>
		<input type="text" name="discordkey" value="<?php echo get_activate($conn, $userek); ?>" disabled>
		<label for="password">Stare hasło:</label>
		<input type="password" name="oldpassword" placeholder="Wprowadź stare hasło" required>
		<button type="submit" style="margin-left: 25%; margin-top: 5%;" class="btn login">Zmień</button>
		<button type="button" id="delbutton" style="background-color: red; margin-left: 18%; margin-top: 15%;" class="btn login">Usuń konto</button>
	</form>
	</div>
	</section>
	<section class="featured-posts" id="deldiv" style="display: none;">
	<div class="section-title" class="settings">
		<h2><span>Skasuj konto</span></h2>
		</div>
		<div class="loginpage settings">
		<form action="" method="POST" class="settings_form">
		<label for="password">Hasło:</label>
		<input type="password" name="delpassword" id="delpassword" placeholder="Wprowadź hasło">
		<button type="submit" style="margin-left: 18%; margin-top: 5%;" class="btn login" onclick="return confirm('Czy jesteś pewien?\nTwoje konto zostanie skasowane nieodwracalnie!')">Skasuj konto</button>
		</form>
	</div>
	</section>
	<section class="featured-posts">
	<div class="section-title">
		<h2><span>Statystyki</span></h2>
	</div>
	<div class="loginpage settings">
	<form action="" method="POST" class="settings_form">
		<label for="questions">Wrzucone pytania:</label>
		<input type="questions" value="<?php echo get_user_all_q($conn, $userek); ?>" disabled>
		<label for="answers">Udzielone odpowiedzi:</label>
		<input type="answers" value="<?php echo get_user_all_a($conn, $userek); ?>" disabled>
	</form>
	</div>
	</section>
	
<div class="alertbar">
	<div class="container text-center">
		<img src="assets/img/logo.png" alt=""> &nbsp; Copyright &copy; <?php echo $year . " " . $author;  ?>
	</div>
</div>

	<div class="footer">
		
		<div class="clearfix">
		</div>
	</div>


</div>
<?php
$conn->close();
?>
<script>
const div = document.getElementById('deldiv');
const btn = document.getElementById('delbutton');
const passinpt = document.getElementById('delpassword');

btn.addEventListener('click', function handleClick() {
  if (div.style.display === 'none') {
    div.style.display = 'block';
	passinpt.setAttribute('required', '');
  } else {
    div.style.display = 'none';
	passinpt.removeAttribute('required');
  }
});


</script>
<script src="assets/js/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
<?php
}
else 
{
	header('Location: login.php');
}
?>