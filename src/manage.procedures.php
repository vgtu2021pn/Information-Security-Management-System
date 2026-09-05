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

$return = '/src/manage.procedures.php';

// Simple language chooser
$lengui = array(
    'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
    'title' => array ('en' => 'Manegement of procedures - Information Security Management System', 'lt' => 'Procedūrų vadyba - Informacijos saugos valdymo sistema', 'pl' => 'Kierowanie o procedur - System zarządzania bezpieczeństwem informacji'),
    'nav_page' => array ('en' => 'Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistema', 'pl' => 'System zarządzania bezpieczeństwem informacji'),
    'nav_title' => array ('en' => 'Management of ISMS procedures', 'lt' => 'ISVS procedūrų vadyba', 'pl' => 'Kierowanie o SZBI procedur'),
    'nav_title_new' => array ('en' => 'Add new procedure', 'lt' => 'Pridėti naują procedūrą', 'pl' => 'Dodawanie nowe procedurę'),
    'nav_title_dis' => array ('en' => 'Discard this procedure', 'lt' => 'Pašalinti esamą procedūrą', 'pl' => 'Usuwanie ten procedurze'),
    'logout' => array ('en' => 'Log Out', 'lt' => 'Atsijungti', 'pl' => 'Wyloguj'),
    'cookie' => array ('en' => 'Wrong HTTP Cookie', 'lt' => 'Neteisingas HTTP Cookie', 'pl' => 'Blędne HTTP Cookie'),
    'action-add' => array ('en' => 'Add records to procedures', 'lt' => 'Įkelti naujus procedūrų įrašus', 'pl' => 'Wprowadzenie nowych wpisów do procedur'),
    'action-update' => array ('en' => 'Edit records of procedures', 'lt' => 'Keisti procedūrų įrašus', 'pl' => 'Zmiana wpisów w procedur'),
    'button-add' => array ('en' => 'Add new records', 'lt' => 'Įkelti naujus įrašus', 'pl' => 'Wprowadzenie nowych wpisów'),
    'button-update' => array ('en' => 'Edit records', 'lt' => 'Keisti įrašus', 'pl' => 'Zmiana wpisów'),
    'document_type' => array ('en' => 'Document type', 'lt' => 'Dokumento tipas', 'pl' => 'Typ dokumentu'),
    'document_type1' => array ('en' => 'Incident report', 'lt' => 'Pranešimas apie incidentą', 'pl' => 'Raport incydentu'),
    'document_type2' => array ('en' => 'Policy', 'lt' => 'Norma', 'pl' => 'Norm'),
    'document_type3' => array ('en' => 'Procedure', 'lt' => 'Procedūra', 'pl' => 'Procedura'),
    'document_type4' => array ('en' => 'Standard', 'lt' => 'Standartas', 'pl' => 'Standard'),
    'document_type5' => array ('en' => 'Guideline', 'lt' => 'Gairės', 'pl' => 'Wytyczne'),
    'document_type6' => array ('en' => 'Template', 'lt' => 'Paruoštukė', 'pl' => 'Szablon'),
    'document_type7' => array ('en' => 'Audit', 'lt' => 'Audito ataskaita', 'pl' => 'Odpowiedzialna zgłoszenie audytu'),
    'document_language' => array ('en' => 'Original document language', 'lt' => 'Dokumento originalo kalba', 'pl' => 'Język oryginału dokumentu'),
    'document_languageEN' => array ('en' => 'English', 'lt' => 'Anglų', 'pl' => 'Angielski'),
    'document_languageLT' => array ('en' => 'Lithuanian', 'lt' => 'Lietuvių', 'pl' => 'Litewski'),
    'document_languagePL' => array ('en' => 'Polish', 'lt' => 'Lenkų', 'pl' => 'Polski'),
    'document_name' => array ('en' => 'Document name', 'lt' => 'Dokumento pavadinimas', 'pl' => 'Tytuł dokumentu'),
    'document_status' => array ('en' => 'Document status', 'lt' => 'Dokumento būsena', 'pl' => 'Położenie dokumentu'),
    'document_status1' => array ('en' => 'Draft', 'lt' => 'Juodraštis', 'pl' => 'Projekt'),
    'document_status2' => array ('en' => 'Approved', 'lt' => 'Įsiteisėjęs', 'pl' => 'Prawomocny'),
    'document_status3' => array ('en' => 'Obsolete', 'lt' => 'Atšauktas', 'pl' => 'Anulowane'),
    'document_owner_name' => array ('en' => 'Document owner', 'lt' => 'Dokumento savininkas', 'pl' => 'Właściciel dokumentu'),
    'following_review_date' => array ('en' => 'Following review date', 'lt' => 'Sekanti patikrinimo data', 'pl' => 'Następna data przeglądanie'),
    'err1' => array ('en' => 'Wrong unique number of a procedure', 'lt' => 'Neteisingas procedūros unikalus numeris', 'pl' => 'Nieprawidłowy unikat numer procedury'),
    'serr1' => array ('en' => 'Wrong document name.', 'lt' => 'Neteisingas dokumento pavadinimas.', 'pl' => 'Nieprawilna nazwa dokumentu.'),
    'serr2' => array ('en' => 'Wrong document type.', 'lt' => 'Neteisingas dokumento tipas.', 'pl' => 'Nieprawidłowy typ dokumentu.'),
    'serr3' => array ('en' => 'Wrong document language.', 'lt' => 'Neteisinga dokumento kalba.', 'pl' => 'Nieprawidłowy język dokumentu.'),
    'serr4' => array ('en' => 'Wrong document owner.', 'lt' => 'Neteisingas dokumento savininkas.', 'pl' => 'Nieprawilny właściciel dokumentu.'),
    'serr5' => array ('en' => 'Wrong document status.', 'lt' => 'Neteisinga dokumento būsena.', 'pl' => 'Nieprawilny status dokumenta.')
);

