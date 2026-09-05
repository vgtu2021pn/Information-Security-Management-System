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

$return = '/src/manage.controls.php';

// Simple language chooser
$lengui = array(
    'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
    'title' => array ('en' => 'Manegement of controls - Information Security Management System', 'lt' => 'Kontrolės vadyba - Informacijos saugos valdymo sistema', 'pl' => 'Kierowanie o kontroli - System zarządzania bezpieczeństwem informacji'),
    'nav_page' => array ('en' => 'Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistema', 'pl' => 'System zarządzania bezpieczeństwem informacji'),
    'nav_title' => array ('en' => 'Management of ISMS controls', 'lt' => 'ISVS kontrolės vadyba', 'pl' => 'Kierowanie o SZBI kontroli'),
    'nav_title_new' => array ('en' => 'Add new control', 'lt' => 'Pridėti naują kontrolę', 'pl' => 'Dodawanie nowe kontrol'),
    'nav_title_dis' => array ('en' => 'Discard this control', 'lt' => 'Pašalinti esamą kontrolę', 'pl' => 'Usuwanie ten kontrol'),
    'logout' => array ('en' => 'Log Out', 'lt' => 'Atsijungti', 'pl' => 'Wyloguj'),
    'cookie' => array ('en' => 'Wrong HTTP Cookie', 'lt' => 'Neteisingas HTTP Cookie', 'pl' => 'Blędne HTTP Cookie'),
    'action-add' => array ('en' => 'Add records to controls', 'lt' => 'Įkelti naujus kontrolės įrašus', 'pl' => 'Wprowadzenie nowych wpisów do kontroli'),
    'action-update' => array ('en' => 'Edit records of controls', 'lt' => 'Keisti kontrolės įrašus', 'pl' => 'Zmiana wpisów w kontroli'),
    'button-add' => array ('en' => 'Add new records', 'lt' => 'Įkelti naujus įrašus', 'pl' => 'Wprowadzenie nowych wpisów'),
    'button-update' => array ('en' => 'Edit records', 'lt' => 'Keisti įrašus', 'pl' => 'Zmiana wpisów'),
    'control_name' => array ('en' => 'Control\'s name', 'lt' => 'Kontrolės pavadinimas', 'pl' => 'Nazwisko kontroli'),
    'control_description' => array ('en' => 'Control\'s description', 'lt' => 'Kontrolės aprašas', 'pl' => 'Opis kontroli'),
    'applicability_status' => array ('en' => 'Applicability status', 'lt' => 'Pritaikomumo būsena', 'pl' => 'Stosowania status'),
    'applicability_status0' => array ('en' => 'Not applicable', 'lt' => 'Nepritaikoma', 'pl' => 'Nie stosowane'),
    'applicability_status1' => array ('en' => 'Applicable', 'lt' => 'Pritaikoma', 'pl' => 'Stosowane'),
    'justification_text' => array ('en' => 'Justification', 'lt' => 'Pagrindimas', 'pl' => 'Usprawiedliwienie'),
    'implementation_status' => array ('en' => 'Implementation status', 'lt' => 'Įgyvendinimo būsena', 'pl' => 'Położenie zastosowania'),
    'implementation_status1' => array ('en' => 'Planned', 'lt' => 'Planuojama', 'pl' => 'Planowany'),
    'implementation_status2' => array ('en' => 'Implemented', 'lt' => 'Įgyvendinta', 'pl' => 'Zastosowany'),
    'implementation_status3' => array ('en' => 'Partial implementation', 'lt' => 'Ne visiškai įgyvendinta', 'pl' => 'Częściowy zastosowanie'),
    'implementation_status4' => array ('en' => 'Not implemented', 'lt' => 'Neįgyvendinta', 'pl' => 'Nie zastosowany'),
    'control_owner_name' => array ('en' => 'Control\'s owner', 'lt' => 'Kontrolės savininkas', 'pl' => 'Właściciel kontroli'),
    'err1' => array ('en' => 'Wrong unique number of a control', 'lt' => 'Neteisingas kontrolės unikalus numeris', 'pl' => 'Nieprawidłowy unikat numer kontroli'),
    'serr1' => array ('en' => 'Wrong control name.', 'lt' => 'Neteisingas kontrolės pavadinimas.', 'pl' => 'Nieprawilna nazwa kontroli.'),
    'serr2' => array ('en' => 'Wrong applicability status.', 'lt' => 'Neteisinga pritaikomumo būsena.', 'pl' => 'Nieprawidłowa stosowania statusu.'),
    'serr3' => array ('en' => 'Wrong implementation status.', 'lt' => 'Neteisinga įgyvendinimo būsena.', 'pl' => 'Nieprawidłowa położenie zastosowaniu.'),
    'serr4' => array ('en' => 'Wrong name of control owner.', 'lt' => 'Neteisingas kontrolės savininko vardas.', 'pl' => 'Nieprawilny właściciel kontroli.')
);

include("credens.php");

$connection = mysqli_connect($servername, $user, $pw, $db);			

