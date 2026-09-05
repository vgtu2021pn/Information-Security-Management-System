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

$return = '/src/evidence.php';

// Simple language chooser
$lengui = array(
    'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
    'title' => array ('en' => 'Evidences - Information Security Management System', 'lt' => 'Įrodymai - Informacijos saugos valdymo sistema', 'pl' => 'Dowody - System zarządzania bezpieczeństwem informacji'),
    'nav_page' => array ('en' => 'Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistema', 'pl' => 'System zarządzania bezpieczeństwem informacji'),
    'nav_title' => array ('en' => 'Evidences of ISMS', 'lt' => 'ISVS įrodymai', 'pl' => 'Dowody SZBI'),
    'nav_title_new' => array ('en' => 'Add new evidence', 'lt' => 'Pridėti naują įrodymą', 'pl' => 'Dodawanie nowe dowody'),
    'nav_title_tabel' => array ('en' => 'Initiate backup', 'lt' => 'Įgyvendinti atsarginę kopiją', 'pl' => 'Inicjować kopia zapasowa'),
    'remove_notice' => array ('en' => 'Your evidence has been deleted.', 'lt' => 'Jūsų įrodymas buvo pašalintas.', 'pl' => 'Wasze dowody bylo usuwano.'),
    'create_notice' => array ('en' => 'Your evidence has been created.', 'lt' => 'Jūsų įrodymas buvo pridėtas.', 'pl' => 'Wasze dowody bylo prowadzone.'),
    'logout' => array ('en' => 'Log Out', 'lt' => 'Atsijungti', 'pl' => 'Wyloguj'),
    'evidence' => array ('en' => 'Evidences', 'lt' => 'Įrodymai', 'pl' => 'Dowody'),
    'isms' => array ('en' => 'ISMS', 'lt' => 'ISVS', 'pl' => 'SZBI'),
    'backup' => array ('en' => 'Backup of ISMS evidences', 'lt' => 'ISVS įrodymų atsarginė kopija', 'pl' => 'Kopia zapasowa o SZBI dowodach'),
    'following_info' => array ('en' => 'Information about evidence', 'lt' => 'Informacija apie įrodymus', 'pl' => 'Informacje o dowodach'),
    'artifact_id' => array ('en' => 'No. of the evidence unit', 'lt' => 'Įrodymo vieneto Nr.', 'pl' => 'Jednostka dowodowa Nr.'),
    'artifact_type' => array ('en' => 'The type of the evidence unit', 'lt' => 'Įrodymo vieneto tipas', 'pl' => 'Rodzaj jednostki dowodowej'),
    'explanation_of_artifact' => array ('en' => 'Explanation about the evidence unit', 'lt' => 'Įrodymo vieneto paaiškinimas', 'pl' => 'Wyjaśnienie dotyczące jednostki dowodowej'),
    'date_of_artifact' => array ('en' => 'Date of the evidence unit', 'lt' => 'Įrodymo vieneto data', 'pl' => 'Data jednostki dowodowej'),
    'artifact_owner' => array ('en' => 'Owner of the evidence unit', 'lt' => 'Įrodymo vieneto savininkas', 'pl' => 'Właściciel jednostki dowodowej'),
    'description_of_artifact_storage' => array ('en' => 'The location and storage of the evidence unit', 'lt' => 'Buvimo vieta ir saugykla įrodymo vienetui', 'pl' => 'Lokalizacja i przechowywanie dowodu'),
    'integrity_data_of_artifact' => array ('en' => 'Checksums', 'lt' => 'Kontrolinės sumos', 'pl' => 'Suma kontrolna') 
);

include_once("xlsxwriter.class.php");
include("credens.php");

$connection = mysqli_connect($servername, $user, $pw, $db);

if(!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}

