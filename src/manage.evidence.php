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

$return = '/src/manage.evidence.php';

// Simple language chooser
$lengui = array(
    'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
    'title' => array ('en' => 'Manegement of evidence - Information Security Management System', 'lt' => 'Įrodymo vadyba - Informacijos saugos valdymo sistema', 'pl' => 'Kierowanie o dowody - System zarządzania bezpieczeństwem informacji'),
    'nav_page' => array ('en' => 'Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistema', 'pl' => 'System zarządzania bezpieczeństwem informacji'),
    'nav_title' => array ('en' => 'Management of ISMS evidence', 'lt' => 'ISVS įrodymo vadyba', 'pl' => 'Kierowanie o SZBI dowody'),
    'nav_title_new' => array ('en' => 'Add new evidence', 'lt' => 'Pridėti naują įrodymą', 'pl' => 'Dodawanie nowe dowódu'),
    'nav_title_dis' => array ('en' => 'Discard this control', 'lt' => 'Pašalinti esamą kontrolę', 'pl' => 'Usuwanie ten dowód'),
    'logout' => array ('en' => 'Log Out', 'lt' => 'Atsijungti', 'pl' => 'Wyloguj'),
    'cookie' => array ('en' => 'Wrong HTTP Cookie', 'lt' => 'Neteisingas HTTP Cookie', 'pl' => 'Blędne HTTP Cookie'),
    'action-add' => array ('en' => 'Add records to evidence', 'lt' => 'Įkelti naujus įrodymo įrašus', 'pl' => 'Wprowadzenie nowych wpisów do dowody'),
    'action-update' => array ('en' => 'Edit records of evidence', 'lt' => 'Keisti įrodymo įrašus', 'pl' => 'Zmiana wpisów w dowody'),
    'button-add' => array ('en' => 'Add new records', 'lt' => 'Įkelti naujus įrašus', 'pl' => 'Wprowadzenie nowych wpisów'),
    'button-update' => array ('en' => 'Edit records', 'lt' => 'Keisti įrašus', 'pl' => 'Zmiana wpisów'),
    'artifact_type' => array ('en' => 'The type of the evidence unit', 'lt' => 'Įrodymo vieneto tipas', 'pl' => 'Rodzaj jednostki dowodowej'),
    'explanation_of_artifact' => array ('en' => 'Explanation about the evidence unit', 'lt' => 'Įrodymo vieneto paaiškinimas', 'pl' => 'Wyjaśnienie dotyczące jednostki dowodowej'),
    'date_of_artifact' => array ('en' => 'Date of the evidence unit', 'lt' => 'Įrodymo vieneto data', 'pl' => 'Data jednostki dowodowej'),
    'artifact_owner_name' => array ('en' => 'Owner of the evidence unit', 'lt' => 'Įrodymo vieneto savininkas', 'pl' => 'Właściciel jednostki dowodowej'),
    'description_of_artifact_storage' => array ('en' => 'The location and storage of the evidence unit', 'lt' => 'Buvimo vieta ir saugykla įrodymo vienetui', 'pl' => 'Lokalizacja i przechowywanie dowodu'),
    'integrity_data_of_artifact' => array ('en' => 'Checksums', 'lt' => 'Kontrolinės sumos', 'pl' => 'Suma kontrolna'),
    'err1' => array ('en' => 'Wrong unique number of evidence', 'lt' => 'Neteisingas įrodymo unikalus numeris', 'pl' => 'Nieprawidłowy unikat numer dowódu'),
    'serr1' => array ('en' => 'Wrong artifact type.', 'lt' => 'Neteisingas įrodymo vieneto tipas.', 'pl' => 'Nieprawilna typ jednostki dowodowej.'),
    'serr2' => array ('en' => 'Wrong artifact owner.', 'lt' => 'Neteisingas įrodymo vieneto savininkas.', 'pl' => 'Nieprawidłowy właściciel jednostki dowodowej.'),
    'serr3' => array ('en' => 'Wrong checksum.', 'lt' => 'Neteisinga kontrolinė suma.', 'pl' => 'Nieprawidłowa suma kontrolna.')
);

include("credens.php");

$connection = mysqli_connect($servername, $user, $pw, $db);			

if(!$connection) {
	die("Connection failed: " .mysqli_connect_error());
}

