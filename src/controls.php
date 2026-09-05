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

$return = '/src/controls.php';

// Simple language chooser
$lengui = array(
    'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
    'title' => array ('en' => 'Control measures - Information Security Management System', 'lt' => 'Kontrolės priemonės - Informacijos saugos valdymo sistema', 'pl' => 'Sposoby kontroli - System zarządzania bezpieczeństwem informacji'),
    'nav_page' => array ('en' => 'Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistema', 'pl' => 'System zarządzania bezpieczeństwem informacji'),
    'nav_title' => array ('en' => 'ISMS control measures', 'lt' => 'ISVS kontrolės priemonės', 'pl' => 'SZBI sposoby kontroli'),
    'nav_title_new' => array ('en' => 'Add new controls', 'lt' => 'Pridėti naują kontrolę', 'pl' => 'Dodawanie nowe kontrol'),
    'nav_title_tabel' => array ('en' => 'Initiate backup', 'lt' => 'Įgyvendinti atsarginę kopiją', 'pl' => 'Inicjować kopia zapasowa'),
    'remove_notice' => array ('en' => 'Your control has been deleted.', 'lt' => 'Jūsų kontrolė buvo pašalinta.', 'pl' => 'Wasze kontrol bylo usuwane.'),
    'create_notice' => array ('en' => 'Your control has been created.', 'lt' => 'Jūsų kontrolė buvo pridėta.', 'pl' => 'Wasze kontrol bylo prowadzone.'),
    'logout' => array ('en' => 'Log Out', 'lt' => 'Atsijungti', 'pl' => 'Wyloguj'),
    'control' => array ('en' => 'Controls', 'lt' => 'Kontrolės', 'pl' => 'Kontroli'),
    'isms' => array ('en' => 'ISMS', 'lt' => 'ISVS', 'pl' => 'SZBI'),
    'backup' => array ('en' => 'Backup of ISMS controls', 'lt' => 'ISVS kontrolės atsarginė kopija', 'pl' => 'Kopia zapasowa o SZBI kontrol'),
    'search' => array ('en' => 'Search:', 'lt' => 'Paieška:', 'pl' => ' Szukać:'),
    'filter_type' => array ('en' => 'Search type', 'lt' => 'Paieškos tipas', 'pl' => 'Typ szukanie'),
    'filter_type1' => array ('en' => 'Control\'s name', 'lt' => 'Kontrolės pavadinimas', 'pl' => 'Nazwisko kontroli'),
    'filter_type2' => array ('en' => 'Applicability status', 'lt' => 'Pritaikomumo būsena', 'pl' => 'Stosowania status'),
    'filter_type3' => array ('en' => 'Implementation status', 'lt' => 'Įgyvendinimo būsena', 'pl' => 'Położenie zastosowania'),
    'filter_search' => array ('en' => 'Initiate', 'lt' => 'Inicijuoti', 'pl' => 'Inicjować'),
    'filter_clear' => array ('en' => 'Empty it', 'lt' => 'Ištuštinti', 'pl' => 'Puści'),
    'following_info' => array ('en' => 'Information about control No.', 'lt' => 'Informacija apie kontrolę Nr.', 'pl' => 'Informacje o kontroli Nr.'),
    'isms_control_un' => array ('en' => 'Control\'s No.', 'lt' => 'Kontrolės Nr.', 'pl' => 'Kontrol Nr.'),
    'isms_control_name' => array ('en' => 'Control\'s name', 'lt' => 'Kontrolės pavadinimas', 'pl' => 'Nazwisko kontroli'),
    'isms_control_description' => array ('en' => 'Control\'s description', 'lt' => 'Kontrolės aprašas', 'pl' => 'Opis kontroli'),
    'isms_applicability_status' => array ('en' => 'Applicability status', 'lt' => 'Pritaikomumo būsena', 'pl' => 'Stosowania status'),
    'isms_applicability_status0' => array ('en' => 'Not applicable', 'lt' => 'Nepritaikoma', 'pl' => 'Nie stosowane'),
    'isms_applicability_status1' => array ('en' => 'Applicable', 'lt' => 'Pritaikoma', 'pl' => 'Stosowane'),
    'isms_justification_text' => array ('en' => 'Justification', 'lt' => 'Pagrindimas', 'pl' => 'Usprawiedliwienie'),
    'isms_implementation_status' => array ('en' => 'Implementation status', 'lt' => 'Įgyvendinimo būsena', 'pl' => 'Położenie zastosowania'),
    'isms_implementation_status1' => array ('en' => 'Planned', 'lt' => 'Planuojama', 'pl' => 'Planowany'),
    'isms_implementation_status2' => array ('en' => 'Implemented', 'lt' => 'Įgyvendinta', 'pl' => 'Zastosowany'),
    'isms_implementation_status3' => array ('en' => 'Partial implementation', 'lt' => 'Ne visiškai įgyvendinta', 'pl' => 'Częściowy zastosowanie'),
    'isms_implementation_status4' => array ('en' => 'Not implemented', 'lt' => 'Neįgyvendinta', 'pl' => 'Nie zastosowany'),
    'isms_control_owner_name' => array ('en' => 'Control\'s owner', 'lt' => 'Kontrolės savininkas', 'pl' => 'Właściciel kontroli'),
    'isms_review_date' => array ('en' => 'Review date', 'lt' => 'Patikrinimo data', 'pl' => 'Data przeglądu'),
    'isms_review_status' => array ('en' => 'Review status', 'lt' => 'Patikrinimo būsena', 'pl' => 'Położenie przeglądu'),
    'isms_review_status0' => array ('en' => 'Not reviewed', 'lt' => 'Nepatikrinta', 'pl' => 'Nie przeglądny'),
    'isms_review_status1' => array ('en' => 'Reviewed', 'lt' => 'Patikrinta', 'pl' => 'Przeglądny')
);

