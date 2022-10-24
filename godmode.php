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
<title><?php echo $title ?> - Panel Adminstracyjny</title>

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
	check_mod($conn, $_SESSION["user_id"]); // sprawdzenie czy moderator

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
			 Panel Administracyjny
		</p>
	</div>
	<?php
	if(isset($_POST['category']) && !empty($_POST['category'])){
		if(check_is_category($conn, $_POST['category']))
		{			
			echo '<center><font color="green"><b>Dodano kategorię pomyślnie!</b></font></center>';
			addcategory($conn, $_POST['category'], $_SESSION['userlogin']); // dodanie kategorii poprzez funkcje
		}
		else 
		{
			echo '<center><font color="red"><b>Kategoria już istnieje!</b></font></center>';
		}
	}
	if(isset($_POST['cat_id']) && !empty($_POST['cat_id'])) {
		if($_SESSION['permission']>0)
		{
			change_hide_cat($conn, $_POST['cat_id']); // zmiana widoczności kategorii
			save_log($conn, $_SESSION['userlogin']." - ".$_SESSION['name']." zmienił widoczność kategorii ".$_POST['cat_id']);
		}
	}
	if(isset($_POST['quest_id']) && !empty($_POST['quest_id'])) {
		if($_SESSION['permission']>0)
		{
			change_hide_quest($conn, $_POST['quest_id']); // zmiana widoczności pytania
			save_log($conn, $_SESSION['userlogin']." - ".$_SESSION['name']." zmienił widoczność pytania ".$_POST['quest_id']);
		}
	}
	if(isset($_POST['user_id']) && !empty($_POST['user_id'])) {
		if(get_perm($conn, $_POST['user_id']) < $_SESSION['permission'])
			change_user_active($conn, $_POST['user_id']); // zmiana aktywności użytkownika
			save_log($conn, $_SESSION['userlogin']." - ".$_SESSION['name']." zmienił aktywność użytkownika ".$_POST['user_id']);
	}
	if(isset($_POST['mod_id']) && !empty($_POST['mod_id'])) {
		if(is_god())
			change_moderator_active($conn, $_POST['mod_id']); // zmiana moderatora
	}
	if(isset($_POST['del_id']) && !empty($_POST['del_id'])) {
		if(is_god())
			del_user($conn, $_POST['del_id']); // usunięcie użytkownika
	}
	if(isset($_POST['ban_id']) && !empty($_POST['ban_id'])) {
		if($_SESSION['permission']>0)
		{
			if(get_perm($conn, $_POST['ban_id']) < $_SESSION['permission']) {
				ban_user($conn, $_POST['ban_id'], $_SESSION['user_id'], $_POST['reason']); // banowanie użytkownika
				save_log($conn, $_SESSION['userlogin']." - ".$_SESSION['name']." zbanował użytkownika ".$_POST['ban_id']." - Powód: ".$_POST['reason']);
			}
		}
	}
	if(isset($_POST['unban_id']) && !empty($_POST['unban_id'])) {
		if($_SESSION['permission']>0)
		{
			unban_user($conn, $_POST['unban_id'], $_SESSION['user_id']); // odbanowanie użytkownika
			save_log($conn, $_SESSION['userlogin']." - ".$_SESSION['name']." odbanował użytkownika ".$_POST['unban_id']);
		}
	}
	if(isset($_POST['delcat_id']) && !empty($_POST['delcat_id'])) {
		if(is_god())
		{
			delete_category($conn, $_POST['delcat_id']); // skasowanie kategorii
		}
	}
	if(isset($_POST['delquest_id']) && !empty($_POST['delquest_id'])) {
		if(is_god())
		{
			delete_question($conn, $_POST['delquest_id']); // skasowanie pytania
		}
	}
	?>
	<?php if(!isset($_GET['choose']) || ($_GET['choose'] != 1 && $_GET['choose'] != 2))
	{
		?>
	<div class="choosemod">
		<button type="button" class="butmode user" onclick="location.href='?choose=1';"></button>
		<button type="button" class="butmode question" onclick="location.href='?choose=2';"></button>	
	</div>
	<div class="choosemod">
		<span class="choosetext">Użytkownicy</span>
		<span class="choosetext">Pytania</span>
		</div>
		<?php
	}
	if(isset($_GET['choose']) && $_GET['choose'] == 2)
	{
		?>
	<div class="list-group">
		<?php if($_SERVER['REQUEST_URI'] != '/godmode.php?choose=2&cat=1') { ?>
		<h2 class="list-group-item list-group-item-action between" onclick="location.href='?choose=2&cat=1'">Kategorie<span class="arrow down"></span></h2>
		<?php } else { ?>
		<h2 class="list-group-item list-group-item-action between" onclick="location.href='?choose=2'">Kategorie<span class="arrow down"></span></h2>
		<?php
		}
		if(isset($_GET['cat']) && $_GET['cat'] == 1) {
			?>
	<div class="loginpage" id="catdiv" style="display: unset;">
	<form action="" method="POST" class="catpanel">
	<label for="category">Stwórz kategorię:</label>
		<input type="text" size="50" name="category" placeholder="Wprowadź kategorię">
		<button type="submit" class="btn addq" onclick="return confirm('Czy na pewno dodać nową kategorię?')">Dodaj</button>
	</form>
		<table class="table table-striped">
		  <thead>
			<tr>
			  <th style="text-align: left;" scope="col">#</th>
			  <th scope="col">Nazwa</th>
			  <th scope="col">Ilość pytań/Pytania z odp.</th>
			  <th scope="col">Założyciel</th>
			  <th scope="col">Widoczność</th>
			  <?php if(is_god()) { ?>
				<th scope="col">Usuń</th>
			  <?php } ?>
			</tr>
		  </thead>
		  <tbody>
			<?php
			$resultek = get_all_categories($conn);
				while($row = $resultek->fetch_assoc())
				{
			?>
				<tr>
				  <th scope="row"><?php echo $row['id']; ?></th>
				  <td><?php echo $row['name']; ?></td>
				  <td><?php echo get_numbers_question($conn, $row['id']); ?>/<?php echo get_numbers_question_answer($conn, $row['id']); ?></td>
				  <td><?php echo $row['description']; ?></td>
				  <td style="width: 5%;">
				  <form action="" method="POST">
				  <input type="hidden" name="cat_id" value="<?php echo $row['id']; ?>">
				  <?php 
					if(!$row['hidden'])
					{	
						?>
						<button type="submit" class="btn" style="border: 1px solid; background-color: darkseagreen;" onclick="return confirm('Czy na pewno ukryć kategorię?')"><img width='35%' src='assets/img/show.svg' title='Widoczne'></button>
						<?php	
					}
					else
					{
						?>
						<button type="submit" class="btn" style="border: 1px solid; background-color: brown;" onclick="return confirm('Czy na pewno odsłonić kategorię?')"><img width='35%' src='assets/img/hide.svg' title='Ukryte'></button>
						<?php
					}
				  ?>
				  </form>
				  </td>
				  <?php if(is_god()) { ?>
				<td>
				<form action="" method="POST">
				<input type="hidden" name="delcat_id" value="<?php echo $row['id']; ?>">
				<button type="submit" class="btn" style="border: 1px solid; background-color: brown;" onclick="return confirm('Czy na pewno usunąć kategorię?')" title="Usuń kategorię">X</button>
				</form>
				</td>
			  <?php } ?>
				</tr>
			<?php
				}
			?>
		  </tbody>
		</table>
	</div>
	<?php 
		}
		?>
		<?php if($_SERVER['REQUEST_URI'] != '/godmode.php?choose=2&cat=2') { ?>
		<h2 class="list-group-item list-group-item-action between" onclick="location.href='?choose=2&cat=2'">Pytania<span class="arrow down"></span></h2>
		<?php } else { ?>
		<h2 class="list-group-item list-group-item-action between" onclick="location.href='?choose=2'">Pytania<span class="arrow down"></span></h2>
		<?php
		}
		if(isset($_GET['cat']) && $_GET['cat'] == 2) {
		?>
	<!-- Kolejna QUESTION sekcja -->
	<div class="loginpage" id="questiondiv" style="display: unset; padding-top: 1%;">
	<form action="" method="POST" class="catpanel">
	<label for="selcategory">Wybierz kategorię:</label>
		<select name="selcategory" id="selcategory" onchange="this.form.submit()">
		<option value="0">Wybierz...</option>
		 <?php
			$resultek = get_all_categories($conn);
				while($row = $resultek->fetch_assoc())
				{
			?>
			<option value="<?php echo $row['id'];?>"<?php if(isset($_POST['selcategory']) && $_POST['selcategory'] == $row['id']) echo 'selected'; ?>><?php echo $row['name']; ?></option>
			<?php
				}
				?>
		</select>
	</form>
	
		<table class="table table-striped">
		  <thead>
			<tr>
			  <th class="lefttext" scope="col">#</th>
			  <th scope="col">Pytanie</th>
			  <th scope="col">A</th>
			  <th scope="col">B</th>
			  <th scope="col">C</th>
			  <th scope="col">D</th>
			  <th scope="col">E</th>
			  <th scope="col">Dodał</th>
			  <th scope="col">Widoczność</th>
			  <?php if(is_god()) { ?>
				<th scope="col">Usuń</th>
			  <?php } ?>
			</tr>
		  </thead>
		  <tbody>
			<?php
			if(isset($_POST['selcategory']) && !empty($_POST['selcategory'])) {
				$resultek = get_all_question($conn, $_POST['selcategory']);
					while($row = $resultek->fetch_assoc())
					{
			?>
				<tr>
				  <th scope="row"><?php echo $row['id']; ?></th>
				  <td><?php echo $row['text']; ?></td>
				  <td><?php echo $row['answer_a']; ?></td>
				  <td><?php echo $row['answer_b']; ?></td>
				  <td><?php echo $row['answer_c']; ?></td>
				  <td><?php echo $row['answer_d']; ?></td>
				  <td><?php echo $row['answer_e']; ?></td>
				  <td><?php echo get_nickname($conn, $row['user_id']); ?></td>
				  <td style="width: 5%;">
				  <form action="" method="POST">
				  <input type="hidden" name="quest_id" value="<?php echo $row['id']; ?>">
				  <input type="hidden" name="selcategory" value="<?php echo $_POST['selcategory']; ?>">
				  <?php 
					if(!$row['hidden'])
					{	
						?>
						<button type="submit" class="btn" style="border: 1px solid; background-color: darkseagreen;" onclick="return confirm('Czy na pewno ukryć pytanie?')"><img width='35%' src='assets/img/show.svg' title='Widoczne'></button>
						<?php	
					}
					else
					{
						?>
						<button type="submit" class="btn" style="border: 1px solid; background-color: brown;" onclick="return confirm('Czy na pewno odsłonić pytanie?')"><img width='35%' src='assets/img/hide.svg' title='Ukryte'></button>
						<?php
					}
				  ?>
				  </form>
				  </td>
				  <?php if(is_god()) { ?>
				<td>
				<form action="" method="POST">
				<input type="hidden" name="delquest_id" value="<?php echo $row['id']; ?>">
				<input type="hidden" name="selcategory" value="<?php echo $_POST['selcategory']; ?>">
				<button type="submit" class="btn" style="border: 1px solid; background-color: brown;" onclick="return confirm('Czy na pewno usunąć pytanie?')" title="Usuń pytanie">X</button>
				</form>
				</td>
			  <?php } ?>
				</tr>
			<?php
				}
			}
			?>
		  </tbody>
		</table>
	</div>
	<?php 
		}
	}
	if(isset($_GET['choose']) && $_GET['choose'] == 1)
	{
	?>
	<?php if($_SERVER['REQUEST_URI'] != '/godmode.php?choose=1&que=1') { ?>
		<h2 class="list-group-item list-group-item-action between" onclick="location.href='?choose=1&que=1'">Użytkownicy<span class="arrow down"></span></h2>
		<?php } else { ?>
		<h2 class="list-group-item list-group-item-action between" onclick="location.href='?choose=1'">Użytkownicy<span class="arrow down"></span></h2>
		<?php
		}
		if(isset($_GET['que']) && $_GET['que'] == 1) {
			?>
	<!-- Kolejna USER sekcja -->
	<div class="loginpage" id="userdiv" style="display: unset; padding-top: 1%;">
		<table class="table table-striped">
		  <thead>
			<tr>
			  <th class="lefttext" scope="col">#</th>
			  <th class="lefttext" scope="col">Login</th>
			  <th class="lefttext" scope="col">Nick</th>
			  <th class="lefttext" scope="col">Imię i Nazwisko</th>
			  <th class="lefttext" scope="col">Data ważności</th>
			  <th class="lefttext" scope="col">Konto</th>
			  <th class="lefttext" scope="col">Grupa</th>
			  <th class="lefttext" scope="col">Rola</th>
			  <th class="lefttext" scope="col">Ban</th>
			  <?php if(is_god()) { ?>
				<th class="lefttext" scope="col">Moderator</th>
				<th class="lefttext" scope="col">Usuń</th>
			  <?php } ?>
			</tr>
		  </thead>
		  <tbody>
			<?php
			$resultek = get_all_users($conn);
				while($row = $resultek->fetch_assoc())
				{
			?>
				<tr>
				  <th scope="row"><?php echo $row['id']; ?></th>
				  <td><?php echo $row['login']; ?></td>
				  <td><?php echo $row['nick']; ?></td>
				  <td><?php echo $row['name']; ?></td>
				  <td><?php echo $row['date_expire']; ?></td>
				  
				  <td>
				  <form action="" method="POST">
				  <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
				  <?php 
					if($row['active'])
					{	
						?>
						<button type="submit" <?php if(get_perm($conn, $row['id']) >= $_SESSION['permission']) echo "disabled"; ?> class="btn" style="border: 1px solid; background-color: darkseagreen;" onclick="return confirm('Czy na pewno dezaktywować użytkownika?')"><img width="50%" src='assets/img/active.svg' title='Aktywne'></button>
						<?php	
					}
					else
					{
						?>
						<button type="submit" <?php if(get_perm($conn, $row['id']) >= $_SESSION['permission']) echo "disabled"; ?> class="btn" style="border: 1px solid; background-color: brown;" onclick="return confirm('Czy na pewno aktywować użytkownika?')"><img width="50%" src='assets/img/nonactive.svg' title='Nieaktywne'></button>
						<?php
					}
				  ?>
				  </form>
				  </td>
				  
				  <td><?php echo getgroupname($conn, $row['group_id']); ?></td>
				  <td><?php check_rule($row['permission']); ?></td>
				  <td style="width: 5%;">
				  <form id="banform" action="" method="POST">
				  <input id="banid" type="hidden" name="ban_id" value="<?php echo $row['id']; ?>">
				  <input id="banreason" type="hidden" name="reason" value="Brak powodu">
					<?php 
						if(!is_banned($conn, $row['id']))
						{
					?>
							<button type="button" class="btn" style="border: 1px solid; background-color: brown;" title="Zbanuj użytkownika" onclick="banned(<?php echo $row['id']; ?>)" <?php if(get_perm($conn, $row['id']) >= $_SESSION['permission']) echo "disabled"; ?>>X</button>
							</form>
					<?php 
						}
						else
						{
							?>
							</form>
							<form action="" method="POST">
							<input type="hidden" name="unban_id" value="<?php echo $row['id']; ?>">
							<button type="submit" class="btn" style="border: 1px solid; background-color: darkseagreen;" title="Odbanuj użytkownika" onclick="return confirm('Czy na pewno odbanować użytkownika?')">O</button>	
							</form>
						<?php
						}
						?>
				  </td>
				   <?php if(is_god()) { ?>
				<td style="text-align: center;">
				<form action="" method="POST">
				  <input type="hidden" name="mod_id" value="<?php echo $row['id']; ?>">
				  <?php 
					if($row['permission']==0)
					{	
						?>
						<button type="submit" class="btn" style="border: 1px solid; background-color: darkseagreen;" onclick="return confirm('Czy na pewno nadać moderatora?')" title="Nadaj moderatora">+</button>
						<?php	
					}
					else
					{
						?>
						<button type="submit" class="btn" style="border: 1px solid; background-color: brown;" onclick="return confirm('Czy na pewno zabrać moderatora?')" title="Odbierz moderatora">-</button>
						<?php
					}
				  ?>
				  </form>
				  </td>
			  <?php } ?>
			   <?php if(is_god()) { ?>
				<td>
				<form action="" method="POST">
				<input type="hidden" name="del_id" value="<?php echo $row['id']; ?>">
				<button type="submit" class="btn" style="border: 1px solid; background-color: brown;" onclick="return confirm('Czy na pewno usunąć użytkownika?')" title="Usuń użytkownika">X</button>
				</form>
				</td>
			  <?php } ?>
				</tr>
			<?php
				}
			?>
		  </tbody>
		</table>
	</div>
	<?php
		}
		?>
		<?php if($_SERVER['REQUEST_URI'] != '/godmode.php?choose=1&que=2') { ?>
		<h2 class="list-group-item list-group-item-action between" onclick="location.href='?choose=1&que=2'">Bany<span class="arrow down"></span></h2>
		<?php } else { ?>
		<h2 class="list-group-item list-group-item-action between" onclick="location.href='?choose=1'">Bany<span class="arrow down"></span></h2>
		<?php
		}
		if(isset($_GET['que']) && $_GET['que'] == 2) {
			?>
<!-- KOLEJNA BAN SEKCJA -->

	<div class="loginpage" id="bandiv" style="display: unset; padding-top: 1%;">
		<table class="table table-striped">
		  <thead>
			<tr>
			  <th class="lefttext" scope="col">#</th>
			  <th class="lefttext" scope="col">Zbanowany</th>
			  <th class="lefttext" scope="col">Banujący</th>
			  <th class="lefttext" scope="col">Data</th>
			  <th class="lefttext" scope="col">Powód</th>
			  <th class="lefttext" scope="col">Data Unbana</th>
			  <th class="lefttext" scope="col">Odbanowujący</th>
			</tr>
		  </thead>
		  <tbody>
			<?php
			$resultek = get_bans($conn);
				while($row = $resultek->fetch_assoc())
				{
			?>
				<tr <?php if($row['active'] == 1) echo 'style="background-color: firebrick;"'; ?>id="id<?php echo $row['id']+20; ?>">
				  <th scope="row"><?php echo $row['id']; ?></th>
				  <td><?php echo get_login($conn, $row['id_user']); ?></td>
				  <td><?php echo get_login($conn, $row['id_mod']); ?></td>
				  <td><?php echo $row['ban_date']; ?></td>
				  <td><?php echo $row['reason']; ?></td>
				  <td><?php echo $row['unban_date']; ?></td>
				  <td><?php if($row['unban_id']) echo get_login($conn, $row['unban_id']); ?></td>
				</tr>
			<?php
				}
			?>
		  </tbody>
		</table>
	</div>
	</div>
<?php
		}
	}
?>
<!-- KOLEJNA SEKCJA -->
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


</div>
<?php
$conn->close();
?>
<script>
function hide (elements) {
  elements = elements.length ? elements : [elements];
  for (var index = 0; index < elements.length; index++) {
    if(elements[index].style.display == 'none')
	{
		elements[index].style.display = 'unset';
	}
	else
	{
		elements[index].style.display = 'none';
	}
  }
}
function banned (id)
{
	var reason=prompt("Podaj powód bana","Brak powodu!");
	var inputek = document.getElementById("banreason");
	var baninputek = document.getElementById("banid");
	inputek.setAttribute("value", reason);
	baninputek.setAttribute("value", id);
	document.getElementById("banform").submit();
}
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