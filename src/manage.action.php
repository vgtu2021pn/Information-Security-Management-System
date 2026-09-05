<?php
session_start();
if(!isset($_SESSION["username"]) && !isset($_SESSION["usertype"]) && $_SESSION["usertype"] != 1) {
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

$return = '/src/manage.action.php';

// Simple language chooser
$lengui = array(
    'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
    'title' => array ('en' => 'Manegement of corrective actions - Information Security Management System', 'lt' => 'Taisomųjų veiksmų vadyba - Informacijos saugos valdymo sistema', 'pl' => 'Kierowanie o działanie naprawcze - System zarządzania bezpieczeństwem informacji'),
    'nav_page' => array ('en' => 'Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistema', 'pl' => 'System zarządzania bezpieczeństwem informacji'),
    'nav_title' => array ('en' => 'Management of ISMS corrective actions', 'lt' => 'ISVS kontrolės vadyba', 'pl' => 'Kierowanie o SZBI działanie naprawcze'),
    'nav_title_new' => array ('en' => 'Add new corrective action', 'lt' => 'Pridėti naują taisomąjį veiksmą', 'pl' => 'Dodawanie nowe działan naprawcze'),
    'nav_title_dis' => array ('en' => 'Discard this corrective action', 'lt' => 'Pašalinti esamą taisomąjį veiksmą', 'pl' => 'Usuwanie ten działan naprawcze'),
    'logout' => array ('en' => 'Log Out', 'lt' => 'Atsijungti', 'pl' => 'Wyloguj'),
    'cookie' => array ('en' => 'Wrong HTTP Cookie', 'lt' => 'Neteisingas HTTP Cookie', 'pl' => 'Blędne HTTP Cookie'),
    'action-add' => array ('en' => 'Add records to corrective actions', 'lt' => 'Įkelti naujus taisomųjų veiksmų įrašus', 'pl' => 'Wprowadzenie nowych wpisów do działanie naprawcze'),
    'action-update' => array ('en' => 'Edit records of corrective actions', 'lt' => 'Keisti taisomųjų veiksmų įrašus', 'pl' => 'Zmiana wpisów w działan naprawcze'),
    'button-add' => array ('en' => 'Add new records', 'lt' => 'Įkelti naujus įrašus', 'pl' => 'Wprowadzenie nowych wpisów'),
    'button-update' => array ('en' => 'Edit records', 'lt' => 'Keisti įrašus', 'pl' => 'Zmiana wpisów'),
    'found_issues' => array ('en' => 'Found issues', 'lt' => 'Radinių ypatumai', 'pl' => 'Znajdowane charakterystyki'),
    'root_cause' => array ('en' => 'Root cause', 'lt' => 'Pagrindinė priežastis', 'pl' => 'Główny powód'),
    'corrective_action' => array ('en' => 'Corrective action', 'lt' => 'Taisomasis veiksmas', 'pl' => 'Działanie naprawcze'),
    'owner_name' => array ('en' => 'Owner\'s name', 'lt' => 'Savininko vardas', 'pl' => 'Imię właściciela'),
    'due_date' => array ('en' => 'Due date', 'lt' => 'Galutinė įgyvendinimo data', 'pl' => 'Data końcowa'),
    'status' => array ('en' => 'Status', 'lt' => 'Būsena', 'pl' => 'Położenie'),
    'status1' => array ('en' => 'Open', 'lt' => 'Atidarytas', 'pl' => 'Otwarty'),
    'status2' => array ('en' => 'Partial completion', 'lt' => 'Dalinai baigtas', 'pl' => 'Częściowo ukończony'),
    'status3' => array ('en' => 'Closed', 'lt' => 'Uždarytas', 'pl' => 'Zamknięty'),
    'err1' => array ('en' => 'Wrong unique identification number of corrective action', 'lt' => 'Neteisingas taisomojo veiksmo unikalus identifikavimo numeris', 'pl' => 'Nieprawidłowy identyfikacijny unikat numer działania naprawcze'),
    'serr1' => array ('en' => 'Wrong owner name.', 'lt' => 'Neteisingas savininkas.', 'pl' => 'Nieprawidłowa nazwa właścicielia.'),
    'serr2' => array ('en' => 'Wrong status.', 'lt' => 'Neteisinga būsena.', 'pl' => 'Nieprawidłowy status.')
);

include("credens.php");

$connection = mysqli_connect($servername, $user, $pw, $db);			

if(!$connection) {
	die("Connection failed: " .mysqli_connect_error());
}

if(isset($_GET['discard'])) {
	$un = (int)$_GET['discard'];
	$checkqry = "SELECT
				IF(EXISTS(SELECT 1 FROM corrective_action WHERE corrective_action_id = ? LIMIT 1), 1, 0) AS one;";
				
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'i', $un);
	mysqli_stmt_execute($chstmt);
	$result2 = mysqli_stmt_get_result($chstmt);
	
	if(mysqli_num_rows($result2) > 0) {
	    $rowCheckData = mysqli_fetch_assoc($result2);
		
	    if($rowCheckData['one']== 0) {
		echo '<script> alert("'.$lengui['err1'][$current].'") </script>';
	    } else {
	    	$removeReq = "DELETE FROM corrective_action WHERE corrective_action_id=?;";
	    	
	    	$removeprepare = mysqli_prepare($connection, $removeReq);
	    	mysqli_stmt_bind_param($removeprepare, 'i', $un);
	    	mysqli_stmt_execute($removeprepare);
	    }
	}
	mysqli_free_result($result2);
	header("Location: action.php?remove_notice=1");
}