if(isset($_GET['discard'])) {
	$un = (int)$_GET['discard'];
	$checkqry = "SELECT
				IF(EXISTS(SELECT 1 FROM evidence WHERE artifact_id = ? LIMIT 1), 1, 0) AS one;";
				
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'i', $un);
	mysqli_stmt_execute($chstmt);
	$result2 = mysqli_stmt_get_result($chstmt);
	
	if(mysqli_num_rows($result2) > 0) {
	    $rowCheckData = mysqli_fetch_assoc($result2);
		
	    if($rowCheckData['one']== 0) {
		echo '<script> alert("'.$lengui['err1'][$current].'") </script>';
	    } else {
	    	$removeReq = "DELETE FROM evidence WHERE artifact_id=?;";
	    	
	    	$removeprepare = mysqli_prepare($connection, $removeReq);
	    	mysqli_stmt_bind_param($removeprepare, 'i', $un);
	    	mysqli_stmt_execute($removeprepare);
	    }
	}
	mysqli_free_result($result2);
	header("Location: evidence.php?remove_notice=1");
}

if(isset($_POST['btnAdd'])) {
	$artifact_type = $_POST["artifact_type"];
	$explanation_of_artifact = $_POST["explanation_of_artifact"];
	$date_of_artifact = $_POST["date_of_artifact"];
	$artifact_owner_name = $_POST["artifact_owner_name"];
	$description_of_artifact_storage = $_POST["description_of_artifact_storage"];
	$integrity_data_of_artifact = $_POST["integrity_data_of_artifact"];
	$control = $_POST['manage_sec'];
        
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
	
	$insert = "INSERT INTO evidence (artifact_type,explanation_of_artifact,date_of_artifact,artifact_owner,description_of_artifact_storage,integrity_data_of_artifact) VALUES (?,?,?,?,?,?);";
	
	$insertprepare = mysqli_prepare($connection, $insert);
	mysqli_stmt_bind_param($insertprepare,'ssssss', $artifact_type, $explanation_of_artifact, $date_of_artifact, $artifact_owner_name, $description_of_artifact_storage, $integrity_data_of_artifact);
	mysqli_stmt_execute($insertprepare);
	header("Location: evidence.php?create_notice=1");
}

