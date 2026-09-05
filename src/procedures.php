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

$return = '/src/procedures.php';

// Simple language chooser
$lengui = array(
    'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
    'title' => array ('en' => 'Procedures - Information Security Management System', 'lt' => 'Procedūros - Informacijos saugos valdymo sistema', 'pl' => 'Procedury - System zarządzania bezpieczeństwem informacji'),
    'nav_page' => array ('en' => 'Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistema', 'pl' => 'System zarządzania bezpieczeństwem informacji'),
    'nav_title' => array ('en' => 'ISMS procedures', 'lt' => 'ISVS procedūros', 'pl' => 'SZBI procedury'),
    'nav_title_new' => array ('en' => 'Add new procedure', 'lt' => 'Pridėti naują procedūrą', 'pl' => 'Dodawanie nowe procedurze'),
    'nav_title_tabel' => array ('en' => 'Initiate backup', 'lt' => 'Įgyvendinti atsarginę kopiją', 'pl' => 'Inicjować kopia zapasowa'),
    'remove_notice' => array ('en' => 'Your procedure has been deleted.', 'lt' => 'Jūsų procedūra buvo pašalinta.', 'pl' => 'Wasze procedura bylo usuwane.'),
    'create_notice' => array ('en' => 'Your procedure has been created.', 'lt' => 'Jūsų procedūra buvo pridėta.', 'pl' => 'Wasze procedura bylo prowadzone.'),
    'logout' => array ('en' => 'Log Out', 'lt' => 'Atsijungti', 'pl' => 'Wyloguj'),
    'procedures' => array ('en' => 'Procedures', 'lt' => 'Proceduros', 'pl' => 'Procedury'),
    'isms' => array ('en' => 'ISMS', 'lt' => 'ISVS', 'pl' => 'SZBI'),
    'backup' => array ('en' => 'Backup of ISMS procedures', 'lt' => 'ISVS procedūrų atsarginė kopija', 'pl' => 'Kopia zapasowa o SZBI procedury'),
    'search' => array ('en' => 'Search:', 'lt' => 'Paieška:', 'pl' => ' Szukać:'),
    'discard' => array ('en' => 'Discard', 'lt' => 'Išmesti', 'pl' => 'Odrzucić'),
    'filter_type' => array ('en' => 'Search type', 'lt' => 'Paieškos tipas', 'pl' => 'Typ szukanie'),
    'filter_type1' => array ('en' => 'Document name', 'lt' => 'Dokumento pavadinimas', 'pl' => 'Tytuł dokumentu'),
    'filter_type2' => array ('en' => 'Document type', 'lt' => 'Dokumento tipas', 'pl' => 'Typ dokumentu'),
    'filter_type3' => array ('en' => 'Document owner', 'lt' => 'Dokumento savininkas', 'pl' => 'Właściciel dokumentu'),
    'filter_type4' => array ('en' => 'Document status', 'lt' => 'Dokumento būsena', 'pl' => 'Położenie dokumentu'),
    'filter_search' => array ('en' => 'Initiate', 'lt' => 'Inicijuoti', 'pl' => 'Inicjować'),
    'filter_clear' => array ('en' => 'Empty it', 'lt' => 'Ištuštinti', 'pl' => 'Puści'),
    'following_info' => array ('en' => 'Information about procedure No.', 'lt' => 'Informacija apie procedūrą Nr.', 'pl' => 'Informacje o procedurze Nr.'),
    'document_un' => array ('en' => 'No. of document', 'lt' => 'Dokumento Nr.', 'pl' => 'Nr. dokumentu'),
    'document_type' => array ('en' => 'Document type', 'lt' => 'Dokumento tipas', 'pl' => 'Typ dokumentu'),
    'document_type1' => array ('en' => 'Incident report', 'lt' => 'Pranešimas apie incidentą', 'pl' => 'Raport incydentu'),
    'document_type2' => array ('en' => 'Policy', 'lt' => 'Norma', 'pl' => 'Norm'),
    'document_type3' => array ('en' => 'Procedure', 'lt' => 'Procedūra', 'pl' => 'Procedura'),
    'document_type4' => array ('en' => 'Standard', 'lt' => 'Standartas', 'pl' => 'Standard'),
    'document_type5' => array ('en' => 'Guideline', 'lt' => 'Gairės', 'pl' => 'Wytyczne'),
    'document_type6' => array ('en' => 'Template', 'lt' => 'Paruoštukė', 'pl' => 'Szablon'),
    'document_type7' => array ('en' => 'Audit', 'lt' => 'Audito ataskaita', 'pl' => 'Odpowiedzialna zgłoszenie audytu'),
    'document_language' => array ('en' => 'Original document language', 'lt' => 'Dokumento originalo kalba', 'pl' => 'Język oryginału dokumentu'),
    'document_name' => array ('en' => 'Document name', 'lt' => 'Dokumento pavadinimas', 'pl' => 'Tytuł dokumentu'),
    'document_status' => array ('en' => 'Document status', 'lt' => 'Dokumento būsena', 'pl' => 'Położenie dokumentu'),
    'document_status1' => array ('en' => 'Draft', 'lt' => 'Juodraštis', 'pl' => 'Projekt'),
    'document_status2' => array ('en' => 'Approved', 'lt' => 'Įsiteisėjęs', 'pl' => 'Prawomocny'),
    'document_status3' => array ('en' => 'Obsolete', 'lt' => 'Atšauktas', 'pl' => 'Anulowane'),
    'document_owner' => array ('en' => 'Document owner', 'lt' => 'Dokumento savininkas', 'pl' => 'Właściciel dokumentu'),
    'review_date' => array ('en' => 'Review date', 'lt' => 'Patikrinimo data', 'pl' => 'Data przeglądu'),
    'review_status' => array ('en' => 'Review status', 'lt' => 'Patikrinimo būsena', 'pl' => 'Położenie przeglądu'),
    'review_status0' => array ('en' => 'Not reviewed', 'lt' => 'Nepatikrinta', 'pl' => 'Nie przeglądny'),
    'review_status1' => array ('en' => 'Reviewed', 'lt' => 'Patikrinta', 'pl' => 'Przeglądny'),
    'following_review_date' => array ('en' => 'Following review date', 'lt' => 'Sekanti patikrinimo data', 'pl' => 'Następna data przeglądanie'),
    'no' => array ('en' => 'No.:', 'lt' => 'Nr.:', 'pl' => 'Nr.:'),
    'date' => array ('en' => 'Date:', 'lt' => 'Data:', 'pl' => 'Data:'),
    'version' => array ('en' => 'Version:', 'lt' => 'Versija:', 'pl' => 'Wersja:')
);