if(isset($_POST['btnAdd'])) {
	$found_issues = $_POST["found_issues"];
	$root_cause = $_POST["root_cause"];
	$corrective_action = $_POST["corrective_action"];
	$owner_name = $_POST["owner_name"];
	$due_date = $_POST["due_date"];
	$status = (int)$_POST["status"];
	$control = $_POST['manage_sec'];
        
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
	
	$insert = "INSERT INTO corrective_action (found_issues,root_cause,corrective_action,owner_name,due_date,status) VALUES (?,?,?,?,?,?);";
	
	$insertprepare = mysqli_prepare($connection, $insert);
	mysqli_stmt_bind_param($insertprepare,'sssssi', $found_issues, $root_cause, $corrective_action, $owner_name, $due_date, $status);
	mysqli_stmt_execute($insertprepare);
	header("Location: action.php?create_notice=1");
}

if(isset($_POST['btnManage'])) {
	$un = $_POST["isms_un"];
	$found_issues = $_POST["found_issues"];
	$root_cause = $_POST["root_cause"];
	$corrective_action = $_POST["corrective_action"];
	$owner_name = $_POST["owner_name"];
	$due_date = $_POST["due_date"];
	$status = (int)$_POST["status"];
	$control = $_POST['manage_sec'];
        
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
	
	$checkqry = "SELECT
				IF(EXISTS(SELECT 1 FROM corrective_action WHERE corrective_action_id = ? LIMIT 1), 1, 0) AS one;";
				
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'i', $un);
	mysqli_stmt_execute($chstmt);
	$result2 = mysqli_stmt_get_result($chstmt);
	
	if(mysqli_num_rows($result2) > 0) {
	    $rowCheckData = mysqli_fetch_assoc($result2);
		
	    if($rowCheckData['one']== 0) {
		echo '<script> alert("'.$lengui['err1'][$current].'") </script>';
	    } else {
			$update = "UPDATE 
						corrective_action 
					SET 
						found_issues = ?,
						root_cause = ?,
						corrective_action = ?,
						owner_name = ?,
						due_date = ?,
						status = ?
					WHERE
						corrective_action_id = ?;";
			
			$updateprepare = mysqli_prepare($connection, $update);
			mysqli_stmt_bind_param($updateprepare,'sssssii', $found_issues, $root_cause, $corrective_action, $owner_name, $due_date, $status, $un);
			mysqli_stmt_execute($updateprepare);
		}
	}
	mysqli_free_result($result2);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lengui['langu'][$current]; ?>">
