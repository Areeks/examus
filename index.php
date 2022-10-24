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
<title><?php echo $title ?></title>

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
	check_expires($conn, $_SESSION["user_id"]); // sprawdzanie czy konto nie wygaslo
	
	if(isset($_POST['idq']) && !is_null($_POST['idq']))  $_SESSION["cattegory"] = $_POST['idq'];
	if(isset($_POST['allquestion']) && !is_null($_POST['allquestion']))
	{
		if($_POST['allquestion'] == 0) $_SESSION["showall"] = 0;
		if($_POST['allquestion'] == 1) $_SESSION["showall"] = 1;
	}
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
			<li class="nav-item">
			<a class="nav-link" href="quiz.php">Quiz</a>
			</li>
			<li class="nav-item active">
			<a class="nav-link" href="index.php"><?php echo $_SESSION["cattegory"]==0 ? 'Kategorie' : 'Pytania';?></a>
			</li>
			<?php
			if(isset($_SESSION["cattegory"]) && $_SESSION["cattegory"]!= 0)
			{
			?>
			<li class="nav-item">
			<a class="nav-link" href="addquestion.php">Dodaj pytanie</a>
			</li>
			<?php
			}
			?>
			<?php
			if(!isset($_SESSION["login"]) || $_SESSION["login"]== 0)
			{
			?>
			<li class="nav-item active">
			<a class="nav-link" href="login.php">Zaloguj</a>
			</li>
			<?php
			} else 
			{
			?>
			<li class="nav-item">
			<a class="nav-link" href="profile.php">Profil</a>
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
			<?php
			}
			?>
		</ul>

	</div>
</div>
</nav>

<div class="container">
	<div class="mainheading">
		<h1 class="sitetitle"><?php echo $title; ?> - do usług :)</h1>
