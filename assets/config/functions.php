<?php

function get_uniq_id($conn, $user_id)
{
	$sql = "SELECT uniq_id FROM users where id='$user_id'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	
	return $row['uniq_id'];
}

function check_expires($conn, $id) // sprawdzenie czy nie wygasl
{
	$sql = "SELECT date_expire FROM users where id='$id'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$date=date_create(date("Y-m-d"));
	$f_date=date_format($date,"Y-m-d");
	
	if($row['date_expire']<$f_date)
	{
		header('Location: logout.php');
	}
}

function deactive_expires($conn, $id)
{
	$sqlek = "UPDATE users SET active=0 WHERE id=$id";
	$resultek = $conn->query($sqlek);
}

function max_q($conn, $id)
{
	$sqlek = "SELECT count_a, count_b, count_c, count_d, count_e FROM question WHERE id=$id";
	$resultek = $conn->query($sqlek);
	if($resultek && $resultek->num_rows >= 1)
	{
		$row = $resultek->fetch_assoc();
		if(($row['count_a'] > $row['count_b']) && ($row['count_a'] > $row['count_c']) && ($row['count_a'] > $row['count_d']) && ($row['count_a'] > $row['count_e'])) return "a";
		else if(($row['count_b'] > $row['count_a']) && ($row['count_b'] > $row['count_c']) && ($row['count_b'] > $row['count_d']) && ($row['count_b'] > $row['count_e'])) return "b";
		else if(($row['count_c'] > $row['count_a']) && ($row['count_c'] > $row['count_b']) && ($row['count_c'] > $row['count_d']) && ($row['count_c'] > $row['count_e'])) return "c";
		else if(($row['count_d'] > $row['count_a']) && ($row['count_d'] > $row['count_b']) && ($row['count_d'] > $row['count_c']) && ($row['count_d'] > $row['count_e'])) return "d";
		else if(($row['count_e'] > $row['count_a']) && ($row['count_e'] > $row['count_b']) && ($row['count_e'] > $row['count_c']) && ($row['count_e'] > $row['count_d'])) return "e";
	}
}

function save_log($conn, $text)
{
	$sql = "INSERT INTO logs (text) VALUES ('$text')";
	$result = $conn->query($sql);
}

function get_perm($conn, $id)
{
	$sqlek = "SELECT permission FROM users WHERE id=$id";
	$resultek = $conn->query($sqlek);
	if($resultek && $resultek->num_rows >= 1)
	{
		$row = $resultek->fetch_assoc();
		return $row['permission'];
	}
	else return 0;
}

function check_mod($conn, $id)
{
	if(get_perm($conn, $id) < 1)
	{
		header('Location: logout.php');
	}
}

function get_nickname($conn, $userid)
{
	$sqlek = "SELECT nick FROM users WHERE id=$userid";
	$resultek = $conn->query($sqlek);
	$row = $resultek->fetch_assoc();
	if(!isset($row['nick'])) $row['nick'] = "<u>Użytkownik usunięty</u>";
	return $row['nick'];
}

function get_ban_info($conn, $id)
{
	$sqlek = "SELECT * FROM bans WHERE id_user=$id AND active=1";
	$resultek = $conn->query($sqlek);
	$row = $resultek->fetch_assoc();
	
	return "<b>Data:</b> " . $row['ban_date'] . "<br><b>Powód:</b> " . $row['reason'] . "<br><b>Moderator:</b> " . get_nickname($conn, $row['id_mod']);
}

function delete_category($conn, $id)
{
	$sqlek = "DELETE FROM category WHERE id=$id";
	$resultek = $conn->query($sqlek);
}

function delete_question($conn, $id)
{
	$sqlek = "DELETE FROM question WHERE id=$id";
	$resultek = $conn->query($sqlek);
}