if(isset($_GET["tabel"])) {
	if($_GET["tabel"] == 1) {
	$filename = "tabel-edc-".date("Ymd").".xlsx";
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	
	$writer = new XLSXWriter();
		
	$keywords = array($lengui['isms'][$current],$lengui['evidence'][$current]);
	$writer->setTitle($lengui['evidence'][$current]);
	$writer->setSubject($lengui['evidence'][$current]);
	$writer->setAuthor($lengui['isms'][$current]);
	$writer->setCompany($lengui['isms'][$current]);
	$writer->setKeywords($keywords);
	$writer->setDescription($lengui['backup'][$current].'.');
	
	$rows = array(
		array($lengui['backup'][$current])
	);
	$sheet1 = 'tabel';
	
	$header = array("string","string","integer","string","string","string");
		
	$selectqry = "SELECT `artifact_type`, `explanation_of_artifact`, `artifact_id`, `artifact_owner`, `description_of_artifact_storage`, `integrity_data_of_artifact` FROM `evidence`;";
	
	if($result=mysqli_query($connection,$selectqry)) {
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = [$row["artifact_type"],$row["explanation_of_artifact"],$row["artifact_id"],$row["artifact_owner"],$row["description_of_artifact_storage"],$row["integrity_data_of_artifact"]];
		}
		mysqli_free_result($result);
	}
		
	$writer->writeSheetHeader($sheet1, $header, $col_options = ['suppress_row'=>true] );
	foreach($rows as $row) {
		$writer->writeSheetRow($sheet1, $row);
	}
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
				<li><a href="manage.evidence.php"><?php echo $lengui['nav_title_new'][$current]; ?></a></li>
				<li><a href="evidence.php?tabel=1"><?php echo $lengui['nav_title_tabel'][$current]; ?></a></li>
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
    $selectqry = "SELECT * FROM evidence;";
	$result = mysqli_query($connection, $selectqry);
	
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
    $getQuery = "SELECT * FROM evidence ORDER BY date_of_artifact DESC LIMIT " . $initial_page . ',' . $limit;
    $resultLimit = mysqli_query($connection, $getQuery);

    //display the retrieved result on the webpage 
    if(mysqli_num_rows($result)>0) {
        while ($row = mysqli_fetch_array($resultLimit)) {
            $isms_un = $row["artifact_id"];
            $isms_artifact_type = $row["artifact_type"];
            $isms_explanation_of_artifact = $row["explanation_of_artifact"];
            $isms_date_of_artifact = $row["date_of_artifact"];
            $isms_artifact_owner = $row["artifact_owner"];
            $isms_description_of_artifact_storage = $row["description_of_artifact_storage"];
            $isms_integrity_data_of_artifact = $row["integrity_data_of_artifact"];
			
?>
<!-- Brief view -->
					<tr class="view-row">
						<td class="view-cell view-head">
							<?php echo $lengui['following_info'][$current]; ?>
														
							<form method="POST" action="manage.evidence.php">
								<input type="hidden" name="isms_un" value="<?php echo $isms_un; ?>">
								<input type="hidden" name="manage_sec" value="<?php echo $COOKIE_SECURITY; ?>">							
								<button type="submit" name="manage" class="edit-buttons"><img src="images/edit.png" class="edit-icons"></button>
							</form>
						</td>
					</tr>					
					
					<tr class="view-row">
						<td class="view-cell">																					<!-- -->
							<font style="font-size: 20px; font-family: Tahoma, sans-serif;">
								<b><?php echo $lengui['artifact_type'][$current]; ?></b> <?php echo htmlentities($isms_artifact_type, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!-- -->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['explanation_of_artifact'][$current]; ?></b> <?php echo htmlentities($isms_explanation_of_artifact, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!-- -->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['date_of_artifact'][$current]; ?></b> <?php echo htmlentities($isms_date_of_artifact, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!-- -->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['artifact_owner'][$current]; ?></b> <?php echo htmlentities($isms_artifact_owner, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!-- -->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['description_of_artifact_storage'][$current]; ?></b> <?php echo htmlentities($isms_description_of_artifact_storage, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!-- -->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['integrity_data_of_artifact'][$current]; ?></b> <?php echo htmlentities($isms_integrity_data_of_artifact, ENT_QUOTES, 'UTF-8'); ?>
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
					<a href = "evidence.php?page=<?php echo $page_number; ?>" class="pagenumbers"><?php echo $page_number; ?></a> 	
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