include_once("xlsxwriter.class.php");
include("credens.php");

$connection = mysqli_connect($servername, $user, $pw, $db);

if(!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}

if(isset($_GET["tabel"])) {
	if($_GET["tabel"] == 1) {
	$filename = "tabel-ctrl-".date("Ymd").".xlsx";
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	
	$writer = new XLSXWriter();
		
	$keywords = array($lengui['isms'][$current],$lengui['control'][$current]);
	$writer->setTitle($lengui['control'][$current]);
	$writer->setSubject($lengui['control'][$current]);
	$writer->setAuthor($lengui['isms'][$current]);
	$writer->setCompany($lengui['isms'][$current]);
	$writer->setKeywords($keywords);
	$writer->setDescription($lengui['backup'][$current].'.');
	
	$rows = array(
		array($lengui['backup'][$current])
	);
	$sheet1 = 'tabel';
	
	$header = array("string","string","integer","string","integer","string");
		
	$selectqry = "SELECT `control_name`, `control_description`, `applicability_status`, `justification_text`, `implementation_status`, `control_owner_name` from `statement_of_applicability`;";
		
	if($result=mysqli_query($connection,$selectqry)) {
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = [$row["control_name"],$row["control_description"],$row["applicability_status"],$row["justification_text"],$row["implementation_status"],$row["control_owner_name"]];
		}
		mysqli_free_result($result);
	}
		
	$writer->writeSheetHeader($sheet1, $header, $col_options = ['suppress_row'=>true] );
	foreach($rows as $row)
		$writer->writeSheetRow($sheet1, $row);
	$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=5);
		
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
				<li><a href="manage.controls.php"><?php echo $lengui['nav_title_new'][$current]; ?></a></li>
				<li><a href="controls.php?tabel=1"><?php echo $lengui['nav_title_tabel'][$current]; ?></a></li>
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
$appl_v = "";
$impl_v = "";
    
