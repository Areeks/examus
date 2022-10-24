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
<title><?php echo $title ?> - Dodaj pytanie</title>

<link href="assets/css/bootstrap.min.css" rel="stylesheet">

<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Righteous%7CMerriweather:300,300i,400,400i,700,700i" rel="stylesheet">

<link href="assets/css/style.css" rel="stylesheet">
<script src="assets/js/include.js"></script>
</head>
<?php
$get_uid = get_uniq_id($conn, $_SESSION["user_id"]);
if(isset($_SESSION["login"]))
{
	logout_time(); // funkcja logout po czasie
	check_uniqid($get_uid); // sprawdzenie uid
	$category_id = $_SESSION["cattegory"];
?>
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
			<?php show_menu(); ?>
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
		<h1 class="sitetitle"><?php echo $title; ?> - do usług :)</h1>
		<p class="lead">
			 Dodawanie pytania
		</p>
	</div>

<div class="container">
	<div class="row">

	
			
			<div class="article-post" <?php if(isset($_POST['question']) && !empty($_POST['question'])) echo 'style="visibility: hidden; margin-bottom: -10%;"'; ?>>
				<form action="" method="POST">
					<input type="text" name="question" size="50" placeholder="Wprowadź pytanie" required>
					<label for="multiquestion">Pytanie powtarzające się?</label>
					<input type="checkbox" name="multiquestion" value="1" <?php if(isset($_POST['multiquestion']) && $_POST['multiquestion'] == 1) echo 'checked="checked"' ?>>
					<button type="submit" class="btn add">Dodaj pytanie</button>
				</form>
			</div>
			
<?php
if(
	isset($_POST['question_add']) && !empty($_POST['question_add']) &&
	isset($_POST['q_a']) && (!empty($_POST['q_a']) || $_POST['q_a'] == 0) &&
	isset($_POST['q_b']) && (!empty($_POST['q_b']) || $_POST['q_b'] == 0) &&
	isset($_POST['q_c']) && isset($_POST['q_d']) && isset($_POST['q_e']) 
)
{
	if(!empty($_POST['question_img']))
	{
		$imglink = prntscr_parse($_POST['question_img']);
	}
	else $imglink = "";
	$userek_id = (int)$_SESSION["user_id"];
	$text = $conn->real_escape_string($_POST['question_add']);
	if(isset($_POST['multianswer']) && $_POST['multianswer'] == 1) $multianswer=1;
	else $multianswer=0;
	if(isset($_POST['multiquestion']) && $_POST['multiquestion'] == 1) $multiquestion=1;
	else $multiquestion=0;
	$a_answer = $conn->real_escape_string($_POST['q_a']);
	$b_answer = $conn->real_escape_string($_POST['q_b']);
	$c_answer = $conn->real_escape_string($_POST['q_c']);
	$d_answer = $conn->real_escape_string($_POST['q_d']);
	$e_answer = $conn->real_escape_string($_POST['q_e']);
	$sql = "INSERT INTO question (category_id, user_id, text, img_link, answer_a, answer_b, answer_c, answer_d, answer_e, count_a, count_b, count_c, count_d, count_e, multianswer)
	VALUES ('$category_id', '$userek_id', '$text', '$imglink', '$a_answer', '$b_answer', '$c_answer' , '$d_answer', '$e_answer', '0','0','0','0','0','$multianswer')";
	
	$text = $conn->real_escape_string($text);
	$sql2 = "SELECT id, category_id FROM question WHERE text='$text'";
	$result = $conn->query($sql2);
	if ($result->num_rows > 0) {
		$check = false;
		while($rowek = $result->fetch_assoc()) {
			if($rowek['category_id'] == $category_id) {
				$check = true;
				$idek = $rowek['id'];
			}				
		}
		if($multiquestion) $check = false;
		if($check)
		{	
			echo "<script>isQuestion($idek)</script>";
		}
		else 
		{
			$result = $conn->query($sql);
			save_log($conn, "Dodano pytanie: $text - ".$_SESSION['name']." - ".$_SESSION['userlogin']);
			header('Location: index.php');
		}
	}
	else
	{
		$result = $conn->query($sql);
		save_log($conn, "Dodano pytanie: $text - ".$_SESSION['name']." - ".$_SESSION['userlogin']);
		header('Location: index.php');
	}

}