include("credens.php");

$connection = mysqli_connect($servername, $user, $pw, $db);			

if(!$connection) {
	die("Connection failed: " .mysqli_connect_error());
}

if(isset($_GET['discard'])) {
	$un = (int)$_GET['discard'];
	$checkqry = "SELECT
				IF(EXISTS(SELECT 1 FROM procedures WHERE document_un = ? LIMIT 1), 1, 0) AS one;";
				
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'i', $un);
	mysqli_stmt_execute($chstmt);
	$result2 = mysqli_stmt_get_result($chstmt);
	
	if(mysqli_num_rows($result2) > 0) {
	    $rowCheckData = mysqli_fetch_assoc($result2);
		
	    if($rowCheckData['one']== 0) {
		echo '<script> alert("'.$lengui['err1'][$current].'") </script>';
	    } else {
	    	$removeReq = "DELETE FROM procedures WHERE document_un=?;";
	    	
	    	$removeprepare = mysqli_prepare($connection, $removeReq);
	    	mysqli_stmt_bind_param($removeprepare, 'i', $un);
	    	mysqli_stmt_execute($removeprepare);
	    }
	}
	mysqli_free_result($result2);
	header("Location: procedures.php?remove_notice=1");
}

if(isset($_POST['btnAdd'])) {
	$document_type = (int)$_POST["document_type"];
	$document_language = $_POST["document_language"];
	$document_name = $_POST["document_name"];
	$document_status = (int)$_POST["document_status"];
	$document_owner_name = $_POST["document_owner_name"];
	$following_review_date = $_POST["following_review_date"];
	$control = $_POST['manage_sec'];
        
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
	
	$insert = "INSERT INTO procedures (document_type,document_language,document_name,document_status,document_owner,following_review_date) VALUES (?,?,?,?,?,?);";
	
	$insertprepare = mysqli_prepare($connection, $insert);
	mysqli_stmt_bind_param($insertprepare,'ississ', $document_type, $document_language, $document_name, $document_status, $document_owner_name, $following_review_date);
	mysqli_stmt_execute($insertprepare);
	header("Location: procedures.php?create_notice=1");
}

