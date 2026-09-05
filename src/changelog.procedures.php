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

$return = '/src/changelog.procedures.php';

// Simple language chooser
$lengui = array(
    'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
    'title' => array ('en' => 'Manegement of documents of procedures - Information Security Management System', 'lt' => 'Procedūrų dokumentų vadyba - Informacijos saugos valdymo sistema', 'pl' => 'Kierowanie o dokumentów procedur - System zarządzania bezpieczeństwem informacji'),
    'nav_page' => array ('en' => 'Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistema', 'pl' => 'System zarządzania bezpieczeństwem informacji'),
    'nav_title' => array ('en' => 'Management of ISMS documents of procedures', 'lt' => 'ISVS procedūrų dokumentų vadyba', 'pl' => 'Kierowanie o SZBI dokumentów procedur'),
    'logout' => array ('en' => 'Log Out', 'lt' => 'Atsijungti', 'pl' => 'Wyloguj'),
    'cookie' => array ('en' => 'Wrong HTTP Cookie', 'lt' => 'Neteisingas HTTP Cookie', 'pl' => 'Blędne HTTP Cookie'),
    'action-manage' => array ('en' => 'Manage records of documents of procedures', 'lt' => 'Valdyti naujus procedūrų dokumentų įrašus', 'pl' => 'Wprowadzenie nowych wpisów do dokumentów procedur'),
    'button-manage' => array ('en' => 'Manage records', 'lt' => 'Valdyti įrašus', 'pl' => 'Kierować wpisów'),
    'document_name' => array ('en' => 'Document name', 'lt' => 'Dokumento pavadinimas', 'pl' => 'Tytuł dokumentu'),
    'document_name' => array ('en' => 'Document name', 'lt' => 'Dokumento pavadinimas', 'pl' => 'Tytuł dokumentu'),
    'document_status' => array ('en' => 'Document status', 'lt' => 'Dokumento būsena', 'pl' => 'Położenie dokumentu'),
    'document_status1' => array ('en' => 'Draft', 'lt' => 'Juodraštis', 'pl' => 'Projekt'),
    'document_status2' => array ('en' => 'Approved', 'lt' => 'Įsiteisėjęs', 'pl' => 'Prawomocny'),
    'err1' => array ('en' => 'Wrong unique number of a document of procedure', 'lt' => 'Neteisingas procedūros dokumento unikalus numeris', 'pl' => 'Nieprawidłowy unikat numer dokumenta procedury'),
    'serr1' => array ('en' => 'Wrong version of document.', 'lt' => 'Neteisinga dokumento versija.', 'pl' => 'Nieprawilna wersja dokumentu.'),
    'date' => array ('en' => 'Date:', 'lt' => 'Data:', 'pl' => 'Data:'),
    'version' => array ('en' => 'Version:', 'lt' => 'Versija:', 'pl' => 'Wersja:'),
    'main_changes' => array ('en' => 'Main changes:', 'lt' => 'Pagrindiniai pakeitimai:', 'pl' => 'Glówny zmiany:')
);

include("credens.php");

$connection = mysqli_connect($servername, $user, $pw, $db);			

if(!$connection) {
	die("Connection failed: " .mysqli_connect_error());
}

if(isset($_GET['discard'])) {
	$un = (int)$_GET['discard'];
	$checkqry = "SELECT
				IF(EXISTS(SELECT 1 FROM procedures_change_log WHERE un = ? LIMIT 1), 1, 0) AS one;";
				
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'i', $un);
	mysqli_stmt_execute($chstmt);
	$result2 = mysqli_stmt_get_result($chstmt);
	
	if(mysqli_num_rows($result2) > 0) {
	    $rowCheckData = mysqli_fetch_assoc($result2);
		
	    if($rowCheckData['one']== 0) {
		echo '<script> alert("'.$lengui['err1'][$current].'") </script>';
	    } else {
	    	$removeReq = "DELETE FROM procedures_change_log WHERE un=?;";
	    	
	    	$removeprepare = mysqli_prepare($connection, $removeReq);
	    	mysqli_stmt_bind_param($removeprepare, 'i', $un);
	    	mysqli_stmt_execute($removeprepare);
	    }
	}
	mysqli_free_result($result2);
	header("Location: procedures.php?remove_notice=1");
}

if(isset($_POST['btnManage'])) {
	$un = (int)$_POST["isms_un"];
	$document_date = $_POST["date"];
	$document_version = $_POST["version"];
	$document_main_changes = $_POST["main_changes"];
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
			$insert = "INSERT INTO procedures_change_log (procedures_un,date,version,main_changes) VALUES (?,?,?,?);";
	
			$insertprepare = mysqli_prepare($connection, $insert);
			mysqli_stmt_bind_param($insertprepare,'isss', $un, $document_date, $document_version, $document_main_changes);
			mysqli_stmt_execute($insertprepare);
		}
	}
	mysqli_free_result($result2);
}
 
$isms_un = (int)$_POST['isms_un'];
$control = $_POST['manage_sec'];
	
$selectqry = "SELECT * FROM procedures WHERE document_un = ?;";