function is_banned($conn, $id)
{
	$sqlek = "SELECT * FROM bans WHERE id_user=$id AND active=1";
	$resultek = $conn->query($sqlek);
	if($resultek && $resultek->num_rows >= 1) return true;
	else return false;
}

function ban_user($conn, $id_user, $id_mod, $reason)
{
	$sql = "INSERT INTO bans (id_user, id_mod, ban_date, reason, active) VALUES ('$id_user', '$id_mod', NOW(), '$reason', 1)";
	$result = $conn->query($sql);
	$sql = "UPDATE users SET uniq_id = 0 WHERE id=$id_user";
	$result = $conn->query($sql);
	
}

function unban_user($conn, $id_user, $id_mod)
{
	$sql = "UPDATE bans SET active=0, unban_date=NOW(), unban_id=$id_mod WHERE id_user=$id_user";
	$result = $conn->query($sql);
}


function del_user($conn, $id)
{
	$sqlek = "DELETE FROM users WHERE id=$id";
	$resultek = $conn->query($sqlek);
}

function get_bans($conn)
{
	$sqlek = "SELECT * FROM bans ORDER BY active DESC";
	$resultek = $conn->query($sqlek);
	
	return $resultek;
}

function is_god()
{
	if($_SESSION['permission'] == 2)
		return 1;
	else
		return 0;
}

function check_rule($rule)
{
	switch($rule) {
		case 0: echo "User";
		break;
		case 1: echo "<font color='green'><b>Moderator</b></font>";
		break;
		case 2: echo "<font color='red'><b>Admin</b></font>";
		break;
	}
}

function change_user_active($conn, $id)
{
	$sqlek = "SELECT active FROM users WHERE id=$id";
	$resultek = $conn->query($sqlek);
	if($resultek && $resultek->num_rows >= 1)
	{
		$row = $resultek->fetch_assoc();
		if($row['active'] == 0)
		{
			$date=date_create(date("Y-m-d"));
			date_modify($date,"+365 days");
			$final_date = date_format($date,"Y-m-d");
			$sql = "UPDATE users SET active = 1, date_expire = DATE('$final_date') WHERE id=$id";
		}
		else {
			$date=date_create(date("Y-m-d"));
			date_modify($date,"-1 day");
			$final_date = date_format($date,"Y-m-d");
			$sql = "UPDATE users SET active = 0, uniq_id = 0, date_expire = DATE('$final_date') WHERE id=$id";
		}
		$result = $conn->query($sql);
	}
}

function change_moderator_active($conn, $id)
{
	$sqlek = "SELECT permission FROM users WHERE id=$id";
	$resultek = $conn->query($sqlek);
	if($resultek && $resultek->num_rows >= 1)
	{
		$row = $resultek->fetch_assoc();
		if($row['permission'] == 0)
		{
			$sql = "UPDATE users SET permission = 1 WHERE id=$id";
		}
		else {
			$sql = "UPDATE users SET permission = 0, uniq_id = 0 WHERE id=$id";
		}
		$result = $conn->query($sql);
	}
}

function getgroupname($conn, $id)
{
	$sqlek = "SELECT name FROM groups WHERE id=$id";
	$resultek = $conn->query($sqlek);
	if($resultek && $resultek->num_rows >= 1)
	{
		$row = $resultek->fetch_assoc();
		return $row['name'];
	}
	else return "<font color='red'<b><u>Brak</u></b></font>";
	
}

function addcategory($conn, $text, $user)
{
	$sql = "INSERT INTO category (name, description) VALUES ('$text', '$user')";
	$result = $conn->query($sql);
}

function change_hide_cat($conn, $id)
{
	$sqlek = "SELECT hidden FROM category WHERE id=$id";
	$resultek = $conn->query($sqlek);
	if($resultek && $resultek->num_rows >= 1)
	{
		$row = $resultek->fetch_assoc();
		if($row['hidden'] == 0)
		{
			$sql = "UPDATE category SET hidden = 1 WHERE id=$id";
		}
		else {
			$sql = "UPDATE category SET hidden = 0 WHERE id=$id";
		}
		$result = $conn->query($sql);
	}
}

