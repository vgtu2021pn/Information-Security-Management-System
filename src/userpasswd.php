<?php 
session_start();
if(!isset($_SESSION["username"]) || (isset($_SESSION["usertype"]) && $_SESSION["usertype"] != 1)) {
	header("Location: Login.php?action=login");  
}

require_once __DIR__ . '/notphish.php';
require_once __DIR__ . '/language/language.php';

$supported = [
        'en' => 'English',
        'lt' => 'Lietuvių',
        'pl' => 'Polska'
    ];
    
    $current = defined('SITE_LANG') ? SITE_LANG : ($_COOKIE['site_lang'] ?? 'en');
    
    $return = '/src/userpasswd.php';
    
    // Simple language chooser
    $lengui = array(
        'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
        'title' => array ('en' => 'My Account\'s Password', 'lt' => 'Mano paskyros slaptažodis', 'pl' => 'Hasło mojego konta'),
        'site' => array ('en' => 'Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistema', 'pl' => 'System zarządzania bezpieczeństwem informacji'),
        'logout' => array ('en' => 'Log Out', 'lt' => 'Atsijungti', 'pl' => 'Wylogui'),
        'welcome' => array ('en' => 'Welcome', 'lt' => 'Sveikiname', 'pl' => 'Witamy'),
        'name' => array ('en' => 'Name:', 'lt' => 'Vardas:', 'pl' => 'Imię:'),
        'type' => array ('en' => 'Type:', 'lt' => 'Rūšis:', 'pl' => 'Typ:'),
        'administrator' => array ('en' => 'Administrator', 'lt' => 'Administratorius', 'pl' => 'Administrator'),
        'created' => array ('en' => 'Created:', 'lt' => 'Sukurta:', 'pl' => 'Utworzono:'),
        'initiate' => array ('en' => 'Initiate:', 'lt' => 'Inicijuoti:', 'pl' => 'Inicjować:'),
        'home' => array ('en' => 'Home', 'lt' => 'Pradžia', 'pl' => 'Strona główna'),
        'changepass' => array ('en' => 'Password change', 'lt' => 'Keisti slaptažodį', 'pl' => 'Zmiana hasła'),
        'terms' => array ('en' => 'Terms', 'lt' => 'Sąlygos', 'pl' => 'Warunki'),
        'removeacc' => array ('en' => 'Remove this account', 'lt' => 'Pašalinti šią paskyrą', 'pl' => 'Usuń to konto'),
        'cookie' => array ('en' => 'Data of Cookie is wrong.', 'lt' => 'Neteisingi slapuko duomenys.', 'pl' => 'Błędne dane Cookie.'),
        'err1' => array ('en' => 'Enter required fields.', 'lt' => 'Įvesti reikalaujamus laukus.', 'pl' => 'Wprowadz wymaganych pól.'),
        'err2' => array ('en' => 'Password fields does not match.', 'lt' => 'Slaptažodžio laukai nesutampa.', 'pl' => 'Nie są spójne pól hasła.'),
        'err5' => array ('en' => 'Wrong User Details No. 5.', 'lt' => 'Neteisinga naudotojo pateiktis Nr. 5.', 'pl' => 'Błędne dane użytkownika Nr. 5.'),
        'err6' => array ('en' => 'Wrong User Details No. 6.', 'lt' => 'Neteisinga naudotojo pateiktis Nr. 6.', 'pl' => 'Błędne dane użytkownika Nr. 6.'),
        'success' => array ('en' => 'Password was updated.', 'lt' => 'Slaptažodis buvo atnaujintas.', 'pl' => 'Hasło było zaktualizowane.'),
	'password' => array ('en' => 'Your password', 'lt' => 'Tavo slaptažodis', 'pl' => 'Twoje hasło'),
	'newpassword' => array ('en' => 'Your new password', 'lt' => 'Tavo naujasis slaptažodis', 'pl' => 'Twoje nowe hasło'),
        'repeatnewpassword' => array ('en' => 'Repeat your new password', 'lt' => 'Pakartoti savo naująjį slaptažodį', 'pl' => 'Powtórz swoje nowe hasło'),
        'button' => array ('en' => 'Change It', 'lt' => 'Keisti tai', 'pl' => 'Zmienić to'),
        'question' => array ('en' => 'Do You want to proceed it further?', 'lt' => 'Ar pageidauji tęsti?', 'pl' => 'Chcesz to kontynuować?')
    );

require_once __DIR__ . '/credens.php';