if(!$connection) {
	die("Connection failed: " .mysqli_connect_error());
}

if(isset($_GET['discard'])) {
	$un = (int)$_GET['discard'];
	$checkqry = "SELECT
				IF(EXISTS(SELECT 1 FROM statement_of_applicability WHERE control_un = ? LIMIT 1), 1, 0) AS one;";
				
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'i', $un);
	mysqli_stmt_execute($chstmt);
	$result2 = mysqli_stmt_get_result($chstmt);
	
	if(mysqli_num_rows($result2) > 0) {
	    $rowCheckData = mysqli_fetch_assoc($result2);
		
	    if($rowCheckData['one']== 0) {
		echo '<script> alert("'.$lengui['err1'][$current].'") </script>';
	    } else {
	    	$removeReq = "DELETE FROM statement_of_applicability WHERE control_un=?;";
	    	
	    	$removeprepare = mysqli_prepare($connection, $removeReq);
	    	mysqli_stmt_bind_param($removeprepare, 'i', $un);
	    	mysqli_stmt_execute($removeprepare);
	    }
	}
	mysqli_free_result($result2);
	header("Location: controls.php?remove_notice=1");
}

if(isset($_POST['btnAdd'])) {
	$control_name = $_POST["control_name"];
	$control_description = $_POST["control_description"];
	$applicability_status = (int)$_POST["applicability_status"];
	$justification_text = $_POST["justification_text"];
	$implementation_status = (int)$_POST["implementation_status"];
	$control_owner_name = $_POST["control_owner_name"];
	$control = $_POST['manage_sec'];
        
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
	
	$insert = "INSERT INTO statement_of_applicability (control_name,control_description,applicability_status,justification_text,implementation_status,control_owner_name) VALUES (?,?,?,?,?,?);";
	
	$insertprepare = mysqli_prepare($connection, $insert);
	mysqli_stmt_bind_param($insertprepare,'ssisis', $control_name, $control_description, $applicability_status, $justification_text, $implementation_status, $control_owner_name);
	mysqli_stmt_execute($insertprepare);
	header("Location: controls.php?create_notice=1");
}