function change_hide_quest($conn, $id)
{
	$sqlek = "SELECT hidden FROM question WHERE id=$id";
	$resultek = $conn->query($sqlek);
	if($resultek && $resultek->num_rows >= 1)
	{
		$row = $resultek->fetch_assoc();
		if($row['hidden'] == 0)
		{
			$sql = "UPDATE question SET hidden = 1 WHERE id=$id";
		}
		else {
			$sql = "UPDATE question SET hidden = 0 WHERE id=$id";
		}
		$result = $conn->query($sql);
	}
}

function check_is_category($conn, $text)//funkcja sprawdzajaca czy kategoria sie powtarza
{
	$text = $conn->real_escape_string($text);
	$text = trim($text);
	$sql = "SELECT id FROM category WHERE name='$text'";
	$result = $conn->query($sql);
	if($result && $result->num_rows >= 1) return 0;
	else return 1;
}

function get_numbers_question($conn, $c_id)
{
	$sqlek = "SELECT * FROM question WHERE category_id=$c_id AND hidden=0";
	$resultek = $conn->query($sqlek);
	$num_rows = mysqli_num_rows($resultek);
	return $num_rows;
}

function get_numbers_question_answer($conn, $c_id)
{
	$sqlek = "SELECT * FROM question WHERE category_id=$c_id AND (count_a > 0 OR count_b > 0 OR count_c > 0 OR count_d > 0 OR count_e > 0) AND hidden=0";
	$resultek = $conn->query($sqlek);
	$num_rows = mysqli_num_rows($resultek);
	return $num_rows;
}

function get_all_groups($conn)
{
	$sqlek = "SELECT * FROM groups";
	$resultek = $conn->query($sqlek);
	
	return $resultek;
}

function get_all_question($conn, $cat_id)
{
	$sqlek = "SELECT * FROM question WHERE category_id=$cat_id";
	$resultek = $conn->query($sqlek);
	
	return $resultek;
}

function get_all_categories($conn)
{
	$sqlek = "SELECT * FROM category";
	$resultek = $conn->query($sqlek);
	
	return $resultek;
}

function get_all_users($conn)
{
	$sqlek = "SELECT * FROM users ORDER BY permission DESC";
	$resultek = $conn->query($sqlek);
	
	return $resultek;
}

function get_group_name($conn, $group_id)
{
	$sqlek = "SELECT name FROM groups WHERE id=$group_id";
	$resultek = $conn->query($sqlek);
	$row = $resultek->fetch_assoc();
	if(!$row)
	{
		return "<font color='red'>[Zmień grupę w zakładce profil!]</font>";
	}
	else {
		return $row['name'];
	}
}

function get_login($conn, $userid)
{
	$sqlek = "SELECT login FROM users WHERE id=$userid";
	$resultek = $conn->query($sqlek);
	$row = $resultek->fetch_assoc();
	
	if(!empty($row['login'])) return $row['login'];
	else return "<i>Użytkownik skasowany</i>";
}

function get_name($conn, $userid)
{
	$sqlek = "SELECT name FROM users WHERE id=$userid";
	$resultek = $conn->query($sqlek);
	$row = $resultek->fetch_assoc();
	
	return $row['name'];
}

function get_activate($conn, $userid)
{
	$sqlek = "SELECT date_expire FROM users WHERE id=$userid";
	$resultek = $conn->query($sqlek);
	$row = $resultek->fetch_assoc();
	
	return $row['date_expire'];
}


function get_user_all_q($conn, $userid)
{
	$sqlek = "SELECT id FROM question WHERE user_id=$userid";
	$resultek = $conn->query($sqlek);
	
	return $resultek->num_rows;
}