<head> 
<meta charset="utf-8">
<title><?php echo $lengui['title'][$current]; ?></title>
<link rel="icon" href="images/logo.webp" type="image/webp">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<style>
label{
	font-size: 15px;
	color: white;
}

.form-control{
	font-size: 15px;
}
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
.back {
	margin: 2px 20px;
}
</style>
</head>
<body style="background: #173457;">

<?php
$purpose = 'view';
$isms_un = (int)$_POST['isms_un'];
$control = $_POST['manage_sec'];
	
$selectqry = "SELECT * FROM corrective_action WHERE corrective_action_id = ?;";

$stmt = mysqli_prepare($connection, $selectqry);
mysqli_stmt_bind_param($stmt,'i', $isms_un);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)>0 && $control == $COOKIE_SECURITY) {
    $purpose = 'manage';
    $row = mysqli_fetch_assoc($result);
    
    $isms_un = $row["corrective_action_id"];
    $isms_found_issues = $row["found_issues"];
    $isms_root_cause = $row["root_cause"];
    $isms_corrective_action = $row["corrective_action"];
    $isms_owner_name = $row["owner_name"];
    $isms_due_date = $row["due_date"];
    $isms_status = (int)$row["status"];
} else {
    $isms_un = '';
    $isms_found_issues = '';
    $isms_root_cause = '';
    $isms_corrective_action = '';
    $isms_owner_name = '';
    $isms_due_date = date("Y-m-d");
    $isms_status = 0;
}
?>
	<!-- Navigation Bar -->
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="#"><?php echo $lengui['nav_page'][$current]; ?></a>
			</div>
    
			<ul class="nav navbar-nav">
				<li class="active"><a href="#"><?php echo $lengui['nav_title'][$current]; ?></a></li>