$connection = mysqli_connect($servername, $user, $pw, $db);			

if(!$connection) {
	die("Connection failed: " .mysqli_connect_error());
}
?>  

<!DOCTYPE html>  
<html lang="<?php echo $lengui['langu'][$current]; ?>">  
<head>
<meta charset="utf-8">
<title><?php echo $lengui['title'][$current]; ?></title>
<link rel="icon" href="images/logo.webp" type="image/webp">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<style>
a.active {
	color: #D34143;
}

.user-cell{
	padding: 0px;
}

table.view-table{
	width: 100%;
	margin-left: auto;
	margin-right: auto;
	background-color: rgba(192, 192, 192, 0.5);
	color: black;
	margin-bottom: 20px;
}

tr.view-row{
	border-collapse: collapse;
}

td.view-cell{
	padding: 10px 10px 10px 10px; /*top right bottom left*/
}

.edit-icons{
	height: 25px;
	width: 25px;
}

.edit-buttons{
	padding: 8px;
	border: 1px solid black;
	background-color: #173457;
	border-radius: 20px;
}

.edit-buttons:hover{
	background-color: rgba(250, 250, 250, 0.5);
	border: 0px;
}

/* Language */

.langs { 
 display: flex; 
 gap: 1rem; 
 flex-wrap: wrap;
 margin: 10px;
 align-items: center;
 justify-content: center;
}

.lang { 
 padding: .5rem 1rem; 
 border: 1px solid #000; 
 border-radius: 6px; 
 text-decoration: none; 
 color: #fff;
 background-color: #173457;
 box-shadow: 2px 10px 6px -3px rgba(0, 0, 0, 0.5);
 opacity: 0.95;
}

.lang.active { 
 background: #0b78e3;
 border-color: #0b78e3; 
}
</style>
</head>
<body style="background: #173457;">

	<!-- Navigation Bar -->
	
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="#"><?php echo $lengui['site'][$current]; ?></a>
			</div>
    
			<ul class="nav navbar-nav navbar-right">
				<li><a href="useraccount.php"><span class="glyphicon glyphicon-user"></span> <?php echo htmlentities($_SESSION["username"], ENT_QUOTES, 'UTF-8'); ?> </a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span><?php echo $lengui['logout'][$current]; ?></a></li>
			</ul>
		</div>
	</nav>
	
<?php

/* Change Password */

if(isset($_POST['btnChangePasswd']))
{
	$oldpw = $_POST['oldpw'];
	$newpw = $_POST['newpw'];
	$repnewpw = $_POST['repnewpw'];
	$hashednewpw = password_hash($newpw,PASSWORD_DEFAULT);
	$control = $_POST['control'];
		
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
	
	$username = $_SESSION['username'];
	
	$selectqry = "SELECT * FROM user WHERE username=?;";
	$stmt = mysqli_prepare($connection, $selectqry);
	mysqli_stmt_bind_param($stmt,'s', $username);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	
	if($oldpw=="" || $newpw=="" || $repnewpw=="") {
		echo '<script>alert("'.$lengui['err1'][$current].'")</script>';
	}
	elseif($newpw!=$repnewpw) {
		echo '<script>alert("'.$lengui['err2'][$current].'")</script>';
	}
	else {
		if(mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_array($result)) {
				if(password_verify($oldpw, $row["password"])) {
					$update = "	UPDATE 
								user
							SET
								password = ?
							WHERE
								username = ?;";
		
					$updateprepare = mysqli_prepare($connection, $update);
					mysqli_stmt_bind_param($updateprepare, 'ss', $hashednewpw, $username);
					mysqli_stmt_execute($updateprepare);
					
					echo '<script>alert("'.$lengui['success'][$current].'")</script>';
					
					header("Location: useraccount.php");
				}
				else {
					echo '<script>alert("'.$lengui['err5'][$current].'")</script>';
				}
			}
		}
		else {
			echo '<script>alert("'.$lengui['err6'][$current].'")</script>';
		}
	}
	mysqli_free_result($result);
}
?>
	
	<!-- Displaying user info -->
 
	<div class="container" style="width: 100%; color: white; display: flex;">

<?php
$username = $_SESSION["username"];

$selectUserDataqry = "	SELECT 
				uno,
				CONCAT(first_name, ' ', last_name) AS fullname,
				email,
				activated,
				termsofservice,
				created
			FROM
				user
			WHERE
				username = ?;";

