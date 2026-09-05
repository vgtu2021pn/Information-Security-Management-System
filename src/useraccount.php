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
    
    $return = '/src/useraccount.php';
    
    // Simple language chooser
    $lengui = array(
        'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
        'title' => array ('en' => 'My Account', 'lt' => 'Mano paskyra', 'pl' => 'Moje konto'),
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
        'riskregister' => array ('en' => 'Risk register', 'lt' => 'Rizikos registras', 'pl' => 'Rejestr ryzyki'),
        'controls' => array ('en' => 'Control measures', 'lt' => 'Kontrolės priemonės', 'pl' => 'Metody kontroli'),
        'procedures' => array ('en' => 'Procedures', 'lt' => 'Procedūros', 'pl' => 'Procedury'),
        'action' => array ('en' => 'Corrective actions', 'lt' => 'Taisomasis veiksmas', 'pl' => 'Działania naprawcze'),
        'evidence' => array ('en' => 'Evidence', 'lt' => 'Įrodymai', 'pl' => 'Dowody'),
        'locked' => array ('en' => 'Locked!', 'lt' => 'Uždaryta!', 'pl' => 'Zamknięte'),
        'review' => array ('en' => 'Reviews', 'lt' => 'Peržiūros', 'pl' => 'Recenzje')
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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

/* Menu */

.isms { 
 display: flex; 
 gap: 1rem; 
 flex-wrap: wrap;
 margin: 10px;
 align-items: center;
 justify-content: center;
}

.check { 
 padding: .5rem 1rem; 
 border: 1px solid #000; 
 border-radius: 6px; 
 text-decoration: none; 
 color: #fff;
 background-color: #173457;
 box-shadow: 2px 10px 6px -3px rgba(0, 0, 0, 0.5);
 opacity: 0.95;
}
</style>
</head>
<body style="background: #173457;">
	<!--Navigation Bar-->
	
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="#"><?php echo $lengui['site'][$current]; ?></a>
			</div>
			
			<ul class="nav navbar-nav navbar-right">
				<li class="active"><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo htmlentities($_SESSION["username"], ENT_QUOTES, 'UTF-8'); ?> </a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span><?php echo $lengui['logout'][$current]; ?></a></li>
			</ul>
		</div>
	</nav>
	
	<!-- Displaying user info -->
 
	<div class="container" style="width: 100%; color: white; display: flex;">

<?php  
$username = $_SESSION["username"];
$usertype = (int)$_SESSION["usertype"];

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
						<a class="active" href="useraccount.php"><span class="glyphicon glyphicon-home"></span> <?php echo $lengui['home'][$current]; ?></a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell">
						<a href="userpasswd.php"><span class="glyphicon glyphicon-lock"></span> <?php echo $lengui['changepass'][$current]; ?></a>
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
		
		<!-- User's Dashboard -->
		
		<div style="width: 55%; margin-left: 100px;">
			<table class="view-table" border=0>
				<tr class="view-row">
					<td class="view-cell">																											<!--riskregister-->
						<div class="isms">
							<a class="check" href="riskregister.php"><?php echo $lengui['riskregister'][$current]; ?></a>
						</div>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">																						<!--controls-->
						<div class="isms">
							<a class="check" href="controls.php"><?php echo $lengui['controls'][$current]; ?></a>
						</div>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">																						<!--procedures-->
						<div class="isms">
							<a class="check" href="procedures.php"><?php echo $lengui['procedures'][$current]; ?></a>
						</div>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">																						<!--action-->
						<div class="isms">
							<a class="check" href="action.php"><?php echo $lengui['action'][$current]; ?></a>
						</div>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">																						<!--evidence-->
						<div class="isms">
							<a class="check" href="evidence.php"><?php echo $lengui['evidence'][$current]; ?></a>
						</div>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">																						<!--review-->
						<div class="isms">
							<a class="check" href="javascript:void(0);" onclick="alert('<?php echo $lengui['locked'][$current]; ?>')"><?php echo $lengui['review'][$current]; ?></a>
						</div>
					</td>
				</tr>

				<tr class="view-row view-foot">
					<td>																						<!--end-->
						<div>&nbsp;</div>
					</td>
				</tr>
			</table>
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
</body>  
</html>