if(isset($_POST['question']) && !empty($_POST['question']))
{
	$text = $conn->real_escape_string($_POST['question']);
	if(isset($_POST['multiquestion']) && $_POST['multiquestion'] == 1) $multiquestion = 1;
	else $multiquestion = 0;
	$sql = "SELECT id, category_id FROM question WHERE text='$text'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$check = false;
		while($rowek = $result->fetch_assoc()) {
			if($rowek['category_id'] == $category_id) 
			{
				$check = true;
				$idek = $rowek['id'];
			}
		}
		if($multiquestion) $check = false;
		if($check)
		{	
			echo "<script>isQuestion($idek)</script>";
		}
		else 
		{
			?>
			<script src="assets/js/lightshot.js"></script>
	<form action="" style="display: grid;" method="POST">
					 <label for="question_add">Pytanie:</label>
					 <input type="hidden" name="multiquestion" value="<?php echo $multiquestion; ?>">
					<input type="text" name="question_add" size="50" value="<?php echo htmlspecialchars(str_replace('\"', '"', str_replace("\'", "'", $text))); ?>" required>
					<label for="multianswer">Pytanie wielokrotnego wyboru?</label>
					<input type="checkbox" name="multianswer" value="1">
					<label for="question_img">Link do IMG:</label>
					<input type="text" name="question_img" size="50">
					 <label for="q_a">a:</label>
					<input type="text" name="q_a" size="50" required>
					
					<label for="q_b">b:</label>
					<input type="text" name="q_b" size="50" required>
					
					<label for="q_c">c:</label>
					<input type="text" name="q_c" size="50">
					
					<label for="q_d">d:</label>
					<input type="text" name="q_d" size="50">
					
					<label for="q_e">e:</label>
					<input type="text" name="q_e" size="50">
					<br>
					<button type="submit" class="btn addq">Dodaj</button>
				</form>
			<?php
		}
	}
	else
	{
?>




<script src="assets/js/lightshot.js"></script>
	<form action="" style="display: grid;" method="POST">
					 <label for="question_add">Pytanie:</label>
					 <input type="hidden" name="multiquestion" value="<?php echo $multiquestion; ?>">
					<input type="text" name="question_add" size="50" value="<?php echo htmlspecialchars(str_replace('\"', '"', str_replace("\'", "'", $text))); ?>" required>
					<label for="multianswer">Pytanie wielokrotnego wyboru?</label>
					<input type="checkbox" name="multianswer" value="1">
					<label for="question_img">Link do IMG:</label>
					<input type="text" name="question_img" size="50">
					 <label for="q_a">a:</label>
					<input type="text" name="q_a" size="50" required>
					
					<label for="q_b">b:</label>
					<input type="text" name="q_b" size="50" required>
					
					<label for="q_c">c:</label>
					<input type="text" name="q_c" size="50">
					
					<label for="q_d">d:</label>
					<input type="text" name="q_d" size="50">
					
					<label for="q_e">e:</label>
					<input type="text" name="q_e" size="50">
					<br>
					<button type="submit" class="btn addq">Dodaj</button>
				</form>
				
		
		
		
<?php

}
}
	
	
?>
		<?php
$conn->close();

?>

		</div>


	</div>
</div>

<div class="alertbar">
	<div class="container text-center">
		<img src="assets/img/logo.png" alt=""> &nbsp; Copyright &copy; <?php echo $year . " " . $author;  ?>
	</div>
</div>


	<div class="footer">
		<div class="clearfix">
		</div>
	</div>

<script src="assets/js/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/ie10-viewport-bug-workaround.js"></script>
<script src="assets/js/mediumish.js"></script>
</body>
</html>
<?php
}
else 
{
	header('Location: login.php');
}
?>