include_once("xlsxwriter.class.php");
include("credens.php");

$connection = mysqli_connect($servername, $user, $pw, $db);

if(!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}

if(isset($_GET["tabel"])) {
	if($_GET["tabel"] == 1) {
	$filename = "tabel-pcd-".date("Ymd").".xlsx";
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	
	$writer = new XLSXWriter();
		
	$keywords = array($lengui['isms'][$current],$lengui['procedures'][$current]);
	$writer->setTitle($lengui['procedures'][$current]);
	$writer->setSubject($lengui['procedures'][$current]);
	$writer->setAuthor($lengui['isms'][$current]);
	$writer->setCompany($lengui['isms'][$current]);
	$writer->setKeywords($keywords);
	$writer->setDescription($lengui['backup'][$current].'.');
	
	$rows = array(
		array($lengui['backup'][$current])
	);
	$sheet1 = 'tabel';
	
	$header = array("string","integer","string","integer","string");
		
	$selectqry = "SELECT `document_language`, `document_type`, `document_name`, `document_status`, `document_owner` FROM `procedures`;";
	
	if($result=mysqli_query($connection,$selectqry)) {
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = [$row["document_language"],$row["document_type"],$row["document_name"],$row["document_status"],$row["document_owner"]];
		}
		mysqli_free_result($result);
	}
		
	$writer->writeSheetHeader($sheet1, $header, $col_options = ['suppress_row'=>true] );
	foreach($rows as $row) {
		$writer->writeSheetRow($sheet1, $row);
	}
	$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=4);
		
	$writer->writeToStdOut();
		
	mysqli_close($connection);
	exit(0);
	}
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
body {
	font-family: Verdana, sans-serif;
	color: #FFFFFF;
	margin: 0;
}

* {
	box-sizing: border-box;
}

.row > .column {
	padding: 0 8px;
}

.row:after {
	content: "";
	display: table;
	clear: both;
}

.column {
	float: left;
	width: 25%;
}

/* The Modal (background) */
.modal {
	display: none;
	position: fixed;
	z-index: 1;
	padding-top: 100px;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	overflow: auto;
	background-color: black;
}

/* Modal Content */
.modal-content {
	position: relative;
	background-color: #fefefe;
	margin: auto;
	padding: 0;
	width: 90%;
	max-width: 1200px;
}