if(isset($_POST['btnManage'])) {
	$un = $_POST["isms_un"];
	$document_type = (int)$_POST["document_type"];
	$document_language = $_POST["document_language"];
	$document_name = $_POST["document_name"];
	$document_status = (int)$_POST["document_status"];
	$document_owner_name = $_POST["document_owner_name"];
	$following_review_date = $_POST["following_review_date"];
	$control = $_POST['manage_sec'];
        
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
	
	$checkqry = "SELECT
				IF(EXISTS(SELECT 1 FROM procedures WHERE document_un = ? LIMIT 1), 1, 0) AS one;";
				
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
						procedures 
					SET 
						document_type = ?,
						document_language = ?,
						document_name = ?,
						document_status = ?,
						document_owner = ?,
						following_review_date = ?
					WHERE
						document_un = ?;";
			
			$updateprepare = mysqli_prepare($connection, $update);
			mysqli_stmt_bind_param($updateprepare,'ississi', $document_type, $document_language, $document_name, $document_status, $document_owner_name, $following_review_date, $un);
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
	
$selectqry = "SELECT * FROM procedures WHERE document_un = ?;";

$stmt = mysqli_prepare($connection, $selectqry);
mysqli_stmt_bind_param($stmt,'i', $isms_un);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)>0 && $control == $COOKIE_SECURITY) {
    $purpose = 'manage';
    $row = mysqli_fetch_assoc($result);
    
    $isms_un = $row["document_un"];
    $isms_document_type = (int)$row["document_type"];
    $isms_document_language = $row["document_language"];
    $isms_document_name = $row["document_name"];
    $isms_document_status = (int)$row["document_status"];
    $isms_document_owner_name = $row["document_owner"];
    $isms_following_review_date = $row["following_review_date"];
} else {
    $isms_un = '';
    $isms_document_type = 0;
    $isms_document_language = '';
    $isms_document_name = '';
    $isms_document_status = 0;
    $isms_document_owner_name = '';
    $isms_following_review_date = date("Y-m-d");
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
				<li class="active"><a href="manage.procedures.php"><?php echo $lengui['nav_title_new'][$current]; ?></a></li>
<?php } else { ?>
				<li><a href="manage.procedures.php"><?php echo $lengui['nav_title_new'][$current]; ?></a></li>
				<li><a href="manage.procedures.php?discard=<?php echo $isms_un; ?>".><?php echo $lengui['nav_title_dis'][$current]; ?></a></li>
<?php } ?>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="useraccount.php"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION["username"]; ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> <?php echo $lengui['logout'][$current]; ?></a></li>
			</ul>
		</div>
	</nav>

	<!-- Management Form -->
	<form name="manageForm" method="POST" action="manage.procedures.php" enctype="multipart/form-data">
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
					<td style="padding: 5px;">	<!--document_type-->
						<div class="col-md-4 mb-3">
							<label for="validation01" ><?php echo $lengui['document_type'][$current]; ?></label>
							<select name="document_type" class="form-control" id="validation01">
								<option value="0" <?php if(empty($isms_document_type)) { ?>selected="selected"<?php } ?>>--<?php echo $lengui['document_type'][$current]; ?>--</option>
								<option value="1" <?php if($isms_document_type == 1){ ?>selected="selected"<?php } ?>><?php echo $lengui['document_type1'][$current]; ?></option>
								<option value="2" <?php if($isms_document_type == 2){ ?>selected="selected"<?php } ?>><?php echo $lengui['document_type2'][$current]; ?></option>
								<option value="3" <?php if($isms_document_type == 3){ ?>selected="selected"<?php } ?>><?php echo $lengui['document_type3'][$current]; ?></option>
								<option value="4" <?php if($isms_document_type == 4){ ?>selected="selected"<?php } ?>><?php echo $lengui['document_type4'][$current]; ?></option>
								<option value="5" <?php if($isms_document_type == 5){ ?>selected="selected"<?php } ?>><?php echo $lengui['document_type5'][$current]; ?></option>
								<option value="6" <?php if($isms_document_type == 6){ ?>selected="selected"<?php } ?>><?php echo $lengui['document_type6'][$current]; ?></option>
								<option value="7" <?php if($isms_document_type == 7){ ?>selected="selected"<?php } ?>><?php echo $lengui['document_type7'][$current]; ?></option>
							</select>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--document_language-->
						<div class="col-md-4 mb-3">
							<label for="validation02" ><?php echo $lengui['document_language'][$current]; ?></label>
							<select name="document_language" class="form-control" id="validation02">
								<option value="" <?php if(empty($isms_document_language)) { ?>selected="selected"<?php } ?>>--<?php echo $lengui['document_language'][$current]; ?>--</option>
								<option value="en" <?php if($isms_document_language == 'en'){ ?>selected="selected"<?php } ?>><?php echo $lengui['document_languageEN'][$current]; ?></option>
								<option value="lt" <?php if($isms_document_language == 'lt'){ ?>selected="selected"<?php } ?>><?php echo $lengui['document_languageLT'][$current]; ?></option>
								<option value="pl" <?php if($isms_document_language == 'pl'){ ?>selected="selected"<?php } ?>><?php echo $lengui['document_languagePL'][$current]; ?></option>
							</select>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--document_name-->
						<div class="col-md-4 mb-3">
							<label for="validation03"><?php echo $lengui['document_name'][$current]; ?></label>
							<input type="text" name="document_name" class="form-control" id="validation03" placeholder="" value="<?php echo htmlentities($isms_document_name, ENT_QUOTES, 'UTF-8'); ?>" required>
						</div>	
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--document_status-->
						<div class="col-md-4 mb-3">
							<label for="validation04" ><?php echo $lengui['document_status'][$current]; ?></label>
							<select name="document_status" class="form-control" id="validation04">
								<option value="0" <?php if(empty($isms_document_status)) { ?>selected="selected"<?php } ?>>--<?php echo $lengui['document_status'][$current]; ?>--</option>
								<option value="1" <?php if($isms_document_status == 1){ ?>selected="selected"<?php } ?>><?php echo $lengui['document_status1'][$current]; ?></option>
								<option value="2" <?php if($isms_document_status == 2){ ?>selected="selected"<?php } ?>><?php echo $lengui['document_status2'][$current]; ?></option>
								<option value="3" <?php if($isms_document_status == 3){ ?>selected="selected"<?php } ?>><?php echo $lengui['document_status3'][$current]; ?></option>
							</select>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--document_owner_name-->
						<div class="col-md-4 mb-3">
							<label for="validation05"><?php echo $lengui['document_owner_name'][$current]; ?></label>
							<input type="text" name="document_owner_name" class="form-control" id="validation05" placeholder="" value="<?php echo htmlentities($isms_document_owner_name, ENT_QUOTES, 'UTF-8'); ?>" required>
						</div>	
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--following_review_date-->
						<div class="col-md-3 mb-3">
						  <label for="validation06"><?php echo $lengui['following_review_date'][$current]; ?></label>
						  <input type="date" name="following_review_date" class="form-control" id="validation06" placeholder="" value="<?php echo htmlentities($isms_following_review_date, ENT_QUOTES, 'UTF-8'); ?>" required>
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
		<p class="back"><a href="/src/procedures.php" target="_SELF"><i class="bi bi-arrow-return-left"></i></a></p>
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
	
	var doc_name = document.querySelector( "input[name='document_name']" );
	let doc_name_v = doc_name.value;
	var doc_ownr = document.querySelector( "input[name='document_owner_name']" );
	let doc_ownr_v = doc_ownr.value;
	
	var type_v = $( "#validation01" ).val();
	var lang_v = $( "#validation02" ).val();
	var stat_v = $( "#validation04" ).val();
	
	if (doc_name_v.length < 3 || doc_name_v.length > 254){
		<?php echo "alert('".$lengui['serr1'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (type_v != 1 && type_v != 2 && type_v != 3 && type_v != 4 && type_v != 5 && type_v != 6 && type_v != 7){
		<?php echo "alert('".$lengui['serr2'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (lang_v != 'en' && lang_v != 'lt' && lang_v != 'pl'){
		<?php echo "alert('".$lengui['serr3'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (doc_ownr_v.length < 3 || doc_ownr_v.length > 150){
		<?php echo "alert('".$lengui['serr4'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (stat_v != 1 && stat_v != 2 && stat_v != 3){
		<?php echo "alert('".$lengui['serr5'][$current]."');"; ?>
		succeed = false;
	}// end if
	    
	if(succeed == true){
		document.getElementById('manageForm').name=<?php if($purpose == 'manage') { echo "'".'btnManage'."'"; } else { echo "'".'btnAdd'."'"; } ?>;
		document.getElementById('manageForm').action='manage.procedures.php';
		document.getElementById('manageForm').submit();
		return(true);
	}else {
		return(false);
	}
}
</script>
</body>
</html>