$stmt = mysqli_prepare($connection, $selectUserDataqry);
mysqli_stmt_bind_param($stmt,'s', $username);
mysqli_stmt_execute($stmt);
$resultUserData = mysqli_stmt_get_result($stmt);

$rowUserData = mysqli_fetch_assoc($resultUserData);
?>
		
		<!-- User's data-->
		
		<div style="width: 30%; padding: 0px 50px 50px 50px;">
			<table border=0 style="width: 100%; ">
				<tr>
					<td align="center" colspan=2>
						<img src="images/user.jpg" style="height: 100px; width: 100px; border-radius: 50px;">
					</td>
				</tr>
				<tr>
					<td class="user-cell" align="center" colspan=2>
						<h1><?php echo $lengui['welcome'][$current]; ?> - <?php echo htmlentities($username, ENT_QUOTES, 'UTF-8'); ?></h1>
					</td>
				</tr>
<?php
if(!empty($rowUserData['fullname']))
{
?>
				<tr>
					<td class="user-cell"style="width: 50%"><b><?php echo $lengui['name'][$current]; ?></b></td>
					<td class="user-cell"><?php echo htmlentities($rowUserData['fullname'], ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
<?php
}
?>
				<tr>
					<td class="user-cell"><b><?php echo $lengui['type'][$current]; ?></b></td>
					<td class="user-cell"><?php echo $lengui['administrator'][$current]; ?></td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell"><b><?php echo $lengui['created'][$current]; ?></b></td>
					<td class="user-cell">
						<?php echo htmlentities($rowUserData['created'], ENT_QUOTES, 'UTF-8'); ?>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell" rowspan=9><b><?php echo $lengui['initiate'][$current]; ?></b></td>
					<td class="user-cell">
						<a href="useraccount.php"><span class="glyphicon glyphicon-home"></span> <?php echo $lengui['home'][$current]; ?></a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell">
						<a class="active" href="userpasswd.php"><span class="glyphicon glyphicon-lock"></span> <?php echo $lengui['changepass'][$current]; ?></a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell">
						<a href="terms.php"><span class="glyphicon glyphicon"></span> <?php echo $lengui['terms'][$current]; ?></a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell">
						<a href="userwipe.php"><span class="glyphicon glyphicon-erase"></span> <?php echo $lengui['removeacc'][$current]; ?></a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>			
			</table>
		</div>
		
		<div style="width: 55%; margin-left: 100px;">
			<form name="changePasswdForm" id="changePasswdForm" action="userpasswd.php" method="post">
			<table class="view-table" border=0>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="oldpw" type="password" class="form-control" data-type="password" placeholder="<?php echo $lengui['password'][$current]; ?>*" autocomplete="off">
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="newpw" type="password" class="form-control" data-type="password" placeholder="<?php echo $lengui['newpassword'][$current]; ?>*" autocomplete="off">
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="repnewpw" type="password" class="form-control" data-type="password" placeholder="<?php echo $lengui['repeatnewpassword'][$current]; ?>*" autocomplete="off"><input type="hidden" name="control" value="<?php echo $COOKIE_SECURITY; ?>">
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="btnChangePasswd" type="submit" class="button" value="<?php echo $lengui['button'][$current]; ?>" style="color:white; font-weight:bold; background-color: #173457;" id="btnChangePasswd" onclick="JavaScript:return validateChangePasswdForm();" />
						</font>
					</td>
				</tr>
			</table>
			</form>
		</div>
	</div>
	
	<!-- Language chooser -->
		
	<div class="langs">
	<?php foreach ($supported as $code => $label): 
		$url = '/src/language/language.php?lang=' . rawurlencode($code) . '&return=' . rawurlencode($return);
		$cls = ($code === $current) ? 'lang active' : 'lang';
	?>
		<a class="<?php echo $cls; ?>" href="<?php echo $url; ?>"><?php echo htmlspecialchars($label); ?></a>
	<?php endforeach; ?>
	</div>
	<script type="text/javascript">
	function validateChangePasswdForm() {
		const userResponse = confirm(<?php echo "'".$lengui['question'][$current]."'"; ?>);
		
	    if (userResponse) {
			document.getElementById('changePasswdForm').name='btnChangePasswd';
			document.getElementById('changePasswdForm').action='userpasswd.php';
			document.getElementById('changePasswdForm').submit();
	    }
	    else {
			document.getElementById('changePasswdForm').addEventListener('submit',
			function(event) {
				event.preventDefault();
			});
		}
	}
	</script>
</body>  
</html>