$stmt = mysqli_prepare($connection, $selectqry);
mysqli_stmt_bind_param($stmt,'i', $isms_un);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)>0 && $control == $COOKIE_SECURITY) {
    $row = mysqli_fetch_assoc($result);
    
    $isms_un = $row["document_un"];
    $isms_document_name = $row["document_name"];
    $isms_document_status = (int)$row["document_status"];
    $isms_date = date("Y-m-d");
    mysqli_free_result($result);
} else {
    header("Location: procedures.php");
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
.head-div {
	display: flex;
	background-color: rgba(255, 255, 255, 0.5);
	border: 1px solid black;
	border-radius: 20px;
	margin-top: 10px;
	margin-left: 5px;
	margin-right: 5px;
}
		
.cell {
	width: 200px;
	text-align: justify;
	font-family: Helvetica, sans-serif;
	color: black;
	background-color: rgba(255, 255, 255, 0.5);
	padding: 10px;
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
	<!-- Navigation Bar -->
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="#"><?php echo $lengui['nav_page'][$current]; ?></a>
			</div>
    
			<ul class="nav navbar-nav">
				<li class="active"><a href="#"><?php echo $lengui['nav_title'][$current]; ?></a></li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="useraccount.php"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION["username"]; ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> <?php echo $lengui['logout'][$current]; ?></a></li>
			</ul>
		</div>
	</nav>

	<!-- Management Form -->
	<form name="manageForm" method="POST" action="changelog.procedures.php" enctype="multipart/form-data">
		<input type="hidden" name="isms_un" value="<?php echo $isms_un; ?>">
		<input type="hidden" name="manage_sec" value="<?php echo $COOKIE_SECURITY; ?>">	
		<center>
		<div>
			<table width="100%" >
				<tr>
					<td style="padding: 5px;">
						<div class="col-md-4 mb-3" style="text-align: center;">
							<h1 style="color: white;"><?php echo $lengui['action-manage'][$current]; ?></h1>
						</div>
					</td>
				</tr>

				<tr>
					<td class="view-cell">																						<!--document_status-->
						<font style="margin-left: 20px; color: white; font-size: 15px; font-family: Helvetica, sans-serif;">
							<b><?php echo $lengui['document_status'][$current]; ?></b> <?php echo htmlentities($lengui['document_status'.$isms_document_status][$current], ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
				</tr>

				<tr>
					<td class="view-cell">																						<!--document_name-->
						<font style="margin-left: 20px; color: white; font-size: 15px; font-family: Helvetica, sans-serif;">
							<b><?php echo $lengui['document_name'][$current]; ?></b> <?php echo htmlentities($isms_document_name, ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--date-->
						<div class="col-md-3 mb-3">
						  <label for="validation01"><?php echo $lengui['date'][$current]; ?></label>
						  <input type="date" name="date" class="form-control" id="validation01" placeholder="" value="<?php echo htmlentities($isms_date, ENT_QUOTES, 'UTF-8'); ?>" required>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--version-->
						<div class="col-md-4 mb-3">
							<label for="validation02"><?php echo $lengui['version'][$current]; ?></label>
							<input type="text" name="version" class="form-control" id="validation02" placeholder="" value="" required>
						</div>	
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--main_changes-->
						<div class="col-md-4 mb-3">
							<label for="validation03"><?php echo $lengui['main_changes'][$current]; ?></label>
							<textarea name="main_changes" class="form-control" id="validation03" placeholder="" rows="5" required></textarea>
						</div>
					</td>
				</tr>
				
				<tr>
					<td colspan=2 style="padding-left:250px;">	<!--submit button-->
						<div class="col-md-4 mb-3" style="padding-top: 5px;">
							<button name="<?php echo 'btnManage'; ?>" class="btn btn-primary" type="submit" style="color: white; font-size: 15px; align:center;"  onclick="JavaScript:return validateManageForm();"><?php echo $lengui['button-manage'][$current]; ?></button>
						</div>
					</td>
				</tr>
			</table>
		</div>
<?php
            $selectqry2 = "	SELECT 
					un,
					procedures_un,
					date,
					version,
					main_changes
				FROM 
					procedures_change_log
				WHERE 
					procedures_un = ?
				ORDER BY 
					version DESC;";
						
            $stmt = mysqli_prepare($connection, $selectqry2);
            mysqli_stmt_bind_param($stmt,'i', $isms_un);
            mysqli_stmt_execute($stmt);
            $result2 = mysqli_stmt_get_result($stmt);
						
            if(mysqli_num_rows($result2)>0) {
                $i = 1;
                while ($row2 = mysqli_fetch_array($result2)) {
                    $isms_procedure_date = $row2["date"];
                    $isms_procedure_version = $row2["version"];
                    $isms_procedure_main_changes = $row2["main_changes"];
?>
		<div style="margin-top: 2%; z-index: 0;">	
			<div style="margin-bottom: 10px; margin-left: 10%;">
				<div>
					<div class="head-div">
						<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b><?php echo $lengui['date'][$current]; ?></b></div>
						<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($isms_procedure_date, ENT_QUOTES, 'UTF-8'); ?></div>
					</div>

					<div class="head-div">
						<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b><?php echo $lengui['version'][$current]; ?></b></div>
						<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($isms_procedure_version, ENT_QUOTES, 'UTF-8'); ?></div>
					</div>

					<div class="head-div">
						<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b><?php echo $lengui['main_changes'][$current]; ?></b></div>
						<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($isms_procedure_main_changes, ENT_QUOTES, 'UTF-8'); ?></div>
					</div>
				</div>
			</div>									
		</div>
<?php
                $i++;
                }
            }
            mysqli_free_result($result2);
?>
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
	
	var version = document.querySelector( "input[name='version']" );
	let version_v = version.value;
	
	if (version_v.length < 1 || version_v.length > 9){
		<?php echo "alert('".$lengui['serr1'][$current]."');"; ?>
		succeed = false;
	}// end if
	    
	if(succeed == true){
		document.getElementById('manageForm').name='btnManage';
		document.getElementById('manageForm').action='changelog.procedures.php';
		document.getElementById('manageForm').submit();
		return(true);
	}else {
		return(false);
	}
}
</script>
</body>
</html>