<?php
$category_id = $_SESSION["cattegory"];
if($category_id == 0)
{
?>
<p class="lead">
			 Wybierz kategorię
		</p>
	</div>
<?php
$sqlcat = "SELECT * FROM category WHERE hidden = 0 ORDER BY id ASC";
$resultcat = $conn->query($sqlcat);
if($resultcat->num_rows > 0)
{
while($rowcat = $resultcat->fetch_assoc()) {
?>
<section class="featured-posts">
	<div class="listfeaturedtag">
	<div class="card">
			<div class="row">
				<div class="col-md-5 wrapthumbnail">
						<div class="thumbnail" style="background-image:url(assets/img/categories.jpg);">
						</div>
				</div>
				<div class="col-md-7">
					<div class="card-block">
						<h2 class="card-title"><?php echo $rowcat['name'];  ?></h2>
						<h4 class="card-text">Ilość pytań: <b><?php echo get_numbers_question($conn, $rowcat['id']); ?></b></h4>
						<h4 class="card-text">Pytania z udzielonymi odpowiedziami: <b><?php echo get_numbers_question_answer($conn, $rowcat['id']); ?></b></h4>
						<div class="metafooter">
							<div class="wrapfooter">
							<form action="" method="POST">
							<input type="hidden" name="idq" value="<?php echo $rowcat['id']; ?>">
						<button type="submit" class="btn addq">Wybierz</button>
						</form>
								<span class="meta-footer-thumb">
								</span>
								<span class="author-meta">
								<span class="post-name"></span><br/>
								<span class="post-date"></span>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php
}
}	
}
else
{
?>
		<p class="lead">
			 Lista pytań
		</p>
	</div>
<?php
$user_id = $_SESSION["user_id"];
$sqlcat = "SELECT * FROM category where id=$category_id";
$resultcat = $conn->query($sqlcat);
$rowcat = $resultcat->fetch_assoc();
if($rowcat > 0)
{
?>
	<section class="featured-posts">
	<div class="section-title">
		<h2><font color="green"><?php echo $rowcat['name']; ?></font></h2>
		<div class="section-title" style="display: flex; justify-content: space-between;">
						<span>
						<form action="" method="POST">
							<input type="hidden" name="idq" value="0">
						<button type="submit" class="btn addq">Zmień Kategorię</button>
						</form>
						</span>
						<?php if($_SESSION["showall"] == 1){ ?>
						<span>
						<form action="" method="POST">
							<input type="hidden" name="allquestion" value="0">
						<button type="submit" class="btn addq">Moje pytania</button>
						</form>
						</span>
						<?php }
						else {
							?>
							<span>
						<form action="" method="POST">
							<input type="hidden" name="allquestion" value="1">
						<button type="submit" class="btn addq">Wszystkie pytania</button>
						</form>
						</span>
						<?php } ?>
						<span>
						<button onclick="location.href='addquestion.php'" type="button" class="btn addq">Dodaj Pytanie</button>
						</span>
						</div>
						
	</div>
	<div class="listfeaturedtag">
<?php
if(isset($_POST['id']) && !empty($_POST['id']))
{
	$id = (int)$_POST['id'];
	if(isset($_POST['q1']))
	{
		if(!check_answer_clicked($conn, $id, $user_id, 1))
		{
			$sql = "UPDATE question SET count_a = count_a + 1 WHERE id=$id";
			$sql1 = "INSERT INTO answer (question_id, answer_id, user) VALUES ('$id', '1', '$user_id')";
			$result = $conn->query($sql);
			$result = $conn->query($sql1);
		}
	}
	if(isset($_POST['q2']))
	{
		if(!check_answer_clicked($conn, $id, $user_id, 2))
		{
			$sql = "UPDATE question SET count_b = count_b + 1 WHERE id=$id";
			$sql1 = "INSERT INTO answer (question_id, answer_id, user) VALUES ('$id', '2', '$user_id')";
			$result = $conn->query($sql);
			$result = $conn->query($sql1);
		}
	}
	if(isset($_POST['q3']))
	{
		if(!check_answer_clicked($conn, $id, $user_id, 3))
		{
			$sql = "UPDATE question SET count_c = count_c + 1 WHERE id=$id";
			$sql1 = "INSERT INTO answer (question_id, answer_id, user) VALUES ('$id', '3', '$user_id')";
			$result = $conn->query($sql);
			$result = $conn->query($sql1);
		}
	}
	if(isset($_POST['q4']))
	{
		if(!check_answer_clicked($conn, $id, $user_id, 4))
		{
			$sql = "UPDATE question SET count_d = count_d + 1 WHERE id=$id";
			$sql1 = "INSERT INTO answer (question_id, answer_id, user) VALUES ('$id', '4', '$user_id')";
			$result = $conn->query($sql);
			$result = $conn->query($sql1);
		}
	}
	
	if(isset($_POST['q5']))
	{
		if(!check_answer_clicked($conn, $id, $user_id, 5))
		{
			$sql = "UPDATE question SET count_e = count_e + 1 WHERE id=$id";
			$sql1 = "INSERT INTO answer (question_id, answer_id, user) VALUES ('$id', '5', '$user_id')";
			$result = $conn->query($sql);
			$result = $conn->query($sql1);
		}
	}
	
	
	if(isset($_POST['del']))
	{
		$sql = "SELECT answer_id from answer WHERE user='$user_id' AND question_id='$id'";
		$result = $conn->query($sql);
		if($result && $result->num_rows > 0)
		{
			while($row = $result->fetch_assoc()) {
				if($row['answer_id'] == 1)
				{
					$sql = "UPDATE question SET count_a = count_a - 1 WHERE id=$id";
				}
				else if($row['answer_id'] == 2)
				{
					$sql = "UPDATE question SET count_b = count_b - 1 WHERE id=$id";
				}
				else if($row['answer_id'] == 3)
				{
					$sql = "UPDATE question SET count_c = count_c - 1 WHERE id=$id";
				}
				else if($row['answer_id'] == 4)
				{
					$sql = "UPDATE question SET count_d = count_d - 1 WHERE id=$id";
				}
				else if($row['answer_id'] == 5)
				{
					$sql = "UPDATE question SET count_e = count_e - 1 WHERE id=$id";
				}
				$resultquery = $conn->query($sql);
				$sql = "DELETE FROM answer WHERE user='$user_id' AND question_id='$id'";
				$resultquery2 = $conn->query($sql);
			}
		}
	}
		
}


if($_SESSION["showall"])
$sql = "SELECT * FROM question WHERE category_id=$category_id AND hidden=0";
else
$sql = "SELECT * FROM question WHERE category_id=$category_id AND user_id=$user_id AND hidden=0";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
	$max = max($row['count_a'], $row['count_b'], $row['count_c'], $row['count_d'], $row['count_e']); 
	if($max==0) $max=-1;
	$idq = $row['id'];
	$sqlquestion = "SELECT * FROM answer WHERE question_id='$idq' AND user='$user_id'";
	$resultq = $conn->query($sqlquestion);
	$rowq = $resultq->fetch_assoc();
	$userqid = $row['user_id'];
	$sqluser = "SELECT nick, name FROM users WHERE id='$userqid'";
	$resutuser = $conn->query($sqluser);
	$rowuser = $resutuser->fetch_assoc();
	if(!isset($rowuser['name'])) $rowuser['name'] = "Admin";
	if(!isset($rowuser['nick'])) $rowuser['nick'] = "<u>Użytkownik usunięty</u>";
	if((!$row['multianswer'] && $rowq > 0) || ($row['multianswer']) && $resultq->num_rows >= check_count_answer($conn, $idq)) $checked = true; // sprawdzenie czy uzytkownik dal odpowiedz
	else $checked = false; // tu też
	if($rowq > 0) $checkdel = true;
	else $checkdel = false;
?>
		<div id="catid<?php echo $row['id']; ?>" class="card">
			<div class="row">
				<div class="col-md-5 wrapthumbnail">
					<a href="<?php echo $row['img_link']; ?>" target="_blank">
						<div class="thumbnail" style="background-image:url(<?php echo $row['img_link']; ?>);">
						</div>
					</a>
				</div>
				<div class="col-md-7">
					<div class="card-block">
					<form action="index.php#catid<?php echo $row['id']; ?>" method="POST">
						<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
						<h2 class="card-title"><?php echo $row['text']; ?></h2>
						<?php if(!empty($row['answer_a']) || $row['answer_a'] == 0) { ?>
						<div class="inline">
						<h4 class="card-text"><?php echo ( $row['count_a'] == $max) ? '<font color="green"><b>' : '<font color="black">';  ?><?php echo strip_tags($row['answer_a']); ?></b></font></h4><button type="submit" name="q1" <?php echo ($checked) ? 'disabled' : '' ; ?>><?php echo $row['count_a']; ?></button>
						</div><?php } ?>
						<?php if(!empty($row['answer_b']) || $row['answer_b'] == 0) { ?>
						<div class="inline">
						<h4 class="card-text"><?php echo ( $row['count_b'] == $max) ? '<font color="green"><b>' : '<font color="black">';  ?><?php echo strip_tags($row['answer_b']); ?></b></font></h4><button type="submit" name="q2"<?php echo ($checked) ? 'disabled' : '' ; ?>><?php echo $row['count_b']; ?></button>
						</div><?php } ?>
						<?php if(!empty($row['answer_c']) || $row['answer_c'] == 0) { ?>
						<div class="inline">
						<h4 class="card-text"><?php echo ( $row['count_c'] == $max) ? '<font color="green"><b>' : '<font color="black">';  ?><?php echo strip_tags($row['answer_c']); ?></b></font></h4><button type="submit" name="q3"<?php echo ($checked) ? 'disabled' : '' ; ?>><?php echo $row['count_c']; ?></button>
						</div><?php } ?>
						<?php if(!empty($row['answer_d']) || $row['answer_d'] == 0) { ?>
						<div class="inline">
						<h4 class="card-text"><?php echo ( $row['count_d'] == $max) ? '<font color="green"><b>' : '<font color="black">';  ?><?php echo strip_tags($row['answer_d']); ?></b></font></h4><button type="submit" name="q4"<?php echo ($checked) ? 'disabled' : '' ; ?>><?php echo $row['count_d']; ?></button>
						</div><?php } ?>
						<?php if(!empty($row['answer_e']) || $row['answer_e'] == 0) { ?>
						<div class="inline">
						<h4 class="card-text"><?php echo ( $row['count_e'] == $max) ? '<font color="green"><b>' : '<font color="black">';  ?><?php echo strip_tags($row['answer_e']); ?></b></font></h4><button type="submit" name="q5"<?php echo ($checked) ? 'disabled' : '' ; ?>><?php echo $row['count_e']; ?></button>
						</div><?php } ?>
						<div class="inline">
						<h4 class="card-text"></h4><?php echo ($checkdel) ? '<button type="submit" name="del">Usuń odpowiedź</button>' : '' ; ?>
						</div>
						</form>
						<div class="metafooter">
							<div class="wrapfooter">
								<span class="meta-footer-thumb">
								<?php if(is_female($rowuser['name'])) { ?>
								<img class="author-thumb" src="assets/img/female.png" alt="<?php echo $rowuser['nick']?>">
								<?php } else { ?>
								<img class="author-thumb" src="https://www.gravatar.com/avatar/e56154546cf4be74e393c62d1ae9f9d4?s=250&amp;d=mm&amp;r=x" alt="<?php echo $rowuser['nick']?>">
								<?php } ?>
								</span>
								<span class="author-meta">
								<span class="post-name"><?php echo $rowuser['nick']; ?></span><br/>
								<span class="post-date"><?php if(check_repeat_question($conn, $category_id, $row['text'])) { ?>
								<font color="red"><b>Pytanie powtarzające się</b></font>
								<?php  } ?>
								</span>
								</span>
								<span class="post-read-more"><?php echo (is_multianswer($conn, $row['id'])) ? '<b><u>Wielokrotny wybór!</u></b>' : ''; ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		

		
<?php
}
}

?>
	</div>
	</section>


<?php
}
}
$conn->close();
?>
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