if(isset($_POST['btnSearch'])) {
    $type = (int)$_POST['filter_type'];
    $name = $_POST['filter_name'];
    $name = str_replace(["'", '"', '#', '-', '%', '_'], ['', '', '', '', '', ''], $name);
    $name = mb_strtolower($name);
    $control = $_POST['filter_sec'];
    if($control == $COOKIE_SECURITY) {
        if($type == 1) {//control_name
            $src = "WHERE control_name LIKE ?";
            $tip = "B";
            $_SESSION['filter'] = array('type' => 1, 'name' => $name);
        } elseif($type == 2) {//applicability_status
            if(strpos($name,'not applicable') !== false || strpos($name,'not') !== false || strpos($name,'nepritaikoma') !== false || strpos($name,'nie') !== false || strpos($name,'nie stosowane') !== false) {
                $src = "WHERE applicability_status = ?";
                $appl_v = 0;
                $tip = "C";
            } elseif(strpos($name,'applicable') !== false || strpos($name,'pritaikoma') !== false || strpos($name,'stosowane') !== false) { 
                $src = "WHERE applicability_status = ?";
                $appl_v = 1;
                $tip = "C";
            } else {
                $src = "";
                $tip = "A";
            }
            $_SESSION['filter'] = array('type' => 2, 'name' => $name);
        } elseif($type == 3) {//implementation_status
            if(strpos($name,'planned') !== false || strpos($name,'planuojama') !== false || strpos($name,'planowany') !== false) {
                $src = "WHERE implementation_status = ?";
                $impl_v = 1;
                $tip = "D";
            } elseif(strpos($name,'implemented') !== false || strpos($name,'įgyvendinta') !== false || strpos($name,'zastosowany') !== false) { 
                $src = "WHERE implementation_status = ?";
                $impl_v = 2;
                $tip = "D";
            } elseif(strpos($name,'partial') !== false || strpos($name,'partial implementation') !== false || strpos($name,'ne visiškai') !== false || strpos($name,'ne visiškai įgyvendinta') !== false || strpos($name,'częściowy zastosowanie') !== false || strpos($name,'częściowy') !== false) { 
                $src = "WHERE implementation_status = ?";
                $impl_v = 3;
                $tip = "D";
            } elseif(strpos($name,'not') !== false || strpos($name,'not implemented') !== false || strpos($name,'neįgyvendinta') !== false || strpos($name,'nie zastosowany') !== false || strpos($name,'nie') !== false) { 
                $src = "WHERE implementation_status = ?";
                $impl_v = 4;
                $tip = "D";
            } else {
                $src = "";
                $tip = "A";
            }
            $_SESSION['filter'] = array('type' => 3, 'name' => $name);
        } else {
            $_SESSION['filter'] = array('type' => 0, 'name' => '');
        }
    }
} elseif(isset($_POST['btnClear'])) {
    $_SESSION['filter'] = array('type' => 0, 'name' => '');
} elseif(!empty($_SESSION['filter']['type']) && !empty($_SESSION['filter']['name'])) {
    $type = $_SESSION['filter']['type'];
    $name = $_SESSION['filter']['name'];
    if($type == 1) {//control_name
        $src = "WHERE control_name LIKE ?";
        $tip = "B";
    } elseif($type == 2) {//applicability_status
        if(strpos($name,'not applicable') !== false || strpos($name,'not') !== false || strpos($name,'nepritaikoma') !== false || strpos($name,'nie') !== false || strpos($name,'nie stosowane') !== false) {
            $src = "WHERE applicability_status = ?";
            $appl_v = 0;
            $tip = "C";
        } elseif(strpos($name,'applicable') !== false || strpos($name,'pritaikoma') !== false || strpos($name,'stosowane') !== false) {
            $src = "WHERE applicability_status = ?";
            $appl_v = 1;
            $tip = "C";
        } else {
            $src = "";
            $tip = "A";
        }
    } elseif($type == 3) {//implementation_status
        if(strpos($name,'planned') !== false || strpos($name,'planuojama') !== false || strpos($name,'planowany') !== false) {
            $src = "WHERE implementation_status = ?";
            $impl_v = 1;
            $tip = "D";
        } elseif(strpos($name,'implemented') !== false || strpos($name,'įgyvendinta') !== false || strpos($name,'zastosowany') !== false) { 
            $src = "WHERE implementation_status = ?";
            $impl_v = 2;
            $tip = "D";
        } elseif(strpos($name,'partial') !== false || strpos($name,'partial implementation') !== false || strpos($name,'ne visiškai') !== false || strpos($name,'ne visiškai įgyvendinta') !== false || strpos($name,'częściowy zastosowanie') !== false || strpos($name,'częściowy') !== false) { 
            $src = "WHERE implementation_status = ?";
            $impl_v = 3;
            $tip = "D";
        } elseif(strpos($name,'not') !== false || strpos($name,'not implemented') !== false || strpos($name,'neįgyvendinta') !== false || strpos($name,'nie zastosowany') !== false || strpos($name,'nie') !== false) { 
            $src = "WHERE implementation_status = ?";
            $impl_v = 4;
            $tip = "D";
        } else {
            $src = "";
            $tip = "A";
        }
    }
} else {
    $_SESSION['filter'] = array('type' => 0, 'name' => '');
}
?>
<!-- Filter -->
					<tr class="view-row">
						<td class="view-cell view-head">
							<p><?php echo $lengui['search'][$current]; ?></p>
							<form method="POST" action="controls.php">
								<select name="filter_type" class="form-control">
									<option value="0" <?php if(empty($_SESSION['filter']['type'])){ ?>selected="selected"<?php } ?>>--<?php echo $lengui['filter_type'][$current]; ?>--</option>
									<option value="1" <?php if($_SESSION['filter']['type'] == 1){ ?>selected="selected"<?php } ?>><?php echo $lengui['filter_type1'][$current]; ?></option>
									<option value="2" <?php if($_SESSION['filter']['type'] == 2){ ?>selected="selected"<?php } ?>><?php echo $lengui['filter_type2'][$current]; ?></option>
									<option value="3" <?php if($_SESSION['filter']['type'] == 3){ ?>selected="selected"<?php } ?>><?php echo $lengui['filter_type3'][$current]; ?></option>
								</select>
								<input type="text" name="filter_name" class="form-control" placeholder="" value="<?php echo htmlentities($_SESSION['filter']['name']); ?>">
								<input type="hidden" name="filter_sec" value="<?php echo $COOKIE_SECURITY; ?>">				
								<input type="submit" name="btnSearch" value="<?php echo $lengui['filter_search'][$current]; ?>" style="font-weight:bold;">
								<input type="submit" name="btnClear" value="<?php echo $lengui['filter_clear'][$current]; ?>" style="font-weight:bold;">
							</form>
						</td>
					</tr>