function get_user_all_a($conn, $userid)
{
	$sqlik = "SELECT id FROM answer WHERE user=$userid";
	$resultik = $conn->query($sqlik);
	
	return $resultik->num_rows;
}
function check_password($conn, $userid, $password)
{
	$userid = (int)$userid;
	$sql = "SELECT password FROM users WHERE id='$userid'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	if(md5($password) == $row['password']) return true;
	else return false;
}


function show_menu() // funkcja wyswietalajaca menu
{
?>
<li class="nav-item ">
			<li class="nav-item ">
			<a class="nav-link<?php echo (basename($_SERVER['PHP_SELF']) == "quiz.php") ? " active" : "";?>" href="quiz.php">Quiz</a>
			</li>
			<a class="nav-link<?php echo (basename($_SERVER['PHP_SELF']) == "index.php") ? " active" : "";?>" href="index.php">Pytania</a>
			</li>
			<li class="nav-item<?php echo (basename($_SERVER['PHP_SELF']) == "addquestion.php") ? " active" : "";?>">
			<a class="nav-link" href="addquestion.php">Dodaj pytanie</a>
			</li>
			<li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == "profile.php") ? " active" : "";?>">
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
<?php
}


function logout_time()
{
	if(time() - $_SESSION['timestamp'] > 3600) { // po godzinie wylogowanie
		$_SESSION["login"] = 0;
		echo"<script>isLogout()</script>";
	} else {
    $_SESSION['timestamp'] = time(); 
	}
}

function check_uniqid($uid) // sprawdzenie czy uid zgadza sie z tym w bazie z usera jak nie to logout
{
	if($uid != $_SESSION["uniqid"])
	{
		header('Location: logout.php');
	}
}

function check_fast_multianswer($text) // funkcja parsujaca i sprawdzajaca czy pytania z wtyczki sa multianswer
{
	$search = "<multianswer>";
	$pos = strpos($text, $search);
	if($pos !== false) return true;
	else return false;
}


function free_user($conn, $nick) // funkcja sprawdzjaaca czy login jest wolny
{
	$sql = "SELECT id FROM users WHERE login='$nick'";
	$result = $conn->query($sql);
	if($result && $result->num_rows > 0) return 0;
	else return 1;
}

function free_nick($conn, $nick, $id=0) // funkcja sprawdzjaca czy nick jest wolny
{
	$sql = "SELECT id FROM users WHERE nick='$nick'";
	$result = $conn->query($sql);
	if($result && $result->num_rows > 0)
	{
		$row = $result->fetch_assoc();
		if($row['id'] != $id) return 0;
		else return 1;
	}		
	else return 1;
}

function free_discord($conn, $nick) // sprawdzjaca wolny id discorda
{
	$nick = $conn->real_escape_string($nick);
	$sql = "SELECT id FROM users WHERE discord='$nick'";
	$result = $conn->query($sql);
	if($result && $result->num_rows > 0) return 0;
	else return 1;
}

function is_female($nick) // funkcja sprawdzajaca plec do avatara
{
	$nick = explode(" ", $nick);
	if($nick[0][strlen($nick[0])-1] == "a") return 1;
	else return 0;
}

function get_nick($conn, $id) // funkcja pobierajaca nazwe uzytkownika z bazy
{
	$id = (int)$id;
	$sql = "SELECT nick FROM users WHERE id='$id'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	
	return $row['nick'];
}

function check_repeat_question($conn, $id_cat, $text)//funkcja sprawdzajaca czy pytanie sie powtarza
{
	$id_cat = (int)$id_cat;
	$text = $conn->real_escape_string($text);
	$sql = "SELECT id FROM question WHERE category_id='$id_cat' AND text='$text'";
	$result = $conn->query($sql);
	if($result && $result->num_rows > 1) return 1;
	else return 0;
}