if(isset($_POST['btnManage'])) {
	$un = $_POST["isms_un"];
	$artifact_type = $_POST["artifact_type"];
	$explanation_of_artifact = $_POST["explanation_of_artifact"];
	$date_of_artifact = $_POST["date_of_artifact"];
	$artifact_owner_name = $_POST["artifact_owner_name"];
	$description_of_artifact_storage = $_POST["description_of_artifact_storage"];
	$integrity_data_of_artifact = $_POST["integrity_data_of_artifact"];
	$control = $_POST['manage_sec'];
        
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
	
	$checkqry = "SELECT
				IF(EXISTS(SELECT 1 FROM evidence WHERE artifact_id = ? LIMIT 1), 1, 0) AS one;";
				
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
						evidence 
					SET 
						artifact_type = ?,
						explanation_of_artifact = ?,
						date_of_artifact = ?,
						artifact_owner = ?,
						description_of_artifact_storage = ?,
						integrity_data_of_artifact = ?
					WHERE
						artifact_id = ?;";
			
			$updateprepare = mysqli_prepare($connection, $update);
			mysqli_stmt_bind_param($updateprepare,'ssssssi', $artifact_type, $explanation_of_artifact, $date_of_artifact, $artifact_owner_name, $description_of_artifact_storage, $integrity_data_of_artifact, $un);
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
	
$selectqry = "SELECT * FROM evidence WHERE artifact_id = ?;";

$stmt = mysqli_prepare($connection, $selectqry);
mysqli_stmt_bind_param($stmt,'i', $isms_un);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)>0 && $control == $COOKIE_SECURITY) {
    $purpose = 'manage';
    $row = mysqli_fetch_assoc($result);
    
    $isms_un = $row["artifact_id"];
    $isms_artifact_type = $row["artifact_type"];
    $isms_explanation_of_artifact = $row["explanation_of_artifact"];
    $isms_date_of_artifact = $row["date_of_artifact"];
    $isms_artifact_owner_name = $row["artifact_owner"];
    $isms_description_of_artifact_storage = $row["description_of_artifact_storage"];
    $isms_integrity_data_of_artifact = $row["integrity_data_of_artifact"];
} else {
    $isms_un = '';
    $isms_artifact_type = '';
    $isms_explanation_of_artifact = '';
    $isms_date_of_artifact = date("Y-m-d");
    $isms_artifact_owner_name = '';
    $isms_description_of_artifact_storage = '';
    $isms_integrity_data_of_artifact = '';
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
				<li class="active"><a href="manage.evidence.php"><?php echo $lengui['nav_title_new'][$current]; ?></a></li>
<?php } else { ?>
				<li><a href="manage.evidence.php"><?php echo $lengui['nav_title_new'][$current]; ?></a></li>
				<li><a href="manage.evidence.php?discard=<?php echo $isms_un; ?>".><?php echo $lengui['nav_title_dis'][$current]; ?></a></li>
<?php } ?>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="useraccount.php"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION["username"]; ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> <?php echo $lengui['logout'][$current]; ?></a></li>
			</ul>
		</div>
	</nav>

	<!-- Management Form -->
	<form name="manageForm" method="POST" action="manage.evidence.php" enctype="multipart/form-data">
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
					<td style="padding: 5px;">	<!--artifact_type-->
						<div class="col-md-4 mb-3">
							<label for="validation01"><?php echo $lengui['artifact_type'][$current]; ?></label>
							<input type="text" name="artifact_type" class="form-control" id="validation01" placeholder="" value="<?php echo htmlentities($isms_artifact_type, ENT_QUOTES, 'UTF-8'); ?>" required>
						</div>	
					</td>
				</tr>
				
				<tr>
					<td style="padding: 5px;">	<!--explanation_of_artifact-->
						<div class="col-md-4 mb-3">
							<label for="validation02"><?php echo $lengui['explanation_of_artifact'][$current]; ?></label>
							<textarea name="explanation_of_artifact" class="form-control" id="validation02" placeholder="" rows="5" required><?php echo htmlentities($isms_explanation_of_artifact, ENT_QUOTES, 'UTF-8'); ?></textarea>
						</div>
					</td>
				</tr>
				
				<tr>
					<td style="padding: 5px;">	<!--date_of_artifact-->
						<div class="col-md-3 mb-3">
						  <label for="validation03"><?php echo $lengui['date_of_artifact'][$current]; ?></label>
						  <input type="date" name="date_of_artifact" class="form-control" id="validation03" placeholder="" value="<?php echo htmlentities($isms_date_of_artifact, ENT_QUOTES, 'UTF-8'); ?>" required>
						</div>
					</td>
				</tr>
				
				<tr>
					<td style="padding: 5px;">	<!--artifact_owner_name-->
						<div class="col-md-4 mb-3">
							<label for="validation04"><?php echo $lengui['artifact_owner_name'][$current]; ?></label>
							<input type="text" name="artifact_owner_name" class="form-control" id="validation04" placeholder="" value="<?php echo htmlentities($isms_artifact_owner_name, ENT_QUOTES, 'UTF-8'); ?>" required>
						</div>	
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--description_of_artifact_storage-->
						<div class="col-md-4 mb-3">
							<label for="validation05"><?php echo $lengui['description_of_artifact_storage'][$current]; ?></label>
							<textarea name="description_of_artifact_storage" class="form-control" id="validation05" placeholder="" rows="5" required><?php echo htmlentities($isms_description_of_artifact_storage, ENT_QUOTES, 'UTF-8'); ?></textarea>
						</div>
					</td>
				</tr>
				
				<tr>
					<td style="padding: 5px;">	<!--integrity_data_of_artifact-->
						<div class="col-md-4 mb-3">
							<label for="validation06"><?php echo $lengui['integrity_data_of_artifact'][$current]; ?></label>
							<input type="text" name="integrity_data_of_artifact" class="form-control" id="validation06" placeholder="" value="<?php echo htmlentities($isms_integrity_data_of_artifact, ENT_QUOTES, 'UTF-8'); ?>" required>
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
		<p class="back"><a href="/src/evidence.php" target="_SELF"><i class="bi bi-arrow-return-left"></i></a></p>
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
	
	var arty = document.querySelector( "input[name='artifact_type']" );
	let arty_v = arty.value;
	var arow = document.querySelector( "input[name='artifact_owner_name']" );
	let arow_v = arow.value;
	var inar = document.querySelector( "input[name='integrity_data_of_artifact']" );
	let inar_v = inar.value;
	
	if (arty_v.length < 2 || arty_v.length > 99){
		<?php echo "alert('".$lengui['serr1'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (arow_v.length < 3 || arow_v.length > 150){
		<?php echo "alert('".$lengui['serr2'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (inar_v.length < 1 || inar_v.length > 255){
		<?php echo "alert('".$lengui['serr3'][$current]."');"; ?>
		succeed = false;
	}// end if
	    
	if(succeed == true){
		document.getElementById('manageForm').name=<?php if($purpose == 'manage') { echo "'".'btnManage'."'"; } else { echo "'".'btnAdd'."'"; } ?>;
		document.getElementById('manageForm').action='manage.evidence.php';
		document.getElementById('manageForm').submit();
		return(true);
	}else {
		return(false);
	}
}
</script>
</body>
</html>