<?php
    $selectqry = "SELECT * FROM statement_of_applicability {$src};";
    
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
         case "C":
            $stmt = mysqli_prepare($connection, $selectqry);
            mysqli_stmt_bind_param($stmt,'i', $appl_v);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            break;
         case "D":
            $stmt = mysqli_prepare($connection, $selectqry);
            mysqli_stmt_bind_param($stmt,'i', $impl_v);
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
    $getQuery = "SELECT * FROM statement_of_applicability {$src} ORDER BY applicability_status DESC, control_name ASC LIMIT " . $initial_page . ',' . $limit;
    
    switch ($tip) {
        case "A":
            $resultLimit = mysqli_query($connection, $getQuery);
            break;
        case "B":
            $stmt2 = mysqli_prepare($connection, $getQuery);
            $qry = '%'.$name.'%';
            mysqli_stmt_bind_param($stmt2,'s', $qry);
            mysqli_stmt_execute($stmt2);
            $resultLimit = mysqli_stmt_get_result($stmt2);
            break;
         case "C":
            $stmt2 = mysqli_prepare($connection, $getQuery);
            mysqli_stmt_bind_param($stmt2,'i', $appl_v);
            mysqli_stmt_execute($stmt2);
            $resultLimit = mysqli_stmt_get_result($stmt2);
            break;
         case "D":
            $stmt2 = mysqli_prepare($connection, $getQuery);
            mysqli_stmt_bind_param($stmt2,'i', $impl_v);
            mysqli_stmt_execute($stmt2);
            $resultLimit = mysqli_stmt_get_result($stmt2);
            break;
    }
    
    //display the retrieved result on the webpage 
    if(mysqli_num_rows($result)>0) {
        while ($row = mysqli_fetch_array($resultLimit)) {
            $isms_un = $row["control_un"];
            $isms_control_name = $row["control_name"];
            $isms_control_description = $row["control_description"];
            $isms_applicability_status = $row["applicability_status"];
            $isms_justification_text = $row["justification_text"];
            $isms_implementation_status = $row["implementation_status"];
            $isms_control_owner_name = $row["control_owner_name"];
            $isms_review_date = $row["review_date"];
            $isms_review_status = $row["review_status"];
			
?>
<!-- Brief view -->
					<tr class="view-row">
						<td class="view-cell view-head">
							<?php echo $lengui['following_info'][$current]; ?> <?php echo htmlentities($isms_un, ENT_QUOTES, 'UTF-8'); ?>
							<form method="POST" action="manage.controls.php">
								<input type="hidden" name="isms_un" value="<?php echo $isms_un; ?>">
								<input type="hidden" name="manage_sec" value="<?php echo $COOKIE_SECURITY; ?>">							
								<button type="submit" name="manage" class="edit-buttons"><img src="images/edit.png" class="edit-icons"></button>
							</form>
						</td>
					</tr>					
					
					<tr class="view-row">
						<td class="view-cell">																					<!--control_name-->
							<font style="font-size: 20px; font-family: Tahoma, sans-serif;">
								<b><?php echo $lengui['isms_control_name'][$current]; ?></b> <?php echo htmlentities($isms_control_name, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--control_description-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['isms_control_description'][$current]; ?></b> <?php echo htmlentities($isms_control_description, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--applicability_status-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['isms_applicability_status'][$current]; ?></b> <?php echo htmlentities($lengui['isms_applicability_status'.$isms_applicability_status][$current], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--justification-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['isms_justification_text'][$current]; ?></b> <?php echo htmlentities($isms_justification_text, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--implementation_status-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['isms_implementation_status'][$current]; ?></b> <?php echo htmlentities($lengui['isms_implementation_status'.$isms_implementation_status][$current], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--control_owner-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['isms_control_owner_name'][$current]; ?></b> <?php echo htmlentities($isms_control_owner_name, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--review_date-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['isms_review_date'][$current]; ?></b> <?php echo htmlentities($isms_review_date, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--review_status-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['isms_review_status'][$current]; ?></b> <?php echo htmlentities($lengui['isms_review_status'.$isms_review_status][$current], ENT_QUOTES, 'UTF-8'); ?>
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
					<a href = "controls.php?page=<?php echo $page_number; ?>" class="pagenumbers"><?php echo $page_number; ?></a> 	
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