if(isset($_POST['btnManage'])) {
	$un = $_POST["isms_un"];
	$control_name = $_POST["control_name"];
	$control_description = $_POST["control_description"];
	$applicability_status = (int)$_POST["applicability_status"];
	$justification_text = $_POST["justification_text"];
	$implementation_status = (int)$_POST["implementation_status"];
	$control_owner_name = $_POST["control_owner_name"];
	$control = $_POST['manage_sec'];
        
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
	
	$checkqry = "SELECT
				IF(EXISTS(SELECT 1 FROM statement_of_applicability WHERE control_un = ? LIMIT 1), 1, 0) AS one;";
				
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
						statement_of_applicability 
					SET 
						control_name = ?,
						control_description = ?,
						applicability_status = ?,
						justification_text = ?,
						implementation_status = ?,
						control_owner_name = ?
					WHERE
						control_un = ?;";
			
			$updateprepare = mysqli_prepare($connection, $update);
			mysqli_stmt_bind_param($updateprepare,'ssisisi', $control_name, $control_description, $applicability_status, $justification_text, $implementation_status, $control_owner_name, $un);
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
	
$selectqry = "SELECT * FROM  statement_of_applicability WHERE control_un = ?;";

$stmt = mysqli_prepare($connection, $selectqry);
mysqli_stmt_bind_param($stmt,'i', $isms_un);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)>0 && $control == $COOKIE_SECURITY) {
    $purpose = 'manage';
    $row = mysqli_fetch_assoc($result);
    
    $isms_un = $row["control_un"];
    $isms_control_name = $row["control_name"];
    $isms_control_description = $row["control_description"];
    $isms_applicability_status = (int)$row["applicability_status"];
    $isms_justification_text = $row["justification_text"];
    $isms_implementation_status = (int)$row["implementation_status"];
    $isms_control_owner_name = $row["control_owner_name"];
} else {
    $isms_un = '';
    $isms_control_name = '';
    $isms_control_description = '';
    $isms_applicability_status = '';
    $isms_justification_text = '';
    $isms_implementation_status = 0;
    $isms_control_owner_name = '';
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
				<li class="active"><a href="manage.controls.php"><?php echo $lengui['nav_title_new'][$current]; ?></a></li>
<?php } else { ?>
				<li><a href="manage.controls.php"><?php echo $lengui['nav_title_new'][$current]; ?></a></li>
				<li><a href="manage.controls.php?discard=<?php echo $isms_un; ?>".><?php echo $lengui['nav_title_dis'][$current]; ?></a></li>
<?php } ?>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="useraccount.php"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION["username"]; ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> <?php echo $lengui['logout'][$current]; ?></a></li>
			</ul>
		</div>
	</nav>

	<!-- Management Form -->
	<form name="manageForm" method="POST" action="manage.controls.php" enctype="multipart/form-data">
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
					<td style="padding: 5px;">	<!--control_name-->
						<div class="col-md-4 mb-3">
							<label for="validation01"><?php echo $lengui['control_name'][$current]; ?></label>
							<input type="text" name="control_name" class="form-control" id="validation01" placeholder="" value="<?php echo htmlentities($isms_control_name, ENT_QUOTES, 'UTF-8'); ?>" required>
						</div>	
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--control_description-->
						<div class="col-md-4 mb-3">
							<label for="validation02"><?php echo $lengui['control_description'][$current]; ?></label>
							<textarea name="control_description" class="form-control" id="validation02" placeholder="" rows="5" required><?php echo htmlentities($isms_control_description, ENT_QUOTES, 'UTF-8'); ?></textarea>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--applicability_status-->
						<div class="col-md-4 mb-3">
							<label for="validation03" ><?php echo $lengui['applicability_status'][$current]; ?></label>
							<select name="applicability_status" class="form-control" id="validation03">
								<option value="-1" <?php if($isms_applicability_status == '') { ?>selected="selected"<?php } ?>>--<?php echo $lengui['applicability_status'][$current]; ?>--</option>
								<option value="0" <?php if($isms_applicability_status == 0){ ?>selected="selected"<?php } ?>><?php echo $lengui['applicability_status0'][$current]; ?></option>
								<option value="1" <?php if($isms_applicability_status == 1){ ?>selected="selected"<?php } ?>><?php echo $lengui['applicability_status1'][$current]; ?></option>
							</select>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--justification_text-->
						<div class="col-md-4 mb-3">
							<label for="validation04"><?php echo $lengui['justification_text'][$current]; ?></label>
							<textarea name="justification_text" class="form-control" id="validation04" placeholder="" rows="5" required><?php echo htmlentities($isms_justification_text, ENT_QUOTES, 'UTF-8'); ?></textarea>
						</div>
					</td>
				</tr>
				
				<tr>
					<td style="padding: 5px;">	<!--implementation_status-->
						<div class="col-md-4 mb-3">
							<label for="validation05" ><?php echo $lengui['implementation_status'][$current]; ?></label>
							<select name="implementation_status" class="form-control" id="validation05">
								<option value="0" <?php if(empty($isms_implementation_status)){ ?>selected="selected"<?php } ?>>--<?php echo $lengui['implementation_status'][$current]; ?>--</option>
								<option value="1" <?php if($isms_implementation_status == 1){ ?>selected="selected"<?php } ?>><?php echo $lengui['implementation_status1'][$current]; ?></option>
								<option value="2" <?php if($isms_implementation_status == 2){ ?>selected="selected"<?php } ?>><?php echo $lengui['implementation_status2'][$current]; ?></option>
								<option value="3" <?php if($isms_implementation_status == 3){ ?>selected="selected"<?php } ?>><?php echo $lengui['implementation_status3'][$current]; ?></option>
								<option value="4" <?php if($isms_implementation_status == 4){ ?>selected="selected"<?php } ?>><?php echo $lengui['implementation_status4'][$current]; ?></option>
							</select>
						</div>
					</td>
				</tr>
				
				<tr>
					<td style="padding: 5px;">	<!--control_owner_name-->
						<div class="col-md-4 mb-3">
							<label for="validation06"><?php echo $lengui['control_owner_name'][$current]; ?></label>
							<input type="text" name="control_owner_name" class="form-control" id="validation06" placeholder="" value="<?php echo htmlentities($isms_control_owner_name, ENT_QUOTES, 'UTF-8'); ?>" required>
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
		<p class="back"><a href="/src/controls.php" target="_SELF"><i class="bi bi-arrow-return-left"></i></a></p>
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
	
	var ctrl_name = document.querySelector( "input[name='control_name']" );
	let ctrl_name_v = ctrl_name.value;
	var ctrl_ownr = document.querySelector( "input[name='control_owner_name']" );
	let ctrl_ownr_v = ctrl_ownr.value;
	
	var appl_v = $( "#validation03" ).val();
	var impl_v = $( "#validation05" ).val();
	
	if (ctrl_name_v.length < 3 || ctrl_name_v.length > 200){
		<?php echo "alert('".$lengui['serr1'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (appl_v != 0 && appl_v != 1){
		<?php echo "alert('".$lengui['serr2'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (impl_v != 1 && impl_v != 2 && impl_v != 3 && impl_v != 4){
		<?php echo "alert('".$lengui['serr3'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (ctrl_ownr_v.length < 1 || ctrl_ownr_v.length > 150){
		<?php echo "alert('".$lengui['serr4'][$current]."');"; ?>
		succeed = false;
	}// end if
	    
	if(succeed == true){
		document.getElementById('manageForm').name=<?php if($purpose == 'manage') { echo "'".'btnManage'."'"; } else { echo "'".'btnAdd'."'"; } ?>;
		document.getElementById('manageForm').action='manage.controls.php';
		document.getElementById('manageForm').submit();
		return(true);
	}else {
		return(false);
	}
}
</script>
</body>
</html>