function check_count_answer($conn, $id) // funkcja liczaca ilosc odpowiedzi w pytaniu do ustawienia limitu zaznaczen
{
	$id = (int)$id;
	$sql = "SELECT answer_a, answer_b, answer_c, answer_d, answer_e FROM question WHERE id='$id'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$counter = 0;
	if(!empty($row['answer_a']) || $row['answer_a'] == 0) $counter++;
	if(!empty($row['answer_b']) || $row['answer_b'] == 0) $counter++;
	if(!empty($row['answer_c']) || $row['answer_c'] == 0) $counter++;
	if(!empty($row['answer_d']) || $row['answer_d'] == 0) $counter++;
	if(!empty($row['answer_e']) || $row['answer_e'] == 0) $counter++;
	
	return $counter;
}

function is_multianswer($conn, $id)
{
	$id = (int)$id;
	$sql = "SELECT multianswer FROM question WHERE id='$id'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	
	return $row['multianswer'];
}

function get_answer($conn, $id, $choose) // funkcja zwracajaca nazwe odpowiedzi
{
	$id = (int)$id;
	$sql = "SELECT * FROM question WHERE id='$id'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	if($choose == 1) return $row['answer_a'];
	if($choose == 2) return $row['answer_b'];
	if($choose == 3) return $row['answer_c'];
	if($choose == 4) return $row['answer_d'];
	if($choose == 5) return $row['answer_e'];
}

function get_count_answer($conn, $id, $choose) // funkcja zwracajaca liczbe odpowiedzi
{
	$id = (int)$id;
	$sql = "SELECT id FROM answer WHERE question_id='$id' AND answer_id='$choose'";
	$result = $conn->query($sql);
	
	return $result->num_rows;
}

function check_answer_clicked($conn, $id, $id_user, $answer)//sprawdzanie czy uzytkownik kliknal ptaszek
{
	$id = (int)$id;
	$id_user = (int)$id_user;
	$answer = (int)$answer;
	$sql = "SELECT id FROM answer WHERE question_id='$id' AND user='$id_user' AND answer_id='$answer'";
	$result = $conn->query($sql);
	if($result && $result->num_rows > 0) return 1;
	else return 0;
}

function GeraHash($qtd){

$RandChar = 'abcdefghijklmopqrstuvxwyz0123456789';
$QuantidadeRandChar = strlen($RandChar);
$QuantidadeRandChar--;

$Hash=NULL;
    for($x = 1 ; $x <= $qtd ; $x++){
        $Posicao = rand(0,$QuantidadeRandChar);
        $Hash .= substr($RandChar,$Posicao,1);
    }

return $Hash;
}


function prntscr_parse($url) // funkcja parsujaca prtscr i zapisujca z linku prtscr do folderu zdjecie + do bazy link
{
	
	if(strpos($url, "https://prnt.sc/") !== false)
	{
		$srcLink = array();
		$srcSrting = array();
		
			
		$imgname = GeraHash(10);


				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
				$html = curl_exec($ch);
				curl_close($ch);

				$doc = new DOMDocument();
				@$doc->loadHTML($html);

				$tags = $doc->getElementsByTagName('img');

				$srcLinkWithImage = array();

				foreach ($tags as $tag) {
					array_push($srcLinkWithImage, $tags[0]->getAttribute('src'));
					$srcSrting = $srcLinkWithImage[0];
				}

				if (!isset($srcSrting) || $srcSrting == "//st.prntscr.com/2018/10/13/2048/img/0_173a7b_211be8ff.png") {
					echo "IMAGE NOT FOUND". "</br>";
				}else{
					copy("$srcSrting" , "assets/img/tests_images/" . $imgname . ".png");
					$link = "assets/img/tests_images/" . $imgname . ".png";
					return $link;

				}
	}
	else
	{
		$imgname = GeraHash(10);
		copy("$url" , "assets/img/tests_images/" . $imgname . ".png");
		$link = "assets/img/tests_images/" . $imgname . ".png";
		return $link;
	}
}	

?>