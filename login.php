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
<title><?php echo $title ?> - Panel Logowania</title>

<link href="assets/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Righteous" rel="stylesheet">

<link href="assets/css/style.css" rel="stylesheet">
</head>
<body onload="changesettings()">

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
			<?php
			if(!isset($_SESSION["login"]) || $_SESSION["login"]== 0)
			{
			?>
			<li class="nav-item active">
			<a class="nav-link" href="login.php">Zaloguj</a>
			</li>
			<li class="nav-item">
			<a class="nav-link" href="register.php">Załóż konto</a>
			</li>
			<?php
			} else 
			{
			?>
			<li class="nav-item">
			<a class="nav-link" href="logout.php">Wyloguj</a>
			</li>
			<?php
			}
			?>
		</ul>

	</div>
</div>
</nav>

<div class="container">
	<div class="mainheading">
		<h1 class="sitetitle"><?php echo $title ?></h1>
		<p class="lead">
			 <?php echo $description; ?>
		</p>
	</div>

	<section class="featured-posts">
	<div class="section-title">
		<h2><span>Zaloguj</span></h2>
	</div>
	<?php
	if(isset($_POST['login']) && !empty($_POST['login']) &&
	   isset($_POST['password']) && !empty($_POST['password'])
	){
		$login = stripslashes($_POST['login']);
		$login = $conn->real_escape_string($login);
		$password = md5($_POST['password']);
		$sql = "SELECT * FROM users WHERE BINARY login='$login' AND password='$password'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		$date=date_create(date("Y-m-d"));
		$f_date=date_format($date,"Y-m-d");
		if (($result) && ($result->num_rows > 0)) 
		{
			if ($row['active'] == 1) {
				if(!is_banned($conn, $row['id'])) {
					if($row['date_expire']>=$f_date)
					{
						$_SESSION["login"] = 1;
						$_SESSION["user_id"] = $row['id'];
						$_SESSION["user"] = $row['nick'];
						$_SESSION["cattegory"] = 0;
						$_SESSION["uniqid"] = $uniq_id = uniqid();
						$_SESSION["showall"] = 1;
						$_SESSION["name"] = $row['name'];
						$_SESSION['timestamp'] = time();
						$_SESSION['group_id'] = $row['group_id'];
						$_SESSION['userlogin'] = $row['login'];
						$_SESSION['permission'] = $row['permission'];
						$sql2 = "UPDATE users SET uniq_id='$uniq_id' WHERE login='$login' AND password='$password'";
						$result2 = $conn->query($sql2);
						
						header('Location: index.php');
					}
					else
					{
						echo "<b><font color='red'>Ważność konta wygasła! Opłać konto i aktywuj je ponownie!</font></b><br>";
						deactive_expires($conn, $row['id']);
					}
				}
				else {
					echo "<b><font color='red'>Konto zbanowane!</font></b><br>";
					echo get_ban_info($conn, $row['id']);
				}
			} else 
			{
				echo "<b>Konto nieaktywne!</b><br>Aby aktywować bądź re-aktywować konto opłać je i aktywuj ponownie!</font>";
			}				
		} 
		else
		{
			echo "Wprowadzono bledny login badz haslo!";
		}
	}
	$conn->close();
	?>
	<div class="loginpage">
	<form action="" method="POST">
		<input type="text" name="login" placeholder="Login" required>
		<input type="password" id="id_password" name="password" placeholder="Hasło" required>
		<i class="far fa-eye" id="togglePassword" style="margin-left: -30px; cursor: pointer;"></i>
		<button type="submit" style="margin-left: 1%;" class="btn login">Zaloguj</button>
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

<script>
const togglePassword = document.querySelector('#togglePassword');
  const password = document.querySelector('#id_password');

  togglePassword.addEventListener('click', function (e) {
    // toggle the type attribute
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    // toggle the eye slash icon
    this.classList.toggle('fa-eye-slash');
});
</script>

<script src="assets/js/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