/* The Close Button */
.close {
	color: white;
	position: absolute;
	top: 10px;
	right: 25px;
	font-size: 35px;
	font-weight: bold;
}

.close:hover,
.close:focus {
	color: #999;
	text-decoration: none;
	cursor: pointer;
}

.mySlides {
	display: none;
}

.cursor {
	cursor: pointer;
}

/* Next & previous buttons */
.prev,
.next {
	cursor: pointer;
	position: absolute;
	top: 50%;
	width: auto;
	padding: 16px;
	margin-top: -50px;
	color: white;
	background-color: rgba(0, 0, 0, 0.5);
	font-weight: bold;
	font-size: 20px;
	transition: 0.6s ease;
	border-radius: 0 3px 3px 0;
	user-select: none;
	-webkit-user-select: none;
}

/* Position the "next button" to the right */
.next {
	right: 0;
	border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover,
.next:hover {
	background-color: rgba(0, 0, 0, 0.8);
}

/* Number text (1/3 etc) */
.numbertext {
	color: #f2f2f2;
	font-size: 12px;
	padding: 8px 12px;
	position: absolute;
	top: 0;
	font-family: Helvetica, sans-serif;
}

img {
	display: block;
	margin-right: auto;
	margin-left: auto;
	margin-bottom: -4px;
}

.caption-container {
	text-align: center;
	background-color: black;
	padding: 2px 16px;
	color: white;
}

.demo {
	opacity: 0.6;
}

.active,
.demo:hover {
	opacity: 1;
}

img.hover-shadow {
	transition: 0.3s;
}

.hover-shadow:hover {
	box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}

.vertical-menu {
	width:300px;
}

.vertical-menu a {
	background-color: #eee;
	color: black;
	display: block;
	padding: 12px;
	text-decoration: none;
}

.vertical-menu a:hover {
	background-color: #ccc;
}

.vertical-menu a.active {
	background-color: #04AA6D;
	color: white;
}
		
table.view-table {
	width: 60%;
	margin-left: auto;
	margin-right: auto;
	background-color: rgba(192, 192, 192, 0.5);
	color: black;
	margin-bottom: 20px;
}
		
tr.view-row {
	border-collapse: collapse;
}

tr.view-row:hover {
	cursor: pointer;
}
		
td.view-cell {
	padding: 5px 0px 5px 15px; /*top right bottom left*/
}
		
.numbertext {
	background-color: rgba(0, 0, 0, 0.5);
}

.view-head {
	background-color: rgba(23, 52, 86, 0.88);
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
		
.pagenumbers {
	font-size: 15px;
	color: white;
	text-decoration: none;
	padding: 8px;
	border-radius: 8px;
}
		
.pagenumbers:hover {
	color: white;
	background-color: rgba(255, 255, 255, 0.5);
}
		
.validate-buttons {
	width: 80px;
	color: white;
	font-family: Helvetica, sans-serif;
	cursor: pointer;
	border: 0px;
	border-radius: 20px;
	padding: 8px;
	font-weight: bold;
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
				<li><a href="manage.procedures.php"><?php echo $lengui['nav_title_new'][$current]; ?></a></li>
				<li><a href="procedures.php?tabel=1"><?php echo $lengui['nav_title_tabel'][$current]; ?></a></li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="useraccount.php"><span class="glyphicon glyphicon-user"></span> <?php echo isset($_SESSION["username"])? $_SESSION["username"] : ''; ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span><?php echo $lengui['logout'][$current]; ?></a></li>
			</ul>
		</div>
	</nav>
<?php
if(isset($_GET['remove_notice']) && $_GET['remove_notice'] == 1) {
?>
			<div class="container">
				<div class="alert alert-danger alert-dismissible fade in">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<?php echo $lengui['remove_notice'][$current]; ?>
				</div>
			</div>
<?php
}
?>
<?php
if(isset($_GET['create_notice']) && $_GET['create_notice'] == 1) {
?>
			<div class="container">
				<div class="alert alert-danger alert-dismissible fade in">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<?php echo $lengui['create_notice'][$current]; ?>
				</div>
			</div>
<?php
}
?>
	
<!-- Brief view to expanded view -->
<table class="view-table">
<?php
$src = "";
$tip = "A";
$type_v = [];
$status_v = [];
    
if(isset($_POST['btnSearch'])) {
    $type = (int)$_POST['filter_type'];
    $name = $_POST['filter_name'];
    $name = str_replace(["'", '"', '#', '-', '%', '_'], ['', '', '', '', '', ''], $name);
    $name = mb_strtolower($name);
    $control = $_POST['filter_sec'];
    if($control == $COOKIE_SECURITY) {
        if($type == 1) {//document_name
            $src = "WHERE document_name LIKE ?";
            $tip = "B";
            $_SESSION['filter'] = array('type' => 1, 'name' => $name);
        } elseif($type == 2) {//document_type
            if(strpos($name,'incident') !== false || strpos($name,'report') !== false || strpos($name,'raport') !== false  || strpos($name,'pranešimas') !== false || strpos($name,'incidentas') !== false || strpos($name,'incidentą') !== false || strpos($name,'incydent') !== false || strpos($name,'incydentu') !== false) { $type_v[] = 1; }
            if(strpos($name,'policy') !== false || strpos($name,'norma') !== false || strpos($name,'norm') !== false) { $type_v[] = 2; }
            if(strpos($name,'procedure') !== false || strpos($name,'procedūra') !== false || strpos($name,'procedura') !== false) { $type_v[] = 3; }
            if(strpos($name,'standard') !== false || strpos($name,'standartas') !== false) { $type_v[] = 4; }
            if(strpos($name,'guideline') !== false || strpos($name,'guide') !== false || strpos($name,'gairės') !== false || strpos($name,'wytyczne') !== false) { $type_v[] = 5; }
            if(strpos($name,'template') !== false || strpos($name,'paruoštukė') !== false || strpos($name,'szablon') !== false) { $type_v[] = 6; }
            if(strpos($name,'audit') !== false || strpos($name,'audito') !== false || strpos($name,'auditu') !== false || strpos($name,'ataskaita') !== false) { $type_v[] = 7; }
            
            $siz = count($type_v);
            
            switch ($siz) {
            case 0:
                $src = "";
                $tip = "A";
                break;
            case 1:
                $src = "WHERE document_type = ?";
                $tip = "C1";
                break;
            case 2:
                $src = "WHERE document_type = ? OR document_type = ?";
                $tip = "C2";
                break;
            case 3:
                $src = "WHERE document_type = ? OR document_type = ? OR document_type = ?";
                $tip = "C3";
                break;
            case 4:
                $src = "WHERE document_type = ? OR document_type = ? OR document_type = ? OR document_type = ?";
                $tip = "C4";
                break;
            case 5:
                $src = "WHERE document_type = ? OR document_type = ? OR document_type = ? OR document_type = ? OR document_type = ?";
                $tip = "C5";
                break;
            case 6:
                $src = "WHERE document_type = ? OR document_type = ? OR document_type = ? OR document_type = ? OR document_type = ? OR document_type = ?";
                $tip = "C6";
                break;
            case 7:
                $src = "WHERE document_type = ? OR document_type = ? OR document_type = ? OR document_type = ? OR document_type = ? OR document_type = ? OR document_type = ?";
                $tip = "C7";
                break;
            }
            $_SESSION['filter'] = array('type' => 2, 'name' => $name);
        } elseif($type == 3) {//document_owner_name
            $src = "WHERE document_owner LIKE ?";
            $tip = "D";
            $_SESSION['filter'] = array('type' => 3, 'name' => $name);
        } elseif($type == 4) {//document_status
            if(strpos($name,'draft') !== false || strpos($name,'juodraštis') !== false || strpos($name,'projekt') !== false) { $status_v[] = 1; }
            if(strpos($name,'approved') !== false || strpos($name,'įsiteisėjęs') !== false || strpos($name,'prawomocny') !== false) { $status_v[] = 2; }
            if(strpos($name,'obsolete') !== false || strpos($name,'atšauktas') !== false || strpos($name,'anulowane') !== false) { $status_v[] = 3; }
            
            $siz = count($status_v);
            
            switch ($siz) {
            case 0:
                $src = "";
                $tip = "A";
                break;
            case 1:
                $src = "WHERE document_status = ?";
                $tip = "E1";
                break;
            case 2:
                $src = "WHERE document_status = ? OR document_status = ?";
                $tip = "E2";
                break;
            case 3:
                $src = "WHERE document_status = ? OR document_status = ? OR document_status = ?";
                $tip = "E3";
                break;
            }
            $_SESSION['filter'] = array('type' => 4, 'name' => $name);
        } else {
            $_SESSION['filter'] = array('type' => 0, 'name' => '');
        }
    }
} elseif(isset($_POST['btnClear'])) {
    $_SESSION['filter'] = array('type' => 0, 'name' => '');
} elseif(!empty($_SESSION['filter']['type']) && !empty($_SESSION['filter']['name'])) {
        if($type == 1) {//document_name
            $src = "WHERE document_name LIKE ?";
            $tip = "B";
        } elseif($type == 2) {//document_type
            if(strpos($name,'incident') !== false || strpos($name,'report') !== false || strpos($name,'raport') !== false  || strpos($name,'pranešimas') !== false || strpos($name,'incidentas') !== false || strpos($name,'incidentą') !== false || strpos($name,'incydent') !== false || strpos($name,'incydentu') !== false) { $type_v[] = 1; }
            if(strpos($name,'policy') !== false || strpos($name,'norma') !== false || strpos($name,'norm') !== false) { $type_v[] = 2; }
            if(strpos($name,'procedure') !== false || strpos($name,'procedūra') !== false || strpos($name,'procedura') !== false) { $type_v[] = 3; }
            if(strpos($name,'standard') !== false || strpos($name,'standartas') !== false) { $type_v[] = 4; }
            if(strpos($name,'guideline') !== false || strpos($name,'guide') !== false || strpos($name,'gairės') !== false || strpos($name,'wytyczne') !== false) { $type_v[] = 5; }
            if(strpos($name,'template') !== false || strpos($name,'paruoštukė') !== false || strpos($name,'szablon') !== false) { $type_v[] = 6; }
            if(strpos($name,'audit') !== false || strpos($name,'audito') !== false || strpos($name,'auditu') !== false || strpos($name,'ataskaita') !== false) { $type_v[] = 7; }
            
            $siz = count($type_v);
            
            switch ($siz) {
            case 0:
                $src = "";
                $tip = "A";
                break;
            case 1:
                $src = "WHERE document_type = ?";
                $tip = "C1";
                break;
            case 2:
                $src = "WHERE document_type = ? OR document_type = ?";
                $tip = "C2";
                break;
            case 3:
                $src = "WHERE document_type = ? OR document_type = ? OR document_type = ?";
                $tip = "C3";
                break;
            case 4:
                $src = "WHERE document_type = ? OR document_type = ? OR document_type = ? OR document_type = ?";
                $tip = "C4";
                break;
            case 5:
                $src = "WHERE document_type = ? OR document_type = ? OR document_type = ? OR document_type = ? OR document_type = ?";
                $tip = "C5";
                break;
            case 6:
                $src = "WHERE document_type = ? OR document_type = ? OR document_type = ? OR document_type = ? OR document_type = ? OR document_type = ?";
                $tip = "C6";
                break;
            case 7:
                $src = "WHERE document_type = ? OR document_type = ? OR document_type = ? OR document_type = ? OR document_type = ? OR document_type = ? OR document_type = ?";
                $tip = "C7";
                break;
            }
        } elseif($type == 3) {//document_owner_name
            $src = "WHERE document_owner LIKE ?";
            $tip = "D";
        } elseif($type == 4) {//document_status
            if(strpos($name,'draft') !== false || strpos($name,'juodraštis') !== false || strpos($name,'projekt') !== false) { $status_v[] = 1; }
            if(strpos($name,'approved') !== false || strpos($name,'įsiteisėjęs') !== false || strpos($name,'prawomocny') !== false) { $status_v[] = 2; }
            if(strpos($name,'obsolete') !== false || strpos($name,'atšauktas') !== false || strpos($name,'anulowane') !== false) { $status_v[] = 3; }
            
            $siz = count($status_v);
            
            switch ($siz) {
            case 0:
                $src = "";
                $tip = "A";
                break;
            case 1:
                $src = "WHERE document_status = ?";
                $tip = "E1";
                break;
            case 2:
                $src = "WHERE document_status = ? OR document_status = ?";
                $tip = "E2";
                break;
            case 3:
                $src = "WHERE document_status = ? OR document_status = ? OR document_status = ?";
                $tip = "E3";
                break;
            }
        } else {
            $src = "";
            $tip = "A";
        }
    } else {
    $_SESSION['filter'] = array('type' => 0, 'name' => '');
}
?>
<!-- Filter -->
					<tr class="view-row">
						<td class="view-cell view-head">
							<p><?php echo $lengui['search'][$current]; ?></p>
							<form method="POST" action="procedures.php">
								<select name="filter_type" class="form-control">
									<option value="0" <?php if(empty($_SESSION['filter']['type'])){ ?>selected="selected"<?php } ?>>--<?php echo $lengui['filter_type'][$current]; ?>--</option>
									<option value="1" <?php if($_SESSION['filter']['type'] == 1){ ?>selected="selected"<?php } ?>><?php echo $lengui['filter_type1'][$current]; ?></option>
									<option value="2" <?php if($_SESSION['filter']['type'] == 2){ ?>selected="selected"<?php } ?>><?php echo $lengui['filter_type2'][$current]; ?></option>
									<option value="3" <?php if($_SESSION['filter']['type'] == 3){ ?>selected="selected"<?php } ?>><?php echo $lengui['filter_type3'][$current]; ?></option>
									<option value="4" <?php if($_SESSION['filter']['type'] == 4){ ?>selected="selected"<?php } ?>><?php echo $lengui['filter_type4'][$current]; ?></option>
								</select>
								<input type="text" name="filter_name" class="form-control" placeholder="" value="<?php echo htmlentities($_SESSION['filter']['name']); ?>">
								<input type="hidden" name="filter_sec" value="<?php echo $COOKIE_SECURITY; ?>">				
								<input type="submit" name="btnSearch" value="<?php echo $lengui['filter_search'][$current]; ?>" style="font-weight:bold;">
								<input type="submit" name="btnClear" value="<?php echo $lengui['filter_clear'][$current]; ?>" style="font-weight:bold;">
							</form>
						</td>
					</tr>
<?php
    $selectqry = "SELECT * FROM procedures {$src};";
    
    switch ($tip) {
        case "A":
            $result = mysqli_query($connection, $selectqry);
            break;
        case "B":
            $stmt = mysqli_prepare($connection, $selectqry);
            $qry = '%'.$name.'%';
            mysqli_stmt_bind_param($stmt,'s', $qry);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            break;
        case "C1":
            $stmt = mysqli_prepare($connection, $selectqry);
            mysqli_stmt_bind_param($stmt,'i', $type_v[0]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            break;
        case "C2":
            $stmt = mysqli_prepare($connection, $selectqry);
            mysqli_stmt_bind_param($stmt,'ii', $type_v[0], $type_v[1]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            break;
        case "C3":
            $stmt = mysqli_prepare($connection, $selectqry);
            mysqli_stmt_bind_param($stmt,'iii', $type_v[0], $type_v[1], $type_v[2]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            break;
        case "C4":
            $stmt = mysqli_prepare($connection, $selectqry);
            mysqli_stmt_bind_param($stmt,'iiii', $type_v[0], $type_v[1], $type_v[2], $type_v[3]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            break;
        case "C5":
            $stmt = mysqli_prepare($connection, $selectqry);
            mysqli_stmt_bind_param($stmt,'iiiii', $type_v[0], $type_v[1], $type_v[2], $type_v[3], $type_v[4]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            break;
        case "C6":
            $stmt = mysqli_prepare($connection, $selectqry);
            mysqli_stmt_bind_param($stmt,'iiiiii', $type_v[0], $type_v[1], $type_v[2], $type_v[3], $type_v[4], $type_v[5]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            break;
        case "C7":
            $stmt = mysqli_prepare($connection, $selectqry);
            mysqli_stmt_bind_param($stmt,'iiiiiii', $type_v[0], $type_v[1], $type_v[2], $type_v[3], $type_v[4], $type_v[5], $type_v[6]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            break;
         case "D":
            $stmt = mysqli_prepare($connection, $selectqry);
            mysqli_stmt_bind_param($stmt,'s', $name);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            break;
         case "E1":
            $stmt = mysqli_prepare($connection, $selectqry);
            mysqli_stmt_bind_param($stmt,'i', $status_v[0]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            break;
        case "E2":
            $stmt = mysqli_prepare($connection, $selectqry);
            mysqli_stmt_bind_param($stmt,'ii', $status_v[0], $status_v[1]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            break;
        case "E3":
            $stmt = mysqli_prepare($connection, $selectqry);
            mysqli_stmt_bind_param($stmt,'iii', $status_v[0], $status_v[1], $status_v[2]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            break;
    }
	
    $total_rows = mysqli_num_rows($result);
	
    $limit = 5;
    // get the required number of pages
    $total_pages = ceil ($total_rows / $limit);
	
    // update the active page number
    if (!isset ($_GET['page']) ) { 
        $page_number = 1;  
    } else {
        $page_number = (int)$_GET['page'];
    }

    // get the initial page number
    $initial_page = ($page_number-1) * $limit;

    // get data of selected rows per page
    $getQuery = "SELECT * FROM procedures {$src} ORDER BY document_status ASC LIMIT " . $initial_page . ',' . $limit;
    
    switch ($tip) {
        case "A":
            $resultLimit = mysqli_query($connection, $getQuery);
            break;
        case "B":
            $stmt = mysqli_prepare($connection, $getQuery);
            $qry = '%'.$name.'%';
            mysqli_stmt_bind_param($stmt,'s', $qry);
            mysqli_stmt_execute($stmt);
            $resultLimit = mysqli_stmt_get_result($stmt);
            break;
        case "C1":
            $stmt = mysqli_prepare($connection, $getQuery);
            mysqli_stmt_bind_param($stmt,'i', $type_v[0]);
            mysqli_stmt_execute($stmt);
            $resultLimit = mysqli_stmt_get_result($stmt);
            break;
        case "C2":
            $stmt = mysqli_prepare($connection, $getQuery);
            mysqli_stmt_bind_param($stmt,'ii', $type_v[0], $type_v[1]);
            mysqli_stmt_execute($stmt);
            $resultLimit = mysqli_stmt_get_result($stmt);
            break;
        case "C3":
            $stmt = mysqli_prepare($connection, $getQuery);
            mysqli_stmt_bind_param($stmt,'iii', $type_v[0], $type_v[1], $type_v[2]);
            mysqli_stmt_execute($stmt);
            $resultLimit = mysqli_stmt_get_result($stmt);
            break;
        case "C4":
            $stmt = mysqli_prepare($connection, $getQuery);
            mysqli_stmt_bind_param($stmt,'iiii', $type_v[0], $type_v[1], $type_v[2], $type_v[3]);
            mysqli_stmt_execute($stmt);
            $resultLimit = mysqli_stmt_get_result($stmt);
            break;
        case "C5":
            $stmt = mysqli_prepare($connection, $getQuery);
            mysqli_stmt_bind_param($stmt,'iiiii', $type_v[0], $type_v[1], $type_v[2], $type_v[3], $type_v[4]);
            mysqli_stmt_execute($stmt);
            $resultLimit = mysqli_stmt_get_result($stmt);
            break;
        case "C6":
            $stmt = mysqli_prepare($connection, $getQuery);
            mysqli_stmt_bind_param($stmt,'iiiiii', $type_v[0], $type_v[1], $type_v[2], $type_v[3], $type_v[4], $type_v[5]);
            mysqli_stmt_execute($stmt);
            $resultLimit = mysqli_stmt_get_result($stmt);
            break;
        case "C7":
            $stmt = mysqli_prepare($connection, $getQuery);
            mysqli_stmt_bind_param($stmt,'iiiiiii', $type_v[0], $type_v[1], $type_v[2], $type_v[3], $type_v[4], $type_v[5], $type_v[6]);
            mysqli_stmt_execute($stmt);
            $resultLimit = mysqli_stmt_get_result($stmt);
            break;
         case "D":
            $stmt = mysqli_prepare($connection, $getQuery);
            mysqli_stmt_bind_param($stmt,'s', $name);
            mysqli_stmt_execute($stmt);
            $resultLimit = mysqli_stmt_get_result($stmt);
            break;
         case "E1":
            $stmt = mysqli_prepare($connection, $getQuery);
            mysqli_stmt_bind_param($stmt,'i', $status_v[0]);
            mysqli_stmt_execute($stmt);
            $resultLimit = mysqli_stmt_get_result($stmt);
            break;
        case "E2":
            $stmt = mysqli_prepare($connection, $getQuery);
            mysqli_stmt_bind_param($stmt,'ii', $status_v[0], $status_v[1]);
            mysqli_stmt_execute($stmt);
            $resultLimit = mysqli_stmt_get_result($stmt);
            break;
        case "E3":
            $stmt = mysqli_prepare($connection, $getQuery);
            mysqli_stmt_bind_param($stmt,'iii', $status_v[0], $status_v[1], $status_v[2]);
            mysqli_stmt_execute($stmt);
            $resultLimit = mysqli_stmt_get_result($stmt);
            break;
    }

    //display the retrieved result on the webpage 
    if(mysqli_num_rows($result)>0) {
        while ($row = mysqli_fetch_array($resultLimit)) {
            $isms_un = $row["document_un"];
            $isms_document_type = $row["document_type"];
            $isms_document_language = $row["document_language"];
            $isms_document_name = $row["document_name"];
            $isms_document_status = $row["document_status"];
            $isms_document_owner = $row["document_owner"];
            $isms_review_date = $row["review_date"];
            $isms_review_status = $row["review_status"];
            $isms_following_review_date = $row["following_review_date"];
			
?>
<!-- Brief view -->
					<tr class="view-row">
						<td class="view-cell view-head">
							<?php echo $lengui['following_info'][$current]; ?> <?php echo htmlentities($isms_un, ENT_QUOTES, 'UTF-8'); ?>
							
							<form method="POST" action="manage.procedures.php">
								<input type="hidden" name="isms_un" value="<?php echo $isms_un; ?>">
								<input type="hidden" name="manage_sec" value="<?php echo $COOKIE_SECURITY; ?>">							
								<button type="submit" name="manage" class="edit-buttons"><img src="images/edit.png" class="edit-icons"></button>
							</form>
						</td>
					</tr>					

					<tr class="view-row">
						<td class="view-cell">																					<!--document_type-->
							<font style="font-size: 20px; font-family: Tahoma, sans-serif;">
								<b><?php echo $lengui['document_type'][$current]; ?></b> <?php echo htmlentities($lengui['document_type'.$isms_document_type][$current], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--document_language-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['document_language'][$current]; ?></b> <?php echo htmlentities($isms_document_language, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--document_name-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['document_name'][$current]; ?></b> <?php echo htmlentities($isms_document_name, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>

					<tr class="view-row">
						<td class="view-cell">																					<!--document_content-->					
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
					date DESC;";
						
            $stmt = mysqli_prepare($connection, $selectqry2);
            mysqli_stmt_bind_param($stmt,'i', $isms_un);
            mysqli_stmt_execute($stmt);
            $result2 = mysqli_stmt_get_result($stmt);
						
            if(mysqli_num_rows($result2)>0) {
                $i = 1;
                while ($row2 = mysqli_fetch_array($result2)) {
                    $isms_procedure_un = $row2["un"];
                    $isms_procedure_date = $row2["date"];
                    $isms_procedure_version = $row2["version"];		
?>
							<div style="margin-top: 2%; z-index: 0;">	
								<div style="margin-bottom: 10px; margin-left: 30%;">
									<div>
										<div class="head-div">
											<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b><?php echo $lengui['no'][$current]; ?> <a href="changelog.procedures.php?discard=<?php echo $isms_procedure_un; ?>"><?php echo $lengui['discard'][$current]; ?></a></b></div>
											<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo $i; ?></div>
										</div>

										<div class="head-div">
											<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b><?php echo $lengui['date'][$current]; ?></b></div>
											<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($isms_procedure_date, ENT_QUOTES, 'UTF-8'); ?></div>
										</div>

										<div class="head-div">
											<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b><?php echo $lengui['version'][$current]; ?></b></div>
											<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($isms_procedure_version, ENT_QUOTES, 'UTF-8'); ?></div>
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
							<form method="POST" action="changelog.procedures.php">
								<input type="hidden" name="isms_un" value="<?php echo $isms_un; ?>">
								<input type="hidden" name="manage_sec" value="<?php echo $COOKIE_SECURITY; ?>">							
								<button type="submit" name="manage" class="edit-buttons"><img src="images/edit.png" class="edit-icons"></button>
							</form>
						</td>
					</tr>
										
					<tr class="view-row">
						<td class="view-cell">																						<!--document_status-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['document_status'][$current]; ?></b> <?php echo htmlentities($lengui['document_status'.$isms_document_status][$current], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--document_owner-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['document_owner'][$current]; ?></b> <?php echo htmlentities($isms_document_owner, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--review_date-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['review_date'][$current]; ?></b> <?php echo htmlentities($isms_review_date, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--review_status-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['review_status'][$current]; ?></b> <?php echo htmlentities($lengui['review_status'.$isms_review_status][$current], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--following_review_date-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['following_review_date'][$current]; ?></b> <?php echo htmlentities($isms_following_review_date, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row" style="border-bottom: 10px solid #173457;">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
<?php
        }
    }
    mysqli_free_result($result);
    mysqli_free_result($resultLimit);
?>
	<tr class="view-row" style="border-bottom: 20px solid #173457;">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	<tr>
	
	<tr style="background: #173457;">
		<td colspan=2 style="">
			<div style="text-align: center;">
<?php
				for($page_number = 1; $page_number<= $total_pages; $page_number++)
				{  
?>
					<a href = "procedures.php?page=<?php echo $page_number; ?>" class="pagenumbers"><?php echo $page_number; ?></a> 	
<?php
				}
?>
			</div>
		</td>
	</tr>

</table>
<!-- Language links -->

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