<?php if(empty($isms_un)) { ?>
				<li class="active"><a href="manage.action.php"><?php echo $lengui['nav_title_new'][$current]; ?></a></li>
<?php } else { ?>
				<li><a href="manage.action.php"><?php echo $lengui['nav_title_new'][$current]; ?></a></li>
				<li><a href="manage.action.php?discard=<?php echo $isms_un; ?>".><?php echo $lengui['nav_title_dis'][$current]; ?></a></li>
<?php } ?>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="useraccount.php"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION["username"]; ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> <?php echo $lengui['logout'][$current]; ?></a></li>
			</ul>
		</div>
	</nav>

	<!-- Management Form -->
	<form name="manageForm" method="POST" action="manage.action.php" enctype="multipart/form-data">
		<input type="hidden" name="isms_un" value="<?php echo $isms_un; ?>">
		<input type="hidden" name="manage_sec" value="<?php echo $COOKIE_SECURITY; ?>">	
		<center>
		<div>
			<table width="100%" >
				<tr>
					<td style="padding: 5px;">
						<div class="col-md-4 mb-3" style="text-align: center;">
							<h1 style="color: white;"><?php if($purpose == 'manage') { echo $lengui['action-update'][$current]; } else { echo $lengui['action-add'][$current]; } ?></h1>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--found_issues-->
						<div class="col-md-4 mb-3">
							<label for="validation01"><?php echo $lengui['found_issues'][$current]; ?></label>
							<textarea name="found_issues" class="form-control" id="validation01" placeholder="" rows="5" required><?php echo htmlentities($isms_found_issues, ENT_QUOTES, 'UTF-8'); ?></textarea>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--root_cause-->
						<div class="col-md-4 mb-3">
							<label for="validation02"><?php echo $lengui['root_cause'][$current]; ?></label>
							<textarea name="root_cause" class="form-control" id="validation02" placeholder="" rows="5" required><?php echo htmlentities($isms_root_cause, ENT_QUOTES, 'UTF-8'); ?></textarea>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--corrective_action-->
						<div class="col-md-4 mb-3">
							<label for="validation03"><?php echo $lengui['corrective_action'][$current]; ?></label>
							<textarea name="corrective_action" class="form-control" id="validation03" placeholder="" rows="5" required><?php echo htmlentities($isms_corrective_action, ENT_QUOTES, 'UTF-8'); ?></textarea>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--owner_name-->
						<div class="col-md-4 mb-3">
							<label for="validation04"><?php echo $lengui['owner_name'][$current]; ?></label>
							<input type="text" name="owner_name" class="form-control" id="validation04" placeholder="" value="<?php echo htmlentities($isms_owner_name, ENT_QUOTES, 'UTF-8'); ?>" required>
						</div>	
					</td>
				</tr>
				
				<tr>
					<td style="padding: 5px;">	<!--due_date-->
						<div class="col-md-3 mb-3">
						  <label for="validation05"><?php echo $lengui['due_date'][$current]; ?></label>
						  <input type="date" name="due_date" class="form-control" id="validation05" placeholder="" value="<?php echo htmlentities($isms_due_date, ENT_QUOTES, 'UTF-8'); ?>" required>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--status-->
						<div class="col-md-4 mb-3">
							<label for="validation06" ><?php echo $lengui['status'][$current]; ?></label>
							<select name="status" class="form-control" id="validation06">
								<option value="0" <?php if(empty($isms_status)) { ?>selected="selected"<?php } ?>>--<?php echo $lengui['status'][$current]; ?>--</option>
								<option value="1" <?php if($isms_status == 1){ ?>selected="selected"<?php } ?>><?php echo $lengui['status1'][$current]; ?></option>
								<option value="2" <?php if($isms_status == 2){ ?>selected="selected"<?php } ?>><?php echo $lengui['status2'][$current]; ?></option>
								<option value="3" <?php if($isms_status == 3){ ?>selected="selected"<?php } ?>><?php echo $lengui['status3'][$current]; ?></option>
							</select>
						</div>
					</td>
				</tr>
				
				<tr>
					<td colspan=2 style="padding-left:250px;">	<!--submit button-->
						<div class="col-md-4 mb-3" style="padding-top: 5px;">
							<button name="<?php if($purpose == 'manage') { echo 'btnManage'; } else { echo 'btnAdd'; } ?>" class="btn btn-primary" type="submit" style="color: white; font-size: 15px; align:center;"  onclick="JavaScript:return validateManageForm();"><?php if($purpose == 'manage') { echo $lengui['button-update'][$current]; } else { echo $lengui['button-add'][$current]; } ?></button>
						</div>
					</td>
				</tr>
			</table>
		</div>
		</center>
		<p class="back"><a href="/src/action.php" target="_SELF"><i class="bi bi-arrow-return-left"></i></a></p>
	</form>

<!-- Language links -->

<div class="langs">
    <?php foreach ($supported as $code => $label): 
        $url = '/src/language/language.php?lang=' . rawurlencode($code) . '&return=' . rawurlencode($return);
        $cls = ($code === $current) ? 'lang active' : 'lang';
    ?>
        <a class="<?php echo $cls; ?>" href="<?php echo $url; ?>"><?php echo htmlspecialchars($label); ?></a>
    <?php endforeach; ?>
</div>

<script type="text/javascript">
function validateManageForm() {
	var succeed = true;
	//validation
	
	var ownr_name = document.querySelector( "input[name='owner_name']" );
	let ownr_name_v = ownr_name.value;
	
	var stat_v = $( "#validation06" ).val();
	
	if (ownr_name_v.length < 3 || ownr_name_v.length > 150){
		<?php echo "alert('".$lengui['serr1'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (stat_v != 1 && stat_v != 2 && stat_v != 3){
		<?php echo "alert('".$lengui['serr2'][$current]."');"; ?>
		succeed = false;
	}// end if
	    
	if(succeed == true){
		document.getElementById('manageForm').name=<?php if($purpose == 'manage') { echo "'".'btnManage'."'"; } else { echo "'".'btnAdd'."'"; } ?>;
		document.getElementById('manageForm').action='manage.action.php';
		document.getElementById('manageForm').submit();
		return(true);
	}else {
		return(false);
	}
}
</script>
</body>
</html>
