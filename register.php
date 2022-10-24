<?php 
require 'assets/config/connectmysqlwsb.php';
require 'assets/config/configwsb.php';
require 'assets/config/functions.php';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" href="assets/img/favicon.ico">
<title><?php echo $title ?> - Rejestracja</title>

<link href="assets/css/bootstrap.min.css" rel="stylesheet">

<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Righteous%7CMerriweather:300,300i,400,400i,700,700i" rel="stylesheet">

<link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

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
			<a class="nav-link" href="login.php">Zaloguj</a>
			</li>
			<li class="nav-item active">
			<a class="nav-link" href="register.php">Załóż konto</a>
			</li>
		</ul>
	</div>
</div>
</nav>

<div class="container">
	<div class="mainheading">
		<h1 class="sitetitle"><?php echo $title ?></h1>
		<p class="lead">
			 Załóż konto
		</p>
	</div>

<div class="container">
<?php
if(
(isset($_POST['nickname']) && !empty($_POST['nickname'])) &&
(isset($_POST['password']) && !empty($_POST['password'])) &&
(isset($_POST['name']) && !empty($_POST['name'])) &&
(isset($_POST['nick']) && !empty($_POST['nick']))
)
{
	$nick = stripslashes($_POST['nickname']);
	$nick = $conn->real_escape_string($nick);
	$nazwa = stripslashes($_POST['nick']);
	$nazwa = $conn->real_escape_string($nazwa);
	$password = stripslashes($_POST['password']);
	$pass = $conn->real_escape_string($password);
	$nickname = $conn->real_escape_string($_POST['name']);
	$group_id = 1;
	$date=date_create(date("Y-m-d"));
	date_modify($date,"-1 day");
	$final_date = date_format($date,"Y-m-d");
	if(free_user($conn, $nick))
	{
			if(free_nick($conn, $nazwa))
			{
				$sql = "INSERT INTO users (login, password, name, uniq_id, date_expire, active, nick, group_id)
					VALUES ('$nick', MD5('$pass'), '$nickname', '', DATE('$final_date'), '0','$nazwa', '$group_id')";
				$result = $conn->query($sql);
				echo '<font color="green"><b>Konto założone pomyślnie!</b></font>';
			}
			else {
				echo '<font color="red"><b>Nick został juz wykorzystany!</b></font>';
			}
	}
	else
	{
		echo '<font color="red"><b>Login został juz wykorzystany!</b></font>';
	}
	

}

?>
<?php
$min  = 1;
$max  = 50;
$num1 = rand( $min, $max );
$num2 = rand( $min, $max );
$sum  = $num1 + $num2;
?>
	<div class="row">

	<style>
	form { margin: 0 auto; }
	form label { display: inline-block; width: 170px; } 
	</style>
			
			<div class="article-post">
				<form action="" method="POST">
					<label for="nickname">Login:</label>
					<input type="text" name="nickname" required>
					<br>
					<label for="password">Hasło:</label>
					<input type="password" name="password" id="password"  onkeyup='check();' pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-]).{8,20}$" required oninvalid="this.setCustomValidity('Hasło od 8 do 20 znaków. Mała litera, duża litera, cyfra i znak specjany')"
  oninput="this.setCustomValidity('')"/>
					<br>
					<label for="confirm_password">Powtórz hasło:</label>
					<input type="password" name="confirm_password" id="confirm_password"  onkeyup='check();' pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-]).{8,20}$" required oninvalid="this.setCustomValidity('Hasło od 8 do 20 znaków. Mała litera, duża litera, cyfra i znak specjany')"
  oninput="this.setCustomValidity('')"/>
					<br>
					<label for="name">Imię i Nazwisko:</label>
					<input type="text" name="name" pattern="^[A-Z-zóąśłżźćńÓĄŚŁŻŹĆŃ][a-z-zóąśłżźćńÓĄŚŁŻŹĆŃ]{2,20} [A-Z-zóąśłżźćńÓĄŚŁŻŹĆŃ][a-z-zóąśłżźćńÓĄŚŁŻŹĆŃ]{1,25}$" required oninvalid="this.setCustomValidity('Przykład: Jan Kowalski')"
  oninput="this.setCustomValidity('')"/>
					<br>
					<label for="nick">Nick:</label>
					<input type="text" name="nick" required>
					<br>
					<label for="quiz" class="col-sm-3 col-form-label">
					<?php echo $num1 . '+' . $num2; ?>?
					</label>
					<div class="col-sm-4">
						<input type="text" class="form-control quiz-control" id="quiz">
					</div>
					<button data-res="<?php echo $sum; ?>" type="submit" class="btn add" disabled>Dodaj</button>
				</form>
			</div>

		</div>


	</div>
</div>
<?php
$conn->close();
?>
<div class="alertbar">
	<div class="container text-center">
		<img src="assets/img/logo.png" alt=""> &nbsp; Copyright &copy; <?php echo $year . " " . $author;  ?>
	</div>
</div>


	<div class="footer">
		<div class="clearfix">
		</div>
	</div>
<script>
const submitButton = document.querySelector('[type="submit"]');
const quizInput = document.querySelector(".quiz-control");
quizInput.addEventListener("input", function(e) {
    const res = submitButton.getAttribute("data-res");
    if ( this.value == res ) {
        submitButton.removeAttribute("disabled");
    } else {
        submitButton.setAttribute("disabled", "");
    }
});

var password = document.getElementById("password")
  , confirm_password = document.getElementById("confirm_password");

function validatePassword(){
  if(password.value != confirm_password.value) {
    confirm_password.setCustomValidity("Hasła się nie zgadzają!");
  } else {
    confirm_password.setCustomValidity('');
  }
}

password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;
</script>

<script src="assets/js/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/ie10-viewport-bug-workaround.js"></script>
<script src="assets/js/mediumish.js"></script>
</body>
</html>
